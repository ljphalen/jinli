<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_DubiousIpUser {
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function getsBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$sort);
	}
	
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	public static function edit($params,$id){
		if(!is_array($params)) return false;
		return self::_getDao()->update($params,$id);
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
	
	public static function addDubiousUsers(){
		$date = date("Ymd",strtotime("-1 day"));
		list($total,$ipList) = User_Service_DubiousIp::getList(1, 5, array('status'=>0), array('id'=>'ASC'));
		$num = 0;
		foreach ($ipList as $k=>$v){
			$where = array();
			$where['add_date'] = $date;
			$where['user_ip'] = $v['user_ip'];
			$dataList = User_Service_Earn::getsBy($where);
			foreach ( $dataList as $m=>$n){
				$ipUser = User_Service_DubiousIpUser::getBy(array("uid"=>$n['uid']));
				if(empty($ipUser)){
					$params = array();
					$params['pid'] = $v['id'];
					$params['uid'] = $n['uid'];
					$params['add_time'] = time();
					$params['status'] = 0;
					$params['add_date'] = date("Ymd",time());
					User_Service_DubiousIpUser::add($params);
					++$num;
				}
			}
			User_Service_DubiousIp::edit(array("status"=>1), $v['id']);
		}
		return $num;
	}
	
	
	public static  function changeUserStatus(){
		$where = array();
		$where['status'] = 0;
		list($total,$dataList ) = User_Service_DubiousIpUser::getList(1, 10, $where, array('id'=>'ASC'));
		foreach($dataList as $k=>$v){
			$user = Gionee_Service_User::getUser($v['uid']);
			if(!$user['is_black_user']){
				Gionee_Service_User::updateUser(array("is_black_user"=>1), $v['uid']);
			}
			User_Service_DubiousIpUser::edit(array("status"=>1), $v['id']);
		}
	}
	
	private static function _getDao(){
		return Common::getDao("User_Dao_DubiousIpUser");
	}
}