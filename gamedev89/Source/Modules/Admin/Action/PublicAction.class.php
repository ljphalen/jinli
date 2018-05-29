<?php
import ('ORG.Util.Cookie');
class PublicAction extends Action
{
	public function ajaxAssign(&$result)
	{
		$result['statusCode']  =  $result['status'] == 1 ? 200 : $result['status'];
		$result['navTabId']  =  $_REQUEST['navTabId'];
		$result['callbackType']  =  $_REQUEST['callbackType'];
		$result['message'] =  $result['info'];
	}
	
    // 后台首页 查看系统信息
    public function main() {
    	!defined('SYS_VERSION') && include APP_PATH.'/version.php';
        $info = array(
        	'系统版本'=>SYS_VERSION,
			'最后更新'=>SYS_RELEASE,	
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION,
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON [建议关闭]" : "OFF [良好]",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO [良好]',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO [良好]',
        );
        $this->assign('info',$info);
        $this->display();
    }

	// 用户登录页面
	public function login()
	{
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
           	// $this->assign("jumpUrl",__URL__.'/login/');
            // $this->success('登出成功！');
            $this->redirect('Public/login');
        }else {
            $this->error('已经登出！');
        }
    }
	// 登录检测
	public function checkLogin()
	{
		if(($_SESSION['verify'] != md5($_POST['verify'])))
		{
			$this->change_verify();
			$this->error('验证码错误！');
		}
		
		$this->change_verify();
		if(empty($_POST['account']) || empty($_POST['password']))
		{
			$this->error('请正确填写您的登录帐号！');
		}
		
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['account']	= $_POST['account'];
        $map["status"]	=	array('gt',0);
		
		import ('ORG.Util.RBAC');
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != md5($_POST['password'])) {
            	$this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
            $_SESSION['loginUserName']		=	$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
            if($authInfo['account']=='admin') {
            	$_SESSION['administrator']		=	true;
            }
            //保存登录信息
			$User	=	D("Admin");
			$ip		=	get_client_ip();
			$time	=	time();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','login_count+1');
			$data['last_login_ip']	=	$ip;
			$User->save($data);
			
			// 缓存访问权限
            RBAC::saveAccessList();
            
            //记住登陆用户名
            Cookie::set('last_login', $authInfo['account']);

			//如果处于登陆状态，则弹出301超时提示
			if($this->isAjax())
				echo json_encode(array("message"=>"登陆成功", "statusCode"=>1, "callbackType"=>'closeCurrent'));
			else
				$this->display("loginSuccess");
		}
	}
    // 更换密码
    public function changePwd()
    {
		if(md5($_POST['verify'])!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   D("Admin");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
			$this->success('密码修改成功！');
         }
    }
    
	public function profile() {
		$User	 =	 D("Admin");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->display();
	}
	
	public function verify()
    {
        import('ORG.Util.Image');
        Image::buildImageVerify(4,1,'gif');
    }
	
	public function change_verify()
    {
        session('verify', md5(rand(1, 9999).microtime()));
    }

	public function change() {
		$User	 =	 D("Admin");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
}