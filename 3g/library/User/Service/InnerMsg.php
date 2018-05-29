<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_InnerMsg {


	public static function add($params = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->insert($params);
	}

	public static function getList($page, $pageSize, $where, $orderBy) {
		if (!is_array($where)) return false;
		$total = self::_getDao()->count($where);
		$data  = self::_getDao()->getList($pageSize * ($page - 1), $pageSize, $where, $orderBy);
		return array($total, $data);
	}

	public static function unReadMsgNumber($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->count($params);
	}

	public static function mutilInsert($params) {
		return self::_getDao()->mutiInsert($params);
	}

	public static function updateBy($data, $params) {
		if (!is_array($params) || !is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}

	private static function _checkData($params) {
		$temp = array();
		if (isset($params['uid'])) $temp['uid'] = $params['uid'];
		if (isset($params['type'])) $temp['type'] = $params['type'];
		if (isset($params['content'])) $temp['content'] = $params['content'];
		if (isset($params['cat_id'])) $temp['cat_id'] = $params['cat_id'];
		if (isset($params['status'])) $temp['status'] = $params['status'];
		if (isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		if (isset($params['is_read'])) $temp['is_read'] = $params['is_read'];
		return $temp;
	}

	/**
	 * @return User_Dao_InnerMsg
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_InnerMsg");
	}

	/**
	 * @return User_Dao_InnerMsgTpl
	 */
	public static function getTplDao() {
		return Common::getDao("User_Dao_InnerMsgTpl");
	}

	public static function getTplData($code='',$sync = false) {
		static $data = array();
		if (empty($data)) {
			$rcKey = 'USER_INNERMSG_LIST';
			$ret   = Common::getCache()->get($rcKey);
			if (empty($ret) || $sync) {
				$list = User_Service_InnerMsg::getTplDao()->getsBy();
				foreach ($list as $val) {
					$ret[$val['code']] = $val['text'];
				}
				Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
			}
			$data = $ret;
		}

		if (!empty($code)) {
			$data = isset($data[$code])?$data[$code]:'';
		}

		return $data;

	}
}