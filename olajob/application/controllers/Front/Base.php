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
	public $auth = true;
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticRoot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticPath", $staticroot . '/apps/olajob');

		$this->assign("attachroot", Common::getAttachPath());

		$controller = $this->getRequest()->getControllerName();
		/*if (!in_array($controller, array("Notify"))) {
			$this->checkToken();
		}*/
		
		$this->assign('title', 'olajob');
		
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		$this->assign('controller', $controller);

        $this->userInfo = $this->getUserInfo();

        //weixin
        if ($this->userInfo && $this->auth && Common::isWeixin() == true) {
            $open_id = $this->getOpenId();
            if (!$this->userInfo["weixin_open_id"] || !$open_id) {
                $url = Common::currentPageUrl();
                Util_Cookie::set("LR", $url, true);
                $this->redirect("/api/weixin/openid");
                exit;
            }
            //$this->userInfo = $this->getUserInfo($open_id);
        }
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
