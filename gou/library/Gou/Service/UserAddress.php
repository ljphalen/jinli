<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 收货地址
 * @author tiansh
 *
 */
class Gou_Service_UserAddress{
	

	/**
	 *
	 * Enter desUserAddressiption here ...
	 */
	public static function getAllUserAddress() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getUserAddress($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *根据用户id查询收货地址
	 * @param int $user_id
	 * @return array
	 */
	public function getListByUserId($user_id) {
		if (!$user_id) return false;
		return self::_getDao()->getListByUserId($user_id);
	}
	
	/**
	 * 获取用户默认收货地址
	 * @param int $uid
	 * @return boolean|mixed
	 */
	public function getDefaultAddress($uid) {
		if (!$uid) return false;
		$address = self::_getDao()->getBy(array('user_id'=>$uid, 'isdefault'=>1));
		return Gou_Service_UserAddress::cookUserAddress($address);
	}
	
	
	/**
	 *
	 * Enter desUserAddressiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addUserAddress($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateUserAddress($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteUserAddress($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	static public function cookUserAddress($address) {
		if ($address) {
			$province = explode('|', $address['province']);
			$city = explode('|', $address['city']);
			$country = explode('|', $address['country']);
			if($province[0] == '省份'){
				$_province = '';
			} else {
				$_province = $province[0];
			}
			if($city[0] == '城市'){
				$_city = '';
			} else {
				$_city = $city[0];
			}
			if($country[0] == '区县'){
				$_country = '';
			} else {
				$_country = $country[0];
			}
			$adds = $_province." ".$_city." ".$_country;
			$address['adds'] = $adds;
		}
		return $address;
	} 

	/**
	 *
	 * Enter desUserAddressiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['province'])) $tmp['province'] = $data['province'];
		if(isset($data['city'])) $tmp['city'] = $data['city'];
		if(isset($data['country'])) $tmp['country'] = $data['country'];
		if(isset($data['isdefault'])) $tmp['isdefault'] = intval($data['isdefault']);
		if(isset($data['detail_address'])) $tmp['detail_address'] = $data['detail_address'];
		if(isset($data['postcode'])) $tmp['postcode'] = $data['postcode'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Gou_Dao_UserAddress
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_UserAddress");
	}
}
