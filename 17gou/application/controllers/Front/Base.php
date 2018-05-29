<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Front_BaseController extends Common_BaseController {
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
        parent::init();
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->assign("staticRoot", $staticroot);
        $this->assign("staticSysPath", $staticroot . '/sys');
        $this->assign("staticResPath", $staticroot . '/apps/17gou');
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
