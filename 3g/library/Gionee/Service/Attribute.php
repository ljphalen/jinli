<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Attribute {
	
	public static function add($params){
		$data = self::_cookData($params);
		return self::_getDao()->insert($data);
		
	}
	
	public static function getList($page,$limit,$params = array(),$sort = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static  function delete($id){
		return self::_getDao()->delete($id);
	}
	
	public static function getAll($order= array()){
		return self::_getDao()->getAll($order);
	}
	private static function _cookData($data) {
		$temp =array();
		if(isset($data['name'])) $temp['name']= $data['name'];
		if(isset($data['create_time'])) $temp['create_time'] = $data['create_time'];
		return $temp;
	}
	
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Attribute");
	}
} 