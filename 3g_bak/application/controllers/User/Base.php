<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * User_BaseController
 * @author tiansh
 *
 */
class User_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions = array();

	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$webroot    = Common::getCurHost();
		$controller = $this->getRequest()->getControllerName();
		$this->assign("webroot", $webroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/3g');
		$this->checkToken();

		$t_bi  = $this->getSource();
		foreach ($this->actions as $key => $value) {
			$this->assign($key, $value . "?t_bi=" . $t_bi);
		}

		Gionee_Service_Log::pvLog('3g_all');
		Gionee_Service_Log::uvLog('3g_all', $t_bi);

		$this->defaultAction(true);

	}
	
	public function getSource() {
		return Util_Cookie::get('3G-SOURCE', true);
	}

	public function inLoginPage() {
		$module     = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();

		if ($module == 'User' && ($controller == 'Login' || $controller == 'Register' || ($controller == 'Index' && ($action = 'signin' || $action == 'signin_post')))) {
			return true;
		}
		return false;
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
	
}
