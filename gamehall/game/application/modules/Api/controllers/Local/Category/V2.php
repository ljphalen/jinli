<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_Category_V2Controller extends Api_BaseController {
	public $perpage = 10;
	//主分类属性表at_type:取值为1
	public $parentCategoryType = 1;
	//子分类属性表at_type:取值为10
    public $subCategoryType = 10;

    /**
     * 客户端分类首页
     * v1.5.6版本开始用
     * v1.5.9增加按用户访问显示开始
     */
    public function indexAction() {
        $webroot = Common::getWebRoot();
        $attachPath = Common::getAttachPath();
        $page = intval($this->getInput('page'));
        $parentId = (int) $this->getInput('pid');
        $sp = $this->getInput('sp');
        $imei = Common::parseSp($sp, 'imei');
        $perpage = 8;
        if ($page < 1) $page = 1;
        
        if ($parentId > 0 || trim($imei) == '') {
            $params = array('id'=>array('NOT IN', array(100, 101)), 'at_type' => $this->parentCategoryType, 'status'=>1);
            if ($parentId > 0) {
                $params['id'] = $parentId;
            }
            list($total, $result) = Resource_Service_Attribute::getList($page, $perpage, $params, array('sort'=>'DESC'));
        } else {
            list($total, $result) = Resource_Service_Attribute::getListByImeiSortPage(trim($imei), $page, $perpage);
        }
        $data = array();
        if($result){
            foreach ($result as $value){
                $subCategory = Resource_Service_Attribute::getsBy(array('at_type' => $this->subCategoryType, 'parent_id' => $value['id'], 'status'=>1),array('sort' => 'DESC'));
                $subItems = array();
                foreach ($subCategory as $item){
                    $subItems[] = array(
                            "id" => $item['id'],
                            "title" => html_entity_decode($item['title'], ENT_QUOTES),
                            "url" => "{$webroot}/Api/Local_Category_V2/list?id={$item['id']}&pid={$value['id']}"
                    );
                }
                $data[] = array(
                        "id" => $value['id'],
                        "title" => html_entity_decode($value['title'], ENT_QUOTES),
                        "url" => "{$webroot}/Api/Local_Category_V2/list?id=0&pid={$value['id']}",
                        "imageUrl" => $attachPath . $value['img2'],
                        'items'=> $subItems
                );
            }
        }
        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $response = array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page);
        $this->localOutput('','',$response);
    }

    /**
     * 客户端分类列表页
     * v1.5.6开始
     */
    public function listAction() {
        $request = $this->getInput(array('id', 'pid', 'page'));
        $page = $request['page'] ? $request['page'] : 1;
        $parentId = $request['pid'];
        $categoryId = $request['id'] ? $request['id'] : 0;
        if(in_array($parentId, array('100', '101'))){
            $parentId = 100;
            $categoryId = 0;
            $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_NEW;
        }else if($categoryId) {
            $orderBy = Resource_Index_CategoryList::CATLIST_SUB_ORDERBY;
        } else {
            $orderBy = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_DEFAULT;
        }
        $data = Resource_PageCache_CatList::getPage($page, $parentId, $categoryId, $orderBy);
        $response = array('list'=>$data['list'], 'hasnext'=>$data['hasNext'], 'curpage'=>$page, 'totalCount'=>$data['total']);
        $this->localOutput('','',$response);
    }
}