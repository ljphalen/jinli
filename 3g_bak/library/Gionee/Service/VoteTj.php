<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_VoteTj {
	
	//获得所有用户的投票统计结果
	public static function getList($page,$size=10,$where=array(),$group=array(),$order=array()){
		$total 		= self::count($where,$group);
		$dataList 	=  self::_getDao()->getUserRecord(($page -1)*$size,$size,$where,$group,$order);
		return array($total,$dataList);
	}
	
	//排行榜
	public function rank($page,$size,$where,$group,$order){
		$dataList =  self::_getDao()->getUserRecord(($page -1)*$size,$size,$where,$group,$order);
		return $dataList;
	}
	//获得到单个用户的投票记录
	public static function getRecordData($page,$pageSize=10,$params,$order=array()){
		if(!is_array($params) ||!is_array($order)) return false;
		$start = ($page - 1) * $pageSize;
		return array(self::count($params), self::_getDao()->getRecordData($start,$pageSize,$params,$order));
	}
	
	//所有投票有用户数
	public static function count($params,$group=array()){
		$result =  self::_getDao()->getCount($params,$group);
		return count($result);
	}
	
	public static function edit($params){
		$data = self::_checkData($params);
		return self::_getDao()->updateBy(array('score'=>1),$params);
	}
	//检测用户是否已经投注
	public static function check($params= array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	//添加信息
	public static function add($param){
		if(!is_array($param)) return false;
		$temp = array();
		$temp = self::_checkData($param);
		return self::_getDao()->insert($temp);
	}
	
	private static function _checkData($param){
		if(isset($param['uid'])) 	$temp['uid'] = $param['uid'];
		if(isset($param['phone']))	$temp['phone']=$param['phone'];
		if(isset($param['pid'])) 	$temp['pid'] = $param['pid'];
		if(isset($param['score'])) 	$temp['score'] = $param['score'];
		if(isset($param['result']))	$temp['result']= $param['result'];
		if(isset($param['utype']))	$temp['utype'] = $param['utype'];
		if(isset($param['add_time']))$temp['add_time'] = $param['add_time'];
		return $temp;
		
	}
	private static function _getDao(){
		return Common::getDao("Gionee_Dao_VoteTj");
	} 
}
