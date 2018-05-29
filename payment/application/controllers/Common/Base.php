<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author ljp
 *
 */
abstract class Common_BaseController extends Yaf_Controller_Abstract {
    public $actions = array(); 
    public $filter = array();
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
    	ini_set('display_errors', 1);//设置开启错误提示
    	error_reporting('E_ALL ');
    	
		$webroot = Common::getWebRoot();
		
		
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
    	$this->assign("webroot", $webroot);
		$this->assign('token', Common::getToken($this->userInfo));
		
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
     * 请求客户端判断
     * @param unknown $package
     */
    public function getRequestClient($package){
    	//艾米游戏客户端请求
    	if ($package == 'com.android.amigame') return 'amigame';
    	//游戏大厅客户端请求
    	if ($package == 'gn.com.android.gamehall') return 'gamehall';
    	//其他浏览器或设备请求
    	return 'other';
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
    	if(!is_null($value)) return $value;
    	$value = Util_Filter::get($var);
    	if(!is_null($value)) return $value;
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
