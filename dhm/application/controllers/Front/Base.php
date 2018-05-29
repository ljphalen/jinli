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
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticRoot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticPath", $staticroot . '/apps/dhm');

		$this->assign("attachroot", Common::getAttachPath());

		$controller = $this->getRequest()->getControllerName();
		if (!in_array($controller, array("Notify"))) {
			$this->checkToken();
		}
		
		$this->assign('title', '大红帽资讯');
		
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		$this->assign('controller', $controller);
		
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
	 * no cache headers
	 */
	public function nocache_headers() {
	    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	    header('Cache-Control: no-store, no-cache, must-revalidate');
	    header('Cache-Control: post-check=0, pre-check=0', false );
	    header('Pragma: no-cache');
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
}