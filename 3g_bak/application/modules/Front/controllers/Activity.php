<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 活动专用页
 * @author panxb
 *
 */
class ActivityController extends Front_BaseController {

	public $actions = array(
		'getQuotaUrl' => '/Front/Activity/ajaxGetPermission',
		'cindexUrl'   => '/Front/Activity/cindex',
		'clistURL'    => '/Front/Activity/clist',
	);

	/**
	 * 畅聊接口
	 */
	public function cindexAction() {
		$t_bi   = $this->getSource();
		$user   = $this->_checkLogin($this->actions['cindexUrl']);
		$caller = isset($user['mobile']) ? $user['mobile'] : 0;
		$this->_checkUser($caller);
		if (!empty($caller) && !Common::checkIllPhone($caller)) {
			$url = sprintf("%s/user/index/bindphone?f=%d", Common::getCurHost(), 2);
			Common::redirect($url);
		}

		$voipUserInfo = Gionee_Service_VoIPUser::checkLoginTime($caller);
		Gionee_Service_Log::pvLog('voip_pv_list');//PV统计
		Gionee_Service_Log::uvLog('voip_uv_list', $t_bi);//PV统计
		$tel   = $this->getInput('tel');
		$cname = $this->getInput('name');

		//list($total,$dataList) = Gionee_Service_VoIPLog::getList(1,20,array('caller_phone'=>$caller,'show'=>1),array('called_time'=>'DESC'));
		//获得用户信息
		$area = Gionee_Service_VoIP::getAreaCode($caller);
		$this->assign('cname', $cname);
		$this->assign('tel', $tel);
		$this->assign('area', $area);
		$this->assign('mobile', $caller);
		$this->assign('voipUserInfo', $voipUserInfo);
		$this->assign('login', !empty($caller) ? 1 : 0);
	}

	private function _getRecordList($caller) {
		$dataList = array();
		if ($caller) {
			$rkey     = 'KEY:VOIP_CLIST:' . $caller;
			$dataList = Common::getCache()->get($rkey);
			if (empty($dataList)) {
				$dataList = Gionee_Service_VoIPLog::getCommutniateList(1, 20, array(
					'caller_phone' => $caller,
					'show'         => 1
				), array('called_phone'), array('called_time' => 'DESC'));
				foreach ($dataList as $k => $v) {
					$contact = Gionee_Service_Contact::getBy(array(
						'cphone'     => $v['called_phone'],
						'user_phone' => $v['caller_phone']
					));
					if (!empty($contact['cname'])) {
						$dataList[$k]['called_name'] = $contact['cname'];
					}
				}
				Common::getCache()->set($rkey, $dataList, 600);
			}
		}
		return $dataList;
	}

	private function _getServiceInfo() {
		//有信客服
		$rsKey = 'KEY:VOIP:SERVICE';
		$cs    = Common::getCache()->get($rsKey);
		if (empty($cs)) {
			$service = Gionee_Service_Config::getValue('yx_customer_service');
			$service = json_decode($service, true);
			foreach ($service as $k => $v) {
				$cs[] = json_decode($v, true);
			}
			Common::getCache()->set($rsKey, $cs, 60);
		}
		return $cs;
	}

	//获取免费通话资格
	public function ajaxGetPermissionAction() {
		//先测检登陆状态
		$user = $this->_checkLogin($this->actions['cindexUrl'], true);
		//检测是否已领取
		$pid = $this->getInput('pid');
		if (!is_numeric($pid)) {
			$this->output('-1', '参数有错！');
		}
		$obtained = Gionee_Service_VoIPUser::isObtained(array('user_phone' => $user['mobile'], 'pid' => $pid));
		if ($obtained) {
			$this->output('-1', '您已获得本次通话资格，不能重复获取');
		}

		//是否在有效期内
		$expired = Gionee_Service_VoIP::getBy(array(
			'start_time' => array('<=', time()),
			'end_time'   => array('>=', time())
		), array());
		if (!$expired) {
			$this->output('-2', '本期活动已结束，请下次再来！');
		}
		//添加信息
		$time = time();
		$res  = Gionee_Service_VoIPUser::add(array(
			'user_phone' => $user['mobile'],
			'pid'        => $pid,
			'get_time'   => $time,
			'date'       => date('Ymd', $time)
		));
		if ($res) {
			$this->output(0, '获取成功', array('redirect' => $this->actions['clistURL']));
		} else {
			$this->output('-3', '获取失败');
		}
	}

	/**
	 *拨打电话
	 */
	public function callAction() {

		$t_bi = $this->getSource();
		Gionee_Service_Log::pvLog('voip_pv_call1');//PV统计
		Gionee_Service_Log::uvLog('voip_uv_call1', $t_bi);//PV统计
		$user   = $this->_checkLogin($this->actions['clistURL'], false);
		$called = $this->getInput('callee');
		$type   = $this->getInput('type');
		$name   = $this->getInput('name');
		$id     = $this->getInput('id');
		if (empty($user['mobile'])) {
			$this->output(-1, '');
		}
		$caller = $user['mobile'];
		if ($caller == $called) {
			$this->output(-1, '不能打给自己');
		}
		$firstStr = substr($called, 0, 1);
		$flag     = $firstStr == '1' ? 'phone' : 'tel';
		if (!Common::checkIllPhone($called, $flag)) {
			$this->output(-1, '呼叫号码有误！');
		}

		$userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($caller);
		
		if(! $userVoipInfo['is_vip']){
			$diff         = $userVoipInfo['m_sys_sec'] + $userVoipInfo['exchange_sec'];
			$t            = floor($diff / 60) * 60;//值必须为60的倍数
			if ($t == 0) {
				$this->output(-1, '本月拨打额度已用完！');
			}
		}
		Gionee_Service_Log::pvLog('voip_pv_call');//PV统计
		Gionee_Service_Log::uvLog('voip_uv_call', $t_bi);//PV统计
		$now  = time();
		$rkey = 'KEY:VOIP_CLIST:' . $caller;
		Common::getCache()->delete($rkey);//如果用户拔打过电话,就把通话记录列表的缓存清空

		$area         = Gionee_Service_VoIP::getAreaCode($called);
		$callerClient = Gionee_Service_VoIPClient::getClientNumber($caller, true);

		$direct = '';
		if ($type == 1) {//直播模式
			$conf      = Gionee_Service_Config::getValue('voip_config');
			$data      = json_decode($conf, true);
			$directArr = array(
				'accountsid' => $data['accountSid'],
				'accountpwd' => $data['accountToken'],
				'clientsid'  => $callerClient['client_number'],
				'clientpwd'  => $callerClient['client_pwd'],
				'id'         => !empty($id) ? $id : '-1',
				'name'       => !empty($name) && $name != 'undefined' ? $name : '',
				'number'     => $called,
				'city'       => $area,
			);
			$enObj     = new Util_Encrypt();
			$direct    = $enObj->aesEncrypt(json_encode($directArr, JSON_UNESCAPED_UNICODE));
			$out       = 'ok';
		} else {
			$telObj = new Vendor_Tel();
			$out    = $telObj->callBack($callerClient['client_number'], $called);
		}

		//添加联系人信息
		Gionee_Service_Contact::addContactToDb($caller, $called, $name);
		if ($out) {
			$ret = array(
				'address'  => $area,
				'callTime' => $this->_formatDate($now),
				'msg'      => $out,
				'account'  => $direct,
			);
			$this->output(0, '', $ret);
		}

		$this->output(-1, '', '接口异常' . $out);
	}


	private function _checkUser($caller) {
		$now = time();
		if ($caller) {
			$userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($caller);
			if (empty($userVoipInfo['id'])) {
				$param = array(
					'user_phone' => $caller,
					'pid'        => 1,
					'get_time'   => $now,
					'date'       => date('Ymd', $now)
				);
				Gionee_Service_VoIPUser::add($param);
			}
		}

	}

	/**
	 *列表页
	 */
	public function clistAction() {
		//检测用户登陆状态
		$user = $this->_checkLogin($this->actions['cindexUrl'], true);
		//$total    = 0;//已领取活动资格的人数
		$userFlag = 1;//默认用户有领取资格
		$userMsg  = array(0, $user['mobile'], $user['id'], $user['out_uid']);
		$phone    = !empty($user['mobile']) ? $user['mobile'] : '';

		$t_bi = $this->getSource();
		Gionee_Service_Log::pvLog('voip_pv');//PV统计
		Gionee_Service_Log::uvLog('voip_uv', $t_bi);//UV统计
		$cs       = $this->_getServiceInfo();
		$dataList = $this->_getRecordList($phone);
		$data     = array();
		foreach ($dataList as $k => $v) {
			$data[$k]['total']       = $v['total'];
			$data[$k]['callee']      = $v['called_phone'];
			$data[$k]['callTime']    = $this->_formatDate($v['called_time']);
			$data[$k]['address']     = $v['area'] ? $v['area'] : '未知';
			$data[$k]['called_name'] = $v['called_name'] ? $v['called_name'] : '';
		}
		$this->assign('flag', $userFlag);
		$this->assign('userMsg', $userMsg);
		$this->assign('data', $data);
		$this->assign('cs', $cs);
	}

	//规则页
	public function tipsAction() {
		//友情提示
		$tips     = Gionee_Service_Config::getValue('3g_voip_tips');
		$sysTime  = Gionee_Service_Config::getValue('3g_voip_tips_time');
		$selfTime = Util_Cookie::get('3g_voip_tips_time', true);
		$is_look  = 1;
		if ($sysTime != $selfTime) {
			$is_look = 0;
			Util_Cookie::set('3g_voip_tips_time', $sysTime, true, strtotime("+360 day"), '/');
		}
		$this->assign('tips', $tips);
		$this->assign('is_look', $is_look);
	}

	//contcat
	public function contactAction() {
		//检测用户登陆状态
		$t_bi = $this->getSource();
		Gionee_Service_Log::pvLog('voip_pv');//PV统计
		Gionee_Service_Log::uvLog('voip_uv', $t_bi);//UV统计
		//	$total    = 0;//已领取活动资格的人数
		$cs = $this->_getServiceInfo();
		$this->assign('cs', $cs);
	}

	///退出登陆
	public function logoutAction() {
		$res = Gionee_Service_User::logout();
		if ($res) {
			$outUrl = 'http://t-id.gionee.com/member/logout?redirect_uri=';
			if (stristr(ENV, 'product')) {
				$outUrl = 'http://id.gionee.com/member/logout?redirect_uri=';
			}
			Common::redirect($outUrl . Common::getCurHost() . '/nav');
		}
	}

	//清空数据
	public function clearAction() {
		$user   = $this->_checkLogin($this->actions['clistURL'], true);
		$caller = $user['mobile'];
		$res    = Gionee_Service_VoIPLog::updateBy(array('show' => '0'), array(
			'caller_phone' => $caller,
			'show'         => '1'
		));
		if ($res) {
			$rkey = 'KEY:VOIP_CLIST:' . $caller;
			Common::getCache()->delete($rkey);
			$this->output(0, '操作成功！', array('redirect' => $this->actions['clistURL']));
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	//检测通话时间是否有效
	private function _checkValid($phone) {
		$least = Gionee_Service_VoIPUser::getInfoByPhone($phone);
		if (empty($least)) {
			return array('key' => '-1', 'msg' => '没有活动权限！');
		}
		$content = Gionee_Service_VoIP::getBy(array('id' => $least['pid'], 'sta' => 1), array());
		if (!$content) {
			return array('key' => '-1', 'msg' => '该期活动不存在或已结束！');
		}
		if ($content['valid_time'] < time()) {
			return array('key' => '-1', 'msg' => '您的通话权限已过有效期！');
		}
		return array('key' => '1', 'msg' => '');
	}


	//获取通话详细信息记录
	public function getDetailMsgAction() {
		$ret = Gionee_Service_VoIP::collectionFTP();
		var_dump($ret);
		exit;
	}


	//日期格式化
	private function _formatDate($time) {
		return date('n月j日H:i', $time);
	}

	//检测用户登陆情况
	private function _checkLogin($callback_url, $flag = true) {
		$webroot = Common::getCurHost();
		$user    = Gionee_Service_User::getCurUserInfo();
		if (!$user && $flag) {
			$callback = $webroot . '/user/login/login?f=2';
			$url      = Api_Gionee_Oauth::requestToken($callback);
			Util_Cookie::set('GIONEE_LOGIN_REFER', $callback_url, true, Common::getTime() + (5 * 3600), '/');
			Common::redirect($url);
		}

		$obtained = Gionee_Service_VoIPUser::isObtained(array('user_phone' => $user['mobile']));
		if (!$obtained && !empty($user['mobile'])) {
			$time = time();
			Gionee_Service_VoIPUser::add(array(
				'user_phone' => $user['mobile'],
				'get_time'   => $time,
				'date'       => date('Ymd', $time)
			));
		}
		return $user;
	}

	public function meAction() {
		//检测用户登陆状态
		$userInfo      = $this->_checkLogin($this->actions['cindexUrl'], true);
		$voipUserInfo  = Gionee_Service_VoIPUser::checkLoginTime($userInfo['mobile']);
		$userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
		Gionee_Service_Log::pvLog('voip_me_pv');
		Gionee_Service_Log::uvLog('voip_me_uv', $this->getSource());
		$this->assign('voipUserInfo', $voipUserInfo);
		$this->assign('rewardsMinus', $this->_getLevelRewardMinus($userInfo['experience_level']));
		$this->assign('userInfo', $userInfo);
		$this->assign('scoreInfo', $userScoreInfo);

	}

	public function noticeAction() {
		$txt = Gionee_Service_Config::getValue('3g_call_notice');
		$this->assign('notice_txt', $txt);
		$title = Gionee_Service_Config::getValue('user_notice_title');
		$this->assign('notice_title', $title);
	}
	
	private function _getLevelRewardMinus($level){
		$minus = 0;
		$levelData = User_Service_ExperienceInfo::getBy(array('level'=>$level));
		$levelMsg = json_decode($levelData['level_msg'],true);
		if(!empty($levelMsg)){
			foreach ($levelMsg as $k=>$v){
				if($v['reward_type'] == 3){
					$minus = $v['num'];
				}
			}
		}
		return $minus;
	}
}