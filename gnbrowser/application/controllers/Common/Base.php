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
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
    	$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot . '/statics');
		$this->assign("attachPath", $webroot . '/attachs');
		$this->assign('token', Common::getToken($this->userInfo));
		$this->assign('version', Common::getConfig('siteConfig', 'version'));
		//init actions
		foreach ($this->actions as $key=>$value) {
			$this->assign($key, $value);
		}
		
		if ($this->isAjax()) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
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
    public function getPost($var) {
        if(is_string($var)) return Util_Filter::post($var);
        $return = array();
        if (is_array($var)) {
            foreach ($var as $key=>$value) {
               if (is_array($value)) {
               		$return[$value[0]] = Util_Filter::post($value[0], $value[1]);
               } else {
					$return[$value] = Util_Filter::post($value);;
               }
            }
            return $return;
        }
        return null;
    }
    
    /**
     * 
     * 获取get参数
     * @param string $var
     */
    public function getInput($var) {
    	if(is_string($var)) return self::getVal($var);
    	if (is_array($var)) {
    		$return = array();
    		foreach ($var as $key=>$value) {
    			$return[$value] = self::getVal($value);
    		}
    		return $return;
    	}
    	return null;
    }
    
    /**
     * 
     * @param unknown_type $var
     * @return unknown|NULL
     */
    private static function getVal($var) {
    	$value = Util_Filter::post($var);
    	if($value) return $value;
    	$value = Util_Filter::get($var);
    	if($value) return $value;
    	return null;
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
