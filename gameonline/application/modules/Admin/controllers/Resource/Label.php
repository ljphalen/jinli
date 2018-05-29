<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_LabelController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Label/index',
		'addUrl' => '/Admin/Resource_Label/add',
		'addPostUrl' => '/Admin/Resource_Label/add_post',
		'editUrl' => '/Admin/Resource_Label/edit',
		'editPostUrl' => '/Admin/Resource_Label/edit_post',
		'deleteUrl' => '/Admin/Resource_Label/delete'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('title','btype','status'));
		$params = array();
		
		if ($s['title']) $params['title'] = $s['title'];
		if ($s['btype']) $params['btype'] = $s['btype'];
		if ($s['status']) $params['status'] = $s['status'] - 1;
		
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>8,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
				
		list($total, $result) = Resource_Service_Label::getUseLabel($page, $this->perpage,$params);
		$this->assign('result', $result);
		$this->assign('categorys', $categorys);
		$this->assign('s', $s);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Label::getLabel(intval($id));
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>8,'status'=>1));
		$this->assign('categorys', $categorys);
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Label::getLabel(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>8,'status'=>1));
		$this->assign('categorys', $categorys);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'btype', 'status'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Label::addLabel($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'btype', 'status'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Label::updateLabel($info, intval($info['id']));
	    $labels = Resource_Service_Games::getIdxLabelByLabelId(intval($info['id']));
		if($labels){
			Resource_Service_Games::updateIdxLabelByLabelIdStatus(intval($info['id']),$info['status']);
		}
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标签名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Resource_Service_Label::getLabel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Label::deleteLabel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	
}
