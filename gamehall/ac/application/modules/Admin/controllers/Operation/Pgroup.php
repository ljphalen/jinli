<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Operation_PgroupController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Operation_Pgroup/index',
		'editUrl' => '/Admin/Operation_Pgroup/edit',
		'editPostUrl' => '/Admin/Operation_Pgroup/edit_post',
		'sortUrl' => '/Admin/Operation_Pgroup/sort',
		'sortPostUrl' => '/Admin/Operation_Pgroup/sort_post',
	);
	
	public $perpage = 20;

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
	    $search = $this->getInput('pgroup');
	    $this->cookieParams();
		$groups = Resource_Service_Pgroup::getsBy(array('status'=>1));
		$this->assign('groups',$groups);
		
		$params = array();
		$params['status'] =1;
		if(!empty($search)) $params['id'] = array('IN', $search);
		//检索机组
		list($total, $result) = Resource_Service_Pgroup::getList($page, $perpage, $params);
		$this->assign('search',$search);
		$this->assign('result', $result);
		$this->assign("total", $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		
	}
	
	/**
	 * 
	 * edit an Pgroup
	 */
	public function editAction() {
		$info = $this->getInput(array('id', 'title'));
		//检索appid
		$app_id = array();
		$idxData = Resource_Service_IdxAppsPgroup::getsBy(array('pgroup_id' => $info['id']));
		$idxData = Common::resetKey($idxData, 'app_id');
		$app_id = array_keys($idxData);
		$apps = Resource_Service_Apps::getsBy(array('status'=>1), array('id' => 'DESC'));
		
		$this->assign('pgroup_id', $info['id']);
		$this->assign('title', $info['title']);
		$this->assign('app_id', $app_id);
		$this->assign('apps', $apps);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','ids','sort', 'idx_app'));
		$info['create_time'] = time();
		$ret = Resource_Service_IdxAppsPgroup::saveData($info);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->updateVersion('Apps_Data_Version');
		$this->output('0', '操作成功.');
	}
	
	public function sortAction(){
		$info = $this->getInput(array('id', 'title'));
		//检索appid
		$app_id = array();
		$idxData = Resource_Service_IdxAppsPgroup::getsBy(array('pgroup_id' => $info['id']), array('sort' => 'DESC', 'id' => 'DESC'));
		if ($idxData) {
			$idxData = Common::resetKey($idxData, 'app_id');
			$app_id = array_keys($idxData);
			$apps = Resource_Service_Apps::getsBy(array('id'=>array('IN',$app_id)));
			$apps = Common::resetKey($apps, 'id');
		}else {
			$idxData = $apps = array();
		}
		
		$this->assign('idx_data', $idxData);
		$this->assign('pgroup_id', $info['id']);
		$this->assign('title', $info['title']);
		$this->assign('apps', $apps);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function sort_postAction() {
		$info = $this->getPost(array('id','ids','sort'));
		$info['create_time'] = time();
		$ret = Resource_Service_IdxAppsPgroup::saveSort($info);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->updateVersion('Apps_Data_Version');
		$this->output('0', '操作成功.');
	}
}
