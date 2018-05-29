<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_IdxGameResourceVersion
 * @author lichanghua
 *
 */
class Resource_Cache_IdxGameResourceVersion extends Cache_Base{
	public $expire = 60;
	
	public function getAllVersions() {
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	public function getIdxVersionByVersionStatus($status) {
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	public function getIdxVersionByNewVersion() {
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getVersionGames($start, $limit, $params) {
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public function getVersionGamesCount($params) {
		$args = func_get_args();
		return call_user_func_array(array($this->_getDao(), __FUNCTION__), $args);
	}
}
