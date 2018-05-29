<?php
include 'common.php';
/**
 *基于流量上报日志汇总数据1分钟执行一次
 */
$cache = Cache_Factory::getCache();
$lock = $cache->get(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS);
if($lock) exit("nothing to do.\n");
$cache->set(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS, 1, 3*60); //3分钟过期

 //初始化原始日志表数据  --  获取要处理的表数据
 $item = Glog_Service_FreedlProgress::getBy(array('status'=>0), array('table_ymd'=>'asc'));
 //没有日志记录
 if(empty($item)){
     $cache->set(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS, 0, 3*60);
     exit("not progress data\r\n");
 }
 
 if($item['table_ymd'] != date('Ymd')){
	 if(($item['last_id'] != 0) && ($item['last_id'] == $item['progress_id'])){
 		//更新指定表日志表处理完成为状态。防止状态未更改为完成堵塞任务的执行
 		Glog_Service_FreedlProgress::updateBy(array('status'=>1), array('id'=>$item['id']));
        $cache->set(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS, 0, 3*60);
        exit($item['table_ymd']."table work finish\r\n");
 	}
 }
 
$tableName = 'freedl_log_' . $item['table_ymd'];
$startPos = $item['progress_id'];
$limit = 100;
$logs = Glog_Service_TrafficLog::getLimit($tableName, array('id' => array('>', $startPos)), $limit); //每次获取10条原始日志做处理
if(empty($logs)){
    $cache->set(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS, 0, 3*60);
    exit("not found logs\r\n");
}
foreach ($logs as $value){
	//数据汇总处理
	list($ret, $black) = Freedl_Service_Process::cronHandle($value);
	//有异常的数据丢弃
	if(!$ret) continue;
	//处理黑名单用户数据
	if($black) Freedl_Service_Process::addFreedlBlackCache($value['imsi']);
	//更新日志处理进度表数据
	Glog_Service_FreedlProgress::updateBy(array('progress_id' => $value['id']), array('id' => $item['id']));
	echo "freedl_log_{$value['id']}----ok\r\n";
}

//处理表非今日的表数据做处理完整状态的校验
if($item['table_ymd'] != date('Ymd')){
	$gret = Glog_Service_FreedlProgress::getBy(array('id' => $item['id']));
	if($gret && ($gret['last_id'] == $gret['progress_id'])){
		//更新日志表处理完成为态
		Glog_Service_FreedlProgress::updateBy(array('status'=>1), array('id'=>$gret['id']));
	}
}
echo CRON_SUCCESS;

$cache->set(Util_CacheKey::LOCK_FREE_DOWNLOAD_PROCESS, 0, 3*60); //5分钟过期
exit;
