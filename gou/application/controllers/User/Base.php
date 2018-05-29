<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * base
 * @author tiansh
 *
 */
class User_BaseController extends Common_BaseController {
	public $userInfo;
	public $t_bi;
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		$this->notice();
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticRoot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/gou');
		
		/*Common::getCache()->increment('gou_pv');*/
		
		/* if (Gou_Service_User::getToday()) {
			Common::getCache()->increment('gou_uv');
		} */
		
		$this->userInfo = Gou_Service_User::isLogin();
		if ($this->userInfo) $coinInfo = Api_Gionee_Pay::getCoin(array('out_uid'=>$this->userInfo['out_uid']));
		
		if ($coinInfo['status'] == 200) {
			$this->userInfo['gold_coin'] = $coinInfo['gold_coin'];
			$this->userInfo['freeze_silver_coin'] = $coinInfo['freeze_silver_coin'];
			$this->userInfo['silver_coin'] = $coinInfo['silver_coin'];
			$this->userInfo['freeze_gold_coin'] = $coinInfo['freeze_gold_coin'];
		}
		
		$this->assign('user', $this->userInfo);
				
		$controller = $this->getRequest()->getControllerName();
 		$action = $this->getRequest()->getActionName();
 		
 		if (!in_array($controller, array('Account')) && !in_array($action, array('verify'))) {
	 		$this->checkToken();
	 		$this->checkRight();
 		}
 		
 		$return = $this->getInput('return');
 		if ($return) Util_Cookie::set('return', $return, true, Common::getTime()+7200);
 		
 		$webroot = Common::getWebRoot();
 		$return_url = Util_Cookie::get('return', true);
 		
 		$this->assign("returnUrl", $return_url ? $return_url : $webroot);
 		
 		$this->t_bi = Util_Cookie::get('GOU-SOURCE', true);
 		
 		$this->assign('title', '个人中心');
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
		if(!$this->userInfo && !$this->inLoginPage()){
			$t_bi = $this->getInput('t_bi');
			$url = $t_bi ? $webroot."/user/login/index?t_bi=".$t_bi : $webroot."/user/login/index";
			$this->redirect($url);
		} 
	
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		if ($module == 'User' && ($controller == 'Login' || $controller == 'Register')) {
			return true;
		}
		return false;
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
	 * Enter description here ...
	 */
	public function notice() {
		$source = strpos(Util_Http::getServer('REQUEST_URI'), 'debug') ? '.source' : '';
		$version = Common::getConfig('siteConfig', 'version');
		$staticRoot = Yaf_Application::app()->getConfig()->staticroot;
		
		$html =  '
<!DOCTYPE HTML>
<html >
<head>
	<meta charset="UTF-8">
	<script>var webPage = true;</script>
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="format-detection" content="email=no" />
	<meta name="baidu-tc-cerfication" content="e907a7e3cf8e28494d30c67915af6dae" />
	
	<link type="text/css" rel="stylesheet" href="'.$staticRoot.'/sys/reset/phonecore'.$source.'.css?t='.$version.'">
	<link type="text/css" rel="stylesheet" href="'.$staticRoot.'/apps/gou/assets/css/gou'.$source.'.css?t='.$version.'">
	<link type="text/css" rel="stylesheet" href="'.$staticRoot.'/apps/gou/assets/css/web'.$source.'.css?t='.$version.'">
	
	<script type="text/javascript" src="'.$staticRoot.'/sys/icat/1.1.6/icat.js?t='.$version.'"
	data-main="~apps/gou/assets/js/main" data-cssfile="../css/gou'.$source.'.css"></script>
</head>
				<body data-pagerole="body">
	<div class="module">
		<div class="invalid-page">
			<img src="'.$staticRoot.'/apps/gou/pic/pic_invalidPage.png" alt="">
			<p>尊敬的用户，会员中心已下线<br>请升级您的客户端！</p>
		</div>
	</div>
</body>
</html>';
		exit($html);
	}
}
