<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_SubjectController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Subject/index',
		'addUrl' => '/Admin/Client_Subject/add',
		'addPostUrl' => '/Admin/Client_Subject/add_post',
		'editCtUrl' => '/Admin/Client_Subject/editCt',
		'addCtUrl' => '/Admin/Client_Subject/addCt',
		'editUrl' => '/Admin/Client_Subject/edit',
		'editPostUrl' => '/Admin/Client_Subject/edit_post',
		'deleteUrl' => '/Admin/Client_Subject/delete',
		'uploadUrl' => '/Admin/Client_Subject/upload',
		'uploadPostUrl' => '/Admin/Client_Subject/upload_post',
		'uploadImgUrl' => '/Admin/Client_Subject/uploadImg',
		'viewUrl' => '/front/subject/goods/',
		'batchUpdateUrl'=>'/Admin/Client_Subject/batchUpdate'
	);
	
	public $perpage = 20;
	public $appCacheName = array("APPC_Channel_Subject_index","APPC_Client_Subject_index","APPC_Kingstone_Subject_index");
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		$params  = $this->getInput(array('title', 'status'));
		$search =  array();
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
		if ($params['status']) $search['status'] = $params['status'] - 1;
		
		$this->cookieParams();
		list($total, $subjects) = Client_Service_Subject::getList($page, $perpage,$search);
		$this->assign('subjects', $subjects);
		$this->assign('params', $params);
		$url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		$name = $this->getInput('name');
		
		$search = $params = array(); 
		if ($name) $search['name'] = array('LIKE',$name);
		$params['subject_id'] = $id;
		
		$info = Client_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		list(, $subject_games) = Client_Service_Game::getSubjectBySubjectId($params);
		$subject_games = Common::resetKey($subject_games, 'resource_game_id');
		$resource_game_ids = array_unique(array_keys($subject_games));
		
		if (count($resource_game_ids)) {
			$search['id'] = array('IN',$resource_game_ids);
			$search['status'] = 1;
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$games = Common::resetKey($games, "id");
		}
		
		$this->cookieParams();
		$this->assign('total', $total);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('subject_games', $subject_games);
		$this->assign('games', $games);
		$this->assign('name', $name);
		$url = $this->actions['editCtUrl'].'/?id='.$id.'&' . 'name=' . $name."&";
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
			$ret = Client_Service_Game::batchAddBySubject($info['ids'], $id);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Game::deleteGameclientBySubjectGames($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Game::sortGameclientBySubjectGames($info['ids'], $info['sort']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Subject::batchUpdate($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Subject::batchUpdate($info['ids'], 0);
			foreach ($info['ids'] as $val){
				//首页的专题列表的间隔图片链接对应的分类也下线
				Client_Service_Ad::updateByAd(array('status'=>$info['status']), array('ad_type'=>10, 'ad_ptype'=>3, 'link'=>$val));
				//首页图片广告对应的专题也下线
				Client_Service_Ad::updateByAd(array('status'=>$info['status']), array('ad_type'=>9, 'ad_ptype'=>3, 'link'=>$val));
			}
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Subject::getSubject(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','status','isadd', 'category_id'));
		$params = $search = array();
		
		if ($s['name']) {
			$search['name'] = $s['name'];
			$params['name'] = array('LIKE',$s['name']);
		}
		if ($s['status']) $search['status'] = $s['status'] - 1;
		if ($s['isadd']) $search['isadd'] = $s['isadd'];
		if ($s['category_id']) $search['category_id'] = $s['category_id'];
		
		$info = Client_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		//获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
			$params['id'] = array('IN',$resource_game_ids);
			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id'],'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
			    if($ids){
					$params['id'] = array('IN',$ids);
				} else {
					$params['id'] = 's';
				}
			}
			
			//获取主题索引表游戏
			list(, $idx_games) = Client_Service_Game::getSubjectBySubjectId(array('subject_id'=>intval($id),'game_status'=>1));
			$idx_games = Common::resetKey($idx_games, 'resource_game_id');
			$idx_games = array_keys($idx_games);
			$this->assign('idx_games', $idx_games);
			
		    //已经添加
			if ($search['isadd'] == 2) {
				if($s['category_id']){
					if(array_intersect($ids,$idx_games)){
						$params['id'] = array('IN',array_intersect($ids,$idx_games));
					} else {
						$params['id'] = 0;
					}
				} else {
					if(array_intersect($resource_game_ids,$idx_games)){
						$params['id'] = array('IN',array_intersect($resource_game_ids,$idx_games));
					} else {
						$params['id'] = 0;
					}
				}
				
			}
			//未添加
			if ($search['isadd'] == 1) {
				if($s['category_id']){
					if(array_diff($resource_game_ids,$idx_games)){
						if(array_intersect(array_diff($resource_game_ids,$idx_games),$ids)){
							$params['id'] = array('IN',array_intersect(array_diff($resource_game_ids,$idx_games),$ids));
						}
					} else {
						$params['id'] = 0;
					}
				} else {
					if(array_diff($resource_game_ids,$idx_games)){
						$params['id'] = array('IN',array_diff($resource_game_ids,$idx_games));
					} else {
						$params['id'] = 0;
					}
				}
			} 
			
		   if(!$params['id'])	$games = '';
		   list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
		}
		$this->assign('total', $total);
		$this->cookieParams();
		$this->assign('games', $games);
		$this->assign('search', $search);
		$this->assign('oline_versions', $oline_versions);
		$url = $this->actions['addCtUrl'].'/?id='.$id.'&' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	
	/**
	 *
	 * games list
	 */
	public function add_step2Action() {
	
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title','resume', 'hdinfo', 'icon', 'img', 'hot', 'start_time', 'end_time', 'status', 'hits'));
		$info = $this->_cookData($info);
		$info['create_time'] = Common::getTime();
		$result = Client_Service_Subject::addSubject($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title','resume','hdinfo', 'icon', 'img', 'hot', 'start_time', 'end_time', 'status', 'hits'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Subject::updateGameSubject($info, intval($info['id']));
		if($info['status'] == 0){
			//首页图片广告对应的专题也下线
			Client_Service_Ad::updateByAd(array('status'=>$info['status']), array('ad_type'=>9, 'ad_ptype'=>3, 'link'=>$info['id']));
			//首页的专题列表的间隔图片链接对应的分类也下线
			Client_Service_Ad::updateByAd(array('status'=>$info['status']), array('ad_type'=>10, 'ad_ptype'=>3, 'link'=>$info['id']));
		}
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['icon']) $this->output(-1, '图标不能为空.');
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		return $info;
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		
		//查找对应的游戏
		$info = Client_Service_Subject::getSubject($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Client_Service_Subject::deleteSubject($id);
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'subject');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'subject');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
