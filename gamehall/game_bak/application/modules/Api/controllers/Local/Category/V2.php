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
     * v1.5.6开始
     */
    public function indexAction() {
        $webroot = Common::getWebRoot();
        $attachPath = Common::getAttachPath();
        $page = intval($this->getInput('page'));
        $parentId = $this->getInput('pid');
        $perpage = 8;
        if ($page < 1) $page = 1;
        $params = array('id'=>array('NOT IN', array(100, 101)), 'at_type' => $this->parentCategoryType, 'status'=>1);
        if($parentId) $params['id'] = $parentId;

        list($total, $result) = Resource_Service_Attribute::getList($page, $perpage, $params, array('sort'=>'DESC'));
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
        $searchParmas = array(
            Resource_PageCache_Category::PARAMS_ID => $request['id'] ? $request['id'] : 0,
            Resource_PageCache_Category::PARAMS_PID => $request['pid'],
            Resource_PageCache_Category::PARAMS_PAGE => $page,
            Resource_PageCache_Category::PARAMS_PERPAGE => $this->perpage,
            Resource_PageCache_Category::PARAMS_FILTER => $this->filter,
            Resource_PageCache_Category::PARAMS_ACTIONSTR => '/api/local_category_v2/list/'
        );
        $cacheData = Resource_PageCache_Category::getPageCache($searchParmas);

        if($cacheData){
            list($total, $data) = $cacheData;
        } else {
            list($total, $games) = Resource_PageCache_Category::getSearchList($searchParmas);
            $data = $this->getFormatData($request, $games);
            Resource_PageCache_Category::savePageCache($searchParmas, array($total, $data));
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $response = array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
        $this->localOutput('','',$response);
    }

    private function getFormatData($params, $data){
        $result = array();
        foreach ($data as $value) {
            if (in_array($params['pid'], array('100', '101'))) {
                $gameId = $value['id'];
            } else {
                $gameId = $value['game_id'];
            }

            $info = Resource_Service_GameData::getGameAllInfo($gameId);
            $result[] = array(
                'img' => urldecode($info['img']),
                'name' => html_entity_decode($info['name']),
                'resume' => html_entity_decode($info['resume']),
                'package' => $info['package'],
                'link' => $info['link'],
                'gameid' => $info['id'],
                'size' => $info['size'] . 'M',
                'category' => $info['category_title'],
                'attach' => ($info['gift']) ? 1 : 0,
                'hot' => Resource_Service_Games::getSubscript($info['hot']),
                'viewType' => 'GameDetailView',
                'score' => $info['client_star'],
                'freedl' => $info['freedl'],
                'reward' => $info['reward']
            );
        }
        return $result;
    }
}