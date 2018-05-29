<?php
/**
 *
 * 测试用例
 * @author shuhai
 *
 */
class TestAction extends Action
{
	function _initialize()
	{
		header('Content-Type: text/html; charset=utf-8');
	}

	function index()
	{
		$class  = new ReflectionClass($this);
		$method = $class->getMethods();
		foreach ($method as $action)
		{
			if(strpos($action, "test_") !== false)
			{
				printf("<h1><a href='%s'>%s</a></h1>", U("Test/{$action->name}"), $action->name);
				printf("<pre>%s</pre>", $action);
			}
		}
	}

	/**
	 * 测试AppKey通信
	 * 发送三次请求信息，两次相同的联运信息，一次不同的渠道
	 * 期待返回成功提示，app已经存在，成功信息
	 */
	function test_apikey_apply()
	{
		C('GSP_PAY_API', 'https://test3.gionee.com/pay-merchant');
		$data = array(
				"submit_time"		=> date("YmdHis"),
				"package_name"		=> "com.gionee.dev.test".mt_rand(1, time()),
				"company_name"		=> "金立公司",
				"app_name"			=> "金立游戏开发者中心测试环境应用, test" . date("Y-m-d H:i:s"),
				"type"				=> "单机",
				"channel"			=> "游戏大厅"
		);

		$result = Helper("ApiKey")->app_apply($data);
		dump($data);
		dump($result);
		$result = Helper("ApiKey")->app_apply($data);
		dump($data);
		dump($result);

		$data['channel'] = '其它渠道';
		$result = Helper("ApiKey")->app_apply($data);
		dump($data);
		dump($result);
	}

	/**
	 * 测试CURL获取接口是否正常，期待返回数组
	 */
	function test_curl()
	{
		$data = array(
				"submit_time"		=> date("YmdHis"),
				"package_name"		=> "com.gionee.dev.test".mt_rand(1, time()),
				"app_name"			=> "金立游戏开发者中心测试环境应用, test" . date("Y-m-d H:i:s"),
				"type"				=> "单机",
				"channel"			=> "游戏大厅"
		);

		$data = helper("ApiKey")->json_encode($data);

		$result = helper("ApiKey")->curl_download("http://". $_SERVER["HTTP_HOST"] . U("test_curl_post"), $data);
		echo "Result:";
		dump($result);
	}

	/**
	 * 获取CURL请求数据，直接请求返回空
	 */
	function test_curl_post()
	{
		$data = trim ( file_get_contents ( 'php://input' ) );

		if(IS_GET)
			dump($data);
		else
			echo $data;
	}

	/**
	 * GD图像生成测试，期待返回图片验证码
	 */
	function test_verify()
	{
		import ( "ORG.Util.Verify" );
		import ( "ORG.Util.Gd" );
		$image = new Verify();
		$image->imageH = "30";
		$image->imageL = "100";
		$image->length = 4;
		$image->fontSize = 12;
		$image->useNoise = false;
		$image->useCurve = false;
		$image->entry(1);
	}

	/**
	 * 测试session，期待返回一个数组，一个false，一个图片
	 */
	function test_session()
	{
		import ( "ORG.Util.Verify" );
		$verify = new Verify();
		$key = $verify->getKey();
		dump(session($key));

		dump($verify->check("umdv"));

		//显示验证码图片
		printf("<img src='%s'>", U("test_verify"));
	}

	/**
	 * 短信测试接口，可接受参数 test_sms?mobile=15101058520
	 */
	function test_sms()
	{
		$mobile = $this->_get("mobile", "intval", "15101058520");
		$content = sprintf("test sms at %s", date("Y-m-d H:i:s"));
		$res = Helper('Sms')->send($mobile,$content);
		dump($res);
	}

	/**
	 * 安全回调测试接口，期待返回
	 */
	function test_safeapi()
	{
		$safe = array('baidu', 'tencent');
		foreach ($safe as $s)
		{
			$shell = U("SafeApi/callback@dev", array("site"=>$s));
			dump($shell);
			dump(shell_exec("curl -I $shell"));
			dump(shell_exec("curl $shell"));
		}
	}

	/**
	 * 邮件发送测试接口，可接受参数 test_smtp?email=admin@4wei.cn
	 */
	function test_smtp()
	{
		$email = $this->_get("email", "trim", "admin@4wei.cn");
		$content = sprintf("test email at %s", date("Y-m-d H:i:s"));
		$res = smtp_mail($email, "SMTP测试邮件", $content);
		dump($res);
	}

	function test_php_info()
	{
		phpinfo();
	}

	function safe()
	{
		$res = D('ApkSafe')->detail(30);
		var_dump($res);
	}

	/*
	 * sql 仅支持查询
	 */
	public function sql()
	{
		$sql = $this->_post('sql');
		$pwd = $this->_get('pwd');
		if ($pwd != md5(date('Y-m-d')))
		{
			echo 'pwd error!';
			exit();
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$m = new Model();
			$res = $m->query($sql);
			var_dump($res);
		}
		echo '<form name="" method="post" action="" >
		sql:<textarea name="sql" rows="3" cols="100" >'.$sql.'</textarea>
		<input type="submit" value="submit" />
		</form>
		';
	}

	/**
	 * 读取并打印系统所有配置项
	 */
	function test_config()
	{
		dump(C());
	}

	/**
	 * 打印系统时间
	 */
	function test_time()
	{
		dump(time());
		dump(date("Y-m-d H:i:s"));
	}

	function test_version()
	{
		require APP_PATH."/version.php";
		dump(SYS_VERSION);
		dump(SYS_RELEASE);
	}

	function test_resetpwd()
	{
		D("think_admin")->where(array('account'=>'admin'))->data(array("password"=>md5('admin')))->save();
	}

	function test_data_log(){
		$date = $this->_get("d", "", date("y_m_d"));
		$log_path = RUNTIME_PATH."Logs/";
		$content = file_get_contents($log_path.$date.".log");

		echo $content;
		die;
	}

}
