<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class MessageController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Message/index',
		'detailUrl' => '/Admin/Message/detail',
		'deleteUrl' => '/Admin/Message/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$param = array('uid'=>$this->userInfo['uid']);
		list($total, $message) = Theme_Service_Message::getList($page, $perpage, $param);
		$this->assign('message', $message);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function detailAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Message::getMessage(intval($id));
		if(!$info || $info['uid'] != $this->userInfo['uid'] || !$id) $this->redirect($this->actions['listUrl']);
		Theme_Service_Message::updateMessage(array('status'=>1), $id);
		$this->assign('info', $info);
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Message::getMessage($id);
		if (($info && $info['id'] == 0) || $info['uid'] != $this->userInfo['uid']) $this->output(-1, '无法删除');
		$result = Theme_Service_Message::deleteMessage($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
