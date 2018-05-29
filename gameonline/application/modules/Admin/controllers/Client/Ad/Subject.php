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
		'batchUpdateUrl'=>'/Admin/Client_Ad_Subject/batchUpdate'
	);
	
	public $perpage = 20;
	public $ad_type = 3;
	
	public $appCacheName = array("APPC_Client_Index_index","APPC_Channel_Index_index","APPC_Kingstone_Index_index");
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
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		$name = $this->getInput('name');
		$category_id = intval($this->getInput('category_id'));
		$params = array();
		$search = array();
		if($name) {
			$search['name'] = $name;
			$params['name'] = array('LIKE', $name);
		}
		if($category_id) {
			$search['category_id'] = $category_id;
		}		
		//所有首页推荐广告游戏id
		list(, $idx_games) = Client_Service_Ad::getAdGames(array('ad_type'=>$this->ad_type));
		$idx_games = Common::resetKey($idx_games, 'link');
		$this->assign('ad_games', $idx_games);
		$idx_games = array_keys($idx_games);
		
		//获取广告游戏的分类信息
		if($idx_games){
			$tmp = $category_games = $client_games = array();
			$adgame_categorys = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games)));//getIdxGameResourceCategorys();
			
			foreach($adgame_categorys as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					$category_title[$key][] = $categorys_id[$val1]['title'];
				}
			
			}
			
			//获取本地所有游戏
			if ($category_id) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $idx_games), 'status'=>1, 'category_id'=>$category_id,'game_status'=>1));
				$tmp = Common::resetKey($game_ids, 'game_id');
				$tmp = array_keys($tmp);
				if($game_ids){
					$params['id'] = array('IN',$tmp);
				} else {
					$params['create_time'] = 0;
				}
			
			} else {
				$params['id'] = array('IN',$idx_games);
			}
			
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
			foreach($games as $key=>$value){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
		}
		

	    $this->assign('subjects', $subjects);
		$this->assign('ad_type', $this->ad_type);
		$this->assign('category_title', $category_title);
		$this->assign('search', $search);
		$this->assign('games', $games);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('total', $total);
		$this->assign('ad_ptype', $this->ad_ptype);
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
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('name', 'status', 'isadd'));
	
		$search = $params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['name']) $search['name'] = $s['name'];
		$params['subject_id'] = $id;
	
		$info = Client_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
	
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
	
		list($total[0], $subject_games) = Client_Service_Game::getSubjectGames($params);
		$subject_games = Common::resetKey($subject_games, 'resource_game_id');
		$resource_game_ids = array_unique(array_keys($subject_games));
	
		if (count($resource_game_ids)) {
			$params['id'] = array('IN',$resource_game_ids);
			list($total[1], $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$games = Common::resetKey($games, "id");
		}
	
		$total = min($total);
		$this->cookieParams();
		$this->assign('total', $total);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('subject_games', $subject_games);
		$this->assign('games', $games);
		$this->assign('search', $s);
		$url = $this->actions['editCtUrl'].'/?id='.$id.'&' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
	
	
	//批量操作
	function batchUpdateAction() {
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
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_types', $this->ad_types);
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$this->assign('subjects', $subjects);
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
		
		$name = $this->getInput('name');
		$category_id = intval($this->getInput('category_id'));
		$params = array();
		$search = array();
		if($name) {
			$search['name'] = $name;
			$params['name'] = array('LIKE',$name);
		}
		if($category_id) {
			$search['category_id'] = $category_id;
		}
		$search['ad_type'] = $this->ad_type;
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//获取广告游戏
		list(, $idx_games) = Client_Service_Ad::getAdGames(array('ad_type'=>$this->ad_type));
		$idx_games = Common::resetKey($idx_games, 'link');
		$idx_games = array_keys($idx_games);
		$this->assign('idx_games', $idx_games);
		
		
		
	    //获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
			if($resource_game_ids){
				$params['id'] = array('IN',$resource_game_ids);
			} else {
				$params['create_time'] = 0;
			}
			
			if ($category_id) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$category_id,'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				if(!$ids){
					$params['create_time'] = 0;
				} else {
					$params['id'] = array('IN',$ids);
				}	
			}
			list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params);
		}

	    $this->assign('subjects', $subjects);
	    $this->assign('oline_versions', $oline_versions);
		$this->assign('ad_type', $this->ad_type);
		$this->assign('search', $search);
		$this->assign('games', $games);
		$this->assign('total', $total);
		$this->assign('ad_ptype', $this->ad_ptype);
		$this->assign('ad_type', $this->ad_type);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'ad_ptype', 'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceGames($info['link']);
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getResourceAttributeByTypeId($info['link'],1);
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$tip = "专题";
		}
		$msg = $this->_getMsg($adInfo, $tip,$info['ad_ptype'],$info['link']);
		$result = Client_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}	
		
	public function _getMsg($adInfo,$tip,$ad_ptype,$link) {
			if(!$adInfo)  {
				$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
			} else {
				$tmp = $this->_getData($ad_ptype);
				if (in_array($link,$tmp)) $this->output(-1, $tip.'链接ID已经存在不能重复添加');
			}
	}
		
	
	public function _getData($ad_ptype) {
		list(, $ads) = Client_Service_Ad::getAllAd();
		$tmp = array();
		foreach($ads as $key=>$value){
			if(($value['ad_type'] == $this->ad_type) && ($value['ad_ptype'] == $ad_ptype)){
				$tmp[] = $value['link'];
			}
		}
		return $tmp;
	}
	
	public function _getTypeData($type) {
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
