<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_PgroupController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Pgroup/index',
		'addUrl' => '/Admin/Resource_Pgroup/add',
		'addPostUrl' => '/Admin/Resource_Pgroup/add_post',
		'editCtUrl' => '/Admin/Resource_Pgroup/editCt',
		'addCtUrl' => '/Admin/Resource_Pgroup/addCt',
		'editUrl' => '/Admin/Resource_Pgroup/edit',
		'editPostUrl' => '/Admin/Resource_Pgroup/edit_post',
		'deleteUrl' => '/Admin/Resource_Pgroup/delete',
		'filerListUrl' => '/Admin/Resource_Pgroup/filerList',
		'filerAddUrl' => '/Admin/Resource_Pgroup/filerAdd',
		'uploadUrl' => '/Admin/Resource_Pgroup/upload',
		'uploadPostUrl' => '/Admin/Resource_Pgroup/upload_post',
		'uploadImgUrl' => '/Admin/Resource_Pgroup/uploadImg',
		'batchUpdateUrl'=>'/Admin/Resource_Pgroup/batchUpdate',
		'batchOperateUrl'=>'/Admin/Resource_Pgroup/batchOperate',
		'deleteFilterGamesUrl' => '/Admin/Resource_Pgroup/deleteFilterGames',
		'importUrl' => '/Admin/Resource_Pgroup/import',
		'importPostUrl' => '/Admin/Resource_Pgroup/importPost',
	);
	
	public $perpage = 20;
	public $appCacheName = "APPC_Resource_Pgroup_index";
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
	    $title = $this->getInput('title');
		if ($page < 1) $page = 1;
		
		$search = array();
		if($title) {
			$search['title'] = $title;
		}
		
		$this->cookieParams();
		list($total, $pgroups) = Resource_Service_Pgroup::getSortPgroup($page, $perpage,$search);
		$this->assign('pgroups', $pgroups);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an Pgroup
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$this->assign('info', $info);
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$modes = Common::resetKey($modes, 'id');
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
		
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort', 'title'));
		$edit_info = $this->getPost(array('p_title', 'p_id'));
		if (!count($info['ids']) && $info['action'] !='edit_title') $this->output(-1, '未添加机型.');
		sort($info['ids']);
		if(!$info['title']) $this->output(-1, ' 机组名称不能为空.');
		if($info['action'] =='add'){
			$this->checkUniquePhoneModel($info['ids'],$id);
			$ret = Resource_Service_Pgroup::batchAddByPgroup($info['ids'],$info['title']);
		} else if($info['action'] =='delete'){
			$ret = Resource_Service_Pgroup::batchDeleteByPgroup($info['ids'],$id,$info['title']);
		} else if($info['action'] =='edit'){
			$this->checkUniquePhoneModel($info['ids'],$id);
			$ret = Resource_Service_Pgroup::batchUpdateByPgroup($info['ids'],$id,$info['title']);
		} else if($info['action'] =='edit_title'){
			$data = array();
			$data['id'] = intval($id);
			$data['title'] = $info['title'];
			$data['p_title'] = html_entity_decode($edit_info['p_title']);
			$data['p_id'] = html_entity_decode($edit_info['p_id']);
			$data['create_time'] =  Common::getTime();
			$ret = Resource_Service_Pgroup::updatePgroup($data, intval($id));
		}  else if($info['action'] =='addGames'){
			$filterInfo = Resource_Service_FilterGame::mutiGames($info['ids'],$id);
		}   else if($info['action'] =='deleteGames'){
			$filterInfo = Resource_Service_FilterGame::deleteGames($info['ids'],$id);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		Resource_Service_Pgroup::updateModeGroupCacheData();
		$this->output('0', '操作成功.');
	}
	
	function checkUniquePhoneModel($phoneModelIds,$pgroupId) {
		if(!$phoneModelIds)  $this->output(-1, '未添加机型.');
		foreach($phoneModelIds as $key=>$value){
			$groups = Resource_Service_Pgroup::getPgroupBymodelId($value);
			if($groups){
				$modelIds = $this->getModelIds($groups);
				if(in_array($value, $modelIds)){
					$this->output(-1, ' 该机组机型在其他组有重复，不能重复添加.');
				}
			}
		}
		
	}
	
	function getModelIds($pgroups) {
		$modelIds = array();
		if(!$pgroups) return array();
		foreach($pgroups as $key=>$value){
			$pIds = array();
			$pIds = explode(',',$value['p_id']);
			$modelIds = $this->organizeArray($pIds);
		}
		return $modelIds;
	}
	
	
	function organizeArray($pIds){
		$retIds = array();
		foreach($pIds as $key=>$value){
			$retIds[] = $value;
		}
		return $retIds;
	}

	function batchOperateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='addGames'){
			$ret = Resource_Service_FilterGame::mutiGames($info['ids'],$id);
		} else if($info['action'] =='deleteGames'){
			$ret = Resource_Service_FilterGame::deleteGames($info['ids'],$id);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$id = $this->getInput('id');
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addCtAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$this->assign('info', $info);
		list(, $modes) = Resource_Service_Type::getAllResourceSortType();
		$this->assign('modes', $modes);
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$groups_ids = array_unique(array_keys($groups));
		$this->assign('groups_ids', $groups_ids);
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		$this->assign('resolution', $resolution);
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>6));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'p_title', 'p_id', 'create_time'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Pgroup::updatePgroup($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		return $info;
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Resource_Service_Pgroup::getPgroup($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Pgroup::deletePgroup($id);
		Resource_Service_FilterGame::deleteFilterGamesCache($id);
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
		$ret = Common::upload('imgFile', 'pgroup');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Pgroup');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    /**
     *
     * Enter description here ...
     */
    public function filerListAction() {
		$s = $this->getInput(array('name','game_id','id','page'));
		$params = $search  = $gameIds = $filterInfos = array();
		if ($s['page'] < 1) $s['page'] = 1;
		
		$groupInfo = Resource_Service_Pgroup::getPgroup($s['id']);
		
  	    if ($s['name']) $params['name'] = array('LIKE', $s['name']);
  	    if ($s['game_id']) $params['id'] = array('LIKE', $s['game_id']);

  	    if($params){
		    $params['status'] = Resource_Service_Games::STATE_ONLINE;
  	    	$games = Resource_Service_Games::getGamesByGameNames($params);
  	    	$games = Common::resetKey($games, 'id');
  	    	$gameIds = array_unique(array_keys($games));
  	    	if($gameIds){
  	    		$search['game_id'] = array('IN',$gameIds);
  	    	} else {
  	    		$search['game_id'] = 0;
  	    	}
  	    }
  	    $search['pgroup_id'] = intval($s['id']);
	    
		list($total, $filterGames) = Resource_Service_FilterGame::getList($s['page'], $this->perpage, $search, array('id'=>'DESC'));
		
		if($filterGames){
			foreach($filterGames as $k=>$v){
				$Info = array();
				$Info = Resource_Service_GameData::getGameAllInfo($v['game_id']);
				
				$Info['game_id'] = $v['game_id'];
				$Info['id'] = $v['id'];
				$gamesInfo[] = $Info;
				$filterInfos[$v['game_id']] = $v['create_time'];
			}
		}
		
		$this->assign('total', $total);
		$this->assign('groupInfo', $groupInfo);
		$this->assign('gamesInfo', $gamesInfo);
		$this->assign('filterInfos', $filterInfos);
		$this->assign('s', $s);
		$url = $this->actions['filerListUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $s['page'], $this->perpage, $url));
    }
    
    /**
     *
     * Enter description here ...
     */
    public function filerAddAction() {
    	$s = $this->getInput(array('name','game_id','id','page'));
    	if ($s['page'] < 1) $s['page'] = 1;
    	
    	$params = $info  = $gameIds = $filterInfos = $gameInfo = array();
    	$groupInfo = Resource_Service_Pgroup::getPgroup($s['id']);
    	$params['status'] = 1;
    	if ($s['name']) $params['name'] = array('LIKE', $s['name']);
    	if ($s['game_id']) $params['id'] = array('LIKE', $s['game_id']);
    	 
    	list($total, $games) = Resource_Service_Games::getList($s['page'], $this->perpage, $params, array('id'=>'DESC'));
    	if($games){
    		foreach($games as $k=>$v){
    			$Info = array();
    			$Info = Resource_Service_GameData::getGameAllInfo($v['id']);
    			$filterInfo = Resource_Service_FilterGame::getBy(array('game_id'=>$v['id'],'pgroup_id'=>$s['id']));
    			$gameIds[] = $filterInfo['game_id'];
    			$filterInfos[$v['id']] = $filterInfo['create_time'];
    			$gamesInfo[] = $Info;
    		}
    	}
    	
    	$alreadyTotal = Resource_Service_FilterGame::count(array('status'=>1,'pgroup_id'=>$s['id']));
    
    	$this->assign('alreadyTotal', $alreadyTotal);
    	$this->assign('total', $total);
    	$this->assign('groupInfo', $groupInfo);
    	$this->assign('gamesInfo', $gamesInfo);
    	$this->assign('gameIds', $gameIds);
    	$this->assign('filterInfos', $filterInfos);
    	$this->assign('s', $s);
    	$url = $this->actions['filerAddUrl'].'/?' . http_build_query($s) . '&';
    	$this->assign('pager', Common::getPages($total, $s['page'], $this->perpage, $url));
    }
    
    /**
     *
     * Enter description here ...
     */
    public function deleteFilterGamesAction() {
    	$id = intval($this->getInput('id'));
    	$result = Resource_Service_FilterGame::deleteFilterGame($id);
    	if (!$result) $this->output(-1, '操作失败');
    	$this->output(0, '操作成功');
    }
    
    public function importAction() {
    	$id = $this->getInput('id');    	 
    	$info = Resource_Service_Pgroup::getPgroup($id);
    	$this->assign('info', $info);
    	$this->assign('id', $id);
    }
    
    public function importPostAction() {
    	$id = intval($this->getInput('id'));
		$packages = $this->getPost(array(array('packages', '#s_zb')));
		$gamePackages = explode("<br />",html_entity_decode($packages['packages']));
		$gamePackages = array_unique($gamePackages);
		
		$filterPackages = array();
		foreach ($gamePackages as $k=>$v) {
			if ($v) $filterPackages[] = $v;
		}
		
		if(empty($filterPackages) && !count($filterPackages)){
			$this->output(-1, '添加包名不能为空.');
		}
		
		if(count($filterPackages) >= 100){
			$this->output(-1, '添加包名不能超过100个.');
		}
		
		$filterGames = $this->assembleFilterData($filterPackages, $id);	
		if($filterGames){
		  $ret = Resource_Service_FilterGame::mutiFilterGames($filterGames);
		  if (!$ret) $this->output(-1, '操作失败');
		  $this->output(0, '操作成功');
		} else {
			$this->output(-1, '以上游戏包名不存在或者已经添加，请确认导入的包名是否正确!');
		}		
    }
    
    private function assembleFilterData($filterPackages, $id) {
      $filterGames = array();
      foreach($filterPackages as $key=>$value){
			$info = array();
			$info = Resource_Service_Games::getBy(array('package'=>$value));
			if(!$info || !$info['status']) continue;
			$checkGame = Resource_Service_FilterGame::getBy(array('pgroup_id'=>$id,'game_id'=>$info['id']));
			if(!$checkGame){
				$filterGames[] = array(
						'id'=>'',
						'status'=>1,
						'pgroup_id'=>$id,
						'game_id'=>$info['id'],
						'create_time'=>Common::getTime(),
				);
			}
		}
		return $filterGames;
    }
}
