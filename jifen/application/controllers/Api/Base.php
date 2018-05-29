<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Api_BaseController extends Common_BaseController {
	public $actions = array(); 
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		Yaf_Dispatcher::getInstance()->enableView();
	}
	
	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
				'code' => $code,
				'msg' => $msg,
				'data' => $data
		)));
	}
}
