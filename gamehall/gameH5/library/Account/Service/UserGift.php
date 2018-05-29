<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
class Account_Service_UserGift{
	const USER_SEND = 7;
	const BIRTHDAY_GIFT = 1;
	const POINT_GAIN_MESSAGE = 107;
	const POINT_SYSTEM_MESSAGE = 100;
	const POINT_GAIN_TO_USER = 1;
	const USER_GIFT_ACOUPON = 1;
	const USER_GIFT_POINT = 2;
	const ONE_DAY_UNIX_SECOND = 86400;
	
	
	/**
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function getList($page = 1, $limit = 10, $params, $orderBy = array('id'=>'desc')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $result);
	}
	
	/**
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 
	 * @param array $data
	 * @param array $params
	 * @return 
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data) ) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 赠送生日礼物
	 * @param string $uuid
	 * @param array $config
	 */
	public static function sendGift($uuid, $config){
		if(!$uuid) return false;
		if(!$config) return false;
		$result = false;
		$sendData = array();
		//A券赠送
		if($config['acoupon']['cost'] && $config['acoupon']['day']){
			$result = self::sendAcouponGiftProcess($uuid, $config);
			if(!$result) return false;
		}
		//积分赠送
		if($config['point']['cost']){
			$result = self::sendPointGiftPorcess($uuid, $config);
			if(!$result) return false;
		}
		return $result;
	}
	
	/**
	 * 判断用户礼物是否今年已发送
	 * @param string $uuid
	 */
	public static function isSendGift($uuid){
		$year = date('Y');
		$sendData = self::_getDao()->getByYear(array('uuid'=>$uuid), $year);
		if($sendData) {
			return true;
		}
		return false;
	}
	
	/**
	 * 发送积分生日礼物
	 * @param string $uuid
	 * @param array $config
	 */
	private static function sendPointGiftPorcess($uuid, $config){
		$time = Common::getTime();
		$result = false;
		$gainData = array(
				'uuid' => $uuid,
				'gain_type'=> self::USER_SEND,
				'gain_sub_type' => self::BIRTHDAY_GIFT,
				'points' => $config['point']['cost'],
				'create_time' => $time,
				'update_time' => $time,
				'stauts' => 1
		);
		$result = self::sendPoint($gainData);
		if($result){
			self::saveMessageToCache($uuid, $config['point']['cost']);
			$seasonTime = Common::getSeasonTimeRange();
			$sendData = array(
					'uuid' => $uuid,
					'type' => self::USER_GIFT_POINT,
					'day' => 0,
					'cost' => $config['point']['cost'],
					'effect_start_time' => $time,
					'effect_end_time' => strtotime($seasonTime['endTime']),
					'create_time' => $time
			);
			$result = self::add($sendData);
		}
		return $result;
	}
	
	/**
	 * 赠送A券生日礼物
	 * @param string $uuid
	 * @param array $config
	 */
	private static function sendAcouponGiftProcess($uuid, $config){
		$time = Common::getTime();
		$result = false;
		$ticketData = array(
				'uuid' => $uuid,
				'type' => self::USER_SEND,
				'task_id' => self::BIRTHDAY_GIFT,
				'section_start' => 1,
				'section_end'=> $config['acoupon']['day'],
				'denomination'=> $config['acoupon']['cost'],
				'desc'=>"生日礼物",
		);
		$result = self::sendTicket($ticketData);
		if($result){
			$sendData = array(
					'uuid'=>$uuid,
					'type'=>self::USER_GIFT_ACOUPON,
					'day' => $config['acoupon']['day'],
					'cost' => $config['acoupon']['cost'],
					'effect_start_time' => $time,
					'effect_end_time' => $time + ($config['acoupon']['day'] * self::ONE_DAY_UNIX_SECOND),
					'create_time'=>$time
			);
			$result = self::add($sendData);
		}
		return $result;
	}
	
	/**
	 * 封装赠送A券接口
	 * @param array $data
	 */
	private static function sendTicket($data){
		$activity = new Util_Activity_Context(new Util_Activity_TicketSend($data));
		$result = $activity->sendTictket();
		return $result;
	}
	
	/**
	 * 封装赠送用户积分
	 * @param array $data
	 */
	private static function sendPoint($data){
		$sendResult = Point_Service_User::gainPoint($data);
		if(!$sendResult) return false;
		return true;
	}
	
	/**
	 * 生日礼物增加积分消息
	 * @param string $uuid
	 */
	private  static function saveMessageToCache($uuid, $point){
		$time = Common::getTime();
		$message = array(
				'type' =>  self::POINT_GAIN_MESSAGE,
				'top_type' => self::POINT_SYSTEM_MESSAGE,
				'totype' => self::POINT_GAIN_TO_USER,
				'title' =>  '祝你永远18岁！~',
				'msg' =>  "生日快乐，游戏大厅送你{$point}积分，玩的开心！",
				'status' =>  0,
				'start_time' =>  $time,
				'end_time' =>  strtotime('2050-01-01 23:59:59'),
				'create_time' =>  $time,
				'sendInput' =>  $uuid,
		);
		Common::getQueue()->push('game_client_msg',$message);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['type'])) $tmp['type'] = $data['type'];
		if(isset($data['day'])) $tmp['day'] = $data['day'];
		if(isset($data['cost'])) $tmp['cost'] = $data['cost'];
		if(isset($data['effect_start_time'])) $tmp['effect_start_time'] = $data['effect_start_time'];
		if(isset($data['effect_end_time'])) $tmp['effect_end_time'] = $data['effect_end_time'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Point_Dao_Prize
	 */
	private static function _getDao() {
		return Common::getDao("Account_Dao_UserGift");
	}
	
}