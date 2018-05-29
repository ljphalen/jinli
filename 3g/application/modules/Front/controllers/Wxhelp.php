<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * weixin活动类1
 */
class WxhelpController extends Front_BaseController {
	private $_ckName    = 'wxhelpuid2';
	private $_shareUrl  = '%s/wxhelp/wyindex?%s';
	private $_applyUrl  = '%s/wxhelp/apply?%s';
	private $_resultUrl = '%s/wxhelp/wyresult?%s';

	public function indexAction() {
		$id = 2;
		if (!empty($_GET['token'])) {
			$this->forward('front', 'Wxhelp', 's7index');
			return false;
		} else if ($id == 2) {
			$this->forward('front', 'Wxhelp', 'wyindex');
			return false;
		}
	}

	public function s7indexAction() {

		$token  = $this->getInput('token');
		$params = json_decode(Common::encrypt($token, 'DECODE'), true);
		$id     = intval($params['id']);
		$fuid   = intval($params['fuid']);
		$home   = $params['home'];
		if (empty($id)) {
			exit;
		}

		$info = Gionee_Service_WxHelp::getInfo($id);
		if (empty($info['id'])) {
			exit('非法进入');
		}

		$pageData                = $info;
		$pageData['allAmount']   = $info['total_num'];
		$pageData['totalAmount'] = 0;
		$pageData['curAmount']   = 0;
		$pageData['leftAmount']  = $info['total_num'];
		$pageData['recordList']  = array();

		$pageData['page'] = 'index';
		if ($home != 1) {
			$uid = Util_Cookie::get($this->_ckName . '_' . $id);
			//$uid = 54;
			if (empty($uid)) {//当前用户没登录
				$this->_auth($info, $fuid);
			}

			$fuid     = !empty($fuid) ? $fuid : $uid;
			$pageData = $this->_share($info, $uid, $fuid);

			$pageData['uid']  = intval($uid);
			$pageData['fuid'] = intval($fuid);
		} else {
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':index');
		}

		$urls = array(
			'home_url'         => array($this->_shareUrl, array('id' => $id, 'home' => 1)),
			'self_url'         => array($this->_shareUrl, array('id' => $id, 'fuid' => $uid)),
			'friend_url'       => array($this->_shareUrl, array('id' => $id, 'fuid' => $fuid)),
			'apply_self_url'   => array($this->_applyUrl, array('id' => $id, 'fuid' => $uid)),
			'apply_friend_url' => array($this->_applyUrl, array('id' => $id, 'fuid' => $fuid)),
			'result_url'       => array($this->_resultUrl, array('id' => $id)),
		);

		foreach ($urls as $key => $val) {
			$pageData[$key] = $this->_makeUrl($val[0], $val[1]);
		}

		$this->assign('pageData', $pageData);
	}


	public function wyindexAction() {
		$args     = array(
			'vid'  => FILTER_VALIDATE_INT,
			'uid'  => FILTER_VALIDATE_INT,
			'home' => FILTER_VALIDATE_INT,
		);
		$myinputs = filter_input_array(INPUT_GET, $args);
		$fuid     = $myinputs['vid'];
		$id       = 2;
		$home     = $myinputs['home'];
		$info     = Gionee_Service_WxHelp::getInfo($id);
		if (empty($info['id'])) {
			exit('非法进入');
		}

		$pageData                = $info;
		$pageData['allAmount']   = $info['total_num'];
		$pageData['totalAmount'] = 0;
		$pageData['curAmount']   = 0;
		$pageData['leftAmount']  = $info['total_num'];
		$pageData['recordList']  = array();

		$pageData['page'] = 'index';

		if (empty($home)) {

			$uid = Util_Cookie::get($this->_ckName . '_' . $id);
			//$uid = 54;
			if (empty($uid)) {//当前用户没登录
				$this->_auth($info, $fuid);
			}

			$fuid = !empty($fuid) ? $fuid : $uid;
			$now  = Common::getTime();
			if ($info['start_time'] >= $now) {
				$urlParams = array('home' => 1);
				$url       = $this->_makeUrl($this->_shareUrl, $urlParams);
				Common::redirect($url);
			}

			if ($now >= $info['end_time']) {
				$url = $this->_makeUrl($this->_resultUrl);
				Common::redirect($url);
			}

			$pageData = $this->_share($info, $uid, $fuid);

			$pageData['uid']    = intval($uid);
			$pageData['fuid']   = intval($fuid);
			$follow             = 0;
			$pageData['follow'] = $follow;

		} else {
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':index');
		}


		$this->assign('pageData', $pageData);
	}

	private function _auth($info, $fuid = '', $flag = 0) {
		$code        = $this->getInput('code');
		$id          = $info['id'];
		$sysWXConf   = Common::getConfig('weixinConfig', 'conf');
		$appid       = !empty($info['wx_appid']) ? $info['wx_appid'] : $sysWXConf['appid'];
		$appkey      = !empty($info['wx_appkey']) ? $info['wx_appkey'] : $sysWXConf['secret'];
		$urlParams   = array('vid' => $fuid);
		$callbackUrl = $this->_makeUrl($this->_shareUrl, $urlParams);

		if ($flag == 1) {
			$callbackUrl = $this->_makeUrl($this->_resultUrl);
		}

		if (empty($code)) {
			Gionee_Service_WxHelp::authorize($appid, urlencode($callbackUrl), 'snsapi_userinfo');
		}

		$tokenArr = Gionee_Service_WxHelp::getAccessToken($appid, $appkey, $code);
		if (!empty($tokenArr['openid'])) {
			$where    = array('openid' => $tokenArr['openid'], 'event_id' => $id);
			$userInfo = Gionee_Service_WxHelp::getDaoUser()->getBy($where);
			if (empty($userInfo['id'])) {
				$userRespArr = Gionee_Service_WxHelp::getOpenidInfo($tokenArr['access_token'], $tokenArr['openid']);
				$userInfo    = array(
					'openid'     => $userRespArr['openid'],
					'nickname'   => $userRespArr['nickname'],
					'sex'        => $userRespArr['sex'],
					'city'       => $userRespArr['city'],
					'province'   => $userRespArr['province'],
					'headimgurl' => $userRespArr['headimgurl'],
					'event_id'   => $id,
					'created_at' => Common::getTime(),
				);
				Gionee_Service_WxHelp::getDaoUser()->insert($userInfo);
				$uid = Gionee_Service_WxHelp::getDaoUser()->getLastInsertId();
				if ($uid) {
					$userInfo['id'] = $uid;
				}
			}
			$uid = $userInfo['id'];
			Util_Cookie::set($this->_ckName . '_' . $id, $uid, false, strtotime("+30 day"), '/');

			$urlParams = array('vid' => $fuid);
			$url       = $this->_makeUrl($this->_shareUrl, $urlParams);
			Common::redirect($url);
		}
	}

	public function applyAction() {
		$args     = array(
			'vid' => FILTER_VALIDATE_INT,
		);
		$myinputs = filter_input_array(INPUT_GET, $args);
		$id       = 2;
		$fuid     = $myinputs['vid'];

		$info = Gionee_Service_WxHelp::getInfo($id);
		if (empty($info['id'])) {
			exit('非法进入');
		}
		$uid = Util_Cookie::get($this->_ckName . '_' . $id);
		//$uid = 54;
		if (empty($uid)) {//当前用户没登录
			$this->_auth($info, $fuid);
		}

		$fuid = !empty($fuid) ? $fuid : $uid;
		$now  = Common::getTime();
		if ($info['start_time'] >= $now) {
			$urlParams = array('home' => 1);
			$url       = $this->_makeUrl($this->_shareUrl, $urlParams);
			Common::redirect($url);
		}

		if ($now >= $info['end_time']) {
			$url = $this->_makeUrl($this->_shareUrl);
			Common::redirect($url);
		}

		if ($uid == $fuid) {
			exit('不能给自己操作');
		} else if ($uid != $fuid) {//帮朋友减钱
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':apply_friend');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_WXHELP_UV, $id . ':apply_friend', $uid);

			$fUserInfo = Gionee_Service_WxHelp::getUserInfo($fuid);

			if (!empty($fUserInfo['id'])) {
				$tmpTotal  = $fUserInfo['total_times'];//朋友被操作的人数
				$tmpAmount = $fUserInfo['total_amount'];//计算额定数量
				$tmp       = Gionee_Service_WxHelp::getRelInfo($id, $fuid, $uid);
				$curAmount = $this->_ruleVal($tmpTotal);
				$curAmount = !empty($curAmount) ? $curAmount : 50;
				if ($tmpAmount < 200 && $curAmount < 0) {
					$curAmount = abs($curAmount);
				}

				if (empty($tmp['id']) && !empty($fuid) && !empty($uid)) {//是否已参加
					$data = array(
						'event_id'   => $id,
						'fuid'       => $uid,
						'uid'        => $fuid,
						'amount'     => $curAmount,
						'created_at' => Common::getTime(),
					);
					Gionee_Service_WxHelp::getDaoRel()->insert($data);
					Gionee_Service_WxHelp::getRelInfo($id, $fuid, $uid, true);
					$relList = Gionee_Service_WxHelp::getRelList($id, $fuid, true);
					$upData  = array(
						'total_amount' => $relList['total_amount'] + $fUserInfo['visit_num'] * 5,
						'total_times'  => $relList['total_num'],
						'updated_at'   => time(),
					);
					Gionee_Service_WxHelp::getDaoUser()->update($upData, $fuid);

					$where       = array('event_id' => $id, 'fuid' => $uid);
					$totalTimesF = Gionee_Service_WxHelp::getDaoRel()->count($where);
					$upData      = array(
						'total_times_f' => $totalTimesF,
					);
					Gionee_Service_WxHelp::getDaoUser()->update($upData, $uid);

					Gionee_Service_WxHelp::getUserInfo($fuid, true);
					Gionee_Service_WxHelp::getUserInfo($uid, true);
				}
			}

			$urlParams = array('vid' => $fuid);
			$url       = $this->_makeUrl($this->_shareUrl, $urlParams);
			Common::redirect($url);
		}
		exit('异常访问');
	}

	public function _share($info, $uid, $fuid) {

		$id   = $info['id'];
		$list = array();
		$fuid = !empty($fuid) ? $fuid : $uid;

		if (!empty($uid) && !empty($fuid) && $uid == $fuid) {//帮自己减完界面
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':share_self');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_WXHELP_UV, $id . ':share_self', $uid);

			$userInfo    = Gionee_Service_WxHelp::getUserInfo($uid);
			$totalAmount = $userInfo['total_amount'];//计算额定数量
			$uhelpv      = $userInfo['total_times_f']; //当前用户帮别人次数
			$relList     = Gionee_Service_WxHelp::getRelList($id, $uid);
			$list        = $relList['list'];
			$vhelpu      = $userInfo['total_times'];//朋友被操作的人数
			$relInfo     = Gionee_Service_WxHelp::getRelInfo($id, $uid, $uid);
			$page        = 'share_self';

		} else if (!empty($uid) && !empty($fuid) && $uid != $fuid) {//帮朋友减完界面
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':share_friend');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_WXHELP_UV, $id . ':share_friend', $uid);
			$fuserInfo   = Gionee_Service_WxHelp::getUserInfo($fuid);
			$totalAmount = $fuserInfo['total_amount'];//计算额定数量
			$relList     = Gionee_Service_WxHelp::getRelList($id, $fuid);
			$list        = $relList['list'];
			$relInfo     = Gionee_Service_WxHelp::getRelInfo($id, $fuid, $uid);
			$page        = 'share_friend';
			$userInfo    = Gionee_Service_WxHelp::getUserInfo($uid);
			$uhelpv      = $userInfo['total_times_f']; //当前用户帮别人次数
			$isVisit     = Gionee_Service_WxHelp::ckVisit($fuid, $uid);
			if (!$isVisit) {
				$upData = array(
					'total_amount' => $fuserInfo['total_amount'] + 5,
					'visit_num'    => $fuserInfo['visit_num'] + 1,
				);
				Gionee_Service_WxHelp::getDaoUser()->update($upData, $fuid);
				Gionee_Service_WxHelp::getUserInfo($fuid, true);
			}

			$vhelpu = $userInfo['total_times'];//朋友被操作的人数

		}

		$pageData                = $info;
		$pageData['vhelpu']      = $vhelpu;
		$pageData['uhelpv']      = $uhelpv;
		$pageData['curAmount']   = empty($relInfo['amount']) ? 0 : $relInfo['amount'];
		$pageData['allAmount']   = $info['total_num'];
		$recordList              = array();
		$pageData['totalAmount'] = $totalAmount;
		$pageData['visit']       = Gionee_Service_WxHelp::getVisit($fuid);
		$i                       = 0;
		foreach ($list as $val) {
			//$user  = Gionee_Service_WxHelp::getUserInfo($val['uid']);
			$fuser = Gionee_Service_WxHelp::getUserInfo($val['fuid']);
			if ($val['fuid'] != $val['uid']) {
				$recordList[] = array(
					'vimg'     => $fuser['headimgurl'],
					'vname'    => $fuser['nickname'],
					'vmileage' => $val['amount'],
				);
				$i++;
			}
			if ($i == 10) {
				break;
			}
		}

		$pageData['page']       = $page;
		$pageData['leftAmount'] = max($pageData['allAmount'] - $pageData['totalAmount'], 0);
		$pageData['recordList'] = $recordList;

		return $pageData;
	}


	public function wyresultAction() {
		$id   = 2;
		$info = Gionee_Service_WxHelp::getInfo($id);
		if (empty($info['id'])) {
			exit('非法进入');
		}

		$uid = Util_Cookie::get($this->_ckName . '_' . $id);
		if (empty($uid)) {//当前用户没登录
			$this->_auth($info, '', 1);
		}

		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_WXHELP_PV, $id . ':result');
		$userInfo = array();
		if (!empty($uid)) {
			$userInfo = Gionee_Service_WxHelp::getUserInfo($uid);
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_WXHELP_UV, $id . ':result', $uid);
		}

		$userList = Gionee_Service_WxHelp::getResultTop($id);

		$this->assign('userList', $userList);
		$this->assign('info', $info);
		$this->assign('userInfo', $userInfo);
	}

	public function resultAction() {

	}

	public function addressAction() {
		$id  = 2;
		$uid = Util_Cookie::get($this->_ckName . '_' . $id);
		if (empty($uid)) {
			$this->output('-1', '用户信息错误!');
		}

		$params = $this->getPost(array('username', 'city_id', 'province_id', 'address', 'phone'));
		if (!intval($params['province_id']) || !intval($params['city_id'])) {
			$this->output('-1', '城市信息不完整，请重新填写!');
		}
		if (empty($params['address'])) {
			$this->output('-1', '请输入详细收货地址!');
		}
		if (mb_strlen($params['address']) > 100) {
			$this->output('-1', '收货地址不要超过100个文字');
		}

		if (empty($params['username'])) {
			$this->output('-1', '收货人姓名不能为空！');
		}
		if (mb_strlen($params['username']) > 20) {
			$this->output('-1', '收货人姓名最大长度不能超过20个文字');
		}

		if (!Common::checkIllPhone($params['phone'])) {
			$this->output('-1', '手机号码非法');
		}

		$info          = Gionee_Service_WxHelp::getDaoAddress()->getBy(array('uid' => $uid));
		$params['uid'] = $uid;
		if (empty($info['id'])) {
			$params['created_at'] = time();
			$ret                  = Gionee_Service_WxHelp::getDaoAddress()->insert($params);
		} else {
			$ret = Gionee_Service_WxHelp::getDaoAddress()->update($params, $info['id']);
		}

		if ($ret) {
			$this->output('0', '成功');
		}
		$this->output('-1', '失败');


	}

	private function _makeUrl($url, $urlParams = array()) {
		//$token = Common::encrypt(Common::jsonEncode($urlParams));
		$token = http_build_query($urlParams);
		$url   = sprintf($url, Common::getCurHost(), $token);
		return $url;
	}

	private function _ruleVal($times) {
		$val     = array(50, 100, -50, -100);
		$rateArr = array(
			1001 => array(100, 50, 20, 10),
			501  => array(100, 50, 20, 10),
			301  => array(100, 50, 20, 10),
			201  => array(100, 50, 20, 10),
			151  => array(100, 50, 20, 10),
			101  => array(100, 50, 20, 10),
			51   => array(100, 50, 20, 10),
			21   => array(100, 50, 20, 10),
			0    => array(100, 50, 20, 10),
		);

		foreach ($rateArr as $t => $rate) {
			if ($times >= $t) {
				$k = $this->_calc($rate);
				return $val[$k];
			}
		}
	}

	private function _calc($params) {
		$max        = array_sum($params);
		$randNumber = mt_rand(1, $max);
		$n          = 0;
		foreach ($params as $k => $v) {
			$n += $v;
			if ($randNumber <= $n) {
				return $k;
			}
		}
	}


}