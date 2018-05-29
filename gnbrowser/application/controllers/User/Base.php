<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * base
 * @author tiansh
 *
 */
class User_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->assign("staticSysPath", $staticroot . '/statics/common/sys');
		$this->assign("staticResPath", $staticroot . '/statics/browser/v1.1');
 		$this->checkRight();
 		$this->checkToken();
	}
	
	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) return true;
		$post = $this->getRequest()->getPost();
		$result = Common::checkToken($post['token']);
		if (Common::isError($result)) $this->output(-1, $result['msg']);
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function checkRight() {
		$this->userInfo = Gionee_Service_User::isLogin();
		if(!$this->userInfo && !$this->inLoginPage()){
			$this->redirect("/user/login/index");
		} 
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		if ($module == 'User' && ($controller == 'Login' || $controller == 'Register' || ($controller == 'Index' && ($action='signin' || $action=='signin_post')))) {
			return true;
		}
		return false;
	}	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
}
