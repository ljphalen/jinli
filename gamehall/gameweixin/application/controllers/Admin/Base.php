<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_BaseController extends Common_BaseController {
	public $actions = array();
	/**
	 * 
	 * Enter description here ...
	 */
	protected function init() {
		parent::init();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("adminroot", $adminroot);
		$this->assign("staticPath", $staticroot . '/apps/admin');
		$this->assign('token', Common::getToken());
		$this->checkRight();
		$this->checkToken();
// 		$this->updateAppCache();
		$this->checkCookieParams();
		
		$mainMenu = Common::getConfig('mainMenu');
		$this->assign('menuConfig', $mainMenu[0]);
		$this->assign('menuViews', $mainMenu[1]);
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
	
	/*
	 * 更新缓存文件
	 */
	public function updateAppCache() {
		$action = $this->getRequest()->getActionName();
		if ($this->appCacheName && in_array($action, array('add_post', 'edit_post', 'delete'))) {
			if (is_array($this->appCacheName)) {
				foreach($this->appCacheName as $value) {
					Game_Service_Config::setValue($value, Common::getTime());
				}
			} else {
				Game_Service_Config::setValue($this->appCacheName, Common::getTime());
			}
		}
	}
	
	/**
	 * 维护数据版本
	 */
	public function updateVersion($key) {
		Resource_Service_Config::setValue($key, Common::getTime());
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
		    $this->assign('adminUserName', $this->userInfo['username']);
			$module = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action = $this->getRequest()->getActionName();
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
		
		if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
			return true;
		}
		return false;
	}
	
	public function cookieParams() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);
	
		$tmp = array();
		$not = array('token','s');
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key, $not))$tmp[$key] = $this->getInput($key);
		}
		Util_Cookie::set($name, Common::encrypt(json_encode($tmp)), false, Common::getTime() + (5 * 3600));
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function checkCookieParams() {
		$s = $this->getInput('s');
	
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);
	
		$params = json_decode(Common::encrypt(Util_Cookie::get($name), 'DECODE'), true);
	
		if (count($params) && $s) {
			$adminroot = Yaf_Application::app()->getConfig()->adminroot;
	
			$url = sprintf('%s/%s/%s/%s?%s', $adminroot, $module, $controller, $action, http_build_query($params));
			$this->redirect($url);
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		header("Content-type:text/json");
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
}
