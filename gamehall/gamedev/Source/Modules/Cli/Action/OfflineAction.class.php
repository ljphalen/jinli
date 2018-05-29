<?php
/**
 *
 * 被封号的用户所有apk下线操作
 *
 * 部署
 * crontab -e
 * 		1 * * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Offline index
 *
 * @author liyf
 *
 */
if(!function_exists('php_cli_offline_thread_shutdown'))
{
	function php_cli_offline_thread_shutdown()
	{
		echo "all job done".PHP_EOL;
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_offline_thread.lock";
		unlink($logLock);
		exit(0);
	}
}

//任务完成删除进程锁
register_shutdown_function('php_cli_offline_thread_shutdown');

class OfflineAction extends CliBaseAction {
	
	protected $model = 'Dev://Apks';
	protected $app_model = "Dev://Apps";
	
	function index(){
		
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_offline_thread.lock";
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
		
		//查找所有被封号的已经上线的apk
		$model = new Model();
		$sql = 'select * from apks where apks.status = 3 and exists(select 1 from accounts where apks.author_id = accounts.id and accounts.status = -2) limit 20';
		$apksList = $model->query($sql);
		Log::write($sql,"offline_apks_sql");
		
		//下线操作
		if (!empty($apksList)) {
			foreach ($apksList as $apkinfo) {
			    //更新APK包状态
			    $map['id'] = $apkinfo['id'];
			    $status = -2;
			    $data = array();
			    $data['status'] = $status;
			    $data['offlined_at'] = time();
			    $res = D("Apks")->data($data)->where($map)->save();
			    
			    //更新APP状态
			    $apkinfo_old = D("Apks")->where(array("app_id"=>$apkinfo['app_id'],"status"=>3))->find();
			    if (empty($apkinfo_old)) {
			        $status_old = $apkinfo_old['status'];
			        $res = D("Apps")->data( array('status'=>$status ))->where(array("id"=>$apkinfo['app_id']))->save();
			    }
			    
			    //数据更新更改，同步数据
			    if ($res) {
			        helper("Sync")->done($apkinfo['app_id'], 'offline');
			        //$this->success("");
			        Log::write('应用下线成功ID:'.$apkinfo['app_id'],"offline_apks_success");
			    } else {
			        Log::write('应用下线失败ID:'.$apkinfo['app_id'],"offline_apks_failing");
			    }
			}
		}
		
		unlink($logLock);
	}
}