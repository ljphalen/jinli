<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Gou_Cache_User extends Cache_Base{
	
	public $expire = 30;
	/**
	 *
	 */
	public function getListByTime($start, $limit) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
	
	/**
	 * 
	 * @param unknown_type $username
	 * @return boolean
	 */
	public function getByUserName($username) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}
