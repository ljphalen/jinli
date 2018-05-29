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
		$this->checkToken();
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticSysPath", $staticroot . '/statics/common/sys');
		$this->assign("staticResPath", $staticroot . '/statics/browser/v1.1');
		
		$this->assign("crUrl", $webroot."/Cr/index");
		
		//取机型
		$current_model_id = Yaf_Registry::get("current_model_id");
		if(!$current_model_id) {
			$ua = Util_Http::getServer('HTTP_USER_AGENT');
			//$ua = "Mozilla/5.0 (Linux; U; Android 4.0.3; zh-cn;GiONEE-GN868/S101 Build/IMM76D) AppleWebKit 534.30 (KHTML,like Gecko) Version/4.0 Mobile Safari/534.30 Id/FD34645D0CF3A18C9FC4E2C49F11C510 GNBR/1.3.0003 (securitypay,securityinstalled)";
			preg_match('/GiONEE-(.*)\//iU', $ua, $matches);
			$model_name = $matches[1];
			
			$controller = $this->getRequest()->getControllerName();			
			
			if(!$model_name && $controller != 'Subject') $this->redirect('http://3g.gionee.com');
			
			$model = Gionee_Service_Models::getModelsByName($model_name);
			Yaf_Registry::set("current_model_id", $model ? $model['id'] : 0);
		}
		

		//PV统计
		Common::getCache()->increment('3g_pv');
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
