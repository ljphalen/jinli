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
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		
		if (ENV == 'product') {
			//取机型
			$ua = Util_Http::getServer('HTTP_USER_AGENT');
			preg_match('/GiONEE-(.*)\//iU', $ua, $matches);
			$model_name = $matches[1];			
			//如果检测到有机型，则跳转到1.1版本
			$controller = $this->getRequest()->getControllerName();
			if($model_name  && $controller != 'Nav') $this->redirect('http://m.gionee.com');
		}
		
		$this->checkToken();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/browser');
		$this->assign("crUrl", $webroot."/Cr/index");
				
		//书签跳转
		$server = Util_Http::getServer();
		$url = 'http://'.$server['SERVER_NAME'].$server['REQUEST_URI'];
		if (strpos($url, 'main.asp')){
			$redirect = Browser_Service_Redirect::getRedirectByUrl(md5($url));
			if ($redirect) {
				$this->redirect($redirect['redirect_url']);
			}
		}

		//PV统计
		Common::getCache()->increment('browser_pv');
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
				exit(json_encode($out));
		}
	}
}
