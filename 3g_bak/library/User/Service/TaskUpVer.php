<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_TaskUpVer {
	/**
	 *
	 * @return User_Dao_TaskUpVer
	 */
	public static function getDao() {
		return Common::getDao("User_Dao_TaskUpVer");
	}

	public static function checkPass($uid) {
		$ret = false;
		if (empty($uid)) {
			return $ret;
		}
		$uaArr = Util_Http::ua();

		if (empty($uaArr['app_ver'])) {
			return $ret;
		}
		$tmpRow = User_Service_TaskUpVer::getDao()->getBy(array('uid' => $uid), array('created_at' => 'desc'));
		if (!empty($tmpRow['ver'])) {
			if (version_compare($uaArr['app_ver'], $tmpRow['ver'], '>')) {
				$ret = $uaArr;
			}
		} else {
			$ret = $uaArr;
		}

		return $ret;
	}

	static public function upVerData($uid) {
		$uaArr = User_Service_TaskUpVer::checkPass($uid);
		$coin  = 0;
		if (!empty($uaArr)) {
			$addData = array(
				'uid'        => $uid,
				'imei_id'    => $uaArr['uuid'],
				'ver'        => $uaArr['app_ver'],
				'ip'         => $uaArr['ip'],
				'created_at' => time(),
			);
			$ret     = User_Service_TaskUpVer::getDao()->insert($addData);
			if ($ret) {
				$id = User_Service_TaskUpVer::getDao()->getLastInsertId();
				$coin = Gionee_Service_Config::getValue('browser_up_ver_coin');
				User_Service_Gather::changeScoresAndWriteLog($uid, $coin, 209, 2, $id);
			}
		}

		return $coin;
	}
}

