<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Api_BaseController
 * @author fanch
 *
 */
class Api_BaseController extends Common_BaseController {
    /**
     * 
     * Enter description here ...
     */
    public function init() {
			Yaf_Dispatcher::getInstance()->disableView();
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
