<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Client_Service_ColumnLog
 * @author luojiapeng
 *
 */
class Client_Service_ColumnLog {
	const STATUS_OPEN = 1;
	const STATUS_CLOSE = 0;
	
	const IS_DEAFAULT = 1;
	const NOT_IS_DEFAULT = 0;
	
	const FINISH_STATUS = 4;

	
    /**
     * 
     * @return multitype:NULL multitype:
     */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	

	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @param unknown_type $orderby
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderby = array('id' => 'DESC')) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, $orderby);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}


	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	
	public static function getBy($params = array() , $orderBy=array('id'=>'DESC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;
	
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	
	public static function getsBy($params = array(), $orderBy=array('id'=>'DESC') ){
		$ret = self::_getDao()->getsBy($params, $orderBy);
		if(!$ret) return false;
		return $ret;
	
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean|string
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
	public static function updateByID($data, $id) {
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
	public static function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return multitype:unknown
	 */
	private static function _cookData($data) {
		$tmp = array();
		if (isset($data['column_version'])) $tmp['column_version'] = $data['column_version'];
		if (isset($data['column_name'])) $tmp['column_name'] = $data['column_name'];
		if (isset($data['admin_id'])) $tmp['admin_id'] = $data['admin_id'];
		if (isset($data['admin_name'])) $tmp['admin_name'] = $data['admin_name'];
		if (isset($data['channel_num'])) $tmp['channel_num'] = $data['channel_num'];
		if (isset($data['column_num'])) $tmp['column_num'] = $data['column_num'];
		if (isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if (isset($data['step'])) $tmp['step'] = $data['step'];
		if (isset($data['is_deafault'])) $tmp['is_deafault'] = $data['is_deafault'];
		if (isset($data['temp1'])) $tmp['temp1'] = $data['temp1'];
		if (isset($data['temp2'])) $tmp['temp2'] = $data['temp2'];
		if (isset($data['temp3'])) $tmp['temp3'] = $data['temp3'];
		return $tmp;
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBy($params) {
		return self::_getDao()->deleteBy($params);
	}
	/**
	 *
	 * @return Client_Dao_ColumnLog
	 */
	private static function _getDao() {
		return Common::getDao ( "Client_Dao_ColumnLog" );
	}
}