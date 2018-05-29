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

	public function init() {
		parent::init();
		$webroot    = Yaf_Application::app()->getConfig()->webroot;
		$adminroot  = Yaf_Application::app()->getConfig()->adminroot;
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		$this->assign("attachPath", $attachroot . '/attachs/fanfan');
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("adminroot", $adminroot);
		$this->assign("staticPath", $staticroot . '/apps/admin');
		$this->checkRight();
		$this->checkToken();
		$this->updateAppCache();
		$this->defaultAction();
		$this->checkCookieParams();
	}

	public function updateAppCache() {
		$action = $this->getRequest()->getActionName();
		if ($this->appCacheName && in_array($action, array('add_post', 'edit_post', 'delete'))) {
			if (is_array($this->appCacheName)) {
				foreach ($this->appCacheName as $value) {
					Widget_Service_Config::setValue($value, Common::getTime());
				}
			} else {
				Widget_Service_Config::setValue($this->appCacheName, Common::getTime());
			}
		}
	}

	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) {
			return true;
		}
		$post   = $this->getRequest()->getPost();
		$result = Common::checkToken($post['token']);
		if (Common::isError($result)) {
			$this->output(-1, $result['msg']);
		}
		return true;
	}

	public function checkRight() {
		$this->userInfo = Admin_Service_User::isLogin();
		if (!$this->userInfo && !$this->inLoginPage()) {
			$this->redirect("/Admin/Login/index");
		} else {
			$module     = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action     = $this->getRequest()->getActionName();
			list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
			$mc  = "_" . $module . "_" . $controller;
			$mca = "_" . $module . "_" . $controller . "_" . $action;
			if (!in_array($mc, $userlevels) && !in_array($mca, $userlevels)) {
				exit('没有权限');
			}
		};
	}

	public function inLoginPage() {
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();

		if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
			return true;
		}
		return false;
	}


	public function getUserMenu() {
		$userInfo  = Admin_Service_User::getUser($this->userInfo['uid']);
		$groupInfo = array();
		if ($userInfo['groupid'] == 0) {
			$groupInfo = array('groupid' => 0);
		} else {
			$groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
		}
		$menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
		list($usermenu, $mainview, $usersite, $userlevels) = $menuService->getUserMenu($groupInfo);
		array_push($userlevels, "_Admin_Initiator", "_Admin_Index", '_Admin_Login');
		return array($usermenu, $mainview, $usersite, $userlevels);
	}

	public function cookieParams() {
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf('%s_%s_%s', $module, $controller, $action);

		$tmp = array();
		$not = array('token', 's');
		foreach ($_REQUEST as $key => $value) {
			if (!in_array($key, $not)) $tmp[$key] = $this->getInput($key);
		}
		Util_Cookie::set($name, Common::encrypt(json_encode($tmp)), false, Common::getTime() + (5 * 3600));
	}

	public function checkCookieParams() {
		$s = $this->getInput('s');

		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf('%s_%s_%s', $module, $controller, $action);

		$params = json_decode(Common::encrypt(Util_Cookie::get($name), 'DECODE'), true);

		if (count($params) && $s) {
			$adminroot = Yaf_Application::app()->getConfig()->adminroot;

			$url = sprintf('%s/%s/%s/%s?%s', $adminroot, $module, $controller, $action, http_build_query($params));
			$this->redirect($url);
		}
	}

	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
			'success' => $code == 0 ? true : false,
			'msg'     => $msg,
			'data'    => $data
		)));
	}
}
