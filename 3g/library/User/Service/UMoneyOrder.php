<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_UMoneyOrder{
	
public static function add($params = array()){
	if(!is_array($params)) return false;
	$params = self::_checkData($params);
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

public static function edit($params,$id){
	if(!is_array($params)) return false;
	return self::_getDao()->update($params, $id);
}

public static function getList($page,$limit,$where =array(),$orderBy= array()){
	if(!is_array($where)) return flase;
	$start = (max(intval($page), 1) - 1) * $limit;
	return array(self::_getDao()->count($where),self::_getDao()->getList($start,$limit,$where,$orderBy));
}

public static function generateOrder($uid,$amount=0.0,$fee=0,$orderType=1){
	$params = array();
	$params['order_sn'] = self::getOrderSn();
	$params['uid'] = $uid;
	$params['deal_price'] = $amount;
	$params['total_fee'] = $amount;
	$params['deliver_type'] = 1;
	$params['submit_time'] =time();
	$params['order_status'] = 0;
	$params['order_type'] = $orderType;
	$params['add_time'] = time();
	$params['pay_status'] = 1;
	$ret =   self::add($params);
	$orderId = 0;
	if($ret){
		$orderId = self::_getDao()->getLastInsertId();
	}
	return $orderId;
}

private static function getOrderSn(){
	list($micro,$seconds) = explode(" ",microtime());
	return date('YmdHis',time()).substr($micro,2,5);
}

private static function _checkData($params=array()){
	$temp = array();
	if(isset($params['uid'])) 								$temp['uid'] = $params['uid'];
	if(isset($params['order_sn'])) 					$temp['order_sn'] = $params['order_sn'];
	if(isset($params['deal_price'])) 				$temp['deal_price'] = $params['deal_price'];
	if(isset($params['total_fee'])) 					$temp['total_fee'] = $params['total_fee'];
	if(isset($params['deliver_type'])) 			$temp['deliver_type'] = $params['deliver_type'];
	if(isset($params['expire_time'])) 				$temp['expire_time'] = $params['expire_time'];
	if(isset($params['add_time']))					$temp['add_time'] = $params['add_time'];
	if(isset($params['order_status'])) 			$temp['order_status'] = $params['order_status'];
	if(isset($params['msg'])) 							$temp['msg'] = $params['msg'];
	if(isset($params['pay_status']))				$temp['pay_status'] = $params['pay_status'];
	if(isset($params['front_response_status']))$temp['front_response_status'] = $params['front_response_status'];
	if(isset($params['front_response_time']))	$temp['front_response_time'] = $params['front_response_time'];
	if(isset($params['back_response_status']))$temp['back_response_status'] = $params['back_response_status'];
	if(isset($params['back_response_time']))	$temp['back_response_time'] = $params['back_response_time'];
	return $temp;
}
/**
 *
 * @return User_Dao_UMoneyOrder
 */
private static function _getDao(){
	return Common::getDao("User_Dao_UMoneyOrder");
}
}