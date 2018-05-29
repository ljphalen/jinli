<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_ModelsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Models/index',
		'addUrl' => '/Admin/Resource_Models/add',
		'addPostUrl' => '/Admin/Resource_Models/add_post',
		'editUrl' => '/Admin/Resource_Models/edit',
		'editPostUrl' => '/Admin/Resource_Models/edit_post',
		'deleteUrl' => '/Admin/Resource_Models/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		if($page < 1) $page = 1;
		
		$status = intval($this->getInput('status'));
		$operators = $this->getInput('operators');
		$title = $this->getInput('title');
		$params = array();
		$search = array();
		
		if($operators) {
			$params['operators'] = $operators;
			$search['operators'] = $operators;
		}
		if($status) {
			$params['status'] = $status - 1;
			$search['status'] = $status;
		}
		
		if($title) {
			$params['title'] = $title;
			$search['title'] = $title;
		}
		
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>2));
		$operators = Common::resetKey($operators, 'id');
		
		list($total, $result) = Resource_Service_Models::getList($page, $perpage,$params);		
		$this->assign('result', $result);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('search', $search);
		$this->assign('operators', $operators);
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Models::getModel(intval($id));
		$this->assign('info', $info);
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>2));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Models::getModel(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $operators) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>2));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'operators', 'status'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Models::addModel($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'operators', 'status'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Models::updateModel($info, intval($info['id']));
		//更新机组中机型数据
        $ret = Resource_Service_Pgroup::updateModel($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '机型名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Models::getModel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$ret = Resource_Service_Models::deleteModel($id);
		//删除机组对应机型数据
		$ret = Resource_Service_Pgroup::delModel(intval($id));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
