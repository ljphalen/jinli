<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Ptner_BaseController extends Common_BaseController {
    
	public $user;
	/**
	 * (non-PHPdoc)
	 * @see Common_BaseController::init()
	 */
	public function init() {
		parent::init();
		if (!$this->inLoginPage()) $this->checkUser();
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
	 * @return boolean
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
	
		if ($module == 'Ptner' && $controller == 'User' && in_array($action, array('login', 'login_post','register_post_step1','register','register_post_step2', 'region', 'sms'))) {
			return true;
		}
		return false;
	}
	/**
	 * 
	 * @return boolean
	 */
	public function checkUser() {
		$action = $this->getRequest()->getActionName();
		if (!$this->user = $this->checkLogin($u)) {
			if ($this->isAjax()) {
				$this->output(-1, '用户未登录');
			}
			$this->redirect('/ptner/user/login');
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $u
	 * @return boolean|unknown
	 */
	public function checkLogin() {
		$sid = session_id();
		$cache = Common::getCache();
		if (!$phone = $cache->get($sid)) return false;
		return $phone;
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
