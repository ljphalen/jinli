<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Payment_AlipayController extends Api_BaseController {
	
	/**
	 * 获取Gionee 帐号服务器时间
	 */
	public function testAction(){
		
		$ret = Payment_Service_PayFlow::getBy(array('flowlogid'=>1), array('flowlogid'=>'DESC'));
		
		
		var_dump($ret);
	}
	
	
}
