<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Mall_Service_ExchangeGoods extends Common_Service_Base{
	const CHECK_GOODS_STATUS_NORMAL = 1;                  //合法请求
	const CHECK_GOODS_STATUS_ILLEGAL = 2;                 //非法请求
	const CHECK_GOODS_STATUS_NOTLOGIN = 3;                //未登陆
	const CHECK_GOODS_STATUS_UNDERCARRIAGE = 4;           //商品下架
	
	const GOODS_EXCHANGE_STATUS_SUCCESS = 1;              //兑换状态：兑换成功
	const GOODS_EXCHANGE_STATUS_POINTSLESS = 2;           //兑换状态：积分不足
	const GOODS_EXCHANGE_STATUS_MAXEXCHANGENUM = 3;       //兑换状态：已达到最大限制
	const GOODS_EXCHANGE_STATUS_STOCKLESS = 4;            //兑换状态：库存不足
	const GOODS_EXCHANGE_STATUS_STOCKZERO = 5;            //兑换状态：库存为0
	
	public static $checkRespon = array(
			 self::CHECK_GOODS_STATUS_ILLEGAL,
			 self::CHECK_GOODS_STATUS_NOTLOGIN,
			 self::CHECK_GOODS_STATUS_UNDERCARRIAGE
			);
	
	/**
	 * 兑换商品
	 * Enter description here ...
	 */
	public static function exchangeGoods($data) {
		$ret = self::checkParmes($data);
		if($ret == 2)  return self::CHECK_GOODS_STATUS_ILLEGAL;
		if($ret == 3)  return self::CHECK_GOODS_STATUS_NOTLOGIN;
		$good = self::checkIsGoods($data['goodId']);
		if($good == 4)  return self::CHECK_GOODS_STATUS_UNDERCARRIAGE;
		
		$checkExchangeDataResult = self::checkIsExchangeData($data);
		
		//兑换商品参数错误
		if(in_array($checkExchangeDataResult, self::$checkRespon)){
			return $checkExchangeDataResult;
		}  
		
		$data = $checkExchangeDataResult[0];
		$checkExchangeDataResult = $checkExchangeDataResult[1];
				
		//兑换不满足条件
		if($checkExchangeDataResult['exchangeStatus'] != 1){
			return $checkExchangeDataResult;
		}
		
		//开始事务
		$trans = parent::beginTransaction();
		try {

			//更新库存
			$ret = self::updateMallStock($data);
			if (!$ret) return $checkExchangeDataResult;
			
			//保存兑换记录
			$ret = self::saveExchangeLog($data);
			if (!$ret) return $checkExchangeDataResult;
			
			//更新用户总积分
			$ret = self::updateUsersPoint($data);
			if (!$ret) return $checkExchangeDataResult;
			
		
		//事务提交
		if($trans)  parent::commit();

		if($good['type'] == 2){ //商品是A券
			//赠送A券
			self::sendAcoupons($data);
		}
		
		$checkResult['exchangeStatus'] = 1;
		return $checkResult;
		
		} catch (Exception $e) {
			parent::rollBack();
			return $checkExchangeDataResult;
		}
	}
	
	/*
	 * 检查是否可以兑换
	 */
	private static function checkIsExchangeData($data) {
		$good = Mall_Service_Goods::get($data['goodId']);
		$data['effect_time']  = $good['effect_time'];
		$data['title']  = $good['title'];
		$datetime = date('Y-m-d H:i:s',Common::getTime());
		$data['exchange_time'] = $datetime;
		$checkResult = self::commitCountExchangeNums($data);
		if(is_array($checkResult)){
			$data['exchange_num'] = $checkResult['canExchangeNums'];
			$data['consume_point'] = $checkResult['consumePoints'];
			return array($data, $checkResult);
		} else if(in_array($checkResult, self::$checkRespon)){
			return $checkResult;
		}
	}
	
	/*
	 * 赠送A券
	 */
	public static function sendAcoupons($data) {
		$sendData = array(
				'uuid'=>$data['uuid'],
				'type'=> 6,
				'task_id'=>$data['goodId'],
				'section_start'=>1,
				'section_end'=>$data['effect_time'],
				'denomination'=>$data['exchange_num'],
				'desc'=>'积分兑换',
		);
		$exchange = new Util_Activity_Context(new Util_Activity_TicketSend($sendData));
		return $exchange->sendTictket();
	}
	
	/**
	 * 提交计算可以兑换的商品数量和总的消耗积分
	 * Enter description here ...
	 */
	public static function commitCountExchangeNums($currExchangeReq) {
		$exchangeData = array(
				'consumePoints'=>0,
				'totalPoints'=>0,
				'exchangeTime'=>0,
				'remainingQty'=>0,
				'exchangeStatus'=>0
		);
		
		$ret = self::checkParmes($currExchangeReq);
		if($ret == 2)  return self::CHECK_GOODS_STATUS_ILLEGAL;
		if($ret == 3)  return self::CHECK_GOODS_STATUS_NOTLOGIN;
		//查找当前要兑换的商品
		$good = self::checkIsGoods($currExchangeReq['goodId']);
		if($good == 4)  return self::CHECK_GOODS_STATUS_UNDERCARRIAGE;
		
		//检查该用户积分
		$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$currExchangeReq['uuid']));
		if(!$userInfo)  return self::CHECK_GOODS_STATUS_ILLEGAL;
		
		//查找当前用户兑换该商品的记录
		$exchangedNumber = self::getUserGoodsLog($currExchangeReq);

		$acouponInfo = self::myBalance($currExchangeReq['uuid']);
		$exchangeData['totalPoints'] = $userInfo['points'];
		$exchangeData['remainingQty'] = $acouponInfo['ATick'];
		$exchangeData['haveExchangeNum'] = $exchangedNumber;
				
		if($good['remaind_num'] <= 0){
			//库存为零
			$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_STOCKZERO;
			return $exchangeData;
		}
		
		//已达最大兑换限制
		if($good['preson_limit_num'] < $exchangedNumber + $currExchangeReq['exchange_num']) {
			$exchangeData['exchangeTime'] = 0;
			$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_MAXEXCHANGENUM;
			return $exchangeData;
		}
		
		//用户输入大于库存
		if($currExchangeReq['exchange_num'] > $good['remaind_num']){
			$exchangeData['exchangeTime'] = $good['remaind_num'];
			$exchangeData['canExchangeNums'] = $good['remaind_num'];
			$exchangeData['consumePoints'] = $good['remaind_num'] * $good['consume_point'];
			$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_STOCKLESS;
			return $exchangeData;
		}
		
		//当前可以兑换的最大数量
		$canExchangeNums = $currExchangeReq['exchange_num'];
		
		//兑换消耗的总积分
		$consumePoints = $canExchangeNums * $good['consume_point'];
		//积分不足
		if($userInfo['points'] < $consumePoints) {
			$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_POINTSLESS;
			$exchangeData['consumePoints'] = 0;
			return $exchangeData;
		}

		$exchangeData['canExchangeNums'] = $canExchangeNums;
		$exchangeData['consumePoints'] = $consumePoints;
		$exchangeData['exchangeTime'] = $canExchangeNums;
		$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_SUCCESS;
		return $exchangeData;
	}
	
	
	/**
	 * 页面载入计算可以兑换的商品数量和总的消耗积分
	 * Enter description here ...
	 */
	public static function countExchangeNums($data) {
		$exchangeData = array(
				'consumePoints'=>0,
				'canExchangeNums'=>0,
				'haveExchangeNum'=>0,
		);
		
		$ret = self::checkParmes($data);
		if($ret == 2)  return self::CHECK_GOODS_STATUS_ILLEGAL;
		if($ret == 3)  return self::CHECK_GOODS_STATUS_NOTLOGIN;
		//查找当前要兑换的商品
		$good = self::checkIsGoods($data['goodId']);
		if($good == 4)  return self::CHECK_GOODS_STATUS_UNDERCARRIAGE;
	
		//检查该用户积分
		$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$data['uuid']));
		if(!$userInfo)  return self::CHECK_GOODS_STATUS_ILLEGAL;
	
		//查找当前用户兑换该商品的记录
		$nums = self::getUserGoodsLog($data);
		$exchangeData['haveExchangeNum'] = $nums;
		   		
		//已达最大兑换限制
		if($good['preson_limit_num'] <= $nums) {
			$exchangeData['haveExchangeNum'] = $nums;
			return $exchangeData;
		}
	
		//当前可以兑换的最大数量
		$canExchangeNums = $good['preson_limit_num'] - $nums;
	
		//库存不足(小于可以兑换的最大数量)
		if($good['remaind_num'] < $canExchangeNums && $good['remaind_num'] > 0) {
			$canExchangeNums = $good['remaind_num'];
			$exchangeData['canExchangeNums'] = $good['remaind_num'];
		}
			
		//兑换消耗的总积分
		$consumePoints = $canExchangeNums * $good['consume_point'];
		//积分不足
		if($userInfo['points'] < $consumePoints) {
			$canExchangeNums = floor($userInfo['points'] / $good['consume_point']);
			if($canExchangeNums < 1){
				$exchangeData['canExchangeNums'] = 0;
				$exchangeData['consumePoints'] = 0;
				$exchangeData['haveExchangeNum'] = $nums;
				return $exchangeData;
			} else {
				$consumePoints = $canExchangeNums * $good['consume_point'];
			}
		}
		
		$exchangeData['canExchangeNums'] = $canExchangeNums;
		if($canExchangeNums > 0) $exchangeData['haveExchangeNum'] = 0;
		$exchangeData['consumePoints'] = $consumePoints;
		return $exchangeData;
	}
	
	
	/**
	 * 检查参数是否正确
	 * @param array $data
	 * @return boolean
	 */
	private  static function checkParmes($data) {
		//非法请求
		if (!$data['uname'] || !$data['uuid']) return self::CHECK_GOODS_STATUS_ILLEGAL;
		//未登录或者用户不存在
		$imei = end(explode('_',$data['sp']));
		$userInfo = Account_Service_User::checkOnline($data['uuid'], $imei, 'uuid');
		if(!$userInfo)  return self::CHECK_GOODS_STATUS_NOTLOGIN;
		return self::CHECK_GOODS_STATUS_NORMAL;
	}
	
	public static function checkIsGoods($goodId) {
		$good = array();
		$good = Mall_Service_Goods::get($goodId);
		//商品下架
		if(!$good['status']) return self::CHECK_GOODS_STATUS_UNDERCARRIAGE;
		if($good['start_time'] > Common::getTime() || $good['end_time'] < Common::getTime()) return self::CHECK_GOODS_STATUS_UNDERCARRIAGE;
		return $good;
	}
	
	/**
	 * 更新库存
	 * @param array $data
	 * @return boolean
	 */
	private static function updateMallStock($data){
		if(!is_array($data)){
			return false;
		}
		$stockData = array();
		$good = Mall_Service_Goods::get($data['goodId']);
		if(!$good) return false;
		$remaind_num = $good['remaind_num'] - $data['exchange_num'];
		if($remaind_num < 0) return false;
		$stockData['remaind_num'] = $remaind_num;
		$params['id'] = $data['goodId'];
		$ret  = Mall_Service_Goods::updateBy($stockData, $params);
		if(!$ret){
			return false;
		}
		return true;
	}
	
	/**
	 * 保存兑换记录
	 * @param array $data
	 * @return boolean
	 */
	private static  function saveExchangeLog($data) {
		if (!is_array($data)) return false;
		$good = Mall_Service_Goods::get($data['goodId']);
		$exchangeLog = array();
		$exchangeLog = array(
				'id'=>'',
				'mall_id'=>$data['goodId'],
				'uuid'=>$data['uuid'],
				'uname'=>$data['uname'],
				'exchange_num'=>$data['exchange_num'],
				'status'=>($good['type'] == 1  ? 0 : 1),
				'exchange_time'=> strtotime($data['exchange_time']),
				'send_time'=>($good['type'] == 2  ? strtotime($data['exchange_time']) : ''),
				'address'=>$data['address'],
				'receiver'=>$data['receiver'],
				'receiverphone'=>$data['receiverphone'],
		);
		$ret = Mall_Service_ExchangeLog::add($exchangeLog);
		if(!$ret){
			return false;
		}
		return true;
	}
	
	/**
	 * 更新用户积分(消耗)
	 * @param array $data
	 * @param array $unames
	 * @return boolean
	 */
	private  static function updateUsersPoint($data) {
		if (!is_array($data)) return false;
		$usrGainPoint = array();
		$usrGainPoint = array(
					'id'=>'',
					'uuid'=>$data['uuid'],
					'consume_type'=>'1',
					'consume_sub_type'=> $data['goodId'],
					'points'=>$data['consume_point'],
					'create_time'=>strtotime($data['exchange_time']),
					'update_time'=>strtotime($data['exchange_time']),
		);
		$ret = Point_Service_User::consumePresentUserPoints($usrGainPoint, $data);
		if(!$ret){
			return false;
		}
		return true;
	}
	
	/**
	 * 查找用户兑换记录
	 * @param array $data
	 * @return INT
	 */
	private static function getUserGoodsLog($data){
		if(!is_array($data)){
			return false;
		}
		$parmes = array();
		$parmes['uuid'] = $data['uuid'];
		$parmes['mall_id'] = $data['goodId'];
		$logs = Mall_Service_ExchangeLog::getsBy($parmes);
		$nums = 0;
		foreach($logs as $key=>$value){
			$nums += $value['exchange_num'];
		}
		return $nums;
	}
	
	/**
	 * 我的A币和A券的总数
	 */
	public static function myBalance($uuid){
		$payment_arr = Common::getConfig('paymentConfig','payment_send');
		$api_key    = $payment_arr['api_key'];
		$url      = $payment_arr['coin_url'];
		$ciphertext= $payment_arr['ciphertext'];
		 
		//加密的密文
		$temp['api_key'] = $api_key;
		$temp['uuid']    = $uuid;
		$token = md5($ciphertext.$api_key.$uuid);
		$temp['token']   = $token;
		$data['data']  = array();
		//post到支付服务器
		//A券的余额
		$json_data = json_encode($temp);
		$result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
		$rs_list = json_decode($result->data,true);
		//A券
		$data['ATick'] = $rs_list['voucher']?strval(number_format($rs_list['voucher'], 2, '.', '' )):strval(0);
		//A币
		$data['ACoin'] = $rs_list['gold_coin']?strval(number_format($rs_list['gold_coin'], 2, '.', '')):strval(0);
		//积分
		$userInfo = Account_Service_User::getUserInfo(array('uuid' => $uuid));
		$data['points'] = intval($userInfo['points']);
			
		return $data;
		 
	}
}
