<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_OnlineLog {
	
	public static function add($params){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
		
	}
	
	public  static function getList($params ,$orders,$page,$pageSize=20){
		if($page<1){
			$page = 1;
		}
		$start = ($page -1)*$pageSize;
		$data = self::_checkData($params);
		return array(self::_getDao()->count($data),self::_getDao()->getList($start,$pageSize,$data,$orders));
	}
	
	public static function get($id){
		if (empty($id)) {
			return array();
		}
		return self::_getDao()->get($id);
	}
	
	public static function update($id,$params){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data,intval($id));
		
	}
	
	public static  function delete($id){
		return self::_getDao()->delete($id);
	}
	private static function _checkData($params){
		$temp = array();
		if(!is_array($params)) return  false;
		if(isset($params['online_date'])) 	$temp['online_date'] 	= $params['online_date'];
		if(isset($params['title']))			$temp['title']			= $params['title'];
		if(isset($params['content']))		$temp['content']		= $params['content'];
		if(isset($params['admin_id']))		$temp['admin_id']	    = $params['admin_id'];
		if(isset($params['add_time']))		$temp['add_time']		= $params['add_time'];
		if(isset($params['edit_userid']))	$temp['edit_userid']	= $params['edit_userid'];
		if(isset($params['edit_time']))		$temp['edit_time']		= $params['edit_time'];
		return $temp;
	}
	private  static function _getDao(){
		return Common::getDao("Gionee_Dao_OnlineLog");
	}
}