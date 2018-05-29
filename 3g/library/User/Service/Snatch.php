<?php 

if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Snatch{
	
	
	public static  function add($params){
		if(!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize ,$where =array(),$orderBy = array()){
		if(!is_array($where)) return false;
		$data = self::_checkData($where);
		$dataList =  self::_getDao()->getList(($page-1)*$pageSize, $pageSize,$data,$orderBy);
		return array(self::_getDao()->count($where),$dataList);
	}
	
	public  static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function getBy($where=array(),$order =array()){
		return self::_getDao()->getBy($where,$order);;
	}
	
	public static function getsBy($params =array(),$groupBy=array()){
		if(!is_array($params) ) return false;
		return self::_getDao()->getsBy($params,$groupBy);
	}
	public static function update($params= array(),$id){
		if(empty($params)|| !is_numeric($id)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function delete($id){
		if(!is_numeric($id)) return false;
		return self::_getDao()->delete($id);
	}
	private static function _checkData($params){
		$temp  =array();
		if(isset($params['name']))       $temp['name'] = $params['name'];
		if(isset($params['status']))       $temp['status'] = $params['status'];
		if(isset($params['sort']))           $temp['sort'] = $params['sort'];
		if(isset($params['number']))   $temp['number'] = $params['number'];
		if(isset($params['add_time']))  $temp['add_time'] = $params['add_time'];
		if(isset($params['prize_type']))   $temp['prize_type'] = $params['prize_type'];
		if(isset($params['prize_info']))   $temp['prize_info'] = $params['prize_info'];
		if(isset($params['image']))  		$temp['image'] = $params['image'];
		if(isset($params['cost_scores']))  $temp['cost_scores'] = $params['cost_scores'];
		if(isset($params['subtitle']))		$temp['subtitle'] = $params['subtitle'];
		return $temp;
	
	}
	
	/**
	 * @return User_Dao_Category
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_Snatch");
	}
	
}

?>