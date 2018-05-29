<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_BaseController extends Common_BaseController {
	public $userInfo;
	public $actions = array(); 
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("staticRoot", $staticroot);
		$this->assign("staticPath", '/statics/apps/admin');
		$this->assign("attachPath", $adminroot . '/attachs');
		$this->assign("adminroot", $adminroot);
		$this->assign("ueditorAction", $this->adminroot."/admin/ueditor/index");
		$this->checkRight();
		$this->checkToken();
		$this->checkCookieParams();
		$this->updateAppCache();
		$this->updateVersion();
        $this->behavioralStat();
	}
	
	/**
	 * updateAppCache
	 */
	public function updateAppCache() {
		$action = $this->getRequest()->getActionName();
		if (isset($this->appCacheName) && $this->appCacheName && in_array($action, array('add_post', 'edit_post', 'delete', 'batchupdate'))) {
			if (is_array($this->appCacheName)) {
				foreach($this->appCacheName as $value) {
					Gou_Service_Config::setValue($value, Common::getTime());
				}
			} else {
				Gou_Service_Config::setValue($this->appCacheName, Common::getTime());
			}
		}
	}
	
	/**
	 * updateAppCache
	 */
	public function updateVersion() {
		$action = $this->getRequest()->getActionName();
		if (isset($this->versionName) && $this->versionName && in_array($action, array('add_post', 'edit_post', 'delete', 'batchupdate'))) {
		    if (is_array($this->versionName)) {
				foreach($this->versionName as $value) {
					Gou_Service_Config::setValue($value, Common::getTime());
				}
			} else {
				Gou_Service_Config::setValue($this->versionName, Common::getTime());
			}
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
	 */
	public function checkRight() {
		$this->userInfo = Admin_Service_User::isLogin();
		if(!$this->userInfo && !$this->inLoginPage()){
			$this->redirect("/Admin/Login/index");
		} else {
			$module = $this->getRequest()->getModuleName();
			$controller = $this->getRequest()->getControllerName();
			$action = $this->getRequest()->getActionName();

			list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();

			$mc  = $module . "_" . $controller;
			$mca = $module . "_" . $controller . "_" . $action;
            if (!in_array($mc, $userlevels) && !in_array($mca, $userlevels)) {
				exit('没有权限');
			}
		};
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function inLoginPage() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();

		if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function getUserMenu() {
		$userInfo = $this->userInfo;
		$groupInfo = array();
		if ($userInfo['groupid'] == 0) {
			$groupInfo = array('groupid'=>0);
		} else {
			$groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
		}
		$menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
		list($usermenu, $mainview, $usersite, $userlevels) = $menuService->getUserMenu($groupInfo);
		array_push($userlevels, "Admin_Initiator", "Admin_Index", 'Admin_Login', 'Admin_User_passwd', 'Admin_User_passwd_post');
		$modules = array(
			'Admin_Ad_|ver|'=>'Admin_Ad',
			'Admin_Channel_|ver|'=>'Admin_Channel',
			'Admin_Cod_Guide_|ver|'=>'Admin_Cod_Guide',
			'Admin_Client_Shops_|ver|'=>'Admin_Client_Shops',
			'Admin_Store_Category_|ver|'=>'Admin_Store_Category',
			'Admin_Store_Info_|ver|'=>'Admin_Store_Info',
			'Admin_Clientchannel_|ver|'=>'Admin_Clientchannel',
			'Admin_Cod_Guide_|ver|_Pic'=>'Admin_Cod_Guide',
		);
		$this->addVersionSupport($modules,$userlevels);
		//编辑器
		array_push($userlevels, "Admin_Ueditor");

		//积分特殊处理
//		array_push($userlevels, "Admin_User_Score");

		return array($usermenu, $mainview, $usersite, $userlevels);
	}

	public function addVersionSupport($ver_arr, &$userlevels)
	{
		foreach ($ver_arr as $str => $mod) {
			$pattern = str_replace('|ver|', '\w+', $str);
			$arr = preg_grep("/$pattern/" ,$userlevels);
			if(!empty($arr)){
				array_push($userlevels, $mod);
			}
		}
	}

	public function cookieParams() {
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);

		$tmp = array();
		$not = array('token','s');
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key, $not))$tmp[$key] = $this->getInput($key);
		}
		Util_Cookie::set($name, Common::encrypt(http_build_query($tmp)), false, Common::getTime() + (5 * 3600));
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function checkCookieParams() {
		$s = $this->getInput('s');
		
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action = $this->getRequest()->getActionName();
		$name = sprintf('%s_%s_%s', $module, $controller, $action);
		
		$query_str = Common::encrypt(Util_Cookie::get($name), 'DECODE');
		parse_str($query_str, $params);
		
		if (count($params) && $s) {
				$adminroot = Yaf_Application::app()->getConfig()->adminroot;
				
				$url = sprintf('%s/%s/%s/%s?%s', $adminroot, $module, $controller, $action, http_build_query($params));
				$this->redirect($url);
		}
	}
	
	/**
	 * 上传附件图片时 把图片地址改用cdn
	 * @return boolean
	 */
	public function updateImgUrl($content) {
	    $adminroot = Yaf_Application::app()->getConfig()->adminroot;
	    $attachroot = Yaf_Application::app()->getConfig()->attachroot;
	    $attachPath = Common::getAttachPath();
	    
	    if(!$content) return false;
	    
	    return str_replace($adminroot.'/attachs', $attachPath, $content);
	}

    /**
     * 管理员行为统计
     */
    public function behavioralStat(){
        $userInfo = $this->userInfo;
        $module = strtolower($this->getRequest()->getModuleName());
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());

//        Common::log($action, 'action.log');
        $ex_actions = array(
            'Cs_Feedback_Qa/append_post',
            'Cs_Feedback_Qa/reply_post',
        );

        array_walk($ex_actions, function (&$v) { $v = strtolower($v);});
        if(in_array($controller . '/' . $action, $ex_actions)) return false;

        if( stripos($action, '_post')               ||
            stripos($action, 'delete')  === 0       ||
            stripos($action, 'top')     === 0       ||
            stripos($action, 'batchUpdate') === 0   ||
            stripos($action, 'update') === 0        ||
            stripos($action, 'active')  === 0
        ) {
            $log = array(
                'uid' => $userInfo['uid'],
                'username' => $userInfo['username'],
                'groupid' => $userInfo['groupid'],
                'url' => Common::getCurPageURL(),
                'type' => $action,
                'time' => Common::getTime(),
                'data' => json_encode($_REQUEST)
            );
            Common::getMongo()->insert('behavioral', $log);
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
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
}
