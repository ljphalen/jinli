<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Operation_CategoryController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Operation_Category/index',
		'editUrl' => '/Admin/Operation_Category/edit',
		'editPostUrl' => '/Admin/Operation_Category/edit_post',
		'sortUrl' => '/Admin/Operation_Category/sort',
		'sortPostUrl' => '/Admin/Operation_Category/sort_post',
	);
	
	public $perpage = 20;

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $result) = Resource_Service_Attribute::getList($page, $perpage,array('at_type' => 1,'status' => 1));
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$info = $this->getInput(array('id', 'title'));

		//检索appid
		$app_id = array();
		$idxData = Resource_Service_IdxAppsCategory::getsBy(array('category_id' => $info['id']));
		if($idxData)  $app_id = array_keys(Common::resetKey($idxData, 'app_id'));

		$apps = Resource_Service_Apps::getsBy(array('status'=>1), array('create_time' => 'DESC'));
		$this->assign('category_id', $info['id']);
		$this->assign('title', $info['title']);
		$this->assign('app_id', $app_id);
		$this->assign('apps', $apps);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','ids', 'idx_app'));
		$info['create_time'] = time();
		$ret = Resource_Service_IdxAppsCategory::saveData($info);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 */
	public function sortAction(){
		$info = $this->getInput(array('id', 'title'));
		//检索appid
		$app_id = array();
		$idxData = Resource_Service_IdxAppsCategory::getsBy(array('category_id' => $info['id']), array('sort' => 'DESC', 'id' => 'DESC'));
		if ($idxData) {
			$idxData = Common::resetKey($idxData, 'app_id');
			$app_id = array_keys($idxData);
			$apps = Resource_Service_Apps::getsBy(array('id'=>array('IN',$app_id)));
			$apps = Common::resetKey($apps, 'id');
		}else {
			$idxData = $apps = array();
		}
	
		$this->assign('idx_data', $idxData);
		$this->assign('category_id', $info['id']);
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
		$ret = Resource_Service_IdxAppsCategory::saveSort($info);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
}
