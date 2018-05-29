<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//礼品卡类
class  User_Service_CardInfo {

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params = array(), $sort = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $sort);
	}

	
	public static function getList($page, $pageSize = 20, $where = array(), $order = array()) {
		if (!is_array($where)) return false;
		$page = max($page, 1);
		return array(self::count($where), self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $order));
	}

	public static function add($params = array()) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	public static function count($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function update($id, $params = array()) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data, $id);
	}

	public static function getAll() {
		return self::_getDao()->getAll();
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function getCetegory($where = array(), $group = array()) {
		return self::_getDao()->getCategory($where, $group);
	}

	public static function getFlowCardIds(){
		$key = "USER:CARD:FLOW:IDLIST";
		$data = Common::getCache()->get($key);
		if(empty($data)){
			$data = array();
			$ret = self::getsBy(array('type_id'=>array('IN',array('10','11','12'))));
			foreach($ret as $k=>$v){
				$data[] = $v['id'];
			}
			Common::getCache()->set($key,$data,24*3600);
		}
		return $data;
	}
	
	public static function getCardInfo($id){
		$key = "USER:CARD:INFO:{$id}";
		$info = Common::getCache()->get($key);
		if(empty($info)){
			$info = self::get($id);
			Common::getCache()->set($key,$info,24*3600);
		}
		return $info;
	}
	
	private static function _checkData($params = array()) {

		$temp = array();
		if (isset($params['id'])) $temp['id'] = $params['id'];
		if (isset($params['group_type'])) $temp['group_type'] = $params['group_type'];
		if (isset($params['type_id'])) $temp['type_id'] = $params['type_id'];
		if (isset($params['type_name'])) $temp['type_name'] = $params['type_name'];
		if (isset($params['card_id'])) $temp['card_id'] = $params['card_id'];
		if (isset($params['card_name'])) $temp['card_name'] = $params['card_name'];
		if (isset($params['card_value'])) $temp['card_value'] = $params['card_value'];
		if (isset($params['ext'])) $temp['ext'] = $params['ext'];
		return $temp;
	}


	/**
	 * @return User_Dao_CardInfo
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_CardInfo");
	}
}