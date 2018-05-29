<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_CategoryController extends Api_BaseController {
	public $perpage = 10;

    /**
     * 客户端本地化分类详情页
     */
    public function categoryInfoAction() {
        $id = $this->getInput('id');
        $webroot = Common::getWebRoot();

        $info = Resource_Service_Attribute::getBy(array('id'=>$id));

        $items = array(
            '0' => array(
                'title'=>'最新',
                'source'=>'new',
                'viewType'=>'NewestListView',
                'url'=>$webroot.'/Api/Local_Category/categoryNewList?id=' . $id,
            ),
            '1' => array(
                'title'=>'最热',
                'source'=>'hot',
                'viewType'=>'HotestListView',
                'url'=>$webroot.'/Api/Local_Category/categoryHotList?id=' . $id,
            )
        );
        $tmp['items'] = $items;
        header("Content-type:text/json");
        exit(json_encode(array(
            'success' => $items  ? true : false ,
            'msg' => '',
            'sign' => 'GioneeGameHall',
            'title'=> $id == 'caini'  ? '猜你喜欢' : html_entity_decode($info['title'], ENT_QUOTES),
            'data' => $tmp,
        )));
    }

    /**
     * 分类本地化最热
     */
    public function categoryHotListAction() {
        $id = intval($this->getInput('id'));
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));
        $imcrc = crc32(trim($imei));
        $page = $page ? intval($page) : 1;
        $data = $this->_categoryList($id, $page, $imcrc, 'hot');
        $this->localOutput('','',$data);
    }

    /**
     * 分类本地化最新
     */
    public function categoryNewListAction() {
        $id = intval($this->getInput('id'));
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));
        $imcrc = crc32(trim($imei));
        $page = $page ? intval($page) : 1;
        $data = $this->_categoryList($id, $page, $imcrc, 'new');
        $this->localOutput('','',$data);
    }

    private  function _categoryList($id, $page, $imcrc, $sortType) {
        $parmas = array(
            'categoryId' =>  0,
            'parentId' => $id,
            'page' => $page,
            'perPage' => $this->perpage,
            'imcrc' => $imcrc,
            'sortType' => $sortType,
        );
        $pageData = self::getPageData($parmas);
        $data = array(
            'list'=>$pageData['list'],
            'hasnext'=>$pageData['hasNext'],
            'curpage'=>$page,
            'totalCount'=>$pageData['total']);
        return $data;
    }

    private function getPageData($params){
        if(in_array($params['parentId'], array('100', '101'))){
            $parentId = Resource_Index_CategoryList::CATLIST_GAMES_PARENT_ID;
            $categoryId = 0;
            if($params['sortType'] == Resource_PageCache_CatGuessList::SORTTYPE_HOT) {
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_HOT;
            } else {
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_NEW;
            }
            return Resource_PageCache_CatList::getPage($params['page'], $parentId, $categoryId, $orderBy);
        } else if ($params['parentId'] == 'caini') {
            return self::getGuessData($params);
        } else {
            $parentId = $params['parentId'];
            $categoryId = 0;
            if($params['sortType'] == Resource_PageCache_CatGuessList::SORTTYPE_HOT) {
                $orderBy = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_HOT;
            } else {
                $orderBy = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_DEFAULT;
            }
            return Resource_PageCache_CatList::getPage($params['page'], $parentId, $categoryId, $orderBy);
        }
    }

    private function getGuessData($params){
        $data = array(
            'list' =>array(),
            'hasNext' =>false,
            'total' =>0
        );
        list($pageData, $hasNext, $total) = Resource_PageCache_CatGuessList::getPage($params);
        if(!$pageData){
            return $data;
        }
        $pageData = Resource_Service_GameListFormat::output($pageData);
        $data['list'] = $pageData;
        $data['hasNext'] = $hasNext;
        $data['total'] = $total;
        return $data;
    }
}