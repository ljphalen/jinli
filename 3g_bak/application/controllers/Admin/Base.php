<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Admin_BaseController
 * @author rainkid
 */
class Admin_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions      = array();
	public $currentTheme;
	static $siteThemeArr = array('default', 'bootstrap');
	public $pageSize     = 20;
	public $appCacheName = '';

	public function init() {
		parent::init();
		$webroot    = Common::getCurHost();
		$frontroot  = Yaf_Application::app()->getConfig()->webroot;
		$adminroot  = Yaf_Application::app()->getConfig()->adminroot;
		$this->assign("attachPath", Common::getImgPath());
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		// 定义返回前台页面的地址
		$this->assign("frontroot", $frontroot);
		$this->assign("webroot", $webroot);
		$this->assign("adminroot", $adminroot);
		// 定义后台的静态样式的地址
		$this->assign("staticPath", $staticroot . '/apps/admin');
		// 定义后台主题
		$theme = Util_Cookie::get('_theme_');
		(empty($theme) || !in_array($theme, $this::$siteThemeArr)) && $theme = 'default';
		$this->currentTheme = $theme;
		$this->assign('sitetheme', $theme);
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
					Gionee_Service_Config::setValue($value, Common::getTime());
				}
			} else {
				Gionee_Service_Config::setValue($this->appCacheName, Common::getTime());
			}
		}

		Gionee_Service_Config::setValue('APPC_Front_Nav_test2', Common::getTime());
		Gionee_Service_Config::setValue('APPC_Front_Nav_test3', Common::getTime());
		Gionee_Service_Config::setValue('APPC_Front_News_test', Common::getTime());
	}

	/**
	 * 检查token
	 */
	protected function checkToken() {
		$ajaxSkip = $this->getInput('_skip');
		if (!$this->getRequest()->isPost() || $ajaxSkip) {
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
			Common::redirect('/' . $this->getRequest()->getModuleName() . '/Login/index');
		} else {
			$module     = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action     = $this->getRequest()->getActionName();

			$module = DEFAULT_MODULE;

			list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
			$mc  = "_" . $module . "_" . $controller;
			$mca = "_" . $module . "_" . $controller . "_" . $action;

			$ks = array();
			foreach($userlevels as $val) {
				$tmp = explode('_',$val);
				$k = '_'.$tmp[1].'_'.$tmp[2];
				$ks[$k] = 1;
			}
			$usermodulelevels = array_keys($ks);
			if ($controller != 'Common' && !in_array($mc, $usermodulelevels)) {
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
			if (!in_array($key, $not)) {
				$tmp[$key] = $this->getInput($key);
			}
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
			Common::redirect($url);
		}
	}


	/**
	 *
	 * @param int    $code
	 * @param string $msg
	 * @param string $data
	 */
	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
			'success' => $code == 0 ? true : false,
			'msg'     => $msg,
			'data'    => $data
		)));
	}
}
