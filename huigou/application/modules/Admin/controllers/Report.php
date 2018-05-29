<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ReportController extends Admin_BaseController{
	
	public $actions = array(
				'indexUrl' => '/admin/report/index',
				'bindUrl'=>'/admin/report/bind'
			);
	
	public $perpage = 20;

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$date = $this->getInput('date');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		if (!$date) $date = date('Y-m-d H:i:s');
		
		$session = Gc_Service_Config::getValue('GOU_ADMIN_TOKEN');
		
		if (!$session) {
			$this->redirect(self::_getauthcodeUrl());
		}
		
		$topApi = new Api_Top_Service();
		
		$ret = $topApi->getTaokeReport(array('page_no'=>1, 'session'=>$session, 'page_size'=>$this->perpage, 'date'=>date('Ymd', strtotime($date))));
		if ($ret['code']) {
			$this->redirect(self::_getauthcodeUrl());
		}
		$total = $ret['taobaoke_report']['total_results'];
		$result = $ret['taobaoke_report']['taobaoke_report_members']['taobaoke_report_member'];
		if ($total == 1) {
			$result = array($result);
		}
		$this->assign('result', $result);
		$url = $this->actions['indexUrl'];
		$this->assign('date', $date);
		$this->assign('pager', $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url)));
	}
	
	public function bindAction() {
		$this->redirect(self::_getauthcodeUrl());
		exit;
	}
	
	/**
	 * 绑定淘宝账号获得code
	 */
	public static function _getauthcodeUrl() {
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
		$redirect_url = $adminroot.'/admin/report/getauthtoken';
		$topApi = new Api_Top_Service();
		return $topApi->getAuthUrl($redirect_url);
	}
	
	/**
	 * get access_token
	 */
	public function getauthtokenAction() {
		$code = $this->getInput("code");
		$user_info = $this->userInfo;
		$redirect_url = Yaf_Application::app()->getConfig()->adminroot;
		$topApi = new Api_Top_Service();
		$result = $topApi->getAuthToken($code, $redirect_url);
		$ret = json_decode($result,true);
		Gc_Service_Config::setValue('GOU_ADMIN_TOKEN', $ret['access_token']);
		$this->redirect($webroot.'/admin/report/index');
	
	}
}
