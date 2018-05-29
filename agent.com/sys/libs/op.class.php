<?php
class op{
	
	private $userid = 0;
	private $config;
	
	function __construct(){
		global $config;
		$this->config = $config;
		//$_SESSION[$userid] = array();
	}
	
	public function checkLogin(){
		if (!empty($_SESSION[$this->userid])){
			if ($_SESSION[$this->userid]['status'] == 1){
				return true;
			}
		}
		return false;
	}
	
	
	public function Login($username,$passwd,$captcha,user &$user){
		/* echo $username.'<br>';
		echo $passwd.'<br>';
		echo $captcha.'<br>';
		var_dump($_SESSION);exit; */
		$param = array();
		if ($captcha===-1||isset($_SESSION['captchStr']) && $_SESSION['captchStr'] == $captcha){
			//数据库出来的密码和等级
			$passwd = md5($passwd.ENCRYPTKEY);
			$ret = $user->userLoginLog($username, $passwd, $_SERVER['REMOTE_ADDR']);
                       // var_dump($ret);exit;
			if (empty($ret)){
				return 1;
			}
			$passwddb = $ret[0][0]['userpass'];
			$level = $ret[0][0]['levels'];
			$agentid = $ret[0][0]['agentid'];
			$email = $ret[0][0]['email'];
			$nickname = $ret[0][0]['nickname'];
			$userid = $ret[0][0]['userid'];
			$username = $ret[0][0]['username'];
			$dateline = $ret[0][0]['dateline'];
			$logonip = $ret[0][0]['logonip'];
			
			$clientid = $ret[1][0]['clientid'];
			$clientids = $ret[1][0]['clientids'];
			$channeltype = $ret[1][0]['channeltype'];
                        $describe = $ret[1][0]['describe'];
                        $intoratio = $ret[1][0]['intoratio'];
			$name = $ret[1][0]['name'];
			
			$userinfo = array();
			$userinfo['username'] = $username;
			$userinfo['passwd'] = $passwddb;
			$userinfo['nickname'] = $nickname;
			$userinfo['email'] = $email;
			$userinfo['level'] = $level;
			$userinfo['agentid'] = $agentid;
			$userinfo['userid'] = $userid;
			$userinfo['dateline'] = $dateline;
			$userinfo['logonip'] = $logonip;
			
			$userinfo['clientid'] = $clientid;
			$userinfo['clientids'] = $clientids;
			$userinfo['channeltype'] = $channeltype;
                        $userinfo['intoratio'] = $intoratio;
			$userinfo['name'] = $name;
                        $userinfo['describe'] = $describe;
			$_SESSION['userinfo'] = $userinfo;
			
			//记录登陆时间等等，写登陆日志
			//writeLog(__FUNCTION__, 'ip='.$_SERVER['SERVER_ADDR'], arrayToXml($userinfo));
			//写数据库日志
			return 0;

		}
		return 2;
	}
	
	
	public function Logout(){
		session_destroy();
		return true;
	}
	
	/**
	 * 检查权限
	 * @param unknown_type $name
	 */
	public function check_prvi($name){
		do{
			if (empty($_SESSION['userinfo']))return false;
			if (!isset($config['priv_page'][$name]))return true;
			if (in_array($_SESSION['userinfo']['level'], $this->config['priv_page'][$name]))return false;
		}while(0);
		return true;
	}
	
	/**
	 * 加载菜单
	 */
	public function loadmenu(){
		foreach ($this->config['menu_not_show'] as $key=>$var){
			if (!empty($_SESSION['userinfo']['level']) && in_array($_SESSION['userinfo']['level'], $var)){
				unset($config['menu'][$key]);
			}
		}
	}
	
	
	/**
	 * 绑定邮箱
	 * @param unknown_type $userid
	 * @param unknown_type $passwd
	 * @param unknown_type $email
	 */
	function bindMail($userid,$email,user $user){
            $code = md5(rand());
            $user->validationAdd($userid, $code);
            $url = CHANNELHOST;
            $url .='/index.php?ac=check_email&userid='.$userid.'&mail='.urlencode($email).'&code='.$code.'&sign='.md5($userid.$email.$code.ENCRYPTKEY);
            $msg .= '<a href="'.$url.'" target="_blank" >点击验证邮箱</a>';
            $msg .= MAILWARNING;
            $msg .= $url.MAILBODYTAIL;
            
            $user->sendMail($msg, $email);       

	}
	
	function echoBineMail(user $user){
		$userid = $_GET['userid'];
		$email = $_GET['email'];
		$sign = $_GET['sign'];
		//检查邮件格式
		if (md5($userid.ENCRYPTKEY) != $sign){
			;//提示绑定失败
		}else{
			$user->editUserMail($userid,$email);
		}
	}
	
	/**
	 * 修改密码
	 * @param unknown_type $userid
	 * @param unknown_type $oldPasswd
	 * @param unknown_type $newPasswd
	 */
	function editPasswd($userid,$oldPasswd,$newPasswd,user &$user){
		$oldPasswd = md5($oldPasswd.ENCRYPTKEY);
		$newPasswd = md5($newPasswd.ENCRYPTKEY);
		$info = $_SESSION['userinfo'];
		if ($oldPasswd != $_SESSION['userinfo']['passwd']){
			return 4;
		}
		//走存储过程
		$ret = $user->editUser($userid, $newPasswd, $_SESSION['userinfo']['nickname'], $_SESSION['userinfo']['email']);
		if ($ret[0]['status'] == 0){
			$_SESSION['userinfo']['passwd'] = $newPasswd;
			//writeLog(__FUNCTION__, arrayToXml($info), arrayToXml($_SESSION['userinfo']));
			return 8;
		}else{
			return 5;
		}
	}
	
	/**
	 * 修改名称
	 * @param unknown_type $name
	 */
	function editPersonal($name,user &$user){
		$info = $_SESSION['userinfo'];
		$ret = $user->editUser($_SESSION['userinfo']['userid'], $_SESSION['userinfo']['passwd'], $name, $_SESSION['userinfo']['email']);
		if ($ret[0]['status'] == 0){
			$_SESSION['userinfo']['nickname'] = $name;
			//writeLog(__FUNCTION__, arrayToXml($info), arrayToXml($_SESSION['userinfo']));
			return 7;
		}else{
			return 6;
		}
	}
	
	
	/**
	 * 增加二，三级渠道商
	 * @param user $user
	 * @param company $company
	 */
	public function addSecondThirdCompany(company $company,$companyname,$phone,$mobile,$linkman,$address,$postcode,$intoratio,$clientid,$clientids,$describe,$channneltype){
		$info = array($companyname,$phone,$mobile,$linkman,$address,$postcode,$intoratio,$clientid,$clientids,$describe,$channneltype);
		$ret =$company->addCompany($companyname, $phone, $mobile, $linkman, $address, $postcode, $intoratio, $clientid, $clientids, $describe, $_SESSION['nickname'], $channneltype);
		//var_dump($ret);
		//writeLog(__FUNCTION__, arrayToXml($info), arrayToXml($_SESSION['userinfo']));
		if (!empty($ret[0]['id'])){
			return $ret[0]['id'];
		}else{
			return false;
		}
	}
	
	
	public function addUser($username,$passwd,$nickname,$email,$level,$clentid,$useable,user $user){
		$info = array($username,$passwd,$nickname,$email,$level,$clentid,$useable);
		$ret = $user->addUser($username, $passwd, $nickname, $email, $level, $clentid);
		//writeLog(__FUNCTION__, arrayToXml($info), arrayToXml($_SESSION['userinfo']));
                //var_dump($info);
		if (!empty($ret[0]['userid'])){
			return 11;
		}else{
			return 12;
		}
		
	}
        
        
        public function changeMail($userid,$mail,user $user){
            $info = array($userid,$mail);
		$ret = $user->addUser($username, $passwd, $nickname, $email, $level, $clentid);
		//writeLog(__FUNCTION__, arrayToXml($info), arrayToXml($_SESSION['userinfo']));
		if (!empty($ret[0]['userid'])){
			return 11;
		}else{
			return 12;
		}
        }
        
        
        public function getOneClient(){
            
        }
}