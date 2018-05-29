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
		$this->assign("staticRoot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/gou');

		$controller = $this->getRequest()->getControllerName();
		if (!in_array($controller, array("Notify"))) {
			$this->checkToken();
		}
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		
		//uv
		/* if($action !='clienttj'){
			if (Gou_Service_User::getToday()) {
				Common::getCache()->increment('gou_uv');
			}
		} */
		
		$this->userInfo = Gou_Service_User::isLogin();
		$this->assign('user', $this->userInfo);
		
		$this->assign('title', '购物大厅');
		$this->setSource();
		$this->assign('t_bi',Util_Cookie::get('GOU-SOURCE', true));		
		
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
	 */
	public function checkRight() {
		if(!$this->userInfo){
			$webroot = Common::getWebRoot();
			$url = $webroot.'/user/login/login_step?refer='.urlencode(Common::getCurPageURL());
			$this->redirect($url);
		}
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
        $out      = array(
            'success' => $code == 0 ? true : false,
            'msg'     => $msg,
            'data'    => $data
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
