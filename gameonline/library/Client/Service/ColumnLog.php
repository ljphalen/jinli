<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Service_ColumnLog
 * @author luojiapeng
 *
 */
class Client_Service_ColumnLog {



	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addColumn($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return false;
		return self::_getDao()->getLastInsertId();
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateColumn($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $params
	 * @return boolean
	 */
	public static function updateColumnBywhere($data,$params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['column_version'])) $tmp['column_version'] = $data['column_version'];
		if (isset($data['content'])) $tmp['content'] = $data['content'];
		return $tmp;
	}
	

	/**
	 *
	 * @return Client_Dao_ColumnLog
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_ColumnLog");
	}
}