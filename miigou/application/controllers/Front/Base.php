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
	public $userInfo = '';
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticRoot", $staticroot . '/apps');
		$this->assign("staticResPath", $staticroot . '/apps/gou/star');

		$controller = $this->getRequest()->getControllerName();
		if (!in_array($controller, array("Notify"))) {
			$this->checkToken();
		}
		
		//Common::getCache()->increment('gou_pv');
		
		$this->assign('title', '挑战性');
		$this->setSource();
		$this->assign('t_bi',Util_Cookie::get('GOU-SOURCE', true));
		
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		$this->assign('cn', sprintf('%s_%s_%s', $module, $controller, $action));
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
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
}
