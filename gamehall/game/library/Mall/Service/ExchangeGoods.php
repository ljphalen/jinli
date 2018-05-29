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
	const GOODS_ALREADY_EXCHANGE = 7;                     //兑换状态：已经兑换
	const GOODS_ISCAN_EXCHANGE = 8;                       //兑换状态：兑换
	const EXCHANGE_NET_UNUSUAL = 9;                       //兑换状态：网络异常
	
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
			$updateStockRet = self::updateMallStock($data);
			if (!$updateStockRet) return $checkExchangeDataResult;
			
			//保存兑换记录
			$exchangeRet = self::saveExchangeLog($data);
			if (!$exchangeRet) return $checkExchangeDataResult;
			
			//更新用户总积分
			$updatePointRet = self::updateUsersPoint($data);
			if (!$updatePointRet) return $checkExchangeDataResult;
			
    		if($good['type'] == Mall_Service_Goods::ACOUPON){  //商品是A券
    			$validateRet = self::inspectorIsValidRequest($data);
    			if (!$validateRet) return $checkExchangeDataResult;
    			
    			$sendRet = self::sendAcoupons($data, $good);  //赠送A券
    			if (!$sendRet) return $checkExchangeDataResult;
    		}
    		
    		//事务提交
    		if($trans) {
    		    $result = parent::commit();
    		    if($good['type'] == Mall_Service_Goods::GIFT){
    		        $checkResult['activation_code'] = $exchangeRet;
    		    }
    		    
    		    $checkResult['msg'] = self::getSendSuccessMsg($good);
    		    $checkResult['exchangeStatus'] = 1;
    		    $checkResult['goodId'] = $data['goodId'];
    		    return $checkResult;
    		}
		} catch (Exception $e) {
			parent::rollBack();
			return $checkExchangeDataResult;
		}
	}
	
	private static function getSendSuccessMsg($good) {
	    switch ($good['type']){
	        case Mall_Service_Goods::ENTITY:
	            $msg = '兑换成功';
	            break;
	        case Mall_Service_Goods::ACOUPON:
	            $msg = '兑换成功';
	            break;
	        case Mall_Service_Goods::GIFT:
	            $msg = '激码已保存到我的奖品';
	            break;
            case Mall_Service_Goods::DISCOUNT_COUPON:
                $msg = '兑换成功';
                break;
            case Mall_Service_Goods::PHONE_RECHARGE_CARD:
                $msg = '兑换成功';
                break;
	        default:
	            $msg ="兑换成功";
	            break;
	    }
	    return $msg;
	}
	
	private static function inspectorIsValidRequest($data) {
		$clientVersion = Common::parseSp($data['sp'], 'game_ver');
		$imei = Common::parseSp($data['sp'], 'imei');
				
		if(strnatcmp($clientVersion, '1.5.7') < 0 ) {
			return true;
		} else {
			if(!$data['serverId']){
				return false;
			}
	
			$apiName = strtoupper('index');
			$verifyInfo = array();
			$imeiDecrypt = Util_Imei::decryptImei($imei);
			$verifyInfo['apiName'] = $apiName;
			$verifyInfo['puuid'] = $data['uuid'];
			$verifyInfo['uname'] = $data['uname'];
			$verifyInfo['imei'] = $imeiDecrypt;
			$verifyInfo['version'] = $clientVersion;
			$verifyInfo['serverId'] = $data['serverId'];
			$ret = self::verifyEncryServerId($verifyInfo);
			if(!$ret){
				return false;
			}
	
			return true;
		}
	}
	
	
	private static function verifyEncryServerId($verifyInfo) {
		$keyParam = array(
				'apiName' => $verifyInfo['apiName'],
				'imei' => $verifyInfo['imei'],
				'uname' => $verifyInfo['uname'],
		);
		$ivParam = $verifyInfo['puuid'];
		$serverId = $verifyInfo['serverId'];
		$serverIdParam = array(
				'clientVersion' => $verifyInfo['version'],
				'imei' => $verifyInfo['imei'],
				'uname' => $verifyInfo['uname'],
		);
		return Util_Inspector::verifyServerId($keyParam, $ivParam, $serverId, $serverIdParam);
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
	public static function sendAcoupons($data, $good) {
	    list($ticketType, $useApiKey, $gameId) = self::getAcouponScopeParmes($good);
		$sendData = array(
				'uuid'=>$data['uuid'],
				'type'=> 6,
				'task_id'=>$data['goodId'],
		        'ticket_type' => $ticketType,
                'useApiKey' => $useApiKey,
                'game_id' => $gameId,
				'section_start'=>1,
				'section_end'=>$data['effect_time'],
				'denomination'=>$data['exchange_num'],
				'desc'=>'积分兑换',
		);
		$exchange = new Util_Activity_Context(new Util_Activity_TicketSend($sendData));
		return $exchange->sendTictket();
	}
	
	public static function getAcouponScopeParmes($good) {
	    $ticketType = $useApiKey = $gameId = 0;
	    if(!$good) return array($ticketType, $useApiKey, $gameId);
	    
	    if($good['game_object'] == Mall_Service_Goods::ALL_PLATFORM){
	       $ticketType = Client_Service_Acoupon::TICKET_TYPE_ACOUPON;
	       $useApiKey = Client_Service_TicketTrade::getPaymentApiKey();
	       $gameId = 0;
	    } else if($good['game_object'] == Mall_Service_Goods::GAME_HALL){
	        $ticketType = Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER;
	        $useApiKey = Client_Service_TicketTrade::getGameHallApikey();
	        $gameId = Resource_Service_Games::getGameHallId();
	    } else if($good['game_object'] == Mall_Service_Goods::APPOINT_GAMES){
	        $gameInfo = Resource_Service_Games::getBy(array('id'=>$good['game_ids']));
	        $ticketType = Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER;
	        $useApiKey = $gameInfo['api_key'];
	        $gameId = $good['game_ids'];
	    }
	    return array($ticketType, $useApiKey, $gameId);
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
		$exchangedNumber = self::getUserGoodsLog($currExchangeReq, $good);
		$canExchangInfo = self::countExchangeNums($currExchangeReq);
		$currExchangeReq['exchange_num'] = $canExchangInfo['canExchangeNums'];

		$acouponInfo = self::myBalance($currExchangeReq['uuid']);
		$exchangeData['totalPoints'] = $userInfo['points'];
		$exchangeData['remainingQty'] = $acouponInfo['ATick'];
		$exchangeData['haveExchangeNum'] = $exchangedNumber;
		$eachGoodConsumePoint = self::getEachGoodConsumePoint($good);
				
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
			$exchangeData['consumePoints'] = $good['remaind_num'] * $eachGoodConsumePoint;
			$exchangeData['exchangeStatus'] = self::GOODS_EXCHANGE_STATUS_STOCKLESS;
			return $exchangeData;
		}
		
		//当前可以兑换的最大数量
		$canExchangeNums = $currExchangeReq['exchange_num'];
		
		//兑换消耗的总积分
		$consumePoints = $canExchangeNums * $eachGoodConsumePoint;
		
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
	
	public static function getEachGoodConsumePoint($good) {
	    if(!$good['discountArr']){
	        return $eachGoodConsumePoint = $good['consume_point'];
	    }
	    
	    $discountArr = json_decode($good['discountArr'],true);
	    if($discountArr['discount_start_time'] <= Common::getTime() && $discountArr['discount_end_time'] > Common::getTime()){
	        $eachGoodConsumePoint = round($good['consume_point'] * ($discountArr['discount'] / 10),0);
	        $eachGoodConsumePoint = ($eachGoodConsumePoint < 1 ? 1 : $eachGoodConsumePoint);
	    } else {
	        $eachGoodConsumePoint = $good['consume_point'];
	    }
	        
	    return $eachGoodConsumePoint;
	}
	
	/**
	 * 页面载入计算可以兑换的商品数量和总的消耗积分
	 * Enter description here ...
	 */
	public static function countExchangeNums($data) {
		$exchangeData = array(
				'consumeOriginalPoints'=>0,
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
		$nums = self::getUserGoodsLog($data, $good);
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
		$eachGoodConsumePoint = self::getEachGoodConsumePoint($good);
		$consumePoints = $canExchangeNums * $eachGoodConsumePoint;
		//积分不足
		if($userInfo['points'] < $consumePoints) {
			$canExchangeNums = floor($userInfo['points'] / $eachGoodConsumePoint);
			if($canExchangeNums < 1){
				$exchangeData['canExchangeNums'] = 0;
				$exchangeData['consumePoints'] = 0;
				$exchangeData['haveExchangeNum'] = $nums;
				return $exchangeData;
			} else {
				$consumePoints = $canExchangeNums * $eachGoodConsumePoint;
			}
		}
		
		$exchangeData['consumeOriginalPoints'] = $canExchangeNums * $good['consume_point'];
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
	
	public static function exchangeInfo($parmes, $good){
	    $exchangeInfo = self::countExchangeNums($parmes);
	    $isCanExchange = 'no';
	    if(is_array($exchangeInfo) && $exchangeInfo['canExchangeNums'] && $good['remaind_num']){
	        $isCanExchange = '';
	    }
	    
	    if(is_array($exchangeInfo) && $exchangeInfo['haveExchangeNum']){
	        $buttonCode = self::GOODS_ALREADY_EXCHANGE;
	    } else if(!$good['remaind_num']){
	        $buttonCode = self::GOODS_EXCHANGE_STATUS_STOCKZERO;
	    } else if(is_array($exchangeInfo) && $exchangeInfo['canExchangeNums'] && $good['remaind_num']){
	        $buttonCode = self::GOODS_ISCAN_EXCHANGE;
	    } else if(is_array($exchangeInfo) && !$exchangeInfo['haveExchangeNum'] && !$exchangeInfo['canExchangeNums']){
	        $buttonCode = self::GOODS_EXCHANGE_STATUS_POINTSLESS;
	        $isCanExchange = 'no';
	    } else{
	        $buttonCode = self::EXCHANGE_NET_UNUSUAL;
	    }
	    
	    $buttonTitle = self::convertButtonTitle($buttonCode);
	    
	    return array($isCanExchange, $buttonCode, $buttonTitle);
	}
	
	private static function convertButtonTitle($buttonCode){
	    switch ($buttonCode){
	        case '2':
	            $buttonTitle = '兑换';
	            break;
	        case '5':
	            $buttonTitle = '已抢光';
	            break;
	        case '7':
	            $buttonTitle = '已兑换';
	            break;
	        case '8':
	           $buttonTitle = '兑换';
	            break;
	        default:
	           $buttonTitle ="网络异常";
	           break;
	    }
	    return $buttonTitle;
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
		if($good['type'] == Mall_Service_Goods::GIFT){
		    $ret = self::updateExchangeGiftLog($data, $good);
		} else {
		    $ret = self::saveExchangeGoodLog($data, $good);
		}
		
		if(!$ret){
			return false;
		}
		return $ret;
	}
	
	private static  function saveExchangeGoodLog($data, $good) {
	    $exchangeLog = array();
	    $exchangeTime = ($good['type'] == Mall_Service_Goods::ACOUPON ||  $good['type'] == Mall_Service_Goods::GIFT ? strtotime($data['exchange_time']) : '');
	    $exchangeStatus = ($good['type'] == Mall_Service_Goods::ACOUPON || $good['type'] == Mall_Service_Goods::GIFT ? 1 : 0);
	    $exchangeLog = array(
	            'id'=>'',
	            'mall_id'=>$data['goodId'],
	            'uuid'=>$data['uuid'],
	            'uname'=>$data['uname'],
	            'exchange_num'=>$data['exchange_num'],
	            'status'=>$exchangeStatus,
	            'exchange_time'=> strtotime($data['exchange_time']),
	            'send_time'=>$exchangeTime,
	            'address'=>$data['address'],
	            'receiver'=>$data['receiver'],
	            'receiverphone'=>$data['receiverphone'],
	            'qq'=>$data['qq'],
	    );
	    return  Mall_Service_ExchangeLog::add($exchangeLog);
	}
	
	private static  function updateExchangeGiftLog($data, $good) {
	    $remainGiftExchangeCode = self::getRemainGiftExchangeCode($data['goodId']);
	    if(!$remainGiftExchangeCode){  //没有激活码
	        return false;
	    }
	    
	    $time = strtotime($data['exchange_time']);//Common::getTime();
	    $imei = Common::parseSp($data['sp'], 'imei');
	    $imeicrc = crc32($imei);
	    $updateField = array('uname' => $data['uname'],
	                         'imei'=>$imei,
	                         'uuid'=>$data['uuid'],
	                         'imeicrc'=>$imeicrc,
	                         'create_time'=>$time,
	                         'status'=>Mall_Service_GiftExchangeLog::GIFT_ALREADY_EXCHANGE);
	    $ret = Mall_Service_GiftExchangeLog::updateById($updateField, $remainGiftExchangeCode['id']);
	    if(!$ret){
	        return false;
	    }
	    
	    $saveExchangeLogRet = self::saveExchangeGoodLog($data,$good);
	    $addGiftLogRet = self::addGiftlog($updateField, $data, $remainGiftExchangeCode);
	    if(!$saveExchangeLogRet || !$addGiftLogRet){
	        return false;
	    }
	    
	    return $remainGiftExchangeCode['activation_code'];
	}
	
	private static function addGiftlog($updateField, $data, $remainGiftExchangeCode){
	    $myGiftLogInfo = array('id' => '',
                	            'log_type' => Client_Service_Giftlog::EXCHANGE_GIFT_LOG,
                	            'gift_id' => $data['goodId'],
                	            'game_id' => '',
                	            'uname' => $updateField['uname'],
                	            'imei'=>$updateField['imei'],
                	            'imeicrc'=>$updateField['imeicrc'],
                	            'activation_code'=>$remainGiftExchangeCode['activation_code'],
                	            'create_time'=>$updateField['create_time'],
                	            'status'=>Mall_Service_Goods::STATUS_OPEN,
                	            'send_order'=>$remainGiftExchangeCode['send_order'],
	    );
	    return Client_Service_Giftlog::addGiftlog($myGiftLogInfo);
	}
	
	private static function getRemainGiftExchangeCode($goodId){
	    $remainGiftExchangeCode = Mall_Service_GiftExchangeLog::getBy(
	            array('status'=>Mall_Service_GiftExchangeLog::GIFT_NOT_EXCHANGE,
	                    'good_id'=>$goodId),
	            array('send_order'=>'ASC',
	                    'id'=>'ASC'));
	    return $remainGiftExchangeCode;
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
	private static function getUserGoodsLog($data, $good){
	    if(!is_array($data)){
	        return false;
	    }
	     
	    $nums = 0;
	    if($good['type'] == Mall_Service_Goods::GIFT){
	        $nums = self::getUserExchangeGiftGoodsNum($data);
	    } else {
	        $nums = self::getUserExchangeCommonGoodsNum($data);
	    }
	    return $nums;
	}
	
	private static function getUserExchangeCommonGoodsNum($data){
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
	
	private static function getUserExchangeGiftGoodsNum($data){
	    $parmes = array();
	    $parmes['uuid'] = $data['uuid'];
	    $parmes['good_id'] = $data['goodId'];
	    return  Mall_Service_GiftExchangeLog::getGiftLogCount($parmes);
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
	
	public static function getCountDownTitle($good){
	    $discountTitle = $goodCountDownTitle = "";
	    if(!$good){
	       return array($goodCountDownTitle, $discountTitle);
	    }
	    
	    if($good['discountArr']){
	        $discountArr = json_decode($good['discountArr'],true);
	        $discountTitle = self::getCountDown($discountArr['discount_end_time']);
	    }
	    
	    $goodCountDownTitle = self::getCountDown($good['end_time']);
	    return array($goodCountDownTitle, $discountTitle);
	}
	
	public static function getConsumePoint($good){
	    $isDiscount = false;
	    $discountArr = array();
	    if(!$good['discountArr']){
	        return array($isDiscount, $good['consume_point'], $discountArr);
	    }
	    
	    $isDiscount = true;
	    $discountArr = json_decode($good['discountArr'],true);
	    $consumePoint =  round($good['consume_point'] * ($discountArr['discount'] / 10),0);
	    $consumePoint = $consumePoint < 1 ? 1 : $consumePoint;
	    return array($isDiscount, $consumePoint, $discountArr);
	}
	
	
	public static function getSeckillTime($time){
	    echo $time;
	    if(!$time || $time < Common::getTime()) return "已结束";
	    if($time){
	        $diffTime = $time - Common::getTime();
	        $d = floor($diffTime/3600/24);
	        $h = floor(($diffTime%(3600*24))/3600);
	        $m = floor(($diffTime%(3600*24))%3600/60);
	        $s = floor(($diffTime%(3600*24))%(3600*60)/60);
	    }
	    $seckillTime = array();
	    $seckillTime['d'] = $d;
	    $seckillTime['h'] = $h;
	    $seckillTime['m'] = $m;
	    $seckillTime['s'] = $s;
	    return $seckillTime;
	}
	
	public static function getCountDown($time){
	    if(!$time || $time < Common::getTime()) return "已结束";
	    if($time){
	        $diffTime = $time - Common::getTime();
	        $d = floor($diffTime/3600/24);
	        $h = floor(($diffTime%(3600*24))/3600);
	        $m = floor(($diffTime%(3600*24))%3600/60);
	    }

	    if($d){
	      return   "剩余".$d."天";
	    } 
	    
	    if($h){
	      return   "剩余".$h."小时";
	    } 
	    if($m){
	      return  "剩余".$m."分钟";
	    }
	     
	    if(!$d && !$h && !$m){
	      return  "已结束";
	    }

	}
}
