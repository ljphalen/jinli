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
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/search');
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
