<?php
/**
 * 云测逻辑
 * 
 * 通过计划任务对审核通过的账号下的应用，添加云测请求，并对已经发起的请求进行状态检查
 * 
 * 部署
 * * /10 * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Testin send
 * * /10 * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Testin check
 * 
 * @author shuhai
 */
class TestinAction extends CliBaseAction
{
	protected $config = array();
	
	public $lastFetchTime = 0;
	public $serverUrl = "";
	public $lastLoginTime = 0;
	public $serverSid = "";
	public $device    = array();
	public $totalApp  = 100;
	
	function _initialize()
	{
		parent::_initialize();

		$this->config = C('TESTIN');
		if(!is_array($this->config))
			exit('配置文件 Data/Config/testin.php 不存在或者没有导入');
		
		$this->config = (object)$this->config;
		
		//获取服务器、登陆并获取所有设备
		$this->fetch_server_list();
		$this->login();
		$this->deviceList();
	}
	
	function index()
	{
		//发布测试、检查结果
		$this->send();
		$this->check();
	}
	
	/**
	 * 对已经上传的包，上传到云测
	 */
	function send()
	{
		//已提交的普通应用、已签名的联运应用
		$apks = D("Apks")->where('(status = 1 and is_join = 2) OR (status = 1 and is_join = 1 and sign = 1)')->getField('id as apk_id, app_id, file_path, app_name, apk_md5', true);

		foreach ($apks as $apk)
		{
			//添加到云测
			if(!$find = D("Testin")->where(array("apk_md5"=>$apk["apk_md5"]))->find())
			{
				$apk_file = Helper("Apk")->get_url('apk').$apk["file_path"];
				$apk["adapt_id"] = $this->add_app($apk["apk_id"].":::".$apk["app_name"], $apk_file);
				$apk["create_at"] = time();
				D("Testin")->add($apk);
				printf("app add:%s, apk:%s, adaptId:%s\n", $apk["app_name"], $apk_file, $apk["adapt_id"]);
			}
		}
	}

	/**
	 * 从云测提取测试结果
	 */
	function check()
	{
		$result = $this->get_result();
		foreach ($result as $app)
		{
			//获取excel报告
			if(6 == $app["execStatus"])
				$app['excel'] = sprintf('http://realauto.testin.cn/nativeapp.action?op=Report.exportExcel&adpId=%s&authtoken=%s', $app['id'], $this->serverSid);

			$where = array("adapt_id"=>$app["adaptId"]);
			$data  = array("status"=>$app["execStatus"], "report"=>json_encode($app));
			
			//计算通过率
			$data['fail'] = $app["installFailTotal"] + $app["runCrashTotal"];
			$data['pdf'] = !empty($app["reportUrl"]) ? $app["reportUrl"] : '';
			$data['chk_status'] = intval($app["checkApp"]);
			
			//获取详细报告
			if(6 == $app["execStatus"])
			{
				$detail_result = $this->get_detail_result($app["adaptId"]);
				foreach ($detail_result as $testResult)
				{
					$data['detail_report'][] = array(
							"reportTime"		=>floor($testResult["reportTime"]/1000),
							"reportDevice"		=>sprintf("%s %s", $testResult["reportDevice"]["brandName"], $testResult["reportDevice"]["modelName"]),
							
							"installCode"		=>$testResult["resultCode"] != 5 ? '通过' : '未通过',
							"uninstallCode"		=>'通过',
							"runCode"			=>$testResult["resultCode"] != 6 ? '通过' : '未通过',

							"launch"			=>floor($testResult["logSummary"]["launch"])."ms",
							"maxMem"			=>floor($testResult["logSummary"]["maxMem"]/1024)."M",
							"avgMem"			=>floor($testResult["logSummary"]["avgMem"]/1024)."M",
							"minMem"			=>floor($testResult["logSummary"]["minMem"]/1024)."M",
							
							"maxCpu"			=>trim($testResult["logSummary"]["maxCpu"]),
							"minCpu"			=>trim($testResult["logSummary"]["minCpu"]),
							"avgCpu"			=>trim($testResult["logSummary"]["avgCpu"]),
							"logUrl"			=>$testResult["logUrl"],
					);
				}
				$data['detail_report'] = json_encode($data['detail_report']);
			}

			D("Testin")->where($where)->save($data);
			printf("app saveed, adaptId:%s, testinId:%s, app:%s, pkg:%s chk_status:%d, status:%s, fail:%d\n",
			$app["adaptId"], $app["id"], $app["appName"], $app["package"],
			$data["chk_status"], $app["execStatus"], $data['fail']);
		}
	}
	
	/**
	 * 请求参数签名
	 **/
	protected function hash_sig($array)
	{
		unset($array['sig']);
		ksort($array);
		$md5 = md5(json_encode($array).$this->config->SECKEY);
		$array['sig'] = $md5;
	
		ksort($array);
		return $array;
	}
	
	/**
	 * 获取服务器列表
	 **/
	protected function fetch_server_list()
	{
		$file = DATA_HOME.'/testin_server.list';
		if(is_file($file))
		{
			list($this->lastFetchTime, $this->serverUrl) = file($file);

			if($this->lastFetchTime + 600 > time())
				return true;
		}
	
		$data = array(
				"op"		=>"Dispatch.list",
				"apikey"	=>$this->config->APIKEY,
				"timestamp"	=>time()*1000,
				"sig"		=>"",
		);
		$data = $this->hash_sig($data);
		$json = json_encode($data);
	
		$shell = sprintf("curl -s -X POST -d '%s' 'http://sd.testin.cn/mcfg/mcfg.action'", $json);
		$exec = `{$shell}`;
	
		$exec = json_decode($exec, true);
		if(empty($exec) || false == $exec || !isset($exec['code']))
		{
			D("Syserror")->error("云测同步异常",
			"fetch_server_list http error:尝试获取服务器列表，返回数据结构异常，返回数据{$exec}",
			"检查云测平台是否正常，检查网络是否正常", 0, "2");
			exit("fetch_server_list http error");
		}
	
		if($exec['code'] > 0 || empty($exec['data']['dispatches'][0]['externalIp']))
		{
			D("Syserror")->error("云测同步异常",
			"fetch_server_list error:尝试获取服务器列表，返回数据结构异常，返回数据{$exec}",
			"检查云测平台是否正常，检查对方接口是否有变动", 0, "2");
			exit("fetch_server_list error ".$this->getError($exec['code']));
		}
	
		$this->serverUrl = sprintf("http://%s:%s", $exec['data']['dispatches'][0]['externalIp'], $exec['data']['dispatches'][0]['externalPort']);
		$this->lastFetchTime = time();
	
		file_put_contents($file, sprintf("%s\n%s", $this->lastFetchTime, $this->serverUrl));
		return true;
	}
	
	/**
	 * 登陆获取用户id
	 **/
	protected function login()
	{
		$file = DATA_HOME.'/testin_login.list';
		if(is_file($file))
		{
			list($this->lastLoginTime, $this->serverSid) = file($file);

			if($this->lastLoginTime + 36000 > time())
				return true;
		}
	
		$request = '/sso/user.action';
		$data = array(
				"op"		=>"Login.login",
				"apikey"	=>$this->config->APIKEY,
				"timestamp"	=>time()*1000,
				"sig"		=>"",
				"email"		=>$this->config->EMAIL,
				"pwd"		=>$this->config->PASSWD,
				"sidTimeout"=>10080,	//sid超时时间，单位分钟，默认60分钟，最大值为 10080（7天）
		);
	
		$data = $this->curl($request, $data);
		if($data["code"] > 0)
			exit("login error:".$this->getError($data["code"]));
	
		$this->serverSid = $data['data']['sid'];
		$this->lastLoginTime = time();
	
		file_put_contents($file, sprintf("%s\n%s", $this->lastLoginTime, $this->serverSid));
		return true;
	}
	
	/**
	 * 获取设备列表
	 **/
	protected function deviceList()
	{
		$request = '/deviceunit/cfg.action';
		$data = array(
				"op"		=>"Model.getSpecimens",
				"apikey"	=>$this->config->APIKEY,
				"timestamp"	=>time()*1000,
				"sig"		=>"",
				"sid"		=>$this->serverSid,
	
				"cloud"		=>$this->config->CLOUD,
				"syspfName" =>"android",
				"sidTimeout"=>10080,	//sid超时时间，单位分钟，默认60分钟，最大值为 10080（7天）
		);
	
		$data = $this->curl($request, $data);
		if($data["code"] > 0)
		{
			D("Syserror")->error("云测同步异常",
			"deviceList error:尝试获取设备列表列表，返回数据结构异常，错误码:{$data["code"]}",
			$this->getError($data["code"]), 0, "2");
			exit("deviceList error:".$this->getError($data["code"]));
		}
	
		$this->device = array();
		foreach ($data['data']['list'] as $d)
			$this->device[] = array("modelId"=>$d['modelId'], "releaseVer"=>$d['releaseVer']);
	
		return true;
	}
	
	/**
	 * 提交一个应用进入云测
	 **/
	protected function add_app($apkName, $apkUrl)
	{
		$request = '/realtest/nativeapp.action';
		$data = array(
				"op"		=>"App.add",
				"apikey"	=>$this->config->APIKEY,
				"timestamp"	=>time()*1000,
				"sig"		=>"",
				"sid"		=>$this->serverSid,
	
				"appName"	=>$apkName,
				"packageUrl"=>$apkUrl,//应用包下载地址
				"syspfId"	=>1, //系统id 1-android、2-ios
				"testType"	=>0, //测试类型 0-兼容测试 1-功能测试
				"secrecy"	=>1, //是否需要在首页显示；0-显示、1-不显示
	
				"models"	=>$this->device, //测试机型列表
				"callbackUrl"=>"",
	
				"cloud"		=>$this->config->CLOUD,
				"prodId"	=>$this->config->PRODUCT,
				"syspfName" =>"android",
				"sidTimeout"=>10080,	//sid超时时间，单位分钟，默认60分钟，最大值为 10080（7天）
		);
	
		$data = $this->curl($request, $data);
		if($data["code"] > 0)
		{
			D("Syserror")->error("云测同步异常",
			"app add error:提交应用出错，返回数据结构异常，错误码:{$data["code"]}",
			$this->getError($data["code"]), 0, "2");
			exit("app add error:".$this->getError($data["code"]));
		}
	
		printf("app added, adaptId:%s\n", $data['data']['result']);
		return $data['data']['result'];
	}
	
	/**
	 * 获取所有应用测试状态
	 **/
	protected function get_result()
	{
		$result = array();

		$pageSize = 100;
		$p = range(0, ceil($this->totalApp / $pageSize));
		foreach ($p as $k=>$pid)
		{
			$request = '/realtest/nativeapp.action';
			$data = array(
					"op"		=>"App.list",
					"apikey"	=>$this->config->APIKEY,
					"timestamp"	=>time()*1000,
					"sig"		=>"",
					"sid"		=>$this->serverSid,
						
					"offset"	=>$k * 100,	//从第几条数据开始（默认为0）
					"max"		=>100,	//最大查询条数（默认为15，不超过100）
			);
	
			$data = $this->curl($request, $data);
	
			if($data["code"] > 0)
			{
				D("Syserror")->error("云测同步异常",
				"get_result error:获取数据出错，返回数据结构异常，错误码:{$data["code"]}",
				$this->getError($data["code"]), 0, "2");
				exit("get_result error:".$this->getError($data["code"]));
			}
				
			foreach ($data['data']['list'] as $app)
			{
				$result[] = $app;
				//printf("adaptId:%s, packageName:%s, appVersion:%s, execStatus:%s, reportUrl:%s\n",
				//		$app['adaptId'], @$app['packageName'], @$app['appVersion'], $app['execStatus'], $app['exportStatus'] > 1 ? $app['reportUrl'] : $app['exportStatus']);
			}

			if(count($data['data']['list']) < $pageSize)
				break;
		}

		return $result;
	}
	
	/**
	 * 获取所有应用测试详细报告
	 **/
	protected function get_detail_result($adaptId)
	{
		$request = '/realtest/nativeapp.action';
		$data = array(
				"op"		=>"Report.details",
				"apikey"	=>$this->config->APIKEY,
				"timestamp"	=>time()*1000,
				"sig"		=>"",
				"sid"		=>$this->serverSid,
		
				"adaptId"	=>$adaptId,
				"filter"	=>1,
		);

		$data = $this->curl($request, $data);
		return $data["code"] == 0 ? $data["data"]["list"] : array();
	}
	
	protected function curl($request, $data)
	{
		$data = $this->hash_sig($data);
	
		$shell = sprintf("curl -s -X POST -d '%s' '%s'", json_encode($data), $this->serverUrl.$request);
		$exec = `{$shell}`;
	
		$json = @json_decode($exec, true);
		if(empty($exec) || false === $json || !isset($json['code']))
		{
			Log::write($shell, Log::EMERG);
			Log::write(var_export($json, true), Log::EMERG);
			exit("curl shell error:\n".$shell);
		}
	
		return $json;
	}
	
	protected function getError($code)
	{
		$error = array(
				0=>"成功",
				10000=>"未知错误",
				10001=>"属于IP黑名单",
				10002=>"API接口无效",
				1000201=>"App访问api频率过快",
				1000202=>"App访问api配额超限",
				1000203=>"用户访问api频率过快",
				1000204=>"用户访问api配额超限",
				1000205=>"api配置异常",
				10003=>"配额不足",
				10004=>"该IP地址未经授权",
				10005=>"参数无效",
				1000501=>"参数无效(op)",
				1000502=>"无效的参数(apikey)",
				1000503=>"无效的参数(sid)",
				1000504=>"无效的参数(timestamp)",
				1000505=>"无效的参数(sig)",
				1000506=>"无效的请求URL",
				1000507=>"无效的参数(uid)",
				1000508=>"无效的参数(nodeid)",
				1000509=>"无效的请求(actionUrl)",
				1000510=>"参数无效(cloud)",
				10006=>"无效的应用",
				1000601=>"平台配置app的类型有误，请联系平台管理员",
				1000602=>"该应用被关闭",
				1000603=>"app没有配置uid",
				1000604=>"无效的第三方sid",
				1000605=>"第三方转换注册异常",
				1000606=>"App访问权限配置异常",
				10007=>"信息不匹配",
				10008=>"角色无效",
				10009=>"该角色没有授权",
				10010=>"没有有效数据",
				10011=>"时间戳已过期",
				10012=>"请求的对象为空",
				10013=>"请求的报文超过限制",
				10014=>"该用户未登录",
				10015=>"无效的报文",
				10016=>"执行失败",
				10017=>"用户没有权限，请联系系统管理员",
				10018=>"无效的配置，请联系系统管理员",
				99999=>"服务正在初始化",
				10401=>"无效的账号",
				10402=>"用户被禁止",
				10403=>"无效的密码",
				10404=>"登陆失败",
				10405=>"第三方用户的数据异常",
				1090001=>"新增 Native App 测试数据失败",
				1091001=>"无匹配第三方信息",
				1091002=>"无匹配第三方配置信息",
				1091003=>"创建测试机型数量超出配额限制",
				1091004=>"测试机型来源检测不通过",
				1091005=>"测试机型检测不通过",
				1091006=>"测试所用的设备云信息查询为空",
				1091007=>"没有查询到用户信息",
				1091008=>"测试类型或者测试产品错误",
		);
	
		if(isset($error[$code]))
			return $error[$code];
		else
			return 'unknow error';
	}
}