<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Ad_SubjectController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Subject/index',
		'addUrl' => '/Admin/Client_Ad_Subject/add',
		'addPostUrl' => '/Admin/Client_Ad_Subject/add_post',
		'editUrl' => '/Admin/Client_Ad_Subject/edit',
		'editCtUrl' => '/Admin/Client_Ad_Subject/editCt',
		'addCtUrl' => '/Admin/Client_Ad_Subject/addCt',
		'editPostUrl' => '/Admin/Client_Ad_Subject/edit_post',
		'deleteUrl' => '/Admin/Client_Ad_Subject/delete',
		'uploadUrl' => '/Admin/Client_Ad_Subject/upload',
		'uploadPostUrl' => '/Admin/Client_Ad_Subject/upload_post',
		'batchUpdateUrl'=>'/Admin/Client_Ad_Subject/batchUpdate',
	    

	    'bannerUrl' => '/Admin/Client_Ad_Turn/index',
	    'imgUrl' => '/Admin/Client_Ad_Picture/index',
	    'recImgUrl' => '/Admin/Client_Ad_Recpic/index',
	    'recListUrl' => '/Admin/Client_Ad_Subject/index',
	    'imgLogUrl' => '/Admin/Client_Ad_Recommend/index',
	    'oldListUrl' => '/Admin/Client_Ad_Recommendold/index',
	    
	);
	
	public $perpage = 20;
	public $ad_type = 3;
	
	public $appCacheName = array("APPC_Client_Index_index","APPC_Channel_Index_index");
	//广告链接ID
	public $ad_ptype = array(
			1 => '内容',
			2 => '分类',
			3 => '专题'
	);
	public $versionName = 'Ad_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1,'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		//开始搜索
		$search = $this->getInput(array('name', 'category_id'));
		$params = $gameParams = $categoryParams = $info =  array();
		$params = array('ad_type'=>$this->ad_type, 'status'=>1);
		if($search['name']) { 
			//游戏名称检索
			$gameParams['status'] = 1;
			$gameParams['name'] = array('LIKE', $search['name']);
			$gameData = Resource_Service_Games::getsBy($gameParams);
			if($gameData){
				$gameData = Common::resetKey($gameData, 'id');
				$gameDataIds = array_keys($gameData);
			}else{
				$gameDataIds = array(0);
			}
			$params['link'] = array('IN', $gameDataIds);
		}
		if($search['category_id']){
			//游戏一级分类检索
			$categoryParams = array('parent_id' => $search['category_id'], 'status' => 1, 'game_status' => 1);
			if($gameDataIds){
				$categoryParams['game_id'] = array('IN', $gameDataIds);
			}
			$categoryGame = Resource_Service_GameCategory::getsBy($categoryParams);
			if($categoryGame){
				$categoryGame = Common::resetKey($categoryGame, 'game_id');
				$categoryGameIds = array_keys($categoryGame);
			}else{
				$categoryGameIds =array(0);
			}
			$params['link'] = array('IN', $categoryGameIds);
		}	
		
		list($total, $adGame) = Client_Service_Ad::getList($page, $this->perpage, $params, array('sort'=>'desc', 'id'=>'desc'));
		if($adGame){
			foreach($adGame as $key=>$value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['link']));
				$value['gameName'] = $game['name'];
				$value['gameCategory']  = $game['category_title'];
				$value['gameIcon'] = $game['img'];
				$value['gameSize'] = $game['size'];
				$value['gameVersion'] = $game['version'];
				$info[] = $value;
			}
		}

		$this->assign('ad_type', $this->ad_type);
		$this->assign('search', $search);
		$this->assign('games', $info);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Ad::getAd(intval($id));
		$game_info = Resource_Service_Games::getResourceGames($info['link']);
		$this->assign('game_info', $game_info);
		$this->assign('ad_types', $this->ad_types);
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'ad_type', 'ad_ptype',  'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		$id = $this->getInput('id');
		$edit_info = Client_Service_Ad::getAd(intval($id));
		
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceGames($info['link']);
			$temp = $this->_getTypeData(1);
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getResourceAttributeByTypeId($info['link'],1);
			$temp = $this->_getTypeData(2);
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$temp = $this->_getTypeData(3);
			$tip = "专题";
		}
		
		if ((in_array($info['link'],$temp) && $edit_info['link'] != $info['link']) || (in_array($info['link'],$temp) && $edit_info['link'] == $info['link'] && $edit_info['ad_ptype'] != $info['ad_ptype']) ) {
			$this->output(-1, $tip.'链接ID已经存在不能重复添加');
		} else if(!in_array($info['link'],$temp) && !$adInfo){
			$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
		}
		$ret = Client_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	//批量操作
	public function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Ad::addGameAd($info['ids'], $this->ad_type);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Ad::deleteGameAd($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Ad::sortGameAd($info['ids'], $info['sort']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Ad::updateGameAd($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Ad::updateGameAd($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1, 'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		//获取广告游戏
		$adGame = Client_Service_Ad::getsBy(array('ad_type'=>$this->ad_type));
		$adGame = Common::resetKey($adGame, 'link');
		$adGame = array_keys($adGame);
		$this->assign('adGame', $adGame);
		
		//开始搜索
		$search = $this->getInput(array('name', 'category_id'));
		$params = $gameParams = $categoryParams = $info =  array();
		$params = array('status'=>1);
		if($search['name']) {
			//游戏名称检索
			$gameParams['status'] = 1;
			$gameParams['name'] = array('LIKE', $search['name']);
			$gameData = Resource_Service_Games::getsBy($gameParams);
			if($gameData){
				$gameData = Common::resetKey($gameData, 'id');
				$gameDataIds = array_keys($gameData);
			}else{
				$gameDataIds = array(0);
			}
			$params['id'] = array('IN', $gameDataIds);
		}
		if($search['category_id']){
			//游戏一级分类检索
			$categoryParams = array('parent_id' => $search['category_id'], 'status' => 1, 'game_status' => 1);
			if($gameDataIds){
				$categoryParams['game_id'] = array('IN', $gameDataIds);
			}
			$categoryGame = Resource_Service_GameCategory::getsBy($categoryParams);
			if($categoryGame){
				$categoryGame = Common::resetKey($categoryGame, 'game_id');
				$categoryGameIds = array_keys($categoryGame);
			}else{
				$categoryGameIds =array(0);
			}
			$params['id'] = array('IN', $categoryGameIds);
		}
		
		list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params);
		if($games){
			foreach($games as $value){
				$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
				$info[] = $game;
			}
		}
		$this->assign('ad_type', $this->ad_type);
		$this->assign('search', $search);
		$this->assign('games', $info);
		$this->assign('total', $total);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	private function _getTypeData($type) {
		list(, $ads) = Client_Service_Ad::getAllAd();
		$tmp = array();
		foreach($ads as $key=>$value){
			if(($value['ad_type'] == $this->ad_type) && ($value['ad_ptype'] == $type)){
				$tmp[] = $value['link'];
			}
		}
		return $tmp;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
		if(!$info['link']) $this->output(-1, '广告链接不能为空.'); 
		if(!$info['ad_ptype']) $this->output(-1, '链接类型不能为空.');
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'ad');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
