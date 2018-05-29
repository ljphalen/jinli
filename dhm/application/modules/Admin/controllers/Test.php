<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class TestController extends Admin_BaseController {
	
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
