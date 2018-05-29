<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class IndexController extends Admin_BaseController {
	
	public $actions = array(
		'editpasswd' => '/Admin/User/edit',
		'logout' => '/Admin/Login/logout',
		'default' => '/Admin/Index/default',
		'getdesc' => '/Admin/Index/getdesc',
		'search' => '/Admin/Index/search',
		'passwdUrl' => '/Admin/User/passwd',
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
		$this->assign('jsonmenu', json_encode($usermenu));
		$this->assign('mainmenu', $usermenu);
		$this->assign('mainview', json_encode(array_values($mainview)));
		$this->assign('username', $this->userInfo['username']);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function defaultAction() {
		$this->assign('uid', $this->userInfo['uid']);
		$this->assign('username', $this->userInfo['username']);
	}
}
