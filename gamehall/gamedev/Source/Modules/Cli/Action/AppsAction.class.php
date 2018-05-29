<?php
/**
 *
 * 长时间未更新游戏检测提醒定时任务
 *     	检测已上线游戏中３个月未更新的游戏，并发送邮件
 *
 * 部署
 * crontab -e
 * 		1 * * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Apps index
 *
 * @author xiayang
 *
 */
if(!function_exists('php_cli_apps_thread_shutdown'))
{
	function php_cli_apps_thread_shutdown()
	{
		echo "all job done".PHP_EOL;
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_apps_thread.lock";
		unlink($logLock);
		exit(0);
	}
}

//任务完成删除进程锁
register_shutdown_function('php_cli_apps_thread_shutdown');

class AppsAction extends CliBaseAction {
	
	protected $mApksModel = 'Dev://Apks';
	
	function index(){
		
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_apps_thread.lock";
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
		
		$week = date('w');
		Log::write("crontab--游戏检测开始");
		if ($week != 3) {//每周三运行
			Log::write($week,"Apps_week");
			Log::write(date('Y-m-d H:i:s'),"Apps_date");
			exit(0);
		}
		
		//添加进程文件锁
		touch($logLock);
		
		//查找符合当前定时上线条件的应用
		$apksModel = D($this->mApksModel);
		$startTime = date('Y-01-01 00:00:00');
		$endTime = date('Y-m-d 00:00:00', strtotime("-3 month"));
		
		//计划任务开始运行，检测三个月未更新的上线游戏
		$where = array("status"=>3, "online_time"=>array("between", array($startTime, $endTime)));

		//单机游戏
		Log::write("crontab--单机检测开始");
		$where['is_join'] = 2;
		$singleOnlineApks = $apksModel->where($where)->order('id desc')->select();
		$sendEmail = "yxdtdj@gionee.com";
		Log::write($apksModel->_sql(),"Apps_single_apks_sql");
		if (!empty($singleOnlineApks)) {
			
			$title = "单机3个月未更新的游戏";
			$content = '';
			foreach ($singleOnlineApks as $apkInfo){
				$appId = $apkInfo['app_id'];
				$appName = $apkInfo['app_name'];
				$package = $apkInfo['package'];
				$version = $apkInfo['version_name'];
				$lastTime = $apkInfo['online_time'];
				
				$content .= "APPID：".$appId;
				$content .= "<br>包名：".$package;
				$content .= "<br>游戏名称：".$appName;
				$content .= "<br>当前版本：".$version;
				$content .= "<br>最后一次更新时间：".$lastTime."<br>";
			}
			$send = smtp_mail ( $sendEmail, $title, $content, '单机运营组' );
			
			Log::write("Apps_single_sendemail: \n".json_encode($send, true)."apkinfo:\n".
			print_r($apkInfo, true)."content:\n".print_r($content, true));
		}
		Log::write("crontab--单机检测结束");
		
		//单机游戏
		Log::write("crontab--网游检测开始");
		$where['is_join'] = 1;
		$netOnlineApks = $apksModel->where($where)->order('id desc')->select();
		$sendEmail = "yxdtwy@gionee.com";
		Log::write($apksModel->_sql(),"Apps_net_apks_sql");
		if (!empty($netOnlineApks)) {
				
			$title = "网游3个月未更新的游戏";
			$content = '';
			foreach ($netOnlineApks as $apkInfo){
				$appId = $apkInfo['app_id'];
				$appName = $apkInfo['app_name'];
				$package = $apkInfo['package'];
				$version = $apkInfo['version_name'];
				$lastTime = $apkInfo['online_time'];
		
				$content .= "APPID：".$appId;
				$content .= "<br>包名：".$package;
				$content .= "<br>游戏名称：".$appName;
				$content .= "<br>当前版本：".$version;
				$content .= "<br>最后一次更新时间：".$lastTime."<br>";
			}
			$content .= '<br><br>SDK最新版本下载地址：
					<a href="http://dev.game.gionee.com/help/sdk.html">
					http://dev.game.gionee.com/help/sdk.html</a>';
			$send = smtp_mail ( $sendEmail, $title, $content, '网游运营组' );
				
			Log::write("Apps_net_sendemail: \n".json_encode($send, true)."apkinfo:\n".
					print_r($apkInfo, true)."content:\n".print_r($content, true));
		}
		Log::write("crontab--网游检测结束");
		
		unlink($logLock);
	}
}