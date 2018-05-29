<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_UMoney{
	
	public static function add($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	public static function count($params = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->count($params);
	}
	
	public static function getBy($params = array(),$sort=array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params,$sort);
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function deletesBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	public static function getList($page,$limit,$where =array(),$orderBy= array()){
		if(!is_array($where)) return flase;
		$start = (max(intval($page), 1) - 1) * $limit;
		return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
	}
	
	public static function edit($params,$id){
		if(!is_array($params)) return false;
		return self::_getDao()->update($params, $id);
	}
	
	
	public static function verifyUserMoneyData($uid,$money=0,$goodId=0,$verifyType=1){

		$moneyInfo = User_Service_UMoney::getBy(array('uid'=>$uid));
		if(empty($moneyInfo)){
			$logParams['after_money'] = $money;
			$result = User_Service_UMoney::add(array("uid"=>$uid,'total_money'=>$money,'remained_money'=>$money,'affected_money'=>$money));
		}else{
			$params  = $logParams = array();
			$logParams['before_money'] = $moneyInfo['remained_money'];
			if($money>0){
				$params['total_money'] = $money+ $moneyInfo['total_money'];
			}
			$params['remained_money'] = $moneyInfo['remained_money']+$money;
			$logParams['after_money'] = $params['remained_money'];
			$result = self::edit($params, $moneyInfo['id']);
		}
		$logParams['uid'] = $uid;
		$logParams['type']  = $verifyType;
		$logParams['affected_money'] = $money;
		$logParams['goods_id'] = $goodId;
		$logParams['date'] = date('Ymd',time());
		$logParams['add_time'] = time();
		$logParams['affected_money'] = $money;
		$logRet = User_Service_UMoneyLog::add($logParams);
		return   $result && $logRet;
	}
	
	
	private static function _checkData($params=array()){
		$temp = array(); 
		if(isset($params['uid'])) 								$temp['uid'] = $params['uid'];
		if(isset($params['total_money'])) 			$temp['total_money'] = $params['total_money'];
		if(isset($params['remained_money'])) 	$temp['remained_money'] = $params['remained_money'];
		if(isset($params['affected_money'])) 		$temp['affected_money'] = $params['affected_money'];
		if(isset($params['frozend_money'])) 		$temp['frozend_money'] = $params['frozend_money'];
		return $temp;
	}
	
	/**
	 * 
	 * @return User_Dao_UMoney
	 */
	private static function _getDao(){
		return Common::getDao("User_Dao_UMoney");
	}
}