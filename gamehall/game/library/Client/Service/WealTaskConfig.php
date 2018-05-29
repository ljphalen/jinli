<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_WealTaskConfig{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
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
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsBy($params, $orderBy = array('id'=>'ASC')) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$orderBy);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getCount($params) {
		return self::_getDao()->count($params);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 更新缓存
	 * @return boolean
	 */
	public static function setNumData() {
		$cache = Cache_Factory::getCache();
		$ckey  = 'weal_task_num';
		$num   = self::getCount(array('status'=>1));
		$cache->set($ckey, $num);
		$rs   = self::getsBy(array('status'=>1));
		$total = 0;
		foreach ( $rs as $val){
			$award_json = json_decode($val['award_json'],true);
			foreach ($award_json as $v){
				if($v['denomination']) $total +=$v['denomination'];
			}
			
		}
		$cache->set('weal_task_total', $total);
		return true;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['task_name'])) $tmp['task_name'] = $data['task_name'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['subject_id'])) $tmp['subject_id'] = $data['subject_id'];
		if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
		if(isset($data['award_json'])) $tmp['award_json'] = $data['award_json'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_WealTaskConfig
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_WealTaskConfig");
	}
}
