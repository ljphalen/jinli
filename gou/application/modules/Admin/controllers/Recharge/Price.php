<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 话费充值价格管理功能
 * @author huangsg
 *
 */
class Recharge_PriceController extends Admin_BaseController {
	public $actions = array(
			'indexUrl' => '/Admin/Recharge_Price/index',
			'addUrl' => '/Admin/Recharge_Price/add',
			'addPostUrl' => '/Admin/Recharge_Price/add_post',
			'editUrl' => '/Admin/Recharge_Price/edit',
			'editPostUrl' => '/Admin/Recharge_Price/edit_post',
			'deleteUrl' => '/Admin/Recharge_Price/delete',
	);
	
	public function indexAction(){
		$list = Recharge_Service_Price::getList();
		$this->assign('list', $list);
	}
	
	public function addAction(){}
	
	public function add_postAction(){
		$info = $this->getPost(array('price', 'sort', 'range', 'status'));
		$info = $this->_checkData($info);
		$ret = Recharge_Service_Price::addPrice($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Recharge_Service_Price::getPrice($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'price', 'range', 'sort', 'status'));
		$info = $this->_checkData($info);
		$ret = Recharge_Service_Price::updatePrice($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Recharge_Service_Price::getPrice($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Recharge_Service_Price::deletePrice($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['price']) $this->output(-1, '价格不能为空.');
		if (!$info['range']) $this->output(-1, '显示价格范围不能为空.');
		return $info;
	}
}