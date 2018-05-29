<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Super_BaseController extends Common_BaseController {
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		
		$this->checkToken();
		$webroot = Common::getWebRoot();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/game/3g');
		$this->assign("crUrl", $webroot."/Cr/index");
		
		//PV统计
		Common::getCache()->increment('game_pv');
		
		if ($this->getToday()) {
			Common::getCache()->increment('game_uv');
		}
		$refer = Util_Http::getServer('HTTP_REFERER');
		$this->assign("refer", $refer);
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
}
