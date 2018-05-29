<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_IndexChannel
 * @author tiansh
 *
 */
class Browser_Cache_IndexChannel extends Cache_Base{
	public $expire = 60;
	/**
	 * 
	 */
	public function getCanUseChannels() {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}