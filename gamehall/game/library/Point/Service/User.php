<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class Point_Service_User extends Common_Service_Base{
	
	/**
	 * 事务获取用户积分
	 * @param array $data
	 * @return boolean
	 */
	public static function gainPoint($data){
		if (!is_array($data)) return false;
		//开始事务
		parent::beginTransaction();
		try {
			//事务提交
			$userPoint = Account_Service_User::getUserInfo(array('uuid'=>$data['uuid']));
			if(!$userPoint) return false;
			$logId   = Point_Service_Gain::add($data);
			if(!$logId)  throw new Exception("add gainData fail.", -202);
			$result  = Account_Service_User::addUserPoint($data['points'], array('uuid'=>$data['uuid']));
			if(!$result) throw new Exception("add user points fail.", -202);
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 事务消费用户积分
	 * @param array $data
	 * @return boolean
	 */
	public static function consumePoint($data){
		if (!is_array($data)) return false;
		//开始事务
		parent::beginTransaction();
		try {
			//事务提交
			$userPoint = Account_Service_User::getUserInfo(array('uuid'=>$data['uuid']));
			if(!$userPoint) return false;
			$logId   = Point_Service_Consume::add($data);
			if(!$logId)  throw new Exception("add consumeData fail.", -202);
			$result  = Account_Service_User::subtractUserPoint($data['points'], array('uuid'=>$data['uuid']));
			if(!$result) throw new Exception("subtract user points fail.", -202);
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $uuid
	 * @param unknown_type $points
	 * @return boolean
	 */
	public static function gainPresentUserPoints($data, $uuid, $points){
		if(!is_array($data) || !$uuid || !$points){
			return false;
		}
		$userInfo = self::getUserInfoDao()->getBy(array('uuid'=>$uuid));
		$pointsData = self::cookPointsGainData($data);
		$logRet   = self::getPointGainDao()->insert($pointsData);
		$userData = array();
		$userData['points'] = $userInfo['points'] + $points;
		$params['uuid'] = $uuid;
		$userRet  = self::getUserInfoDao()->updateBy($userData, $params);
		if(!$logRet){
			return false;
		}
		return true;
	}
	
	/**
	 *
	 * @param array $data
	 * @param string $uuid
	 * @param demecil $points
	 * @return boolean
	 */
	public static function consumePresentUserPoints($usrGainPoint, $data){
		if(!is_array($usrGainPoint) || !$data['uuid'] || !$data['consume_point']){
			return false;
		}
		$userInfo = self::getUserInfoDao()->getBy(array('uuid'=>$data['uuid']));
		$pointsData = self::cookPointsConsumeData($usrGainPoint);
		$consumeRet   = self::getPointConsumeDao()->insert($pointsData);
		$userData = array();
		$userData['points'] = $userInfo['points'] - $data['consume_point'];
		if($data['address'] && $data['receiver'] && $data['receiverphone']){
			$userData['address'] = $data['address'];
			$userData['receiver'] = $data['receiver'];
			$userData['receiverphone'] = $data['receiverphone'];
		}
		if($userData['points'] < 0) return false;
		$params['uuid'] = $data['uuid'];
		$userRet  = self::getUserInfoDao()->updateBy($userData, $params);
		if(!$userRet){
			return false;
		}
		return true;
	}
	

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookPointsConsumeData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['consume_type'])) $tmp['consume_type'] = $data['consume_type'];
		if(isset($data['consume_sub_type'])) $tmp['consume_sub_type'] = $data['consume_sub_type'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function cookPointsGainData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['gain_type'])) $tmp['gain_type'] = $data['gain_type'];
		if(isset($data['gain_sub_type'])) $tmp['gain_sub_type'] = $data['gain_sub_type'];
		if(isset($data['days'])) $tmp['days'] = $data['days'];
		if(isset($data['points'])) $tmp['points'] = $data['points'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @return Client_Dao_PointsConsume
	 */
	private static function getPointConsumeDao() {
		return Common::getDao("Client_Dao_PointsConsume");
	}
	
	/**
	 *
	 * @return Client_Dao_PointsGain
	 */
	private static function getPointGainDao() {
		return Common::getDao("Client_Dao_PointsGain");
	}
	
	/**
	 * @return Account_Dao_UserInfo
	 */
	private static function getUserInfoDao() {
		return Common::getDao("Account_Dao_UserInfo");
	}
}
