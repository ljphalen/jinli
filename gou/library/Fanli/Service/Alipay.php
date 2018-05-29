<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desChanneliption here ...
 * @author tiansh
 *
 */
class Fanli_Service_Alipay{
	
	/**
	 *
	 * getOuthToken 获取授权访问令牌 
	 * 
	 */
	public static function getOuthToken() {
		$top = new Api_Top_Service();
		$response = $top->alipaySystemOauthToken(array('grant_type'=>'authorization_code'));
	}	
}
