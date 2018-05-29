<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_Ad
 * @author rainkid
 *
 */
class Gou_Cache_Subject extends Cache_Base{
	
	public $expire = 60;
	/**
	 *
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}	
}
