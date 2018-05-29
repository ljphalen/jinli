<?php
/**
 * 用户帐户的逻辑类
 *
 * @name AccountModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountsClient extends AccountsModel {
	private static $daoObj;
	private static $expire = 2592000; // 自动登录保存30天
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Accounts" );
		}
		return self::$daoObj;
	}
	public $_validate = array (
			array (
					'account',
					'require',
					'帐户必须' 
			),
			array (
					'email',
					'email',
					'邮件格式错误' 
			),
			array (
					'password',
					'require',
					'密码必须' 
			),
			array (
					'repassword',
					'require',
					'确认密码必须' 
			),
			array (
					'repassword',
					'password',
					'确认密码不一致',
					self::EXISTS_VALIDATE,
					'confirm' 
			),
			array (
					'account',
					'',
					'帐号已经存在',
					Model::EXISTS_VALIDATE,
					'unique',
					self::MODEL_INSERT 
			) 
	);
	
	// public $_auto = array (array ('created_at', 'time', self::MODEL_INSERT, 'function' ), array ('updated_at', 'time', self::MODEL_UPDATE, 'function' ) );
	
	// 字段映射
	protected $_map = array (
			'account' => 'login',
			'password' => 'crypted_password',
			'repassword' => 'crypted_password' 
	);
	
	/**
	 * 登录验证
	 * 
	 * @param string $username        	
	 * @param string $password        	
	 * @return number multitype:number
	 */
	public static function login($email, $password) {
		$account = self::checkEmail($email);
		if (empty ( $account )) {
			return 405; // 用户不存在
		} else {
			$sha1 = AuthCodeClient::makepass ( $password, $account ['salt'] );
			$auth = ($account ['crypted_password'] === $sha1) ? true : false;
		}
		if (! $auth) {
			return 406; // 密码错误
		} else {
			return array (
					200,
					$account 
			); // 登录成功
		}
	}
	
	/**
	 * 设置cookie和session
	 * 
	 * @param array $account        	
	 * @param boolean $rememberMe        	
	 * @param boolean $autoLogin        	
	 */
	public static function saveCookieSession($account = array(), $rememberMe = false, $autoLogin = false) {
		$uid = $account ['id'];
		$account ['email'];
		$login = $account ['login'];
		
		import ( "@.Client.SessionClient" );
		$ip = get_client_ip();
		$expire = ! $autoLogin ? 0 : (time () + self::$expire);
		$aSessionKey = SessionClient::createSessionC ( $uid, $ip, $expire );
		if ($aSessionKey) {
			import ( "@.Client.AuthClient" );
			AuthClient::setAuthedCookie ( $uid, $aSessionKey, $rememberMe, $autoLogin, $login );
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 检查用户是否登录
	 */
	public static function checkAuth() {
		import ( "@.Client.AuthClient" );
		$uid = AuthClient::getAuthedUid ();
		if ($uid) { // && isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )
			return $uid;
		}
		return false;
	}
	
	/**
	 * 检测用户信息
	 * @param int $uid
	 */
	public static function checkUser($uid)
	{
		if (empty($uid)) return false;

		$account = self::getInfoById ( $uid );
		$account_info = D('Accountinfo')->getAccInfo( $uid );
		
		$user = array(
			'id' => $account['id'],
			'email' => $account['email'],
			'nickname' => $account['nickname'],
			'status' => $account_info['status'],
			'created_at' => $account['created_at'],
			'contact' => $account_info['contact'],
			'company'=> $account_info['company'],
			'phone' => $account_info['phone'],
			'contact_email' => $account_info['contact_email'],
			'info_status' =>  $account_info['status'],
		);
		
		//一些实时检测,有问题时，返回false
		
		//返回用户信息
		return $user;
	}
	
	// 用户登出
	public static function logout() {
		import ( "@.Client.AuthClient" );
		import ( "@.Client.SessionClient" );
		$uid = AuthClient::getAuthedUid ();
		if ($uid) {
			$sessionkey = AuthClient::getAuthedCookieSession ();
			SessionClient::deleteSessionC ( $sessionkey );
		}
		AuthClient::unsetAuthedCookie (); // 删除系统的登录Cookie
		
		if ($uid) {
			return true;
		} else {
			return false;
		}
	}
	
	// 用户激活
	public static function activate($activeCode) {
		$data ['activation_code'] = null;
		$data ['activated_at'] = date ( 'Y-m-d H:i:s' );
		// $data ['status'] = 1;
		return self::dao ()->where ( "activation_code='$activeCode'" )->save ( $data );
	}
	
	//发送激活邮件
	public static function sendActiveEmail($sendemail, $activeCode) {
		$subject = '【'.C("SMTP.SMTP_NAME").'】邮箱验证';
		$activeLink = "http://" . C ( "SITE_DEV_DOMAIN" ) . "/activate/index/ac/" . $activeCode;
		$body = '亲爱的开发者，您好：<br>
                 欢迎您注册金立游戏开发者平台，请点击下面链接完成邮箱验证：<br>
                 <a href="' . $activeLink . '">' . $activeLink . '</a><br />
                 	(链接24小时内有效，如果链接无法点击，请将它复制并粘贴到浏览器的地址栏中访问) <br>
					您在金立游戏开发者平台的注册帐户: '.$sendemail.'，请妥善保管，避免被他人盗用。<br>
				    祝您	使用愉快！';

		return smtp_mail ( $sendemail, $subject, $body );
	}
	
	// 修改邮箱之后的验证邮件
	public static function sendVerifyEmail($sendemail, $activeCode) {
		$subject = '【'.C("SMTP.SMTP_NAME").'】重置密码';
		$activeLink = "http://" . C ( "SITE_DEV_DOMAIN" ) . "/activate/index/ac/" . $activeCode;
		
		$body = '亲爱的开发者，您好：<br>
				您申请重置金立游戏开发者平台帐户密码，请点击 <a href="' . $activeLink . '">' . $activeLink . '</a><br />
				(链接24小时内有效，如果链接无法点击，请将它复制并粘贴到浏览器的地址栏中访问)<br>
				祝您使用愉快！';
		return smtp_mail ( $sendemail, $subject, $body );
	}
	
	/**
	 * 发送重置密码邮件
	 * 
	 * @param string $nickname        	
	 * @param string $sendemail        	
	 * @param string $code        	
	 * @return number
	 */
	public static function sendRepwdEmail($nickname, $sendemail, $code) {
		$subject = '【'.C("SMTP.SMTP_NAME").'】重置密码!';
		$repwdLink = "http://" . C ( "SITE_DEV_DOMAIN" ) . "/auth/uppwd/code/$code";
		$repwdLink = sprintf('<a href="%s" target="_blank">%s</a>', $repwdLink, $repwdLink);
		$body = '尊敬的 ' . $nickname . '，您好：<br><br>

					此电子邮件旨在帮助您更新密码。<br>
					要创建新密码，请单击下面的链接。<br><br>
					
					（链接如不能点击，请将该链接复制并粘贴到浏览器的地址栏中访问）：<br>
					' . $repwdLink . '<br>
					该链接仅在 24 小时内有效。<br><br>
					
					谨此致意 <br><br>
					
					支持中心<br>';
		return smtp_mail ( $sendemail, $subject, $body );
	}
	public static function getInfoById($aid) {
		return self::dao ()->getAccountById ( $aid );
	}
	public static function checkAccount($account) {
		return self::dao ()->getUserByName ( $account );
	}
	public static function checkEmail($email) {
		return self::dao ()->getUserByEmail ( $email );
	}
	public static function checkByNamePwd($username, $password) {
		return self::dao ()->getUserByNamePwd ( $username, $password );
	}
	public static function checkByUidPwd($aid, $pwd) {
		return self::dao ()->getUserByUidPwd ( $aid, $pwd );
	}
	public static function checkByActivateCode($acode) {
		return self::dao ()->getInfoByActiveCode ( $acode );
	}
	public static function upPwd($uid, $password) {
		return self::dao ()->updatePwd ( $uid, $password );
	}
	public static function upEmail($uid, $email) {
		return self::dao ()->updateEmail ( $uid, $email );
	}
	public static function upAccounts($uid, $data) {
		return self::dao ()->updateAccounts ( $uid, $data );
	}
	public static function addAccount($data) {
		return self::dao ()->createAccount ( $data );
	}
}
?>