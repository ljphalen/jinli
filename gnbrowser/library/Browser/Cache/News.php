<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gionee_Cache_News
 * @author tiansh
 *
 */
class Gionee_Cache_News extends Cache_Base{

	public $expire = 60;
	/**
	 *
	 */
	public function getCanUseNews() {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}
