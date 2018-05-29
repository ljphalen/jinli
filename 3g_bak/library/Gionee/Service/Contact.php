<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Contact {
	
	public static function  add($params){
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize,$where,$order){
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1)*$pageSize,$pageSize,$where,$order));
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function edit($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	/**
	 * @param var $caller  呼叫电话
	 * ＠param var $called 被呼叫电话
	 * @param var $calledName 被呼叫人名
	 */
	public static function addContactToDb($caller,$called,$calledName=''){
		if(empty($caller) || empty($called)) return false;
		$data = self::getBy(array('user_phone'=>$caller,'cphone'=>$called));
		if(empty($data)){
			$params = array(
				'cname'=>$calledName,
				'cphone'=>$called,
				'created_time'=>time(),
				'user_phone'=>$caller,
			);
			return self::add($params);
		}elseif(empty($data['cname'])  || ($data['cname'] != $calledName)){
			return self::edit(array('cname'=>$calledName), $data['id']);
		}
		return true;
	}
	
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['uid']))							$temp['uid'] = $params['uid'];
		if(isset($params['cname']))					$temp['cname']	 = $params['cname'];
		if(isset($params['cphone']))					$temp['cphone']	 = $params['cphone'];
		if(isset($params['created_time']))		$temp['created_time']	 = $params['created_time'];
		if(isset($params['user_phone']))			$temp['user_phone'] = $params['user_phone'];
		return $temp;
	}
	/**
	 * 
	 * @return Gionee_Dao_Contact
	 */
	private static  function _getDao(){
		return Common::getDao('Gionee_Dao_Contact');
	}
}