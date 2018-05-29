<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Front_BaseController extends Common_BaseController {
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$webroot = Common::getWebRoot();
	}

	protected function assignValues() {
	    $isDebug = preg_match('/debug/i', $_SERVER['QUERY_STRING']);
	    $source = '.source';
	    $staticRoot = Yaf_Application::app()->getConfig()->staticroot;
	    $appSys = $staticRoot . '/apps/gameweixin/sys';
	    $appRef = $staticRoot . '/apps/gameweixin/front';
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
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		$callback = $this->getInput('callback');
		$out = array(
				'success' => $code == 0 ? true : false ,
				 'msg' => $msg,
				'data' => $data
			);
		if ($callback) {
				header("Content-type:text/javascript");
				exit($callback . '(' . json_encode($out) . ')');
			} else {
				header("Content-type:text/json");
				exit(json_encode($out));
		}
	}
}
