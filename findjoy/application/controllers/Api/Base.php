<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Api_BaseController extends Common_BaseController {
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
    	Yaf_Dispatcher::getInstance()->disableView();
    	/* $open_id = $this->getOpenId();
    	$this->userInfo = $this->getUserInfo($open_id); */
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
		$out      = array(
			'success' => $code == 0 ? true : false,
			'msg'     => $msg,
			'data'    => $data
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
