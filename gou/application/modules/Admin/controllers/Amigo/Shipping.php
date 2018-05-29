<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 自营商城快递公司管理功能
 * @author huangsg
 *
 */
class Amigo_ShippingController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Amigo_Shipping/index',
		'addUrl' => '/Admin/Amigo_Shipping/add',
		'addPostUrl' => '/Admin/Amigo_Shipping/add_post',
		'editUrl' => '/Admin/Amigo_Shipping/edit',
		'editPostUrl' => '/Admin/Amigo_Shipping/edit_post',
		'deleteUrl' => '/Admin/Amigo_Shipping/delete',
	);
	
	public $perpage = 20;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Amigo_Service_Shipping::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['listUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	public function addAction(){
		
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sort', 'status'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Shipping::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Shipping::getOne($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'sort', 'status'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Shipping::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Shipping::getOne($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Amigo_Service_Shipping::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}