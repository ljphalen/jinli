<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Api_User extends Common_Service_Base{

	/**
	 * 验证签名
	 */
	
	public function checkSign($data, $auth_key){
		if (!is_array($data)) return false;
		$sign = $data['sign'];
		unset($data['appid']);
		unset($data['appsecret']); 
		unset($data['sign']);
		$new_sign = parent::createSign($data, $auth_key);
		if ($sign != $new_sign) {
			return false;
		}
	}
}
