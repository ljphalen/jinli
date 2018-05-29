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
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if (Gionee_Service_User::isLogin()){
			$this->redirect($webroot.'/user/index/signin');
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
		$this->assign('pageTitle','会员注册');
	}
	
	/**
	 * 
	 * 提交
	 */
	public function postAction() {
		$info = $this->getPost(array('username','password','passwd','tips'));
		
		if($info['tips']) {
			$info['password'] = $info['passwd'];
		}
		
		$refer = $this->getInput('refer');
		$data = $this->_cookData($info);
		
		if (Common::isError($data)) {
			$this->output($data['code'], $data['msg']);
		}
		$ret = Gionee_Service_User::addUser($info);
		
		//cookie
		$user = Gionee_Service_User::getUserByName($info['username']);
		Gionee_Service_User::cookieUser($user);
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$url = $refer ? $refer : $webroot.'/user/index/signin';
		
		if (!$ret) $this->output(-1, '注册失败.');
		$this->output(0, '注册成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	private function _cookData($data) {
		if (!preg_match('/^1[3458]\d{9}$/', $data['username'])) $this->output(-1, "用户名格式不对");
		if (strlen($data['password']) < 6 || strlen($data['password']) > 16) $this->output(-1, '用户密码长度6-16位之间.');
		if (Gionee_Service_User::getUserByName($data['username'])) $this->output(-1, '此号码已经注册过.');		
		return $data;
	}
}