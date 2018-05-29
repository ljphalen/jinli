<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Ola_Service_Favorite extends Common_Service_Base{

	/**
	 * 
	 * 分页取用户列表
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 * 
	 * 读取一条信息
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * 更新用户信息
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	

	/**
	 * 
	 * del 
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * add 
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 获取记录
	 * @param $params
	 * @param array $orderBy
	 * @return bool|mixed
	 */
	public static function getBy($params, $orderBy = array()) {
	    if (!is_array($params)) return false;
	    $params = self::_cookData($params);
	    return self::_getDao()->getBy($params, $orderBy);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['user_id'])) $tmp['user_id'] = $data['user_id'];
		if(isset($data['job_id'])) $tmp['job_id'] = $data['job_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Ola_Dao_Favorite
	 */
	private static function _getDao() {
		return Common::getDao("Ola_Dao_Favorite");
	}
}
