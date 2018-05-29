<?php
/**
 *
 * 期望上线的应用定时任务
 *     	检查当前小时内有期望上线、并且状态为审核通过的应用
 *
 * 部署
 * crontab -e
 * 		1 * * * * * cd /wwwroot/xxxx/ && /usr/bin/php ./cli.php Online index
 *
 * @author xiayang
 *
 */
if(!function_exists('php_cli_online_thread_shutdown'))
{
	function php_cli_online_thread_shutdown()
	{
		echo "all job done".PHP_EOL;
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_online_thread.lock";
		unlink($logLock);
		exit(0);
	}
}

//任务完成删除进程锁
register_shutdown_function('php_cli_online_thread_shutdown');

class OnlineAction extends CliBaseAction {
	
	protected $model = 'Dev://Apks';
	protected $app_model = "Dev://Apps";
	
	function index(){
		
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_online_thread.lock";
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
		
		//查找符合当前定时上线条件的应用
		$apks_model = D($this->model);
		$apps_model = D($this->app_model);
		$before_hour = date("Y-m-d H:i:s", strtotime("+5 minute"));
		
		//吴佳晗 10-17 11:11:39
		//确认了，就如你说的，计划任务开始运行，查一下此前审核通过并且已经过了预期上限时间的还没有上线的都可以自动上线。
		$where = array("status"=>array('in', "2,4"), "online_time"=>array("between", array("2014-01-01 00:00:00", $before_hour)));
		$online_apks = $apks_model->where($where)->order('id desc')->select();

		Log::write($apks_model->_sql(),"online_apks_sql");
		if (!empty($online_apks)) {
			
			foreach ($online_apks as $apkinfo){
				$apk_id = $apkinfo['id'];
				$map['id'] = $apk_id;
				
				//上线状态
				$status = 3;
				
				//如果已经有较大的apkid在线，则自动上线不生效
				$count = $apks_model->where(array("app_id"=>$apkinfo["app_id"], "status"=>$status, "id"=>array("gt", $apk_id)))->count();
				if($count > 0) {
					$this->printf("app:%s, apk:%s, bigger apkid found, ignored", $apkinfo["app_id"], $apk_id);
					
					Log::write($apks_model->_sql(),"online_apks_sql");
					continue;
				}
				 
				//更新APK包状态(状态为上线)
				$res = $apks_model->data(array('status'=>$status, "onlined_at"=>time() ))->where($map)->save();
				
				$map_s['version_code']  = array('gt',$apkinfo['version_code']);
				$map_s['app_id']  = $apkinfo['app_id'];
				
				$apkinfo_s = $apks_model->where($map_s)->find();
				if(empty($apkinfo_s)){
					$app_status = 1;
					$appArr = array();
					$appArr['status'] = $app_status;
					$appArr['main_apk_id'] = $apk_id;
					$appArr['updated_at'] = time();
					$res = $apps_model->data($appArr)->where(array("id"=>$apkinfo['app_id']))->save();
				}
				//更新应用的名称
				$app_name = $apps_model->where(array("id"=>$apkinfo['app_id']))->getField("app_name");
				if (!empty($apkinfo['app_name']) && $apkinfo['app_name'] != $app_name) {
					$data_s1 = array("app_name"=>$apkinfo['app_name']);
					$res = $apps_model->data($data_s1)->where(array("id"=>$apkinfo['app_id']))->save();
				}
				
				//同步成功
				if($res){
					//对其它低上线版本的apk进行下线处理
					$outline_map = array("app_id"=>$apkinfo['app_id'],"status"=>3,"id"=>array("neq",$apk_id));
					$res = $apks_model->data(array('status'=>-2,"offlined_at"=>time() ))->where($outline_map)->save();
					
					//差分包自动上线
					$bs_online = D("Bspackage")->online($apk_id);
					$this->printf("app:%s, apk:%s, bspackage online:%s", $apkinfo["app_id"], $apk_id, $bs_online);

					//同步数据
					Helper("Sync")->done($apkinfo['app_id'], 'online');

					$account_info = D("Dev://Accounts")->getAccountAll($apkinfo['author_id']);

					//发送邮件
					$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
					$sendemail = empty($account_info['contact_email']) ? $account_info['email'] : $account_info['contact_email'];

					$subject = "开发者应用上线";
					$body = "亲爱的开发者，您好：<br>";
					$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您上传的 《".$apkinfo['app_name'].$apkinfo['version_name'].'》版本已经上线。';
					$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
					$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
					smtp_mail ( $sendemail, $subject, $body );
					
					Log::write("crontab--上线成功: \n".print_r($apkinfo, true));
					$this->printf("app:%d, apk:%d, auto online result:%s", $apkinfo['app_id'], $apkinfo['id'], 'success');
				}else{
					Log::write("crontab--上线失败: \n".print_r($apkinfo, true));
					$this->printf("app:%d, apk:%d, auto online result:%s", $apkinfo['app_id'], $apkinfo['id'], 'fail');
				}
			}
		}
		
		unlink($logLock);
	}
	
	/**
	 * 批量同步所有已经上线的应用
	 */
	function apisyncall()
	{
		$apps = D("Dev://Apks")->where(array("status"=>"3"))->group('app_id')->getField('app_id', true);
		foreach ($apps as $app_id)
		{
			$res = Helper("Sync")->done($app_id, 'online');
			$this->printf("app:%s, result:%s", $app_id, $res);
		}
	}
}