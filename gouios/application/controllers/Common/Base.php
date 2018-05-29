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
		$webroot = Common::getWebRoot();
    	$this->assign("webroot", $webroot);
    	$staticroot = Yaf_Application::app()->getConfig()->staticroot;
    	$this->assign("staticPath", $staticroot . '/apps');
		$this->assign("attachPath", $webroot . '/attachs');
		$this->assign('token', Common::getToken($this->userInfo));
		$this->assign('version', Common::getConfig('siteConfig', 'version'));
		$this->assign('curPageURL', Common::getCurPageURL());
		
		//init actions
		foreach ($this->actions as $key=>$value) {
			$this->assign($key, $value);
		}
		
		if ($this->isAjax()) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
		
		//pv统计
		Common::getCache()->increment('iosgou_pv');
		
    }

    
    /**
     *
     * @return boolean
     */
    public function getDomain() {
    	$domain = str_replace('http://','',Common::getWebRoot());
    	if($number = strrpos($domain,':'))  $domain = Util_String::substr($domain, 0, $number);
    	return $domain;
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
        return $this->getRequest()->isXmlHttpRequest();
    }
    
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function showMsg($code, $msg = '') {
		throw new Yaf_Exception($msg, $code);
	}
	
	/**
	 * 
	 * @param string $click_url
	 * @return string
	 */
	public function getTaobaokeUrl($click_url) {
		$sid = Gou_Service_User::getUserSid();
		return sprintf("%s&ttid=%s&sid=%s", $click_url, Common::getConfig('apiConfig', 'taobao_taobaoke_ttid'), $sid);
	}
	

	/**
	 *
	 */
	protected function checkSource() {
		$source = $this->getInput("source");
		$source = str_replace(" ", "+", $source);
		if ($source) Gou_Service_Channel::cookieChannel($source);
	}
	
	/**
	 *获取outer_code
	 */
	public function getOuterCode() {
		$uid = 0;
		$channel_id = Gou_Service_Channel::getChannelId();
		$user = Gou_Service_User::isLogin();
		if ($user) $uid = $user['id'];
		return sprintf("%sH%s", intval($channel_id), $uid);
	}
}
