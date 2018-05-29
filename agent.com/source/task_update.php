<?php
@set_time_limit(0);
@ini_set('memory_limit','256M');

//取生成日期，默认昨天
$createdate = get('createdate');

//强制更新渠道商缓存
$forcecom = get('forcecom');

if(empty($createdate)){
	$createdate = date('Y-m-d',strtotime('-1 day'));
}else{
	$createdate = date('Y-m-d',strtotime($createdate));
}

//连接数据库
$db = db::factory(get_db_config());

$task = new task($db);

//更新渠道商
$modtime = file_get_contents(SROOT.'/data/cache/company/modtime'.CACHE_FILE_EXT);
//$createdate为昨天 && 如果24小时内有更新才更新缓存
if($forcecom || $createdate==date('Y-m-d',strtotime('-1 day')) && ($modtime=='' || time()-$modtime < 3600*24)){
	$task->update_company_cache();
}

if($forcecom){
	echo '更新渠道商';
	exit;
}

//更新报表 当天 当月 当年
$year = substr($createdate,0,4);
$month = substr($createdate,0,7);

$task->update_report_date($createdate);

$createtime = strtotime($createdate);
if(date('Y-m-d',$createtime)==date('Y-m-t',$createtime)
		||date('Y-m-d',$createtime)==date('Y-m-d',strtotime('-1 day'))){
	$task->update_report_month($month);
}

if(date('Y-m-d',$createtime)==date('Y-12-31',$createtime)
		||date('Y-m-d',$createtime)==date('Y-m-d',strtotime('-1 day'))){
	$task->update_report_year($year);
}




echo '更新成功:'.$createdate;