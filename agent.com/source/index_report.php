<?php
//更新渠道商缓存
$company_filename = SROOT.'/data/cache/company/modtime'.CACHE_FILE_EXT;
$modtime = file_get_contents($company_filename);
if(time()-$modtime < 86400){
	/* $db = db::factory(get_db_config());
	$task = new task($db);
	$task->update_company_cache();
	$db = null;
	$task = null; */
	$url = get_hosturl().'/task.php?ac=update&forcecom=1';
	do_post_async($url);
}

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

$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;
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
	$dateEnd  = date('Y-m-d', strtotime("$dateStart +1 month -1 day"));
	//$dateStart = '2012-12-01';
	//$dateEnd = '2012-12-31';
	$dataPrompt="本月报表：&nbsp;&nbsp;".$dateStart."&nbsp;—&nbsp;".$dateEnd;
}


//连接数据库
//require 'sys/libs/db.class.php';
$db = null;
if($detail){
	//连接数据库
	$db = db::factory(get_db_config());
}
//调用数据接口
//require 'sys/libs/report.class.php';
$report = new report($db);

if($keyword!='' && !is_numeric($keyword)){
	//取公司渠道号
	$companys = $report->get_channel_list($channeltype);
	foreach($companys as $k=>$v){
		if(strpos($v['name'],$keyword)!==false){
			$clientid = $v['clientid'];
			break;
		}
	}
}elseif(is_numeric($keyword)){
	$clientid = intval($keyword);
}

$subclientid = -1;
//取登录用户信息
$levels = $sess_userinfo['level']>200?200:$sess_userinfo['level'];
if($levels<200){
	$channeltype = $sess_userinfo['channeltype'];
	$clientid    = $sess_userinfo['clientid'];
	$subclientid = $sess_userinfo['clientids'];
}

//print_r($report->get_cache_filename('2013-3-1', '2013-3-27'));exit;

$lists = array();
//不同用户取不同的数据
if($levels == 30){
	//汇总
	$records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, 1, 1);
	$lists  = $records['list'];
	$counts  = $records['counts'];
	
	//子渠道
	$records = $report->get_report_sub_list($gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
	$totalrow  = $records['rows'];
	$list  = $records['list'];

}elseif($levels == 10){
		//子渠道
		$records = $report->get_report_sub_list($gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page,$subclientid);
		$totalrow  = $records['rows'];
		$list  = $records['list'];
		$counts  = $records['counts'];
}else{
	//管理员级别的数据	
	$records = $report->get_report_list($channeltype, $gameid, $clientid, $dateStart, $dateEnd, $pagesize, $page);
	$totalrow  = $records['rows'];
	$list  = $records['list'];
	$counts  = $records['counts'];
}

//格式化数组
foreach ($list as $key=>$val){
	$list[$key] = array('clientid'         =>$val['clientid'],
			            'clientids'        =>$val['clientids'],
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
//格式化数组
foreach ($lists as $key=>$val){
	 $lists[$key] = array('clientid'         =>$val['clientid'],
			'clientids'        =>$val['clientids'],
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

//导出数据
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
		//$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","消费订单数","消费用户数","消费金额","activeusermonth"=>"当月活跃登录用户数","avgplaygamemonth"=>"当月用户平均游戏时长");
		if($channeltype == 2){
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费总金额");
		}else{
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费总金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
		
	}
	else
	{
		if($channeltype == 2){
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费总金额");
		}else{
			$tableArr[]=array("子渠道号",'clientid'=>"渠道号","公司名称","注册用户数","登录用户数","消费订单数","消费用户数","消费总金额","爱贝消费金额","移动消费金额",'人均充值次数','付费率','ARPPU值','用户价值','首次登录人数 ','首次充值人数 ','首次充值的次数 ','首次充值金额','首次付费率','首次ARPPU值');
		}
		
	}
	
	
	
	//取配置信息
	$paygroups = get_configs('payways_config');
	$setting = new setting();
	$our_paytype = $setting->get_our_paytype();
	$channel_paytype = $setting->get_channel_paytype();
	
	/* if($detail && $levels>=200){
		foreach($paygroups as $key=>$val){
			$subsnum = count($val['subs']);
			if($subsnum > 1){
				foreach($val['subs'] as $k=>$v){
					$tableArr[1][] = $v['name'];
				}
			}else{
				if(!empty($val['subs'])){
				}else{
					$tableArr[1][]= $val['name'];
				}
			}
		}
	} */
	
	foreach($list as $k=>$v){
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
	    /* 	if($selectDate=="month"&&$levels==200){
			$value['activeusermonth'] = $v['activeusermonth'];
			$value['avgplaygamemonth'] = $v['avgplaygamemonth'];
		} */
		
		//设置不可用
		$disable=0;
		//附加详细支付方式
		if($detail && $levels>=200 && $disable){
			//$cols = array();
			$values = array();
			
			/*foreach($paygroups as $k=>$v){
				if($k=='yd')continue;
				if(!in_array($k,$our_paytype)){
					if(!(isset($channel_paytype[$clientid])&&$channel_paytype[$clientid]==$k)){
						unset($paygroups[$k]);
					}
				}
			}*/
			@ini_set('memory_limit','256M');
			$paywaydata = $report->get_paywaylist($dateStart,$dateEnd,$value['clientid'],-1,$gameid);
			foreach($paywaydata as $k=>$v){
				if($k>=51 && $k<=59){
					$paywaydata['50']['consumeorders'] += $v['consumeorders'];
					$paywaydata['50']['consumeusers'] += $v['consumeusers'];
					$paywaydata['50']['consumemoney'] += $v['consumemoney'];
					unset($paywaydata[$k]);
				}
			}
			
			foreach($paygroups as $key=>$val){
				$subsnum = count($val['subs']);
				if($subsnum > 1){
					$i=0;
					foreach($val['subs'] as $k=>$v){
						$i++;
						$consumemoney = isset($paywaydata[$k])?$paywaydata[$k]['consumemoney']:0;
						$totals += $consumemoney;
						
						//$cols[] = $v['name'];
						$value[] = $consumemoney;
					}
				}else{
					if(!empty($val['subs'])){
						/* 扩展用
						 * foreach($val['subs'] as $k=>$v){
						$paytable .= '<tr><th colspan="2">'.$val['name'].'</th><td>&nbsp;</td></tr>';
						} */
					}else{
						$consumemoney = isset($paywaydata[$key])?$paywaydata[$key]['consumemoney']:0;
			
						/* if($key==-1){
							$consumemoney = $realcount - $totals;
						} */
						
						if(!in_array($key,$our_paytype)){
							if(!(isset($channel_paytype[$value['clientid']])&&$channel_paytype[$value['clientid']]==$key)){
								$consumemoney = 0;
							}
						}
			
						$totals += $consumemoney;
						
						//$cols[] = $val['name'];
						$value[] = $consumemoney;
							
					}
						
				}
			
			}
		}
		
		
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
		'selectDate' => $selectDate,
		'dateStart' => $dateStart,
		'dateEnd' => $dateEnd,
		'pagesize' => $pagesize,
		'page' => $page,
		'pages' => $pages,
		'pageurl' => $pageurl,
		'levels' => $levels,
		'show_graph' => '查看图表',
		'keyword' => $keyword,
		
		'totalrow' => $totalrow,
		'list' => $list,
		'lists' => $lists,
		'period_count' => $counts,
		'ac' => $ac,
		'dataPrompt' => $dataPrompt,
		
		'agentname' => $sess_userinfo['name'],
		
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');