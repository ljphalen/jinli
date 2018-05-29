<?php
// smarty
require 'plugin/smarty.php';

$setting = new setting();
$ipay_rates = $setting->get_ipay_rate();

/* $ym = get('ym');
$db = db::factory(get_db_config());
$task = new task($db);
$date1 = date('Y-m-01',strtotime($ym));
$date2 = date('Y-m-t',strtotime($ym));
$task->create_report($date1, $date2,$ym);
var_dump('dddddd');
exit;
 */
//生成对帐单
if(get('opt')=='create'){
	@set_time_limit(0);
	@ini_set('memory_limit','256M');
	
	$ym = get('ym');
	$db = db::factory(get_db_config());
	$task = new task($db);
	
	$date1 = date('Y-m-01',strtotime($ym));
	$date2 = date('Y-m-t',strtotime($ym));
	if($task->create_report($date1, $date2,$ym)){
		echo json_encode(array('status'=>1));
	}else{
		echo json_encode(array('status'=>0));
	}
	exit;
}

//导出对帐单
if(get('opt')=='export'){
	$type = get('type');
	$ym = get('ym');
	
	switch ($type){
		case 1:
			$filename = '我方接入计费平台对帐单.xls';
			$filepath = SROOT.'/data/cache/reporthtml/ours/'.$ym.CACHE_FILE_EXT;
			break;
		case 2:
			$filename = '爱贝计费平台对帐单.xls';
			$filepath = SROOT.'/data/cache/reporthtml/ipay/'.$ym.CACHE_FILE_EXT;
			break;
		case 3:
			$filename = '渠道自有支付对帐单.xls';
			$filepath = SROOT.'/data/cache/reporthtml/clients/'.$ym.CACHE_FILE_EXT;
			break;
		case 4:
			$filename = '移动自有渠道对帐单.xls';
			$filepath = SROOT.'/data/cache/reporthtml/ydchannel/'.$ym.CACHE_FILE_EXT;
			break;
	}
	
	$filename = '['.$ym.']'.$filename;
	if (preg_match("/MSIE/", $_SERVER["HTTP_USER_AGENT"])) {
		$filename = urlencode($filename);
	}
	
	if(!file_exists($filepath)){
		alertMsg('对帐单不存在');
		exit;
	}
	
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=\"$filename\" ");
	header("Content-Transfer-Encoding: binary ");
	echo file_get_contents($filepath);
	exit;
}


$dates = array();

$stime = strtotime('2012-12-01');
$etime = strtotime('-1 month');

while(true){
	$stime = strtotime(date('Y-m-d',$stime).' +1 month');
	$ym = date('Y-m',$stime);
	//if($ym)
	$dates[] = $ym;
	if($ym==date('Y-m',$etime))break;
}


rsort($dates);

$dates = array_slice($dates, 0, 24);



$files = array();
foreach($dates as $k=>$v){
	$filename_ipay = SROOT.'/data/cache/reporthtml/ipay/'.$v.CACHE_FILE_EXT;
	if(file_exists($filename_ipay)){
		$files[$v] = 1;
	}
}

$tpl_vars = array(
		'ac' => $ac,
		'actionText' => '导出对帐单',
		'dates' => $dates,
		'files' => $files,
		);
// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');