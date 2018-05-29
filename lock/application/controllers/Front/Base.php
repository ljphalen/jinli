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
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/lock');
		$this->assign("staticroot", $staticroot);
		$this->assign("webroot", $webroot);
		$this->assign("crUrl", $webroot."/Cr/index");
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$this->assign('cn', sprintf('%s_%s_%s', $module, $controller, $action));
		
		$controller = $this->getRequest()->getControllerName();
		if($controller != 'Cr'){
			
			$resolution = Util_Cookie::get('resolution', true);
			if(!$resolution) {
				//取分辨率
				$resolution = $this->getInput('resolution');
				$kernel = intval($this->getInput('kernel'));
						
				if(!$resolution || strpos($resolution, '_') === false) exit('非法的请求');
				$resolution = str_replace('_', '*', $resolution);
				$size = Lock_Service_Size::getSizeByName($resolution);
				if(!$size) exit('非法的请求');
				Util_Cookie::set('resolution', $size['id'], true, Common::getTime()+31536000, '/');
				Util_Cookie::set('kernel', $kernel, true, Common::getTime()+31536000, '/');
			}
			
			//PV统计
			Common::getCache()->increment('Lock_pv');
		}
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
