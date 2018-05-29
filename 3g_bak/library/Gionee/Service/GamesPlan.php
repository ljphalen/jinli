<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//世界杯竟猜活动

class Gionee_Service_GamesPlan {
	
	
	public  static function getAll($order=array()){
		return self::_getDao()->getAll($order);
	}
	
	
	public static function add($data){
		if(!is_array($data)) return false;
		$params = self::_checkData($data);
		return self::_getDao()->insert($params);
	}
	
	public static function edit($data,$id){
		if(!is_array($data)) return false;
		$params = self::_checkData($data);
		return self::_getDao()->update($params,$id);
	}
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	
	public static function getshaudles($field,$where=array(),$group=array(),$order=array()){
		return self::_getDao()->getshaudles($field,$where,$group,$order);
	}
	
	public static function getsBy($params,$order){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params,$order);
	}
	
	public static function getList($page,$limit=10,$params,$orderBy){
		if(!is_array($params)) return false;
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params,$orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	private static function _checkData($info){
		$temp = array();
		if(isset($info['team1'])) 		$temp['team1'] 		= $info['team1'];
		if(isset($info['team2'])) 		$temp['team2'] 		= $info['team2'];
		if(isset($info['pic1']))  		$temp['pic1']  		= $info['pic1'];
		if(isset($info['pic2']))  		$temp['pic2']  		= $info['pic2'];
		if(isset($info['desc1']))  		$temp['desc1']  	= $info['desc1'];
		if(isset($info['desc2']))  		$temp['desc2']  	= $info['desc2'];
		if(isset($info['sort']))  		$temp['sort']  		= $info['sort'];
		if(isset($info['game_date']))	$temp['game_date']	= strtotime($info['game_date']);
		if(isset($info['start_time']))  $temp['start_time'] = $info['start_time'];
		if(isset($info['pre_end']))  	$temp['pre_end']  	= $info['pre_end'];
		if(isset($info['link']))  		$temp['link']  		= $info['link'];
		if(isset($info['status']))  	$temp['status']  	= $info['status'];
		if(isset($info['pre_end'])) 	$temp['pre_end'] 	= $info['pre_end'];

		if(isset($info['id'])){
			if(isset($info['result']))	$temp['result'] 	= $info['result'];
			if(isset($info['score']))	$temp['score']		=$info['score'];
			$temp['edit_time']	= time();
		}else{
			$temp['add_time'] = time();
		}
		return $temp;
		
	}
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Gamesplan");
	}
	
	
}