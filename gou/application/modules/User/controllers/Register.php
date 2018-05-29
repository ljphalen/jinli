<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 会员注册
 * @author tiansh
 *
 */
class RegisterController extends User_BaseController {
	
	public $actions = array(
		'indexUrl' => '/user/register/index',
		'postUrl' => '/user/register/post',
	);
	
	/**
	 *
	 * init
	 */
	public function init() {
		parent::init();
		//已经登录
		$webroot = Common::getWebRoot();
		if (Gou_Service_User::isLogin()){
			$this->redirect($webroot.'/user/account/index');
		}
	}
	
	/**
	 * 
	 * 注册
	 */
	public function indexAction() {
		//来源
		$refer = $this->getInput('refer');
		$this->assign('refer',$refer);
	}
	
	/**
	 * 
	 * 提交
	 */
	public function postAction() {
		$info = $this->getPost(array('username','password'));
		$refer = $this->getInput('refer');
		$data = $this->_cookData($info);
		
		if (Common::isError($data)) {
			$this->output($data['code'], $data['msg']);
		}
		$ret = Gou_Service_User::addUser($info);
		
		//cookie
		$user = Gou_Service_User::getUserByName($info['username']);
		Gou_Service_User::cookieUser($user);
		
		$webroot = Common::getWebRoot();
		$url = $refer ? $refer : $webroot.'/user/account/index';
		
		if (!$ret) $this->output(-1, '注册失败.');
		$this->output(0, '注册成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	private function _cookData($data) {
		if (!preg_match('/^1[3458]\d{9}$/', $data['username'])) $this->output(-1, "手机号码格式不正确");
		if (strlen($data['password']) < 6 || strlen($data['password']) > 16) $this->output(-1, '用户密码长度6-16位之间.');
		if (Gou_Service_User::getUserByName($data['username'])) $this->output(-1, '此手机号码已经注册.');		
		return $data;
	}
}