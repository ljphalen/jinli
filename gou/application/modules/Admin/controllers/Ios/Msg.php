<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Ios_MsgController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ios_Msg/index',
		'addUrl' => '/Admin/Ios_Msg/add',
		'addPostUrl' => '/Admin/Ios_Msg/add_post',
		'editUrl' => '/Admin/Ios_Msg/edit',
		'editPostUrl' => '/Admin/Ios_Msg/edit_post',
		'deleteUrl' => '/Admin/Ios_Msg/delete',
		'pushstepUrl' => '/Admin/Ios_Msg/pushstep',
		'pushUrl' => '/Admin/Ios_Msg/push',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $msgs) = Ios_Service_Msg::getList($page, $perpage);
		
		$this->assign('msgs', $msgs);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Ios_Service_Msg::getMsg(intval($id));		
		$this->assign('info', $info);
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
		//list($total, ) = Ios_Service_Msg::getList(1, 1);
		//if($total) $this->output(-1, '当前已有消息内容');
		$info = $this->getPost(array('title', 'content', 'url'));
		$info = $this->_cookData($info);
		$result = Ios_Service_Msg::addMsg($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'content', 'url'));
		$info = $this->_cookData($info);
		$ret = Ios_Service_Msg::updateMsg($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
		if(!$info['content']) $this->output(-1, '内容不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Ios_Service_Msg::getMsg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Ios_Service_Msg::deleteMsg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 * push step
	 */
	public function pushstepAction() {
		$id = $this->getInput('id');
		$info = Ios_Service_Msg::getMsg(intval($id));
		if(!$info || !$id) $this->redirect($this->actions['litsUrl']);
		$this->assign('id', $id);
	}
	
	/**
	 * pub msg
	 */
	public function pushAction() {
		$info = $this->getPost(array('id', 'imei', 'type_id'));
		
		$msg = Ios_Service_Msg::getMsg(intval($info['id']));
		if(!$msg || $info['id'] ==  0) $this->output(-1, 'push消息不存在.');
		if($msg['status'] == 1)  $this->output(-1, '该消息正在发送中');
		
		//query
		$queue = Common::getQueue();
		
		if($info['type_id'] == 2) {
			$imeis = explode(';', $info['imei']);
			foreach ($imeis as $key=>$value) {
				$token = Ios_Service_Token::getByToken($value);
				if($token) {
					$queue->noRepeatPush('token_'.$info['id'], $value);
				}
			}
		}
		
		//push msg id 到队列
		$queue->noRepeatPush('ios_push_msg', $info['id']);
		//$this->showMsg(-1, '发送中...');
		$this->output(0, '消息提交成功，等待服务器发送……');
	}
}
