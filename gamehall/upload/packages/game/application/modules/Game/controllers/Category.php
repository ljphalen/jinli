<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Game_BaseController{
    private $perpage = 10;
    //主分类属性表at_type:取值为1
    private $parentCategoryType = 1;
    //子分类属性表at_type:取值为10
    private $subCategoryType = 10;
    private $allGameCategoryId = 100;
    private $newGameCategoryId = 101;
    
	public $actions = array(
	                'listUrl' => '/category/index',
	                'listAjaxUrl' => '/category/listAjax',
	                'detailUrl' => '/category/detail/',
	                'detailAjaxUrl' => '/category/detailAjax/',
	                'gameDetailUrl' => '/index/detail/',
	);

	public function indexAction() {
	    
	    $hotSubject = $this->getHotSubject();
	    $categoryData = $this->getCategoryList();
	    $this->assign('hotSubject', json_encode($hotSubject, 1));
	    $this->assign('categoryList', json_encode($categoryData));
	}
	
	public function listAjaxAction() {
	    $data = $this->getCategoryList();
	    $this->ajaxOutput($data);
	}
	
	public function detailAction() {
	    $inputVar = $this->getInput(array('id', 'pid'));
	    $pCategory = Resource_Service_Attribute::get($inputVar['pid']);
	    $data = array(
	                    'id' => $inputVar['id'],
	                    'nameList' => $this->getSubCategoryList($inputVar['pid'], true),
	                    'gameList' => $this->getDetailGameList($inputVar)
	    );
	    $this->assign('title', $pCategory['title']);
	    $this->assign('data', json_encode($data));
	}
	
	public function detailAjaxAction() {
	    $inputVar = $this->getInput(array('id', 'pid'));
	    $data = $this->getDetailGameList($inputVar);
	    $this->ajaxOutput($data);
	}
	
    private function getHotSubject() {
	    $params['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
	    $startTime = strtotime(date('Y-m-d H:00:00'));
	    $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
	    $params['start_time'] = array('<=', $startTime);
	    $params['end_time'] = array('>=', $endTime);
	    $subjectList = Client_Service_Subject::getTopList($params, 2);
	    $list = array();
	    $attachPath = Common::getAttachPath();
	    foreach ($subjectList as $subject) {
	        $item = array(
	                        'id' => $subject['id'],
	                        'name' => $subject['title'],
	                        'imgUrl' => $attachPath. $subject['icon'],
	                        'type' => $subject['view_tpl'],
	                        'url' => Util_Statist::getSubjectDetailUrl($subject['id'])
	        );
	        $list[] = $item;
	    }
	    return array(
	                    'viewAllHref' => Util_Statist::getSubjectListUrl(),
	                    'list' => $list
	    );
	}
	
	private function getCategoryList() {
	    $page = $this->getPageInput();
	    $attachPath = Common::getAttachPath();
	    $params = array(
	                    'id'=>array('NOT IN', array($this->allGameCategoryId, $this->newGameCategoryId)), 
	                    'at_type' => $this->parentCategoryType, 
	                    'status'=>1
	    );
	    list($total, $result) = Resource_Service_Attribute::getList($page, $this->perpage, $params, array('sort'=>'DESC'));
	    $list = array();
	    if($result){
	        foreach ($result as $value){
	            $subItems = $this->getSubCategoryList($value['id'], false);
	            $list[] = array(
	                            "id" => $value['id'],
	                            "title" => html_entity_decode($value['title'], ENT_QUOTES),
	                            "url" => Util_Statist::getCategoryDetailUrl(0, $value['id']),
	                            "imgUrl" => $attachPath . $value['img2'],
	                            'items'=> $subItems
	            );
	        }
	    }
	    $hasNext = $total > $page * $this->perpage;
	    return array('list'=>$list, 'hasNext'=>$hasNext, 'curPage'=>$page);
	}
	
	private function getSubCategoryList($parentId, $isDetail=false) {
	    $queryParams = array(
	                    'at_type' => $this->subCategoryType, 
	                    'parent_id' => $parentId, 
	                    'status'=>1
	    );
	    $subCategory = Resource_Service_Attribute::getsBy($queryParams, array('sort' => 'DESC'));
	    $subItems = array();
	    if ($isDetail && $subCategory) {
	    	$subItems[] = array(
	                        "id" => 0,
	                        "title" => '全部',
	                        "url" => Util_Statist::getCategoryDetailUrl(0, $parentId)
	       );
	    }
	    foreach ($subCategory as $item){
	        $subItems[] = array(
	                        "id" => $item['id'],
	                        "title" => html_entity_decode($item['title'], ENT_QUOTES),
	                        "url" => Util_Statist::getCategoryDetailUrl($item['id'], $parentId)
	        );
	        if(!$isDetail && (count($subItems) >= 6)) {
	            break;
	        }
	    }
	    return $subItems;
	}
	
	private function getDetailGameList($inputVar) {
	    list($gameIds, $hasNext, $page, $total, $perpage) = $this->getGameIds($inputVar);
	    $list = array();
	    $baseIndex = ($page-1) * $perpage + 1;
	    foreach($gameIds as $key => $gameId) {
	        $game = Resource_Service_GameData::getGameAllInfo($gameId);
	        $list[] = array(
	                        'name' => $game['name'],
		                    'stars' => $game['web_star'],
		                    'size' => $game['size'].'M',
		                    'info' => $game['resume'],
		                    'typeName' => $game['category_title'],
		                    'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
		                    'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
		                    'imgUrl' => $game['img'],
	        );
	    }
	    $data = array(
	                    'list' => $list,
	                    'hasNext' => $hasNext,
	                    'curPage' => $page,
	                    'ajaxUrl' => $this->actions['detailAjaxUrl'].'?id='.$inputVar['id'].'&pid='.$inputVar['pid'].'&'.Util_Statist::getCurStatistStr()
	    );
	    return $data;
	}
	
	private function getGameIds($request) {
	    $page = $this->getPageInput();
	    //全部游戏|最新游戏
	    $games = array();
	    $gameIdField = '';
	    $perpage = $this->perpage;
	    if(in_array($request['pid'], array($this->allGameCategoryId, $this->newGameCategoryId))){
	        $params = array('status'=>1);
	        if($request['pid'] == $this->allGameCategoryId){
	            $orderBy = array('id'=>'DESC');
	        }else if($request['pid'] == $this->newGameCategoryId){
	            $orderBy = array('online_time'=>'DESC');
	            $limit = Game_Service_Config::getValue('game_rank_newnum');
	            $perpage = min($limit, $this->perpage);
	        }
	        list($total, $games) = Resource_Service_Games::getList($page, $perpage, $params, $orderBy);
	        $gameIdField = 'id';
	    } else {
	        $params = array(
	                        'parent_id' => $request['pid'],
	                        'game_status'=>1,
	                        'status'=>1
	        );
	        if($request['id']){
	            $params['category_id'] = $request['id'];
	        }
	        $orderBy = array('sort'=>'DESC','game_id'=>'DESC');
	    
	        if(isset($params['category_id'])){
	            //子分类检索
	            list($total, $games) = Resource_Service_GameCategory::getList($page, $perpage, $params, $orderBy);
	        }else{
	            list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $perpage, $params, $orderBy);
	        }
	        $gameIdField = 'game_id';
	    }
	    $gameIds = array();
	    foreach($games as $game) {
	        $gameIds[] = $game[$gameIdField];
	    }
	    $hasNext = $total > $page * $perpage;
	    return array($gameIds, $hasNext, $page, $total, $perpage);
	}
}
