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
		$webroot = Common::getWebRoot();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticroot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/game/3g');
		
		//PV统计
		Cache_Factory::getCache()->increment('game_pv');
		
		if ($this->getToday()) {
			Cache_Factory::getCache()->increment('game_uv');
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
