<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class UserPlugin extends Yaf_Plugin_Abstract {
	
	public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
//		if ($request->module == 'Index')  $request->module = 'Admin';
	}

}
