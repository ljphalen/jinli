<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Common_BaseController extends Yaf_Controller_Abstract {
    public $actions = array(); 
    public $userInfo = '';
    
    /**
     * 
     * Enter description here
     */
    public function init() {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
    	$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot . '/apps/');
		$this->assign("attachPath", $attachroot . '/attachs');
		
		$this->assign('token', Common::getToken($this->userInfo));
		$this->assign('version', Common::getConfig('siteConfig', 'version'));
		//init actions
		foreach ($this->actions as $key=>$value) {
			$this->assign($key, $value);
		}
		
		if ($this->isAjax()) {
            Yaf_Dispatcher::getInstance()->disableView();
        }
        $this->userInfo = $this->getUserInfo();
    }
    
    /**
     * @param $redirect_url
     */
    public function getUserInfo() {
//        return array("id"=>1, "phone"=>"13809886150");
        return Ola_Service_User::isLogin();
    }

    /**
     * @param $redirect_url
     */
    public function getOpenId() {
        $open_id = $this->getInput("openid");
        if ($open_id) return $open_id;
        return Util_Cookie::get('OLAUNIID', true);
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
     * @param unknown_type $var
     * @param unknown_type $value
     */
    public function assign($var, $value) {
        $this->getView()->assign($var, $value);
    }
    
    /**
     * 
     * 获取post参数 
     * @param string/array $var
     */
    public function getPost($vars) {
        return Util_Security::getInput($vars, 'POST');
    }
    
    /**
     * 
     * 获取get参数
     * @param string $var
     */
    public function getInput($vars) {
    	return Util_Security::getInput($vars, 'GP');
    }
    
    /**
     * 
     * 请求是否是ajax
     */
    public function isAjax() {
        return $this->getRequest()->isXmlHttpRequest() || $this->getInput("callback");
    }
    
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function showMsg($code, $msg = '') {
		throw new Yaf_Exception($msg, $code);
	}
}
