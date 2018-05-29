<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Point_Service_Gain{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getBy($params, $orderBy);
	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCount($params) {
		return self::getDao()->count($params);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::cookData($data);
		return self::getDao()->insert($data);
	}
	

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['gain_type'])) $tmp['gain_type'] = $data['gain_type'];
		if(isset($data['gain_sub_type'])) $tmp['gain_sub_type'] = $data['gain_sub_type'];
		if(isset($data['days'])) $tmp['days'] = $data['days'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_PointsGain
	 */
	private static function getDao() {
		return Common::getDao("Point_Dao_Gain");
	}
}
