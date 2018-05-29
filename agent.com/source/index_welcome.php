<?php
// smarty
require 'plugin/smarty.php';

//连接数据库
$db = db::factory(get_db_config());

//print_r($sess_userinfo);
// 模板赋值

$report = new report($db);
$totals = $report->get_report_total($sess_userinfo['clientid'],$sess_userinfo['clientids']);

//print_r($totals);
//var_dump($sess_userinfo);
$sess_userinfo['dateline'] = date('Y-m-d H:i',$sess_userinfo['dateline']);
$sess_userinfo['rolename'] = $config['level'][$sess_userinfo['level']];
if($sess_userinfo['level']>200){
	$sess_userinfo['level'] = 200;
}

//二级用户
if($sess_userinfo['level']>=200){
	$task = new task($db);
	$companys1 = $task->get_company(1);
	$companys2 = $task->get_company(2);
	$companys = $companys1+$companys2;
	
	$sess_userinfo['twouser'] = count($companys);
	
	$company = new company($db);
	$comlist = $company->getCompanyList(-1, 0, 0, 1);
	if(!empty($comlist[1][0]['Total'])){
		$sess_userinfo['threeuser'] = $comlist[1][0]['Total'] - $sess_userinfo['twouser'];
	}
}elseif($sess_userinfo['level']==30){
	$company = new company($db);
	$comlist = $company->getCompanyList($sess_userinfo['clientid'], 0, 0, 1);
	if(!empty($comlist[1][0]['Total'])){
		$sess_userinfo['threeuser'] = $comlist[1][0]['Total']-1;
	}
}
$smarty->assign('user',$sess_userinfo);
$smarty->assign('totals',$totals);
// 显示模板的内容
$smarty->display('index_'.$ac.'.html');