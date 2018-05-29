<?php
// smarty
require 'plugin/smarty.php';

$toexcel=get('toexcel');
$reporttype  = get('reporttype')?get('reporttype'):'day';
$channeltype  = get('channeltype')?intval(get('channeltype')):1;

$gameid  = intval(get('gameid'));
$clientid  = intval(get('clientid'));
$subclientid  = intval(get('subclientid'));
$keyword  = get('keyword');
$dateStart  = get('dateStart');
$dateEnd  = get('dateEnd');
$selectDate  = get('selectDate');

$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;
if($toexcel){
	$pagesize = 0;
}

$levels = $sess_userinfo['level']>200?200:$sess_userinfo['level'];
if($levels<200){
	$channeltype = $sess_userinfo['channeltype'];
	$clientid = $sess_userinfo['clientid'];
}

if($levels==10){
	$subclientid = $sess_userinfo['clientids'];
}

if(empty($dateStart) && empty($dateEnd)){
	$dateStart = date('Y-m-d',strtotime('-1 day'));
	$dateEnd = date('Y-m-d',strtotime('-1 day'));
}


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
	$this->dataPrompt="本月报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
}

if($dateStart!=$dateEnd)
{
	$thedates=$dateStart.'~'.$dateEnd;
}
else
{
	$thedates=$dateStart;
}

//调用数据接口
$report = new report();

if($keyword!='' && !is_numeric($keyword)){
	
	if($levels>=200){
		//取公司渠道号
		$companys = $report->get_channel_list(1)+$report->get_channel_list(2);
		foreach($companys as $k=>$v){
			if(strpos($v['name'],$keyword)!==false){
				$clientid = $v['clientid'];
				break;
			}
		}
	}else{
		//取子渠道号
		$companys = $report->get_subchannel_list($clientid);
		foreach($companys as $k=>$v){
			if(strpos($v['name'],$keyword)!==false){
				$subclientid = $v['clientids'];
				break;
			}
		}
	}
}elseif(is_numeric($keyword)){
	if($levels>=200){
		$clientid = intval($keyword);
	}else{
		$subclientid = intval($keyword);
	}
}

//print_r($report->get_cache_filename('2013-3-1', '2013-3-27'));exit;


if($subclientid==-1){
	$records = $report->get_report_count_list($reporttype, $gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
}else{
	$records = $report->get_report_sub_count_list($reporttype, $gameid, $clientid, $subclientid, $dateStart, $dateEnd, $pagesize, $page);
}

$totalrow  = $records['rows'];
$list  = $records['list'];
$counts  = $records['counts'];
//print_r($list);

//var_dump($list);
 //格式化数组
foreach ($list as $key=>$val){
	$list[$key] = array(
	        'today'           => $val['today'],
	        'clientid'         =>$val['clientid'],
			'name'             =>$val['name'],
			'channeltype'      =>$val['channeltype'],
			'registerusers'    =>$val['registerusers'],
			'loginusers'       =>$val['loginusers'],
			'activeusermonth'  =>$val['activeusermonth'],
			'avgplaygamemonth' =>$val['avgplaygamemonth'],
			'consumeorders'    =>$val['consumeorders'],
			'consumeusers'     =>$val['consumeusers'],
			'consumemoney'     =>$val['consumemoney'],
			'consumemoneyipay' =>$val['consumemoneyipay'],
			'consumemoneyyd'   =>$val['consumemoneyyd'],
			'avgpaymenttime'   =>($val['consumeorders']== 0 || $val['consumeusers']==0 )?0:sprintf("%01.2f",$val['consumeorders']/$val['consumeusers']),
			'paymentrate'      =>($val['consumeusers']== 0 || $val['loginusers']==0 )?0:sprintf("%01.2f",$val['consumeusers']/$val['loginusers']*100).'%',
			'ARPPU'            =>($val['consumemoney']== 0 || $val['consumeusers']==0 )?0:sprintf("%01.2f",$val['consumemoney']/$val['consumeusers']),
			'registerusersvalue'=>($val['consumemoney']== 0 || $val['registerusers']==0 )?0:sprintf("%01.2f",$val['consumemoney']/$val['registerusers']),
			'firstloginnum'        =>$val['firstloginnum'],
			'firstpaymentusernum'  =>$val['firstpaymentusernum'],
			'firstpaymenttime'     =>$val['firstpaymenttime'],
			'firstpaymentmoney'    =>$val['firstpaymentmoney'],
			'firstpaymentrate'     =>($val['firstpaymentusernum']== 0 || $val['loginusers']==0 )?0:sprintf("%01.2f",$val['firstpaymentusernum']/$val['loginusers']*100).'%',
			'firstARPPU'           =>($val['firstpaymentmoney']== 0 || $val['firstpaymentusernum']==0 )?0:sprintf("%01.2f",$val['firstpaymentmoney']/$val['firstpaymentusernum'])
	);
}
 


if($toexcel){
	if($dateStart!=$dateEnd)
	{
		$thedates=$dateStart.'~'.$dateEnd;
	}
	else
	{
		$thedates=$dateStart;
	}

	$tableArr=array(array("日期：", $thedates));
	if($reporttype=="month"&&$levels==200){
		//$tableArr[]=array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","消费订单数","消费用户数","消费金额","activeusermonth"=>"当月活跃登录用户数","avgplaygamemonth"=>"当月用户平均游戏时长");
		if($channeltype == 2){
			$tableArr[]=array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额");
		}else{
			$tableArr[]=array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
		
	}else{
		if($channeltype == 2){
			$tableArr[]=array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额");
		}else{
			$tableArr[]=array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
	}
	
	if($subclientid==-1){
		unset($tableArr[1][1]);
	}
	
	if($reporttype=="month"){
		$tableArr[1][0] = '月份';
	}

	foreach($list as $k=>$v){
		$value = array();
		$value['today'] = $v['today'];
		if($subclientid>-1){
			$value['clientids'] = isset($v['clientids'])?$v['clientids']:0;
		}
		$value['clientid'] = $v['clientid'];
		$value['name'] = $v['name'];

		$value['registerusers'] = $v['registerusers'];
		$value['loginusers'] = $v['loginusers'];
		$value['consumeorders'] = $v['consumeorders'];
		$value['consumeusers'] = $v['consumeusers'];
		$value['consumemoney'] = $v['consumemoney'];
		if($channeltype != 2){
			$value['consumemoneyipay']= $v['consumemoneyipay'];
			$value['consumemoneyyd'] = $v['consumemoneyyd'];
		}
		$value['avgpaymenttime'] = $v['avgpaymenttime'];
		$value['paymentrate']    = $v['paymentrate'];
		$value['ARPPU'] = $v['ARPPU'];
		$value['registerusersvalue'] = $v['registerusersvalue'];
		$value['firstloginnum'] = $v['firstloginnum'];
		$value['firstpaymentusernum'] = $v['firstpaymentusernum'];
		$value['firstpaymenttime'] = $v['firstpaymenttime'];
		$value['firstpaymentmoney'] = $v['firstpaymentmoney'];
		$value['firstpaymentrate'] = $v['firstpaymentrate'];
		$value['firstARPPU'] = $v['firstARPPU'];
		/* if($reporttype=="month"&&$levels==200){
			$value['activeusermonth'] = $v['activeusermonth'];
			$value['avgplaygamemonth'] = $v['avgplaygamemonth'];
		}
        */


		$tableArr[]=$value;
	}

	//print_r($tableArr);exit;
	excel::arrayToFile($tableArr,"report.xls");
	exit;
}

//分页
$pages = ceil($totalrow/$pagesize);
$pageurl = $_SERVER['REQUEST_URI'];
if(strpos($pageurl, 'page=')!==false){
	$pageurl = preg_replace("/&?page\=\d*/","",$pageurl);
	$pageurl = str_replace('?&','?',$pageurl);
}

$tpl_vars = array(
		'games' => $GLOBALS['ini_games'],
		'gameid' => $gameid,
		'channeltype' => $channeltype,
		'clientid' => $clientid,
		'clientids' => $subclientid,
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
		'period_count' => $counts,
		'ac' => $ac,
		'reporttype' => $reporttype,
		'channel_types' => $GLOBALS['ini_channeltypes'],
		'dataPrompt' => $dataPrompt,
		
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');