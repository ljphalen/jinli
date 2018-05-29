<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Game_BaseController extends Common_BaseController {
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->checkToken();
		$this->assignValues();
		
		$webroot = Common::getWebRoot();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticroot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/game/3g');
		$this->assign("crUrl", $webroot."/Cr/index");
		$ami_web_weixin = Game_Service_Config::getValue('ami_web_weixin');
		$this->assign('ami_web_weixin', $ami_web_weixin);
			
		$this->toForwardPC ( $webroot );
		
		//PV统计
		Common::getCache()->increment('game_pv');
		
		if ($this->getToday()) {
			Common::getCache()->increment('game_uv');
		}
		$refer = Util_Http::getServer('HTTP_REFERER');
		$this->assign("refer", $refer);
	}
	
	protected function assignValues() {
	    $isDebug = preg_match('/debug/i', $_SERVER['QUERY_STRING']);
	    $source = '.source';
	    $staticRoot = Yaf_Application::app()->getConfig()->staticroot;
	    $appRef = $staticRoot . '/apps/game/h5';
	    $appPic    =$appRef.'/pic';
	    $appCss = $appRef . '/assets/css';
	    $appImg = $appRef . '/assets/img';
	    $appJs = $appRef . '/assets/js';
	    $timestamp = '?t='.Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY);
	    $this->assign('source', $source);
	    $this->assign('appPic', $appPic);
	    $this->assign('appCss', $appCss);
	    $this->assign('appImg', $appImg);
	    $this->assign('appJs', $appJs);
	    $this->assign('timestamp', $timestamp);
	    $this->assign('token', Common::getToken());
	}
	
	/**
	 * 跳转到pc
	 */
	 private function toForwardPC($webroot) {
	 	if(ENV != 'product'){
	 		return false;
	 	}
	 	
		//判断pc端还是移动端
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action_name =  $this->getRequest()->getActionName();
		$is_mobile = Common::checkMobileRequest();
		
		//新闻资讯跳转
		if( !$is_mobile  && strtolower($module) == 'game' && strtolower($controller) == 'news' && strtolower($action_name) == 'detail' ){
			$id = $this->getInput('id');
			if($id){
				$this->redirect($webroot.'/Front/News/detail/?id='.$id.'&intersrc=mobileweb');
				exit;
			}
		}
		//游戏详情跳转
		if( !$is_mobile  && strtolower($module) == 'game' && strtolower($controller) == 'index' && strtolower($action_name) == 'detail'){
			$id = $this->getInput('id');
			if($id){
				$this->redirect($webroot.'/Front/Game/detail/?id='.$id.'&intersrc=mobileweb');
				exit;
			}
		}
		if( !$is_mobile  && strtolower($module) == 'game' && strtolower($controller) == 'search' && strtolower($action_name) == 'detail'){
			$id = $this->getInput('id');
			if($id){
				$this->redirect($webroot.'/Front/Search/detail/?id='.$id.'&intersrc=mobileweb');
				exit;
			}
		}
	 	//跳转到pc端
		if(!$is_mobile && strtolower($controller) != 'monact') {
			//$this->redirect($webroot.'/Front/Index/close/');
			$this->redirect($webroot.'/Front/Index/index/?intersrc=mobileweb');
			exit;
		}
	}

	
	public function setSource() {
		$source = $this->getInput('source');
	    $from = $this->getInput('from');
		
		if($from) $source = $from;
		
		$sid = $this->getSource();
		
		if ($sid) {
			$sid_arr = explode('_', $sid);
			$sid_arr[0] = $source;
			$string = implode('_', $sid_arr);
		} else {
			$uid = crc32(uniqid());
			$string = sprintf("%s_%s", $source, $uid);
		}
		return Util_Cookie::set('GAME-SOURCE', $string, false, strtotime("+30 day"), '/', $this->getDomain());
	}
	
	public function getSource() {
		return Util_Cookie::get('GAME-SOURCE', false);
	}
	
	//添加抽奖访客唯一ID
	public function setUuid() {
		$uuid = $this->getUuid();
	
		if ($uuid) {
			$string = $uuid;
		} else {
			$uuid = Festival_Service_Monprize::generateUUID();
			$string = $uuid;
		}
		return Util_Cookie::set('UUID', $string, false, strtotime("+30 day"), '/', $this->getDomain());
	}
	
	public function getUuid() {
		return Util_Cookie::get('UUID', false);
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function getDomain() {
		$domain = str_replace('http://','',Common::getWebRoot());
		if($number = strrpos($domain,':'))  $domain = Util_String::substr($domain, 0, $number);
		return $domain;
	}
	
	
	/**
	 *
	 * @return boolean
	 */
	public function getToday() {
		$is_game_user = Util_Cookie::get('ISGAMEUSER', true);
		if ($is_game_user) return false;
		return Util_Cookie::set('ISGAMEUSER', 1, true, strtotime(date('Y-m-d 23:59:59')), '/', $this->getDomain());
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
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		$callback = $this->getInput('callback');
		$out = array(
				'success' => $code == 0 ? true : false ,
				 'msg' => $msg,
				'data' => $data
			);
		if ($callback) {
				header("Content-type:text/javascript");
				exit($callback . '(' . json_encode($out) . ')');
			} else {
				header("Content-type:text/json");
				exit(json_encode($out));
		}
	}
	
	public function ajaxOutput($data = array(), $isSuccess = true, $msg = '') {
	    header("Content-type:text/json");
	    exit(json_encode(array(
	                    'success' => $isSuccess ,
	                    'msg' => $msg,
	                    'data' => $data
	    )));
	}
	
	public function getPageInput() {
	    $page = intval($this->getInput('page'));
	    return $page < 1 ? 1 : $page;
	}
	
	public function display($webPath) {
	    $this->getView()->display($webPath);
	    exit;
	}
}
