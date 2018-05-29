<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Resource_PgroupController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Pgroup/index',
		'addUrl' => '/Admin/Resource_Pgroup/add',
		'addPostUrl' => '/Admin/Resource_Pgroup/add_post',
		'editUrl' => '/Admin/Resource_Pgroup/edit',
		'editPostUrl' => '/Admin/Resource_Pgroup/edit_post',
		'deleteUrl' => '/Admin/Resource_Pgroup/delete',
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
	    $title = $this->getInput('title');
	    $this->cookieParams();
		$params = array();
		if($title) $params['p_title'] = array('FIND', $title);
			
		list($total, $pgroups) = Resource_Service_Pgroup::getList($page, $perpage, $params);

		$this->assign('pgroups', $pgroups);
		$this->assign('title', $title);
		$url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $modes) = Resource_Service_Models::getAllModel();
		$this->assign('modes', $modes);
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>2));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'ids', 'lables','status'));
		$info = $this->_cookData($info);
		foreach ($info['ids'] as $value){
			$p_title[] = $info['lables'][$value];
		}
	
		$data = array();
		$data['title'] = $info['title'];
		$data['p_title'] = implode(',', $p_title);
		$data['p_id'] = implode(',', $info['ids']);
		$data['status'] = $info['status'];
		$data['create_time'] =  Common::getTime();
		$ret = Resource_Service_Pgroup::addPgroup($data);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * edit an Pgroup
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Pgroup::getPgroup(intval($id));
		$this->assign('info', $info);
		
		list(, $modes) = Resource_Service_Models::getAllModel();
		$modes = Common::resetKey($modes, 'id');
		$this->assign('modes', $modes);
	
		$operators = Resource_Service_Attribute::getsBy(array('at_type'=>2));
		$operators = Common::resetKey($operators, 'id');
		$this->assign('operators', $operators);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'ids', 'lables','status'));
		$info = $this->_cookData($info);
		foreach ($info['ids'] as $value){
			$p_title[] = $info['lables'][$value];
		}
	
		$data = array();
		$data['title'] = $info['title'];
		$data['status'] = $info['status'];
		$data['p_title'] = implode(',', $p_title);
		$data['p_id'] = implode(',', $info['ids']);
		$data['status'] = $info['status'];
		$data['create_time'] =  Common::getTime();
		$ret = Resource_Service_Pgroup::updatePgroup($data,$info['id']);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
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
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.');
		if(!count($info['ids'])) $this->output(-1, '未选择添加机型.');
		return $info;
	}
}
