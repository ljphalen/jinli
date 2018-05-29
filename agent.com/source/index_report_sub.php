<?php
// smarty
require 'plugin/smarty.php';

$toexcel=get('toexcel');
$channeltype  = get('channeltype')?intval(get('channeltype')):1;

$gameid  = intval(get('gameid'));
$clientid  = intval(get('clientid'));
$dateStart  = get('dateStart');
$dateEnd  = get('dateEnd');
$selectDate  = get('selectDate');

$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;
if($toexcel){
	$pagesize = 0;
}

//昨天报表
if($selectDate=="yesterday")
{
	$dateStart  = date("Y-m-d",strtotime('-1 day'));
	$dateEnd  = $dateStart;
}
//最近7天报表
if($selectDate=="hebdomad")
{
	$dateStart  = date("Y-m-d",strtotime('-7 day'));
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
}
//本月报表
if($selectDate=="month")
{
	$dateStart  = date("Y-m-1");
	$dateEnd  = date("Y-m-d",strtotime('-1 day'));
}


if(empty($dateStart) && empty($dateEnd)){
	$dateStart = date('Y-m-d',strtotime('-1 day'));
	$dateEnd = date('Y-m-d',strtotime('-1 day'));
}



//取登录用户信息
$levels = $sess_userinfo['level']>200?200:$sess_userinfo['level'];
if($levels<200){
	$channeltype = $sess_userinfo['channeltype'];
	$clientid = $sess_userinfo['clientid'];
	$subclientid = $sess_userinfo['clientids'];
}


//连接数据库
//require 'sys/libs/db.class.php';
//$db = db::factory('db1');
//调用数据接口
//require 'sys/libs/report.class.php';
$report = new report();

//取渠道信息
$companys = $report->get_channel_list(1)+$report->get_channel_list(2);
$company_info = $companys[$clientid];
$channeltype = $company_info['channeltype'];
$companys = null;

//汇总
$records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, 1, 1);
$totalrow  = $records['rows'];
$list  = $records['list'];
//格式化数组
foreach ($list as $key=>$val){
	$list[$key] = array(
			'clientid'         =>$val['clientid'],
			'name'             =>$val['name'],
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
//子渠道
$records = $report->get_report_sub_list($gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
$totalrow  = $records['rows'];
$sublist  = $records['list'];
//print_r($list);
//格式化数组
foreach ($sublist as $key=>$val){
	$sublist[$key] = array(
			'clientid'         =>$val['clientid'],
			'name'             =>$val['name'],
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
	if($selectDate=="month"&&$levels==200)
	{
		//$tableArr=array(array("日期","子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","消费计单数","消费用户数","消费金额","分成收益总金额","activeusermonth"=>"当月活跃登录用户数","avgplaygamemonth"=>"当月用户平均游戏时长"));
		//$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","消费订单数","消费用户数","消费金额","activeusermonth"=>"当月活跃登录用户数","avgplaygamemonth"=>"当月用户平均游戏时长");
		if($channeltype == 2){
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额");
	
		}else{
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
	}
	else
	{
	if($channeltype == 2){
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额");
	
		}else{
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
	}


	foreach($sublist as $k=>$v){
		$value = array();
		$value['clientids'] = isset($v['clientids'])?$v['clientids']:0;
		$value['clientid'] = $v['clientid'];
		$value['name'] = $v['name'];

		$value['registerusers'] = $v['registerusers'];
		$value['loginusers'] = $v['loginusers'];
		$value['consumeorders'] = $v['consumeorders'];
		$value['consumeusers'] = $v['consumeusers'];
		$value['consumemoney'] = $v['consumemoney'];
		if($channeltype != 2){
			$value['consumemoneyipay'] = $v['consumemoneyipay'];
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
		/* if($selectDate=="month"&&$levels==200){
			$value['activeusermonth'] = $v['activeusermonth'];
			$value['avgplaygamemonth'] = $v['avgplaygamemonth'];
		} */

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
		'dateStart' => $dateStart,
		'dateEnd' => $dateEnd,
		'pagesize' => $pagesize,
		'page' => $page,
		'pages' => $pages,
		'pageurl' => $pageurl,
		'levels' => $levels,
		'show_graph' => '查看图表',
		
		'totalrow' => $totalrow,
		'sublist' => $sublist,
		'lists' => $list,
		'ac' => $ac,
		
		'agentname' => $company_info['name'],
		
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');