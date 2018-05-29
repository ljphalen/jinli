<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Front_BaseController extends Common_BaseController {
	public $actions = array();
	public $userInfo = array();

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/gouapk');
		Yaf_Dispatcher::getInstance()->enableView();
		
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		if (!in_array($controller, array("Notify", "Login"))) {
			$this->checkToken();
		}
		
		if($this->getInput('ltoken')) {
			$this->userLogin();
		}
		
		//user info
		$this->userInfo = Gc_Service_User::isLogin();
		
		$coinInfo = Api_Gionee_Pay::getCoin(array('out_uid'=>$this->userInfo['out_uid']));
		$coinInfo['gold_coin'] = Common::money($coinInfo['gold_coin']);
		$coinInfo['silver_coin'] = Common::money($coinInfo['silver_coin']);
		$coinInfo['freeze_gold_coin'] = Common::money($coinInfo['freeze_gold_coin']);
		$coinInfo['freeze_silver_coin'] = Common::money($coinInfo['freeze_silver_coin']);
		
		$this->userInfo = array_merge($this->userInfo, $coinInfo);		
		$this->checkSource();
		
		$sid = Gc_Service_User::getUserSid();
		
		Common::getCache()->increment('gouapk_pv');
		
		
	}
	
	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) return true;
		$post = $this->getRequest()->getPost();
		$result = Common::checkToken($post['token']);
		if (Common::isError($result)) $this->output(-1, $result['msg']);
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function checkRight() {
		if(!$this->userInfo){
			$this->returnJs();
		}
	}
	
	/**
	 * 
	 */
	public function returnJs() {
		exit("<script>window.prompt(\"['\" + location.href + \"']\", \"gn://['GNAccount','getAccountInfo','1','false']\")</script>");
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
	
	/**
	 *
	 * @param string $click_url
	 * @return string
	 */
	public function getTaobaokeUrl($click_url) {
		$sid = Gc_Service_User::getUserSid();
		return sprintf("%s&ttid=%s&sid=%s", $click_url, Common::getConfig('apiConfig', 'taobao_taobaoke_ttid'), $sid);
	}
	
	
	/**
	 *
	 */
	protected function checkSource() {
		$source = $this->getInput("source");
		$source = str_replace(" ", "+", $source);
		if ($source) Gc_Service_Channel::cookieChannel($source);
	}
	
	/**
	 *获取outer_code
	 */
	public function getOuterCode() {
		$uid = 0;
		$channel_id = Gc_Service_Channel::getChannelId();
		$user = Gc_Service_User::isLogin();
		if ($user) $uid = $user['id'];
		return sprintf("%sH%s", intval($channel_id), $uid);
	}
	
	/**
	 * user login
	 * @return boolean
	 */
	public function userLogin() {
		$ltoken = urldecode($this->getInput('ltoken'));
		Common::log("RECIVE A TOKEN : ".$ltoken, 'user.log');
		$info = Common::encrypt($ltoken, 'DECODE');
		list($uid, $out_uid, $time) = explode('|', $info);
	
		if(!$out_uid || !$time) Common::log('TOKEN ERROR : '.$ltoken, 'user.log');
	
		if (!$out_uid) {
			Common::log('TOKEN UID ERROR : '.$out_uid, 'user.log');
			return false;
		}
		if ($time < Common::getTime()) {
			Common::log('TOKEN TIMEOUT : '.$info, 'user.log');
			return false;
		}
// 		$user = Gc_Service_User::getUserByOutUid($out_uid);
		$user = Gc_Service_User::getUserBy(array('out_uid'=>$out_uid, 'id'=>$uid));
		if (!$user) {
			Common::log('USER LOGIN USERINFO ERROR : '.$info, 'user.log');
		}
		$ret = Gc_Service_User::login($user['username'], $user['password']);
		if (Common::isError($ret)) {
			Common::log('USER LOGIN FAILD : '.$info, 'user.log');
		}
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if ($user['isgain'] == 0) {
			$this->redirect($webroot.'/user/index/gain');
		}
	}
}
