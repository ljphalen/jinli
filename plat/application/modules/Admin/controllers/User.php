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
		'deleteUrl' => '/Admin/User/delete',
		'passwdUrl' => '/Admin/User/passwd',
		'passwdPostUrl' => '/Admin/User/passwd_post'
	);
	
	public $perpage = 20;
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
		$info = $this->getPost(array('username','password','r_password','email','groupid','realname','phone'));
		
		if (!$info['phone']) $this->output(-1, '手机号码不能为空.');
		if (!$info['email']) $this->output(-1, '邮箱地址不能为空.');
		if (!$info['realname']) $this->output(-1, '真实姓名不能为空.');
		
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
		$info = $this->getPost(array('uid', 'groupid', 'phone', 'email', 'realname'));
		if (!$info['phone']) $this->output(-1, '手机号码不能为空.');
		if (!$info['email']) $this->output(-1, '邮箱地址不能为空.');
		if (!$info['realname']) $this->output(-1, '真实姓名不能为空.');
		if ($this->getPost('m_passwd')) {
			$info['password'] = $this->getPost('password');
			$info['r_password'] = $this->getPost('r_password');
			if ($info['password'] == '') $this->output(-1, '密码不能为空.');
			if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间');
			if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致');
		}
		$ret = Admin_Service_User::updateUser($info, intval($info['uid']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function passwdAction() {
		if (!$this->userInfo) $this->redirect("/Admin/Login/index");
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function passwd_postAction() {
		$adminInfo = $this->userInfo;
		if (!$adminInfo['uid']) $this->output(-1, '登录超时,请重新登录后操作');
		$info = $this->getPost(array('current_password','password','r_password'));
		$ret = Admin_Service_User::checkUser($adminInfo['username'], $info['current_password']);
		if (Common::isError($ret)) $this->output(-1, $ret['msg']);
		$info['uid'] = $adminInfo['uid'];
		if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间');
		if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致');
		$result = Admin_Service_User::updateUser($info, intval($info['uid']));
		if (!$result) $this->output(-1, '编辑失败');
		$this->output(0, '操作成功');
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
