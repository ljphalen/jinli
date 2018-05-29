<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

//收入汇总
class Gionee_Service_Income {
	
	public static function get($id){
		if (empty($id)) {
			return false;
		}
		return self::_getDao()->get($id);
	}
	
	public static function add($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function getList($page,$pageSize,$where,$order){
		if(!is_array($where)) return false;
		return array(self::_getDao()->count($where), self::_getDao()->getList($pageSize *($page-1),$pageSize,$where,$order));
	}
	
	public static function update($params,$id){
		if(!is_array($params) || !$id) return false;
		return self::_getDao()->update($params,$id);
	}
	
	public static function getsBy($params =array(),$orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$orderBy);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	private static function _checkData($params){
		$temp  = array();
		if(isset($params['get_time']))  $temp['get_time'] = $params['get_time'];
		if(isset($params['income']))	  $temp['income'] = $params['income'];
		if(isset($params['cps']))	  $temp['cps'] = $params['cps'];
		if(isset($params['cpc']))	  $temp['cpc'] = $params['cpc'];
		if(isset($params['cpt']))	  $temp['cpt'] = $params['cpt'];
		if(isset($params['cpa']))	  $temp['cpa'] = $params['cpa'];
		if(isset($params['add_time']))	  $temp['add_time'] = $params['add_time'];
		if(isset($params['add_user']))	  $temp['add_user'] = $params['add_user'];
		return $temp ;
		
	}
	public static function getLastMothIncome(){
			$key = "NAV:INCOME";
			$dataList = Common::getCache()->get($key);
			if(empty($dataList)){
				$date  = date('Ym',strtotime("now"));
				$year = substr($date,0,4);
				$month = substr($date, 4,2);
				$preFirstday= date('Y-m-01 H:i:s', mktime(0,0,0,$month,1,$year));
				$preLastDay= date('Y-m-d 23:59:59', strtotime("$preFirstday +1 month -1 day"));
				$dataList = Gionee_Service_Income::getsBy(array('get_time'=>array(array('>=',strtotime($preFirstday)),array('<=',strtotime($preLastDay)))),array('get_time'=>'DESC'));
				foreach ($dataList as $k=>$v){
					$dataList[$k]['income'] = round($v['income']);
					$dataList[$k]['cps'] = round($v['cps']);
					$dataList[$k]['cpc'] = round($v['cpc']);
					$dataList[$k]['cpt'] = round($v['cpt']);
					$dataList[$k]['cpa'] = round($v['cpa']);
					$dataList[$k]['push']=round($v['push']);
				}
				Common::getCache()->set($key,$dataList,60);
			}
		return $dataList;
	}
	
	private static function _getDao(){
		return Common::getDao("Gionee_Dao_Income");
	}
	
}