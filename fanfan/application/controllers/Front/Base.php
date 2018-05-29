<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Front_BaseController extends Common_BaseController {
	public $actions = array();

	private $sTime = 0;

	public function init() {
		parent::init();
		Common::getCache()->increment('fanfan_pv');
		$this->sTime = microtime(true);
		$webroot     = Yaf_Application::app()->getConfig()->webroot;
		$attachroot  = Yaf_Application::app()->getConfig()->attachroot;
		$this->assign("attachPath", $attachroot . '/attachs/fanfan');
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot . '/apps/admin');

		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/fanfan');
		$this->assign("assetVer", Widget_Service_Config::getValue('asset_ver'));

		$this->checkToken();
		$this->defaultAction();
		$this->checkCookieParams();
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

	public function __destruct() {
		$date       = date('Ymd');
		$eTime      = microtime(true);
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();
		$name       = sprintf("%s_%s_%s", $module, $controller, $action);
		$t          = sprintf("%.2f", $eTime - $this->sTime);
		//echo $name . '_' . $t;
		Common::getCache()->hIncrBy('MON_KEY_NUM:' . $date, $name);
		Common::getCache()->hIncrBy('MON_KEY_TIME:' . $date . ':' . $name, $t);
	}
}
