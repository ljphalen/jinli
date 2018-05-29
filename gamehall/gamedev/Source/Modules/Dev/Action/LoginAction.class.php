<?php
/**
 * 用户登录控制类
 *
 * @name LoginAction.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class LoginAction extends BaseAction {
	private $saveAccount;
	private $autoLogin;
	private $firstPage;
	private $loginPage;
	
	function _initialize() {
		loadClient ( array (
				"Accounts",
				"AuthCode",
				"Accountinfo",
				"Infotemp",
				"Accountauth",
				"Auth",
				"Announcement",
		) );
		$this->firstPage = "/Apps/index";
		$this->loginPage = "/Login/login";
		$this->announcement = AnnouncementClient::getAnnouncement();
		
		$this->assign ( 'announcement', $this->announcement );
	}
	
	function _filter(&$map) {
		$map ['id'] = array ( 'egt', 2 );
		$account = $this->_post ( "account", "trim", "" );
		$map ['account'] = array ( 'like', "%" . $account . "%" );
	}
	
	// 用户登录
	public function login() {
		
		// 检查是否已经登录
		$uid = AccountsClient::checkAuth ();
		$autoLogin = AuthClient::getCookieAutoLogin ();
		
		$userinfo = $uid > 0 ? D('Accountinfo')->getAccInfo($uid) : array();
		
		//读取新闻列表
		$this->article = D("Article")->where(array("category"=>7, "status"=>1))->field('id,title')->order('id desc')->limit(5)->select();
		//读取首页封面
		$this->imglist = D("SettingImage")->where(array("start_time"=>array("lt", time()), "end_time"=>array("gt", time()), "status"=>1))
									->order(array("sort"=>"desc"))
									->getField("id,image,url", true);

		$presist = AuthClient::getCookiePersist ();
		$email = cookie("email");
		$this->assign("email", $email);

		$login = $presist ? cookie ( 'login' ) : '';
		$this->assign ( "saveAccount", $presist );
		$this->assign ( "login", $login );
		$this->assign ( "autoLogin", $autoLogin );
		
		$this->assign ( "uid", $uid);
		$this->assign ( "userinfo", $userinfo);
		$this->display( "Login:login" );
	}
	
	// 登录提交信息
	public function loginsub() {
		$authcode = $this->_post ( "authcode", "trim", "" );
		//前台进行JS加密
		$encrypted = $this->_post ( "encrypted", "trim", "" );
		if(!empty($encrypted)) {
			$post = $this->authcode_mcrypt_decode($encrypted, $authcode);
			if(empty($post))
				$this->error ('请刷新页面重试');
			parse_str($post, $_POST);
		}

		$email = $this->_post ( "email", "trim", "" );
		$password = $this->_post ( "password", "trim", "" );
		$saveAccount = $this->_post ( "saveAccount", "trim", "" );
		$autoLogin = $this->_post ( "autoLogin", "trim", "" );
		
		if (empty ( $email )) {
			$this->error ( '请输入邮箱！' );
		} elseif (empty ( $password )) {
			$this->error ( '请输入密码！' );
		} elseif (empty ( $_SESSION ['verify'] ) || $_SESSION ['verify'] != md5 ( strtoupper ( $authcode ) )) {
			$_SESSION ['verify'] = "";
			$this->error ( '验证码错误！' );
		}
		
		// 是否保存用户名
		$this->saveAccount = $saveAccount == "Y" ? true : false;
		// 是否自动登录
		$this->autoLogin = $autoLogin == 'Y' ? true : false;
		
		$this->_checkLogin ( $email, $password );
	}
	
	// 用户退出
	public function logout() {
		$logout = AccountsClient::logout ();
		$this->assign ( "jumpUrl", $this->loginPage );
		$this->redirect ( $this->loginPage );
	}
	
	// 开发者注册
	public function reg() {
		if (C ( 'ClOSE_REG' )) {
			$this->assign ( "jumpUrl", $this->loginPage );
			$this->error ( "由于系统升级，注册已经关闭，现转到登录页" );
		}
		
		$this->display ();
	}
	
	// 注册信息提交
	public function regsub() {
		$authcode = $this->_post ( "authcode", "trim", "" );
		//前台进行JS加密
		$encrypted = $this->_post ( "encrypted", "trim", "" );
		if(!empty($encrypted)) {
			$post = $this->authcode_mcrypt_decode($encrypted, $authcode);
			if(empty($post))
				$this->error ('请刷新页面重试');
			parse_str($post, $_POST);
		}
		
		$email = $this->_post ( "email", "trim", "" );
		$password = $this->_post ( "password", "trim", "" );
		$email = $this->_checkEmail ($email);

		if (empty ( $_SESSION ['verify'] ) || $_SESSION ['verify'] != md5 ( strtoupper ( $authcode ) )) {
			$this->error ( '验证码错误！' );
		}
		// 数据校验验证
		if (! $email) {
			$this->error ( "邮件地址不合法" );
		}
		
		if (AccountsClient::checkEmail ( $email )) {
			$this->error ( '该email已经存在！' );
		}
		
		$data ['salt'] = $salt = sha1 ( microtime ( true ) );
		$data ['crypted_password'] = AuthCodeClient::makepass ( $password, $salt );
		$data ['email'] = $email;
		$data ['created_at'] = date ( 'Y-m-d H:i:s' );
		$data ['activation_code'] = $activeCode = activeCode ();
		// 写入帐号数据
		$authId = AccountsClient::addAccount ( $data );
		if ($authId) {
			$account ['account_id'] = $authId;
			cookie ( 'authId', $authId );
			$account ['status'] = "-1"; // 未提交资料
			$res = AccountinfoClient::createAccInfo ( $account );
			if ($res) {
				// 发送激活邮件
				$sendEmail = AccountsClient::sendActiveEmail ( $email, $activeCode );
				
				// 获取基本身份的权值
				$account ['audited'] = $sign = 1;
// 				$account ['status'] = "0"; // 提交审核状态
				                           
				// 更新开发者身份
				$data ['audited'] = $account ['audited'];
				AccountsClient::upAccounts ( $authId, $data );
				
				$this->success ( '注册成功', U("Login/confirm", array ('email' => $email)) );
			}
		}
		$this->error ( '注册失败！' );
	}
	
	// 完善资料
	public function perfect()
	{
		$uid = AccountsClient::checkAuth ();
		if (! $uid) {
			$this->redirect( $this->loginPage );
			exit;
		}
		
		$is_perfect = D ( 'Accountinfo' )->isPerfect ( $uid );
		if (!APP_DEBUG && $is_perfect)
		{
			//已经完善过资料将不允许再完善资料
			header ( 'Location: ' . $this->firstPage );
			exit;
		}
		
		$authInfo = AccountsClient::getInfoById ( $uid );
		$this->assign ( "email", $authInfo ['email'] );
		$this->display ();
	}
	
	/**
	 * 短信验证码发送
	 */
	public function authcode()
	{
		$mobile = $this->_post('mobile');
		$authId = cookie ( 'authId' );
		if (empty($authId) || empty($mobile))
		{
			$this->error ( "非法参数！" );
		}
		
		//生成短信码
		import('ORG.Util.String');
		$code = String::randString(6,1);
		
		$content = '您的短信验证码为： '.$code.' ';
		$send_res = helper('Sms')->send($mobile,$content);
		if ($send_res > 0)
		{
			$_SESSION['perfect_code'] = $code;
			$this->success('获取验证码成功');
		}else 
		{
			$this->error('获取验证码失败，请重试');
		}
	}
	
	// 完善信息提交
	public function prefectsub()
	{
		$authId = AccountsClient::checkAuth ();
		
		if (! $authId)
		{
			$this->redirect( $this->loginPage );
			exit;
		}
		
		$is_perfect = D ( 'Accountinfo' )->isPerfect ( $authId );
		if (!APP_DEBUG && $is_perfect)
		{
			//已经完善过资料将不允许再完善资料
			header ( 'Location: ' . $this->firstPage );
			exit;
		}
		
		$account = $this->_post ( "account", "", "" );
		// 数据校验验证
		if (! is_array ( $account )) {
			$this->error ( "非法参数！" );
		}
		
		if ( !SmslogModel::checkCode($account['phone'], $account['authcode'],SmslogModel::SMS_MODULE_PERFECT))
			return $this->error('短信验证码错误!');
		
		// 写入帐号数据
		if (! empty ( $authId )) {
			$account ['account_id'] = $authId;
			AccountsClient::upAccounts ( $authId, array (
					'nickname' => $account ['nickname'] 
			) );
			unset ( $account ['nickname'] );
			unset ( $account ['authcode'] );
			$account ['status'] = "0"; // 提交资料待审核
			$res = AccountinfoClient::upAccInfo ( $authId, $account );
			if ($res) {
				//file size
				if ($_FILES['company_passport']['size'] > 1024*1024)
				{
					$this->error('营业执照扫描件大于1M');
				}
				
				if ($_FILES['tax_passport']['size'] > 2*1024*1024)
				{
					$this->error('税务登记证扫描件大于2M');
				}
				
				if ($_FILES['tax_passport']['size'] > 2*1024*1024)
				{
					$this->error('税务登记证扫描件大于2M');
				}
				
				// 上传营业执照和税务登录扫描件
				$uploadSetting = array(
						'maxSize'	=>2*1024*1024,           					//文件大小限制
						'allowExts'	=>array('jpg','gif','png','jpeg'),	//文件类型设置
						'isWater'	=>false        						//是否加水印
				);
				$uploadList = helper ( "Upload" )->_upload ("user", $uploadSetting);
				if (is_array ( $uploadList[0] )){
					foreach ($uploadList as $file)
					$data[$file['key']] = $file['filepath'];
				}
				elseif ($_FILES['company_passport']['name']){
					$this->error($uploadList);
				}
				
// 				$account ['status'] = "0"; // 提交审核状态
				AccountinfoClient::upAccInfo ( $authId, $data );
				
				//完善税务信息附加表
				if(!empty($account['tax_number']) && !empty($data['tax_passport']))
				{
					$tax["status"] = 0;
					$tax["account_id"] = $authId;
					$tax["tax_number"] = $account['tax_number'];
					$tax["tax_passport"] = $data['tax_passport'];
					D("Dev://AccountTax")->save_tax($tax);
				}
				
				cookie ( 'authId', null );
				
				$this->assign ( "jumpUrl", $this->firstPage );
				$this->display ( "success" );
				die;
			}
		}
		$this->error ( '提交资料失败！' );
	}
	
	// 重新发送激活邮件
	public function resend() {
		$authId = $this->_get ( "authId", "intval", 0 );
		if (! is_int ( $authId )) {
			$this->error ( "非法参数！" );
		}
		$email = $this->_checkEmail ();
		if (! $email) {
			$this->error ( "邮件地址不合法" );
		}
		$accInfo = ! empty ( $email ) ? AccountsClient::checkEmail ( $email ) : AccountsClient::getInfoById ( $authId );
		if ($accInfo) {
			$data ['activation_code'] = $activeCode = activeCode ();
			// 发送激活邮件
			$sendEmail = AccountsClient::sendActiveEmail ( $accInfo ['email'], $activeCode );
			if (! $sendEmail) {
				$this->error ( '发送邮件失败！' );
			} else {
				// 修改激活码
				$data ['activated_at'] = null;
				$upres = AccountsClient::upAccounts ( $accInfo ['id'], $data );
				if (! $upres) {
					$this->error ( '更新激活码失败！' );
				} else {
					if (isset ( $_REQUEST ['jumpUrl'] ) && $_REQUEST ['jumpUrl']) {
						$this->assign ( "jumpUrl", '/Index/' . $_REQUEST ['jumpUrl'] );
					} else {
						$this->assign ( "jumpUrl", "/Login/confirm/email/" . $accInfo ['email'] );
					}
					$this->success ( "重新发送激活邮件成功,请登录邮箱验证" );
				}
			}
		}
	}
	public function checkEmail() {
		$email = $this->_checkEmail ();
		if (! $email) {
			$this->error ( "邮件地址不合法" );
		}
		$checkEmail = AccountsClient::checkEmail ( $email );
		if ($checkEmail) {
			$this->error ( '200' );
		} else {
			$this->success ( '404' );
		}
	}
	
	// 生成验证码
	public function verify() {
		$type = $this->_get ( "type", "trim", "gif" );
		import ( "ORG.Util.Image" );
		Image::buildImageVerify ( 4, 1, $type );
	}
	
	public function verify_check() {
		echo $_SESSION ['verify'] == md5($_POST["verify"]) ? '{"status":"ok"}' : '{"status":"fail"}';
	}
	
	public function confirm() {
		$email = $this->_checkEmail ();
		if (! $email) {
			$this->error ( "邮件地址不合法" );
		}
		$account = AccountsClient::checkEmail ( $email );
		if (! $account) {
			$this->error ( "email地址错误" );
		}
		// 验证是否已经激活
		if (empty ( $account ['activation_code'] )) {
			$this->assign ( "jumpUrl", $this->loginPage );
			$this->error ( "该帐户已经激活" );
		}
		$mail_list = array(
		'163.com' =>'mail.163.com',
		'sina.com.cn' => 'mail.sina.com.cn',
		'sohu.com' => 'mail.sohu.com',
		'tom.com' => 'mail.tom.com',
		'sogou.com' => 'mail.sogou.com',
		'126.com' => 'www.126.com',
		'10086.cn' => 'mail.10086.cn',
		'google.com' => 'gmail.google.com',
		'hotmail.com' => 'www.hotmail.com',
		'189.cn' => 'www.189.cn',
		'qq.com' => 'mail.qq.com',
		'foxmail.com' => 'www.foxmail.com',
		'263.net' => 'www.263.net',
		);
		$pieces = explode ( "@", $email );
		$validateEmail = $mail_list[$pieces[1]];
		$this->assign ( "email", $email );
		$this->assign ( "validateEamil", $validateEmail );
		$this->display ();
	}
	function _checkLogin($email, $password) {
		$res = AccountsClient::login ( $email, $password );
		if ($res === 406) {
			$this->error ( "用户名和密码不匹配！" );
		} elseif ($res === 405) {
			$this->error ( "用户名和密码不匹配！" );
		} elseif (is_array ( $res ) && $res [0] === 200) {
			// 获取用户登录ID
			$account = $res [1];
			$accountId = $account ['id'];
			
			// 验证用户是否已经激活
			if (! empty ( $account ['activation_code'] ) || empty( $account ['activated_at'] )) {
				$data ['jumpUrl'] = "/Login/confirm/email/" . $res [1] ['email'];
				$this->ajaxReturn ( $data, "noActived", 0 );
			}

			if ($res [1] ['status'] == "1") {
				$info = AccountinfoClient::fetchAccInfo ( $accountId );
				
				$error = false;
				$msg = "";
				$jumpUrl = $this->firstPage;
				// 记录当前状态
				$_SESSION ['current_status'] = $info ['status'];
				if ($info ['status'] == "-1") { // 当前为编辑状态
					$msg = "您还没有将开发者资料提交审核，请提交！";
				} elseif ($info ['status'] == "-2") { // 当前为封号状态
					$error = true;
					$jumpUrl = $this->loginPage;
					$msg = "您的帐户已经停用，请联系dev.game@gionee.com处理";
				} elseif ($info ['status'] == "1") { // 当前为审核未通过状态
					$jumpUrl = U('User/edit');
					$msg = "您的审核未通过，请及时修改您的开发者信息！";
				} elseif ($info ['status'] == "0" || $info ['status'] == "2") { // 未审核也可以登录
					
					if ($this->saveAccount) {
						cookie("email",$email);
					}else {
						cookie("email", null);
					}
					$msg = "登录成功！";
				} else {
					$error = true;
					$jumpUrl = $this->loginPage;
					$msg = "登录失败，未知原因！";
				}
				if (! $error) {
					$res = AccountsClient::saveCookieSession ( $account, $this->saveAccount, $this->autoLogin );
					if (! $res) {
						$error = true;
						$jumpUrl = '/Login/logout';
						$msg = "登录异常，请联系dev.game@gionee.com处理";
					}
					
					// 检查开发者资料审核是否通过
					$is_perfect = D('Accountinfo')->isPerfect($info);
					
					if (!$is_perfect) {
						cookie ( 'authId', $accountId );
						$data ['msg'] = "请完善您的基本信息";
						$data ['jumpUrl'] = "/Login/perfect/";
						$this->ajaxReturn ( $data, "perfect", 0 );
					}
				}
				
				$this->assign ( 'jumpUrl', $jumpUrl );
				$error ? $this->error ( $msg ) : $this->success ( $msg );
			} elseif ($res [1] ['status'] == "0") { // 当前为封号状态
				$this->assign ( 'jumpUrl', $this->loginPage );
				$this->error ( "您的帐户已经停用，请联系dev.game@gionee.com处理" );
			}
		} else {
			$this->error ( "登录失败！" );
		}
	}
	
	/**
	 * 验证Email地址
	 */
	function _checkEmail($email=null) {
		$email = isset($email) ? $email : $this->_request ( "email", "trim", "" );
		if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
			return false;
		}
		return $email;
	}
	
	/**
	 * ajax获取用户登陆状态
	 */
	function login_status() {
		$uid = AccountsClient::checkAuth ();
		$uid = intval($uid);
		$this->ajaxReturn(array("uid"=>$uid));
	}
	
	//密码解密
	protected function authcode_mcrypt_decode($string, $key) {
		$key = md5($key);
		$prk = substr($key, 1^1, 16);
		$decode = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode(str_replace(" ", "+", $string)), MCRYPT_MODE_CBC, $prk);
		preg_match("@^(\d+)\|@is", $decode, $length);
		$post = substr($decode, strpos($decode, "|")+1, $length[1]);
		return $post;
	}
}
