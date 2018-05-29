<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 账号设置
 * @author lichanghua
 *
 */
class SettingController extends Front_BaseController {
    
    public $actions = array(
		'indexUrl' => '/user/setting/index',
		'userinfoUrl' => '/user/setting/userinfo',
		'userinfoPostUrl' => '/user/setting/userinfo_post',
		'getauthcodeUrl' => '/user/setting/getauthcode',
		'getauthtokenUrl' => '/user/setting/getauthtoken',
		'bindUrl' => '/user/setting/bind',
		'editaddressUrl'=> '/user/address/edit',
    	'editaddressPostUrl'=> '/user/address/edit_post',
	);
    
    public $perpage = 20;
    
    public function init(){
    	parent::init();
    	$this->checkRight();
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function indexAction() {
		$this->assign('user', $this->userInfo);
		$title = "个人账号设置";
		$address = Gc_Service_UserAddress::getDefaultAddress($this->userInfo['id']);
		$this->assign('address', $address);
		$this->assign('title', $title);
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function userinfoAction() {
    	$title = "基本信息编辑";
    	$this->assign('user', $this->userInfo);
    	$this->assign('title', $title);
    }
    
    /**
     * 编辑基本资料 post
     */
    public function userinfo_postAction() {
    	$user_info = $this->userInfo;
    	//$info = $this->getPost(array('username', 'sex'));
    	$info['sex'] = $this->getPost('sex');
    	$info['realname'] = $this->getPost('username');
    	$birthday = $this->getPost(array('year','month','day'));
    	$info['birthday']  = $birthday['year'] . '-' . $birthday['month'] . '-' . $birthday['day'];
    	if(!checkdate($birthday['month'],$birthday['day'],$birthday['year'])) $this->output(-1, '输入的时间不正确，请输入正确的时间.');
    	$currTime = Common::getTime();
    	if($currTime - strtotime($info['birthday']) <= 0) $this->output(-1, '生日不能大于今天时间.');
    	$result = Gc_Service_User::updateUser($info, $user_info['id']);
    	if (!$result) $this->output(-1, '资料修改失败.');
    
    	$webroot = Yaf_Application::app()->getConfig()->webroot;
    	$this->output(0, '资料修改成功.', array('type'=>'redirect', 'url'=>$webroot.'/user/setting/index'));
    }
    
    /**
     *
     * Enter description here ...
     */
    public function editaddressAction() {
    	$id = $this->getInput('id');
    	$user_info = $this->userInfo;
    	$adress = Gc_Service_Order::getOrderAddress(intval($id));
    	$this->assign('user_info', $user_info);
    	$this->assign('adress', $adress);
    }
    
    /**
     *
     * Enter description here ...
     */
    public function editaddress_postAction() {
    	$id = $this->getInput('id');
    	$info['user_id'] = $this->userInfo['id'];
    	$info = $this->_cookData($info);
    	$adress = Gc_Service_Order::getOrderAddress(intval($id));
    	$this->assign('adress', $adress);
    }
    
    /**
     * 绑定淘宝账号获得code
     */
    public function getauthcodeAction() {
    	$webroot = Yaf_Application::app()->getConfig()->webroot;
    	$redirect_url = $webroot.'/user/setting/getauthtoken';
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
    	$this->redirect($webroot.'/user/setting/index');
    
    }
    
    private function _cookData($info) {
    	if(!$info['realname']) $this->output(-1, '收货人姓名不能为空.');
    	if(!$info['mobile']) $this->output(-1, '手机号码不能为空.');
    	if (!preg_match('/^1[3458]\d{9}$/', $info['mobile'])) $this->output(-1, "手机号码格式不正确");
    	if(!$info['province'] || !$info['city'] || !$info['country']) $this->output(-1, '请选择省市区.');
    	if(!$info['address']) $this->output(-1, '详细地址不能为空.');
    	
    	return $info;
    }
}
