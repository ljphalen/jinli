<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Uprivilege {
	
	public static  function add($params){
		if(empty($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	
	public static function getList($page,$pageSize,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList($pageSize*($page-1),$pageSize,$where,$orderBy));
	}
	
	public static function get($id){
		if(!is_numeric($id)) return false;
		return self::_getDao()->get($id);
	}
	
	public static  function getBy($params =array(),$order = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$order);
	}
	
	public static  function getsBy($params =array(),$order = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$order);
	}
	
	public static function update($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	public static  function deleteBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	//得到等级商品聚合信息
	public static function getLevelGoods($page=1,$pageSize=20,$where =array(),$group= array(),$orderBy = array()){
		if(!is_array($group)) return false;
		$page  = max($page,1);
		$count = self::countByParams($where,$group);
		$dataList = self::_getDao()->getLevelGoods(($page-1)*$pageSize,$pageSize,$where,$group,$orderBy);
		return array($count,$dataList);
		
	}
	
	//group by 的统计
	public static function countByParams($where=array(),$group=array()){
		return self::_getDao()->countByParams($where,$group);
	}
	
	//where 条件的统计
	public static function countBy($params=array(),$order =array()){
		if(!is_array($params)) return false;
		return self::_getDao()->countBy($params,$order);
	}
	
	
	private static function _checkData($params){
		$temp =array();
		if(isset($params['group_id']))		$temp['group_id'] = $params['group_id'];
		if(isset($params['cat_id'])) 		 	 $temp['cat_id'] = $params['cat_id'];
		if(isset($params['goods_id']))		 $temp['goods_id'] = $params['goods_id'];
		if(isset($params['level_group']))	 $temp['level_group'] = $params['level_group'];
		if(isset($params['user_level']))	 	 $temp['user_level'] = $params['user_level'];
		if(isset($params['scores']))	 		 $temp['scores'] = $params['scores'];
		if(isset($params['days'])) 				 $temp['days'] = $params['days'];
		if(isset($params['rewards']))   		 $temp['rewards'] = $params['rewards'];
		if(isset($params['times']))	 			 $temp['times'] = $params['times'];
		if(isset($params['rewards2']))	 	 $temp['rewards2'] = $params['rewards2'];
		if(isset($params['number']))	 		 $temp['number']=$params['number'];
		if(isset($params['status']))      		 $temp['status'] = $params['status'];
		if(isset($params['add_time']))		 $temp['add_time'] = $params['add_time'];
		if(isset($params['add_admin']))	 $temp['add_admin'] = $params['add_admin'];
		if(isset($params['edit_time']))	     $temp['edit_time'] = $params['edit_time'];
		if(isset($params['edit_admin']))	 $temp['edit_admin'] = $params['edit_admin'];
		return  $temp;
	}
	
	private static function _getDao(){
		return Common::getDao('User_Dao_Uprivilege');
	}
}