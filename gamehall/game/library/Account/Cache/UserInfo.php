<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Account_Cache_UserInfo
 * @author fanch
 *
*/
class Account_Cache_UserInfo extends Cache_Base{
	public $expire = 600;
	
	public function subtractPoints($limit,$date){
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	public function addPoints($limit,$date){
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
}