<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class PasswdController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Passwd/index',
		'passwdPostUrl' => '/Admin/Passwd/passwd_post'
	);
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
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
}
