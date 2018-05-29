<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gionee_Cache_Ad
 * @author tiansh
 *
 */
class Theme_Cache_Config extends Cache_Base{
	public $expire = 60;
	
	public function updateByKey($key, $value) {
		$this->revertVersion();
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
}
