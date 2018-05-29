<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信活动类
 */
class Gionee_Service_WxHelp {
	const K = '041701';

	public static function authorize($appid, $callbackUrl, $scope = 'snsapi_userinfo', $state = 'STATE') {
		$url = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect", $appid, $callbackUrl, $scope, $state);
		Common::redirect($url);
	}

	public static function  getAccessToken($appid, $appkey, $code) {
		$url  = sprintf("https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code", $appid, $appkey, $code);
		$resp = Common::getUrlContent($url);
		$ret  = json_decode($resp, true);
		return $ret;
	}

	public static function getOpenidInfo($accessToken, $openid) {
		$url  = sprintf('https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN', $accessToken, $openid);
		$resp = Common::getUrlContent($url);
		$ret  = json_decode($resp, true);
		return $ret;
	}

	/**
	 * @param $appkey
	 * @param $code
	 *
	 * @return string {"access_token":"ACCESS_TOKEN","expires_in":7200}
	 */
	public static function  getJSAccessToken($appid, $appkey) {
		$url  = sprintf("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s", $appid, $appkey);
		$resp = Common::getUrlContent($url);
		$ret  = json_decode($resp, true);
		return $ret;
	}

	/**
	 * @param $accessToken
	 *
	 * @return string {"errcode":0,"errmsg":"ok","ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA","expires_in":7200}
	 *
	 */
	public static function  getJSTicket($accessToken) {
		$url  = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi", $accessToken);
		$resp = Common::getUrlContent($url);
		$ret  = json_decode($resp, true);
		return $ret;
	}


	public static function getJSApiTicket($appid, $appkey) {
		$key = 'WX_EVENT1_JSAPITICKET:' . $appid;
		$ret = Common::getCache()->get($key);
		if (empty($ret)) {
			$tmp = self::getJSAccessToken($appid, $appkey);
			if (!empty($tmp['access_token'])) {
				$row = self::getJSTicket($tmp['access_token']);
				if (!empty($row['ticket'])) {
					$ret = $row['ticket'];
					Common::getCache()->set($key, $ret, Common::T_ONE_HOUR);
				}
			}
		}
		return $ret;
	}

	public static function getJSCurUrl() {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url      = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		return $url;
	}


	public static function getUserInfo($id, $sync = false) {
		$key = self::K . 'WX_EVENT1_USERINFO:' . $id;
		$row = Common::getCache()->get($key);
		if ($row === false || $sync) {
			$row = Gionee_Service_WxHelp::getDaoUser()->get($id);
			if (!empty($row['id'])) {
				Common::getCache()->set($key, $row, Common::T_ONE_DAY);
			}
		}
		return $row;
	}

	public static function getInfo($id, $sync = false) {
		$key = self::K . 'WX_EVENT1_INFO:' . $id;
		$row = Common::getCache()->get($key);
		if ($row === false || $sync) {
			$row = Gionee_Service_WxHelp::getDaoList()->get($id);
			if (!empty($row['id'])) {
				Common::getCache()->set($key, $row, Common::T_ONE_DAY);
			}
		}
		return $row;
	}

	public static function getRelInfo($id, $uid, $fuid, $sync = false) {
		$key = self::K . 'WX_HELP_REL_INFO:' . $id . '_' . $uid . '_' . $fuid;
		$row = Common::getCache()->get($key);
		if (empty($row['id']) || $sync) {
			$where = array('event_id' => $id, 'uid' => $uid, 'fuid' => $fuid);
			$row   = Gionee_Service_WxHelp::getDaoRel()->getBy($where);
			if (!empty($row['id'])) {
				Common::getCache()->set($key, $row, Common::T_ONE_DAY);
			} else {
				Common::getCache()->set($key, array(), Common::T_ONE_DAY);
			}
		}
		return $row;

	}

	public static function getRelList($id, $uid, $sync = false) {
		$key = self::K . 'WX_HELP_REL_LIST:' . $id . '_' . $uid;
		$ret = Common::getCache()->get($key);
		if ($ret === false || $sync) {
			$where = array('event_id' => $id, 'uid' => $uid);
			$list  = Gionee_Service_WxHelp::getDaoRel()->getsBy($where, array('id' => 'desc'));
			if (!empty($list)) {
				$amount = array();
				foreach ($list as $val) {
					$amount[] = $val['amount'];
				}
				$ret = array(
					'list'         => $list,
					'total_amount' => array_sum($amount),
					'total_num'    => count($list),
				);
				Common::getCache()->set($key, $ret, Common::T_ONE_DAY);
			} else {
				$ret = array(
					'list'         => array(),
					'total_amount' => 0,
					'total_num'    => 0,
				);
				Common::getCache()->set($key, $ret, Common::T_ONE_DAY);
			}
		}
		return $ret;

	}

	public static function getRelFNum($id, $uid, $sync = false) {
		$key = self::K . 'WX_HELP_REL_F_NUM:' . $id . '_' . $uid;
		$ret = Common::getCache()->get($key);
		if ($ret === false || $sync) {
			$where = array('event_id' => $id, 'fuid' => $uid);
			$ret   = Gionee_Service_WxHelp::getDaoRel()->count($where);
			Common::getCache()->set($key, $ret, Common::T_ONE_DAY);
		}
		return $ret;

	}


	public static function getResultTop($id, $sync = false) {

		$key      = self::K . 'WX_HELP_RESULT_TOP:' . $id;
		$userList = Common::getCache()->get($key);
		if ($userList === false || $sync) {
			$orderBy  = array('total_amount' => 'desc', 'updated_at' => 'asc');
			$where    = array('event_id' => $id, 'total_times_f'=>array('>=',5));
			$info     = Gionee_Service_WxHelp::getInfo($id);
			$list     = Gionee_Service_WxHelp::getDaoUser()->getList(0, $info['result_num'], $where, $orderBy);
			$userList = array();
			foreach ($list as $val) {
				$userList[] = array(
					'face'     => $val['headimgurl'],
					'nickname' => $val['nickname'],
					'num'      => $val['total_amount'],
					'id'       => $val['id'],
				);
			}
			Common::getCache()->set($key, $userList, 600);
		}
		return $userList;


	}


	public static function getResultList($id, $sync = false) {
		$key      = self::K . 'WX_HELP_RESULT_LIST:' . $id;
		$userList = Common::getCache()->get($key);
		if ($userList === false || $sync) {

			$orderBy  = array('done_time' => 'asc');
			$where    = array('event_id' => $id, 'done_time' => array('>', 0), 'award_code' => array('!=', ''));
			$info     = Gionee_Service_WxHelp::getInfo($id);
			$list     = Gionee_Service_WxHelp::getDaoUser()->getList(0, $info['result_num'], $where, $orderBy);
			$userList = array();
			foreach ($list as $val) {
				$userList[] = array(
					'face'     => $val['headimgurl'],
					'nickname' => $val['nickname'],
				);
			}
			Common::getCache()->set($key, $userList, Common::T_ONE_DAY);
		}
		return $userList;
	}

	static public function ckVisit($uid, $fuid) {
		$rcKey = self::K . 'WXHELP_VISIT:' . $uid;
		$rc    = Common::getCache();
		$ret   = $rc->sismember($rcKey, $fuid);
		if ($ret === false) {
			$rc->sadd($rcKey, $fuid);
		}
		return $ret;
	}

	static public function getVisit($uid) {
		$rcKey = self::K . 'WXHELP_VISIT:' . $uid;
		$rc    = Common::getCache();
		return $rc->smembers($rcKey);

	}


	/**
	 * @return Gionee_Dao_WxHelpUser
	 */
	public static function getDaoUser() {
		return Common::getDao("Gionee_Dao_WxHelpUser");
	}

	/**
	 * @return Gionee_Dao_WxHelpList
	 */
	public static function getDaoList() {
		return Common::getDao("Gionee_Dao_WxHelpList");
	}

	/**
	 * @return Gionee_Dao_WxHelpRel
	 */
	public static function getDaoRel() {
		return Common::getDao("Gionee_Dao_WxHelpRel");
	}

	/**
	 * @return Gionee_Dao_WxHelpAddress
	 */
	public static function getDaoAddress() {
		return Common::getDao("Gionee_Dao_WxHelpAddress");
	}


}
