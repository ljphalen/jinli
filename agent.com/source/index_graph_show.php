<?php
//$graph = new mygraph('pie');
//$graph->title = 'pie';
//$data = array('a'=>20,'b'=>10,'c'=>100);
//$graph->draw($data);
// smarty
require 'plugin/smarty.php';
$toexcel=get('toexcel');
$channeltype  = get('channeltype')?intval(get('channeltype')):1;

$gameid  = intval(get('gameid'));
$keyword  = get('keyword');
$dateStart  = get('dateStart');
$dateEnd  = get('dateEnd');
$selectDate  = get('selectDate');
$detail  = get('detail');
$sac = get('sac');

$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;
$graph = get('graph')?intval(get('graph')):0;
$shape = get('shape')?get('shape'):1;
if($toexcel){
	$pagesize = 0;
}


if(empty($dateStart) && empty($dateEnd)){
	$dateStart = date('Y-m-d',strtotime('-1 day'));
	$dateEnd = date('Y-m-d',strtotime('-1 day'));
}

//$dateStart = date('2012-12-25');
//$dateEnd = date('2012-12-27');

$dataPrompt="报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
//昨天报表
if($selectDate=="yesterday")
{
	$dateStart  = date("Y-m-d",strtotime('-1 day'));
	$dateEnd  = $dateStart;
	$dataPrompt="昨日报表：&nbsp;&nbsp;".$dateStart;
}
//最近7天报表
if($selectDate=="hebdomad")
{
	$dateStart  = date("Y-m-d",strtotime('-7 day'));
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
	$dataPrompt="最近7天报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
		
}
//本月报表
if($selectDate=="month")
{
	$dateStart  = date("Y-m-1");
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
	//$dateStart = '2012-12-01';
	//$dateEnd = '2012-12-31';
	$dataPrompt="本月报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
}


//取登录用户信息
$levels = $sess_userinfo['level']>200?200:$sess_userinfo['level'];
if($levels<200){
	$channeltype = $sess_userinfo['channeltype'];
	$clientid = $sess_userinfo['clientid'];
	$subclientid = $sess_userinfo['clientids'];
}

//print_r($report->get_cache_filename('2013-3-1', '2013-3-27'));exit;

$lists = array();


$tpl_vars = array(
		'games' => $GLOBALS['ini_games'],
		'gameid' => $gameid,
		'channeltype' => $channeltype,
		'selectDate' => $selectDate,
		'dateStart' => $dateStart,
		'dateEnd' => $dateEnd,
		'pagesize' => $pagesize,
		'page' => $page,
		'pages' => $pages,
		'pageurl' => $pageurl,
		'levels' => $levels,
		'show_graph' => '查看图表',
		
		'totalrow' => $totalrow,
		'list' => $list,
		'lists' => $lists,
		'period_count' => $counts,
		'ac' => $sac,
		'dataPrompt' => $dataPrompt,
    		'clientid' => $clientid,
		'clientids' => $subclientid,
		'reporttype' => $reporttype,
		'agentname' => $sess_userinfo['name'],
		
		);

$_GET['ac'] = 'graph_draw';
$url = http_build_query($_GET);
$_GET['shape'] = 2;
$url2 = http_build_query($_GET);
//var_dump($_GET);


// 模板赋值
$smarty->assign($tpl_vars);
$smarty->assign('url',$url);
$smarty->assign('url2',$url2);
// 显示模板的内容
$smarty->display('index_report_graph.html');