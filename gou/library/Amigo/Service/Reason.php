<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Amigo_Service_Reason {

	public static function getAll(){
		return self::_getDao()->getAll();
	}
	
	public static function getList($page, $limit, $params = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getOne($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	public static function add($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	public static function update($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static function delete($id){
		return self::_getDao()->delete(intval($id));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['reason'])) $tmp['reason'] = $data['reason'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['type'])) $tmp['type'] = $data['type'];
		return $tmp;
	}
	
	private static function _getDao() {
		return Common::getDao("Amigo_Dao_Reason");
	}
}