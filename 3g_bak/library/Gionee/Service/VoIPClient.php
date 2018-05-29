<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_VoIPClient {

	public static function insert($params) {
		if (!is_array($params)) return false;
		$ret = self::_getDao()->insert($params);
		if (!$ret) {
			return $ret;
		}
		return self::_getDao()->getLastInsertId();
	}

	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, array('id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	//获取数据
	public static function _getClientNumber($mobile) {
		if (empty($mobile)) {
			return 0;
		}
		$info = self::_getDao()->getBy(array('mobile_number' => $mobile));
		return $info;
	}

	public static function _getMobileNumber($client) {
		if (empty($client)) {
			return 0;
		}
		$info = self::_getDao()->getBy(array('client_number' => $client));
		return isset($info['mobile_number']) ? $info['mobile_number'] : 0;
	}

	public static function getClientNumber($mobile, $isCall = false) {
		if (empty($mobile)) {
			return 0;
		}

		$key  = 'VOIP_MOBILE_NUMBER:' . $mobile;
		$info = Common::getCache()->get($key);
		if (empty($info['client_number'])) {
			$info = self::_getClientNumber($mobile);
			if ($isCall && empty($info['client_number'])) {
				$telObj     = new Vendor_Tel();
				$clientInfo = $telObj->getClientNumber($mobile);
				$info       = array(
					'mobile_number' => $mobile,
					'client_number' => $clientInfo['clientNumber'],
					'client_pwd'    => $clientInfo['clientPwd'],
					'created_at'    => time(),
				);
				$id         = self::insert($info);
			}

			if (!empty($info['client_number'])) {
				Common::getCache()->set($key, $info, 86400);
			}
		}
		return $info;
	}

	public static function getMobileNumber($client) {
		if (empty($client)) {
			return 0;
		}
		$key    = 'VOIP_CLIENT_NUMBER:' . $client;
		$number = Common::getCache()->get($key);
		if (empty($number)) {
			$number = self::_getMobileNumber($client);
			Common::getCache()->set($key, $number, 86400);
		}
		return !empty($number) ? $number : 0;
	}


	public static function update($params, $id) {
		return self::_getDao()->update($params, $id);
	}

	public static function updateBy($params, $where) {
		return self::_getDao()->updateBy($params, $where);
	}

	//
	public static function delete($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->delete($id);
	}

	private static function  _getDao() {
		return Common::getDao('Gionee_Dao_VoIPClient');
	}
}