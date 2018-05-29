<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class LabelController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Label/index',
		'addUrl' => '/Admin/Label/add',
		'addPostUrl' => '/Admin/Label/add_post',
		'editUrl' => '/Admin/Label/edit',
		'editPostUrl' => '/Admin/Label/edit_post',
		'deleteUrl' => '/Admin/Label/delete'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Game_Service_Label::getList($page, $perpage);
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_Label::getLabel(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Game_Service_Label::getLabel(intval($id));
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
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'status', 'hits'));
		$info = $this->_cookData($info);
		$result = Game_Service_Label::addLabel($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'status', 'hits'));
		$info = $this->_cookData($info);
		$ret = Game_Service_Label::updateLabel($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '游戏分类名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Game_Service_Label::getLabel($id);
		//get IdxLabel ByLabelId
		$game_ids = Game_Service_Game::getIdxLabelByLabelId(array('label_id'=>$id));
		if($game_ids ) $this->output(-1, '请先删除游戏列表里面含有该标签的游戏');
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Game_Service_Label::deleteLabel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	
}
