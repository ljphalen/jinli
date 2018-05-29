<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class UserController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/User/index',
		'addUrl' => '/Admin/User/add',
		'addPostUrl' => '/Admin/User/add_post',
		'editUrl' => '/Admin/User/edit',
		'editPostUrl' => '/Admin/User/edit_post',
		'deleteUrl' => '/Admin/User/delete'
	);
	
	public $perpage = 20;
	public $status = array(
		'1'=>'启用',
		'0'=>'禁用'
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $users) = Admin_Service_User::getList($page, $perpage);
		list(,$groups) = Admin_Service_Group::getAllGroup();
		$groups = Common::resetKey($groups, 'groupid');
		
		$this->assign('users', $users);
		$this->assign('groups', $groups);
		$this->assign('status', $this->status);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$uid = $this->getInput('uid');
		$userInfo = Admin_Service_User::getUser(intval($uid));
		list(,$groups) = Admin_Service_Group::getAllGroup();
		$this->assign('userInfo', $userInfo);
		$this->assign('groups', $groups);
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(,$groups) = Admin_Service_Group::getAllGroup();
		$this->assign('groups', $groups);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('username','password','r_password','email','groupid'));
		if (strlen($info['username']) < 5 || strlen($info['username']) > 16) $this->output(-1, '用户名长度5-16位之间');
		if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间.');
		if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致.');
		if ($info['email'] == '') $this->output(-1, '用户EMAIL必填.');
		if (Admin_Service_User::getUserByName($info['username'])) $this->output(-1, '用户名已经存在.');
		if (Admin_Service_User::getUserByEmail($info['email'])) $this->output(-1, '邮件地址已经存在.');
		$info['registerip'] = Util_Http::getClientIp();
		$result = Admin_Service_User::addUser($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('uid', 'groupid', 'password', 'r_password', 'status'));
		if($info['password']) {
			//if ($info['password'] == '') $this->output(-1, '密码不能为空.'); 
			if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间');
			if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致');
		}else{
			unset($info['password']);
		}
		$ret = Admin_Service_User::updateUser($info, intval($info['uid']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$uid = $this->getInput('uid');
		$info = Admin_Service_User::getUser($uid);
		if ($info && $info['groupid'] == 0) $this->output(-1, '此用户无法删除');
		if ($uid < 1) $this->output(-1, '参数错误');
		$result = Admin_Service_User::deleteUser($uid);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
