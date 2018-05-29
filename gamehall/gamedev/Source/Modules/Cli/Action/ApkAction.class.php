<?php
/**
 * 
 * Apk提交安全检查逻辑
 *     用户提交审核以后，计划任务根据提交审核状态且没有apk_safe记录，进行签名并发送安全检查
 * 
 * apk差分包处理逻辑
 * 
 * 流程：
 * 1. 用户在前台申请使用差分包
 * 2. 后台脚本定时生成差分包
 * 3. 管理员在后台审核允许使用差分包
 * 
 * 依赖：
 * yum install bsdiff
 * /usr/bin/bsdiff
 * 
 * 部署
 * crontab -e
 * * /10 * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Apk bsdiff
 * 
 * @author shuhai
 *
 */
if(!function_exists('php_cli_apk_thread_shutdown'))
{
	function php_cli_apk_thread_shutdown()
	{
		echo "all job done".PHP_EOL;
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_apk_thread.lock";
		unlink($logLock);
		exit(0);
	}
}

register_shutdown_function('php_cli_apk_thread_shutdown');

class ApkAction extends CliBaseAction
{
	protected $model = 'Dev://Apks';
	protected $bsdiff = "/usr/bin/bsdiff";
	
	/**
	 * 主入口
	 * 用户提交审核以后，计划任务找到已提交审核且没有apk_safe记录的apk，进行签名并发送安全检查
	 */
	function index()
	{
		$this->printf("Cli Thread start.");
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_apk_thread.lock";
		if(!is_dir($logPath))
		{
			$this->printf("%s path not found, exit.", EVENT_LOG_PATH);
			exit(0);
		}
		
		if(is_file($logLock))
		{
			$this->printf("Thread found, exit.");
			exit(0);
		}
		
		//添加进程文件锁
		touch($logLock);
		
		//测试360安全检查
		$ids = D("Dev://Apks")->where(array("safe_status"=>array("lt", 7)))->getField('id', true);
		foreach ($ids as $id)
			D("Safe")->apkScan($id, 'Qihu');

		$this->safe_sign();
		$this->bsdiff();
		$this->ftpAccountClean();
		$this->get_fingerprint();
		
		unlink($logLock);
	}
	
	protected function safe_sign()
	{
		$model = D($this->model);
		$safeModel = D("Dev://ApkSafe");
		
		$map = array("safe_status"=>0, "status"=>array("gt", 0));//读取提交审核的应用，且没有安全状态的包
		
		//2014-05-07修订，有时安全检查会非常迅速的返回结果，会修改apk的safe_status为1，联运的应用就无法进行签名
		//2014-05-07修订为读取提交审核的应用，且没有安全状态的包，或者读取提交审核且没有进行签名的联运应用，
		$map = "(status=1 AND is_join=1 AND sign<=0) OR (safe_status=0 AND status=1)";
		
		$count = $model->where($map)->count();
		
		$this->printf("safe check start, %d apps found.", $count);
		$apks = $model->where($map)->select();

		foreach ($apks as $apk)
		{
			//检查是否有添加安全检查
			$safe_status = $safeModel->where(array("apk_md5"=>$apk["apk_md5"]))->count() ? true : false;
			
			//如果是联运应用，且没有进行签名，则进行签名
			//是否联运(1:联运，2:普通)
			//签名状态(0:未签名, -1:签名出错, 1:签名成功)
			if($apk['is_join'] == 1 && $apk['sign'] < 1)
			{
				$this->printf("->apk %d signature start", $apk["id"]);
				$result = $this->Signature($apk["id"], $apk["file_path"]);
				$model->data($result)->save();
				$this->printf("->apk signature %s:%s:%s", $apk["id"], $apk["file_path"], $result['sign']);
				
				//签名成功则需要进行安全检查
				if($result['sign'] == 1)
					$safe_status = false;
			}

			if(!$safe_status)
			{
				//发送安全扫描请求
				D('Safe')->scanAll($apk["id"]);
				
				$this->printf("apk id %d safe check send.", $apk["id"]);
			}else{
				$this->printf("apk id %d safe checked, ignore.", $apk["id"]);
			}
		}
	}
	
	/**
	 * 根据apk状态生成差分包
	 * 
	 * apks.bsdiff 状态位标记
	 * apks.bsdiff{ 0，未申请差分包，1,申请差分包，2,已生成差分包 }
	 * apks.status{ 状态(-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:未提交审核, 1:审核中/待审核, 2:审核通过, 3:已上线) }
	 */
	protected function bsdiff()
	{
		$this->installer();

		//获取所有需要生成差分包的应用，再进行差分包处理
		$model = D($this->model);

		$map = array("status"=>array("gt", 1), "bsdiff"=>1);
		$apps = $model->where($map)->getField("id, app_id", true);

		$this->printf("bsdiff package start, %s apps found.", count($apps));
		if(empty($apps))
			return 0;

		$tmp = array();
		foreach ($apps as $apk_id => $app_id)
			$tmp[$app_id][] = $apks[] = $apk_id;
		$this->printf("bsdiff apk:%s.", join(",", $apks));
		
		$bsmodel = D("Bspackage");
		foreach ($tmp as $app_id => $apks)
		{
			//按应用生成差分包目录
			$patch_dir = $this->patch_dir($app_id);
			//$this->printf($patch_dir);

			foreach($apks as $apk_id)
			{
				//获取当前版本的apk信息
				$map = array("id"=>$apk_id);
				$max = $model->where($map)->field(array("id", "version_name", "version_code", "file_path", "file_size", "apk_md5"))->find();
				//print_r($max);
				
				//找出比当前apk id小，且在50M以内的版本
				//v3.0，任何大小的文件，都可以生成差分包
				$map = array("status"=>array("in", "-2,2,3,4"), "app_id"=>$app_id, "id"=>array("lt", $apk_id));
				$apk = $model->where($map)->order(array("id"=>"desc"))->field(array("id", "version_name", "version_code", "file_path", "file_size", "apk_md5"))->select();

				//只有一个版本时不进行处理
				if (empty($apk) || count($apk) < 1)
				{
					$this->printf("->apk:%s no matched.", $max['id']);
					continue;
				}

				$this->printf("App %s %d version found", $app_id, count($apk));
				foreach ($apk as $small)
				{
					//旧版本的apkid
					$_apk_id = $small["id"];
					$version_name = $small["version_name"];
					$version_code = $small["version_code"];
					$file_path = $small["file_path"];

					if($_apk_id == $apk_id)
						continue;
					
					//准备上N个版本的APK文件和当前版本的apk文件
					$old = $this->get_file_path($file_path);
					//$this->printf($old);
					$new = $this->get_file_path($max["file_path"]);
					//$this->printf($new);
					
					//准备数据
					$data = $bsmodel->create(array(), 1);
					$data["app_id"] = $app_id;
					$data["created_at"] = time();
					$data["b_apk_id"] = $max["id"];
					$data["b_vcode"] = $max["version_code"];
					$data["b_vname"] = $max["version_name"];
					$data["b_apk_md5"] = $max["apk_md5"];
					$data["b_apk_size"] = $max["file_size"];
					
					$data["s_apk_id"] = $small["id"];
					$data["s_vcode"] = $small["version_code"];
					$data["s_vname"] = $small["version_name"];
					$data["s_apk_md5"] = $small["apk_md5"];
					$data["s_apk_size"] = $small["file_size"];

					if(!is_file($old) || !is_file($new))
					{
						if(!is_file($old))
							$this->printf("App %s old apk not exist", $app_id);
						if(!is_file($new))
							$this->printf("App %s new apk not exist", $app_id);
						$data["status"] = "-2";
						$bsmodel->save($data);
						continue;
					}
					
					//检查是否有之前的差分包存在，如果apk被重新上传发生变化，用md5进行对比
					$check = $bsmodel->where(array("app_id"=>$app_id, "s_apk_id"=>$small["id"], "b_apk_id"=>$max["id"]))->find();
					if(!empty($check["id"]))
					{
						//如果文件md5发生变化，则重新生成
						if($data["s_apk_md5"] == $check["s_apk_md5"] && $data["b_apk_md5"] == $check["b_apk_md5"])
						{
							$this->printf("%d_%d.patch hash matched, ignore", $_apk_id, $apk_id);
							continue;
						}
						
						//文件发生变化，则重新添加差分包记录
						$bsmodel->where(array("id"=>$check["id"]))->delete();
						$this->printf("%d_%d.patch hash not match, continue", $_apk_id, $apk_id);
					}
					
					$timeStart = time();
					$this->printf("%d_%d.patch start %s", $_apk_id, $apk_id, date("Y-m-d H:i:s", $timeStart));

					//差分包格式，旧版本_to_新版本.patch
					$patchfile = sprintf("%s/%d_%d.patch", $patch_dir, $_apk_id, $apk_id);

					//执行差分包命令
					$result = $this->patch($old, $new, $patchfile);
					$data["status"] = $result ? 1 : -2;
					$data["patch_md5"] = md5_file($patchfile);
					$data["patch_size"] = filesize($patchfile);
					$data["patch_path"] = sprintf("%d/%d_%d.patch", $app_id, $_apk_id, $apk_id);
					$bsmodel->add($data);
					
					//同步数据
					Helper("Sync")->done($app_id, 'online');

					$this->printf("%d_%d.patch done use time: %d", $_apk_id, $apk_id, time()-$timeStart);
				}
			}
		}
	}
	
	/**
	 * @param array $apk
	 * @return array -1 0 1 签名状态(0:未签名, -1:签名出错, 1:签名成功)
	 */
	protected function Signature($apk_id, $apk)
	{
		$save_path = Helper("Apk")->get_path("apk");
		$save_apk = $save_path.$apk;
	
		if(!is_file($save_apk))
		{
			Log::write("apk sign apk not exist", 'APKSIGN');
			Log::write($save_apk, 'APKSIGN');
			return array("id"=>$apk_id, "sign"=>-1);
		}
	
		$apk_path = dirname($apk);
		$signed = $this->getSignName($apk);
		$signed_apk = basename($signed);
		$sign_shell_path = DATA_HOME."/Bin/Signature/sign.sh";
	
		$shell = "/bin/sh $sign_shell_path {$save_apk}";
		$result = shell_exec($shell);
		Log::write('sign shell:'.$shell, 'APKSIGN');
		Log::write('sign result:'.$result, 'APKSIGN');
		if(false === strpos($result, "_signed.apk"))
		{
			Log::write("apk sign shell failure", 'APKSIGN');
			return array("id"=>$apk_id, "sign"=>-1);
		}
	
		$signed_apk_path = $save_path . $signed;
	
		if(!is_file($signed_apk_path))
		{
			Log::write("apk signed file not exist", 'APKSIGN');
			Log::write($save_apk, 'APKSIGN');
			Log::write($signed_apk_path, 'APKSIGN');
			return array("id"=>$apk_id, "sign"=>-1);
		}
		
		//检验签名指纹
		$printcert_shell_path = DATA_HOME."/Bin/Signature/printcert.sh";
		$shell = "/bin/sh $printcert_shell_path {$signed_apk_path}";
		$result = shell_exec($shell);
		Log::write($shell, 'APKSIGN');
		Log::write($result, 'APKSIGN');
		
		//如果指纹与预期不一致，则发则预警
		$fingerprint = C('UNIONAPK_FINGERPRINT');
		$fingerprint = empty($fingerprint) ? 'B4:97:92:A5:68:7B:64:14:92:E1:0A:29:15:2F:74:54' : $fingerprint;
		if(false === strpos($result, $fingerprint))
		{
			if(empty($result))
				$result = '结果获取为空';
			
			Log::write("apk sign fingerprint check failed", 'APKSIGN');
			Log::write($save_apk, 'APKSIGN');
			Log::write('signed_apk_path:'.$signed_apk_path, 'APKSIGN');
			Log::write('fingerprint:'.$fingerprint, 'APKSIGN');
			Log::write('result:'.$result, 'APKSIGN');
			
			$noteEmail = C('APKSIGN_FAIL_NOTE');
			if(empty($noteEmail))
				$noteEmail = array("yxdtspm@gionee.com", "guohb@gionee.com");
			
			$apk = M("Apks")->find($apk_id);
			$app = D("Union")->where(array("package"=>$apk['package'], "author_id"=>$apk['author_id']))->find();
			$subject = '【预警】开发者平台应用签名失败';
			$body = "时间：     ".date("Y-m-d H:i:s")."
<br>应用名称：     {$app['name']}
<br>错误类型：     通知产品负责人异常
<br>应用apiKey：  {$app['api_key']}
<br>错误详情：     签名未成功，预期结果：{$fingerprint}，签名获取结果：{$result}
<br>错误日志：     ".LOG_PATH.date('y_m_d').'.log';
			
			foreach ($noteEmail as $e)
				smtp_mail($e, $subject, $body);
			return array("id"=>$apk_id, "sign"=>-1);
		}
	
		return array("id"=>$apk_id, "sign"=>1, "file_path"=>$signed, "apk_rsa"=>$fingerprint, "apk_md5"=>md5_file($signed_apk_path));
	}
	
	/**
	 * 生成签名以后的文件名
	 * @param string $apk
	 * @return string
	 */
	protected function getSignName($apk)
	{
		return str_replace(".apk_signed.apk", "_signed.apk", $apk."_signed.apk");
	}
	
	/**
	 * bsdiff: usage: bsdiff oldfile newfile patchfile
	 * 
	 * @param string $new
	 * @param string $old
	 * @param string $patchfile
	 * 
	 * @return bool
	 */
	protected function patch(string $old, string $new, string $patchfile)
	{
		if(function_exists('bsdiff_diff'))
		{
			ini_set('memory_limit', '0');
			Log::write("call bsdiff_diff", Log::DEBUG);
			return bsdiff_diff($old, $new, $patchfile);
		}
		
		$cmd = "/usr/bin/bsdiff {$old} {$new} {$patchfile}";
		shell_exec($cmd);
		
		Log::write($cmd, 'APKSIGN');
		Log::write($new, 'APKSIGN');
		Log::write($patchfile, 'APKSIGN');
		
		return is_file($patchfile);
	}
	
	protected function patch_dir($app_id)
	{
		$dir = Helper("Apk")->get_path("patch");
		!is_dir($dir) && mkdir($dir);

		$dir = $dir.$app_id;
		!is_dir($dir) && mkdir($dir);
		return $dir;
	}
	
	protected function get_file_path($file)
	{
		return Helper("Apk")->get_path("apk") .  $file;
	}

	protected function installer()
	{
		if(!is_file($this->bsdiff) && !function_exists('bsdiff_diff'))
		{
			$find = `/usr/bin/which bsdiff`;
			if(empty($find) || !is_file($find))
			{				
				$this->printf("{$this->bsdiff} is not found or not installed");
				$this->printf("install bsdiff:");
				$this->printf("sudo yum install bsdiff");
				$this->printf("sudo apt-get install bsdiff");
				exit;
			}else{
				$this->bsdiff = trim($find);
			}
		}
		return;
	}
	
	//定时清空ftp账号
	protected function ftpAccountClean()
	{
		$day = C("CLEANUP_FTP_ACCOUNT_DAY");
		$day = $day > 0 ? $day : 2;
		D("Ftpd")->AccountCleanUp($day);
	}
	
	//检查所有apk的指纹信息
	function get_fingerprint()
	{
		$this->printf("get fingerprint start");
		$model = D($this->model);
		$apks  = $model->where("(ISNULL(apk_rsa) or apk_rsa = '')")->getField('id,file_path', true);

		APP_DEBUG && $this->printf($model->_sql()."fingerprint_sql");
		$this->printf("fingerprint_sql, %s apks found", count($apks));
		
		$error = $success = 0;
		$save_path = Helper("Apk")->get_path("apk");
		foreach ($apks as $apk_id=>$apk) {
			$md5 = $this->apk_fingerprint($save_path . $apk);
			$md5 = trim($md5);
			if(!empty($md5)) {
				$model->save(array("apk_rsa"=>$md5, "id"=>$apk_id));

				$success ++;
				$this->printf("get fingerprint success apkid:%d, md5:%s", $apk_id, $md5);
			}else{
				$error ++;
				$this->printf("get fingerprint fail apkid:%d, md5:%s", $apk_id, $md5);
			}
		}
		
		$this->printf("fingerprint_sql, %s apks found, %s success, %s fail", count($apks), $success, $error);
	}
	
	protected function apk_fingerprint($apk_path)
	{
		if(!is_file($apk_path))
			return false;

		//检验签名指纹
		$printcert_shell_path = DATA_HOME."/Bin/Signature/printcert.sh";
		$shell = "/bin/sh $printcert_shell_path {$apk_path}";
		$result = shell_exec($shell);

		Log::write($shell, 'APKSIGN');
		Log::write($result, 'APKSIGN');
		preg_match_all("@MD5:\s*?([a-z0-9:]+)\s@is", $result, $match);
		return join(",", $match[1]);
	}
}