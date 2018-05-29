<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_DubiousIp {
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function getsBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$sort);
	}
	
	public static function edit($params,$id){
		if(!is_array($params)) return false;
		return self::_getDao()->update($params,$id);
	}
	
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	public static function getList($page, $pageSize, $where, $orderBy) {
		if (!is_array($where)) return false;
		$total = self::_getDao()->count($where);
		$data  = self::_getDao()->getList($pageSize * ($page - 1), $pageSize, $where, $orderBy);
		return array($total, $data);
	}
	
	public static function addDubiousIps(){
		$date = date("Ymd",strtotime('-1 day'));
		$num = 3;
		$dataList  = User_Service_Earn::getDubiousIpData($date, $num);
		$i = 0;
		foreach($dataList as $k=>$v){
			$data = User_Service_DubiousIp::getBy(array('user_ip'=>$v['user_ip']));
			if(empty($data)){
				$params  = array();
				$params['user_ip'] = $v['user_ip'];
				$params['add_time'] = time();
				$ret = User_Service_DubiousIp::add($params);
				++$i;
			}else{
				User_Service_DubiousIp::edit(array('status'=>0), $v['id']);
			}
		}
		return $i;
	}
	
	/**
	 * 
	 * @return object
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_DubiousIp");
	}
	
}