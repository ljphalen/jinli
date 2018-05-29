<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 收货地址
 * @author tiansh
 *
 */
class Gc_Service_UserConsignee{
	

	/**
	 *
	 * Enter desUserConsigneeiption here ...
	 */
	public static function getAllUserConsignee() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getUserConsignee($id) {
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
	 *
	 * Enter desUserConsigneeiption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($ret);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addUserConsignee($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateUserConsignee($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteUserConsignee($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desUserConsigneeiption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
		if(isset($data['realname'])) $tmp['realname'] = $data['realname'];
		if(isset($data['province'])) $tmp['province'] = $data['province'];
		if(isset($data['city'])) $tmp['city'] = $data['city'];
		if(isset($data['country'])) $tmp['country'] = $data['country'];
		if(isset($data['address'])) $tmp['address'] = $data['address'];
		if(isset($data['postcode'])) $tmp['postcode'] = $data['postcode'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['mobile'])) $tmp['mobile'] = $data['mobile'];
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_UserConsignee
	 */
	private static function _getDao() {
		return Common::getDao("Gc_Dao_UserConsignee");
	}
}
