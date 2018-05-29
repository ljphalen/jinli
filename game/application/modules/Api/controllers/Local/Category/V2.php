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
    	//全部游戏|最新游戏
    	if(in_array($request['pid'], array(100,101))){
    		$params = array('status'=>1);
    		if($request['pid'] == '100'){
    			$orderBy = array('id'=>'DESC');
    			$perpage = $this->perpage;
    		}else if($request['pid'] == '101'){
    			$orderBy = array('online_time'=>'DESC');
    			$limit = Game_Service_Config::getValue('game_rank_newnum');
    			$perpage = min($limit, $this->perpage);
    		}
    		list($total, $result) = Resource_Service_Games::getList($page, $perpage, $params, $orderBy);
    	  } else {
    		$params = array(
    				'parent_id' => $request['pid'],
    				'game_status'=>1,
    				'status'=>1
    		);
    		$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
    		 
    		if($request['id']){
    			$params['category_id'] = $request['id'];
    		}
    		 
    		if(isset($params['category_id'])){
    			//子分类检索
    			list($total, $result) = Resource_Service_GameCategory::getList($page, $this->perpage, $params, $orderBy);
    		}else{
    			list($total, $result) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $params, $orderBy);
    		}
    	}
    	
    	$data = array();
    	$webroot = Common::getWebRoot();
    	foreach($result as $value) {
    			if(in_array($request['pid'], array('100','101'))){
    				$gameId = $value['id'];
    		   	}else{
    				$gameId = $value['game_id'];
    			}
    		
    			$info = Resource_Service_GameData::getGameAllInfo($gameId);
    			$data[] = array(
    				'img'=>urldecode($info['img']),
    				'name'=>html_entity_decode($info['name']),
    				'resume'=>html_entity_decode($info['resume']),
    				'package'=>$info['package'],
    				'link'=>$info['link'],
    				'gameid'=>$info['id'],
    				'size'=>$info['size'].'M',
    				'category'=>$info['category_title'],
    				'attach' => ($info['gift']) ? 1 : 0,
    				'hot' => Resource_Service_Games::getSubscript($info['hot']),
    				'viewType' => 'GameDetailView',
    				'score' => $info['client_star'],
    				'freedl' => $info['freedl'],
    				'reward' => $info['reward']
    			);
    	}
    	
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$response = array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
    	$this->localOutput('','',$response);
    }
}