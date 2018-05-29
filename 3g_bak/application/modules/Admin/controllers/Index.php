<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class IndexController extends Admin_BaseController {

	public $actions = array(
		'editpasswd' => '/Admin/User/edit',
		'logout'     => '/Admin/Login/logout',
		'default'    => '/Admin/Index/default',
		'getdesc'    => '/Admin/Index/getdesc',
		'search'     => '/Admin/Index/search',
		'passwdUrl'  => '/Admin/User/passwd',
	);

	public function indexAction() {
		list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();

		$this->assign('jsonmenu', json_encode($usermenu));
		$this->assign('mainmenu', $usermenu);
		$this->assign('mainview', json_encode(array_values($mainview)));
		$this->assign('username', $this->userInfo['username']);
	}

	public function defaultAction() {

		$ofpayReq    = new Vendor_Ofpay();
		$reminedInfo = $ofpayReq->req_queryuserinfo();
		//$reminedInfo= Api_Ofpay_Recharge::getUserInfo();
		$remined = 'NULL';
		if ($reminedInfo['userinfo']['retcode'] == 1) {
			$remined = $reminedInfo['userinfo']['ret_leftcredit'];
		}
		$this->assign('ofpayRemained', $remined);
		$tel  = new Vendor_Tel();
		$info = $tel->getAccountInfo();
		$this->assign('voipRemained', $info['balance']);
		$this->assign('uid', $this->userInfo['uid']);
		$this->assign('username', $this->userInfo['username']);
	}

}
