<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Games_Dao_Ad
 * @author rainkid
 *
 */
class Games_Cache_Recommend extends Cache_Base{

	/**
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param array $params
	 */
	public function getCanUseRecommends($start, $limit, $params) {
		$args = func_get_args();
		return $this->_cacheData(__FUNCTION__, $args, $this->expire);
	}
}