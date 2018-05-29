<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		$this->assign("attachPath", $adminroot . '/attachs');
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticPath", $staticroot . '/apps/admin');
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
		$this->userInfo = Admin_Service_User::isLogin();
		if(!$this->userInfo && !$this->inLoginPage()){
			$this->redirect("/Admin/Login/index");
		} else {
			$module = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action = $this->getRequest()->getActionName();
			
			list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
			$mc = "_" . $module . "_" . $controller;
			$mca = "_" . $module . "_" . $controller . "_" . $action;
			if (!in_array($mc, $userlevels) && !in_array($mca, $userlevels)) {
				exit('没有权限');
			}
		};
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getUserMenu() {
		$userInfo = Admin_Service_User::getUser($this->userInfo['uid']);
		$groupInfo = array();
		if ($userInfo['groupid'] == 0) {
			$groupInfo = array('groupid'=>0);
		} else {
			$groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
		}
		$menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
		list($usermenu, $mainview, $usersite, $userlevels) = $menuService->getUserMenu($groupInfo);
		array_push($userlevels, "_Admin_Initiator", "_Admin_Index", '_Admin_Login');
		return array($usermenu, $mainview, $usersite, $userlevels);
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
