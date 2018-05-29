<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信用户类
 */
class Gionee_Service_WxUser {

	public static function getList($page = 1, $limit = 10, $params = array(), $orderby = array()) {
		$start = (max($page, 1) - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderby);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * @param int $id
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	public static function getsBy($where = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($where, $orderBy);
	}

	public static function getBy($where, $orderBy = array()) {
		return self::_getDao()->getBy($where, $orderBy);
	}

	public static function getByOpenId($openid,$sync=false) {
		$rcKey = 'WX_USER_OPENID:' . $openid;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret == false || $sync) {
			$ret = self::getBy(array('openid' => $openid));
			Common::getCache()->set($rcKey, $ret, 600);
		}
		return $ret;
	}

	/**
	 * @param array $data
	 * @param int   $id
	 */
	public static function set($data, $id) {
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * @param array $ids
	 * @param array $data
	 */
	public static function sets($ids, $data) {
		if (!is_array($data) || !is_array($ids)) {
			return false;
		}
		return self::_getDao()->updates('id', $ids, $data);
	}

	public static function setBy($upData, $where) {
		if (!is_array($upData) || !is_array($where)) {
			return false;
		}
		return self::_getDao()->updateBy($upData, $where);
	}

	/**
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		$ret = self::_getDao()->insert($data);
		if ($ret) {
			return self::_getDao()->getLastInsertId();
		}
		return false;
	}

	public static function getUserInfo($inData) {
		$userInfo = array();
		if (!empty($inData['FromUserName'])) {
			$userInfo = Gionee_Service_WxUser::getBy(array('openid' => $inData['FromUserName']));
			if (empty($userInfo['id'])) {
				$now  = time();
				$info = array(
					'openid'     => $inData['FromUserName'],
					'created_at' => $now,
					'updated_at' => $now,
					'scene_str'  => $inData['EventKey'],
				);
				$id   = Gionee_Service_WxUser::add($info);

				if ($id) {
					$info['id'] = $id;
					$userInfo   = $info;
				}
			}
		}
		return $userInfo;
	}

	/**
	 * @return Gionee_Dao_WxUser
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_WxUser");
	}
}