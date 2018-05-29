<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 账号设置，目前没有会员中心首页，暂以账户设置为会员中心首页
 * @author tiansh
 *
 */
class IndexController extends Front_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/index/index',
		'userinfoUrl' => '/user/index/userinfo',
		'userinfoPostUrl' => '/user/index/userinfo_post',
		'getauthcodeUrl' => '/user/index/getauthcode',
		'getauthtokenUrl' => '/user/index/getauthtoken',
		'bindUrl' => '/user/index/bind',
		'addconsigneeUrl'=> '/user/consignee/add',
		'editconsigneeUrl'=> '/user/consignee/edit',
	);
	
	public $sex = array(
			1 => '女',
			2 => '男'
	);
	
	/**
	 * 
	 * 首页
	 */
	public function indexAction() {	
		//收货地址
 		$address = Gc_Service_UserAddress::getListByUserId($this->userInfo['id']);
 		$this->assign('address',$address);
 		$this->assign('sex',$this->sex);
 		$this->assign('user_info',$this->userInfo);
 		
	}
	
	public function gainAction() {
		if($this->userInfo['isgain']) $this->redirect('/user/index/index'); 
	}
	
	public function gain_postAction() {
		$title = "银币领取";
		if (!$this->userInfo) $this->output(-1, '用户信息获取失败.');
		if ($this->userInfo['isgain']) $this->output(-1, '您已经领取过了，不能重新领取了.');
		$ret = Gc_Service_User::gain($this->userInfo['out_uid'], $this->userInfo['id']);
		if (!$ret) $this->output(-1, '领取银币失败.');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->output(0, '恭喜您，领取成功.', array('type'=>'redirect','url'=>$webroot.'/user/account/index'));
		$this->assign('title', $title);
	}
	
	/**
	 * 编辑基本资料
	 */
	public function userinfoAction() {
		//生日年份
		$year_list = array();
		$month_list = array();
		$day_list = array();
		for ($i = 1960; $i <= date('Y', Common::getTime()); $i++){
			$year_list[] = $i;
		}
		for ($i = 1; $i <= 12; $i++){
			if ($i< 10) $i = '0'.$i;
			$month_list[] = $i;
		}
		for ($i = 1; $i <= 31; $i++){
			if ($i< 10) $i = '0'.$i;
			$day_list[] = $i;
		}
		
		$user_info = $this->userInfo;
		list($year, $month, $day) = explode('-', $user_info['birthday']);
		
		$this->assign('year_list', $year_list);
		$this->assign('month_list', $month_list);
		$this->assign('day_list', $day_list);
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('user_info',$this->userInfo);
	}
	
	/**
	 * 编辑基本资料 post
	 */
	public function userinfo_postAction() {
		$user_info = $this->userInfo;
		$info = $this->getPost(array('realname', 'sex'));
		$birthday = $this->getPost(array('year','month','day'));
		$info['birthday']  = $birthday['year'] . '-' . $birthday['month'] . '-' . $birthday['day'];
		$result = Gc_Service_User::updateUser($info, $user_info['id']);
		if (!$result) $this->output(-1, '资料修改失败.');
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$this->output(0, '资料修改成功.', array('type'=>'redirect', 'url'=>$webroot.'/user/index/index'));
	}
	
	/**
	 * 绑定淘宝账号获得code
	 */
	public function getauthcodeAction() {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$redirect_url = $webroot.'/user/index/getauthtoken';
		$topApi = new Api_Top_Service();
		$url = $topApi->getAuthUrl($redirect_url);
		$this->redirect($url);
	}
	
	/**
	 * get access_token
	 */
	public function getauthtokenAction() {
		$code = $this->getInput("code");
		$user_info = $this->userInfo;
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$redirect_url = $webroot.'/user/index/bind';
		$topApi = new Api_Top_Service();
		$result = $topApi->getAuthToken($code, $redirect_url);
		$ret = json_decode($result,true);
		$data = array(
				'taobao_nick'=>$ret['taobao_user_nick'],
				'taobao_session'=>$ret['access_token'],
				'taobao_refresh'=>$ret['refresh_token'],
				'taobao_mobile_token'=>$ret['mobile_token'],
				'taobao_refresh_time'=>Common::getTime() + $ret['expires_in'],
				'taobao_refresh_expires'=>$ret['re_expires_in']
		);
		Gc_Service_User::updateUser($data, $user_info['id']);
		$this->redirect($webroot.'/user/index/index');
		
	}
}