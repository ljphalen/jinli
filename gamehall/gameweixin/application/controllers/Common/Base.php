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
    public $userInfo = array();
    
    /**
     * 
     * Enter description here ...
     */
    protected function init() {
		$webroot = Common::getWebRoot();
		
		$this->assignValues();
		$attachroot = Yaf_Application::app()->getConfig()-> attachroot;
		$this->assign("attachroot", $attachroot.'/attachs/gameweixin/');
		
		$this->assign('token', Common::getToken($this->userInfo));
		//init actions
		foreach ($this->actions as $key=>$value) {
			$this->assign($key, $value);
		}
		
		if ($this->isAjax()) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
    }
    
    protected function assignValues() {
        $isDebug = preg_match('/debug/i', $_SERVER['QUERY_STRING']);
        $source = '.source';
        $staticRoot = Yaf_Application::app()->getConfig()->staticroot;
        $appSys = $staticRoot . '/apps/gameweixin/sys';
        $appRef = $staticRoot . '/apps/gameweixin/daemon';
        $appCss = $appRef . '/assets/css';
        $appJs = $appRef . '/assets/js';
        
        $timestamp = '?t='.Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY);
        $this->assign('source', $source);
        $this->assign('appSys', $appSys);
        $this->assign('appCss', $appCss);
        $this->assign('appJs', $appJs);
        $this->assign('timestamp', $timestamp);
    }
    
    /**
     * 
     * @author yinjiayan
     * @param unknown $data
     * @param string $isSuccess
     * @param string $msg
     */
    protected function ajaxJsonOutput($data, $isSuccess = 'true', $msg = '') {
        $output = array();
        $output[Util_JsonKey::SUCCESS] = $isSuccess;
        $output[Util_JsonKey::MSG] = $msg;
        if ($data) {
            $output[Util_JsonKey::DATA] = $data;
        }
        exit(json_encode($output));
    }

    /**
     * 表单提交失败提示
     * @author wupeng
     * @param string $msg
     * */
    protected function failPostOutput($msg = '') {
        $output = array();
        $output[Util_JsonKey::SUCCESS] = false;
        $output[Util_JsonKey::MSG] = $msg;
        $output[Util_JsonKey::DATA] = array(Util_JsonKey::STATUS=>0);
        exit(json_encode($output));
    }

    /**
     * 表单提交成功提示
     * @author wupeng
     * @param string $msg
     * */
    protected function successPostOutput($redirectUrl="") {
        $output = array();
        $output[Util_JsonKey::SUCCESS] = true;
        $output[Util_JsonKey::DATA] = array(Util_JsonKey::STATUS=>1, Util_JsonKey::REDIRECT_URL=>$redirectUrl);
        exit(json_encode($output));
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
	
	public function showMsgHtml($msg, $url='') {
		$this->assign('waittime', '3000');
		$this->assign('url', $url ? $url : 'javascript:history.go(-1);');
		$this->assign('msg', $msg);
		$this->display("../common/showMsg");
		exit;
	}
	
	protected function getPageInput() {
	    $pageInput = $this->getInput('page');
	    if (!$pageInput || $pageInput <= 0) {
	        return 1;
	    }
	    return intval($pageInput);
	}
}
