<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class W3_Service_User {

	static public function initLog($params) {
		if (empty($params['imei']) || strlen($params['imei']) < 10) {
			return false;
		}

		$imei = substr($params['imei'], 0, 15);
		$uKey = 'W3_USER:' . $imei;
		$info = Common::getCache()->get($uKey);
		if (empty($info['id'])) {
			$info = W3_Service_User::getBy(array('imei' => $imei));
		}

		$now = time();
		if (empty($info['id'])) {
			$info       = array(
				'imei'          => $imei,
				'net'           => $params['net'],
				'model'         => $params['model'],
				'app_ver'       => $params['app_ver'],
				'gps'           => $params['gps'],
				'created_at'    => $now,
				'last_visit_at' => $now,
				'ip'            => Util_Http::getClientIp(),
			);
			$id         = W3_Service_User::add($info);
			$info['id'] = $id;
			Common::getCache()->set($uKey, $info, Common::KT_USER);
		} else {
			$change = 0;
			if ($info['app_ver'] != $params['app_ver']) {
				$info['app_ver'] = $params['app_ver'];
				$change++;
			}

			if ($info['net'] != $params['net']) {
				$info['net'] = $params['net'];
				$change++;
			}

			if ($info['gps'] != $params['gps']) {
				//$info['gps'] = $params['gps'];
				//$change++;
			}

			if ($change > 0) {
				$info['last_visit_at'] = $now;
				W3_Service_User::set($info, $info['id']);
			}
			Common::getCache()->set($uKey, $info, Common::KT_USER);

			$hKey = 'SYNC_VISIT_W3';
			Common::getCache()->hSet($hKey, $info['id'], $now);
		}

		return true;
	}


	/**
	 *
	 * 列表
	 * @param int $page
	 * @param int $limit
	 * @param int $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('created_at' => 'DESC', 'id' => 'DESC'));
		return $ret;
	}

	/**
	 * 数量
	 * @param array $params
	 * @return string
	 */
	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		self::_getDao()->insert($data);
		return self::_getDao()->getLastInsertId();

	}

	/**
	 * 字段过滤
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array(
			'id', 'imei', 'model', 'net', 'gps', 'app_ver',
			'created_at', 'last_visit_at', 'ip',
			'column_ids', 'url_ids', 'fav_ids');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	static public function getGroupByField($val) {
		return self::_getDao()->getGroupByField($val);
	}

	/**
	 *
	 * SELECT COUNT(net) AS num, net FROM w3_user GROUP BY net;
	 *
	 * SELECT COUNT(model) AS  num, model FROM w3_user GROUP BY model;
	 *
	 * SELECT COUNT(app_ver) AS  num, app_ver FROM w3_user GROUP BY app_ver;
	 */

	/**
	 *
	 * @return W3_Dao_User
	 */
	private static function _getDao() {
		return Common::getDao("W3_Dao_User");
	}
}