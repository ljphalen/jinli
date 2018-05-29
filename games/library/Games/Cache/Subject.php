<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Games_Dao_Subject
 * @author rainkid
 *
 */
class Games_Cache_Subject extends Cache_Base{
	
	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseSubjects($start, $limit, $params) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}
