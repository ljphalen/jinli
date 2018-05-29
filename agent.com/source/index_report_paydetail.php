<?php
// smarty
require 'plugin/smarty.php';

$toexcel=get('toexcel');
$gameid  = intval(get('gameid'));
$clientid  = intval(get('clientid'));
$subclientid = get('subclientid');
$dateStart  = get('dateStart');
$dateEnd  = get('dateEnd');
$name  = get('name');

$pagesize = get('pagesize')?intval(get('pagesize')):15;
$page = get('page')?intval(get('page')):1;

$levels = 200;

if(empty($dateStart) && empty($dateEnd)){
	$dateStart = date('Y-m-d',strtotime('-1 day'));
	$dateEnd = date('Y-m-d',strtotime('-1 day'));
}

//$dateStart = date('2012-12-25');
//$dateEnd = date('2012-12-27');

//连接数据库
$db = db::factory(get_db_config());
//调用数据接口
//require 'sys/libs/report.class.php';
$report = new report($db);

//取渠道信息
$companys = $report->get_channel_list(1)+$report->get_channel_list(2);
$company_info = $companys[$clientid];
$channeltype = $company_info['channeltype'];
$companys = null;




if($levels==30){
	$clientid=$this->user["clientid"];
	//$clientid=$this->input->get('clientid', TRUE);
	//$subclientid=$this->input->get('subclientid', TRUE);
}
 
$paygroups = get_configs('payways_config');

$setting = new setting();
$our_paytype = $setting->get_our_paytype();
$channel_paytype = $setting->get_channel_paytype();
foreach($paygroups as $k=>$v){
	if($k==='yd')continue;
	if(!in_array($k,$our_paytype)){
		if(!(isset($channel_paytype[$clientid])&&$channel_paytype[$clientid]==$k)){
			unset($paygroups[$k]);
		}
	}
}
//print_r($paygroups);
$paywaydata = $report->get_paywaylist($dateStart,$dateEnd,$clientid,$subclientid,$gameid);
$paytable='<table width="100%" cellspacing="1" cellpadding="2" id="list-table">';
$totals = 0;
$realcount = 0;

/* $paywaydata =   array (
		13 =>
		array (
				'payway' => '13',
				'consumeorders' => '7056',
				'consumeusers' => '8484',
				'consumemoney' => '6401',
		),
		'50_8180' =>
		array (
				'payway' => '50',
				'consumeorders' => '2842',
				'consumeusers' => '1812',
				'consumemoney' => '1266',
		),
		51 =>
		array (
				'payway' => '51',
				'consumeorders' => '842',
				'consumeusers' => '182',
				'consumemoney' => '166',
		),
		52 =>
		array (
				'payway' => '52',
				'consumeorders' => '82',
				'consumeusers' => '812',
				'consumemoney' => '266',
		),
);  */

//未统计的加入未知
/* if(!empty($paywaydata)){
	foreach($paywaydata as $k=>$v){
		$realcount +=$v['consumemoney'];
	}
} */


$ipay_data = array();

foreach($paywaydata as $k=>$v){
	if($k=='50'){
		$ipay_data[50]['consumeorders'] = $v['consumeorders'];
		$ipay_data[50]['consumeusers'] = $v['consumeusers'];
		$ipay_data[50]['consumemoney'] = $v['consumemoney'];
	}
	
	if($k>=51 && $k<=59){
		$ipay_data[$k]['consumeorders'] = $v['consumeorders'];
		$ipay_data[$k]['consumeusers'] = $v['consumeusers'];
		$ipay_data[$k]['consumemoney'] = $v['consumemoney'];
		
		$paywaydata['50']['consumeorders'] += $v['consumeorders'];
		$paywaydata['50']['consumeusers'] += $v['consumeusers'];
		$paywaydata['50']['consumemoney'] += $v['consumemoney'];
		unset($paywaydata[$k]);
	}
}


//print_r($ipay_data);exit;
//print_r($paygroups);exit;
foreach($paygroups as $key=>$val){
	$subsnum = count($val['subs']);
	if($subsnum > 1){
		$i=0;
		foreach($val['subs'] as $k=>$v){
			$i++;
			$consumemoney = isset($paywaydata[$k])?$paywaydata[$k]['consumemoney']:0;
			$totals += $consumemoney;
			if(1==$i){
				$paytable .= '<tr><th rowspan="'.$subsnum.'">'.$val['name'].'</th><th>'.$v['name'].'</th><td>'.number_format($consumemoney).'</td></tr>';
			}else{
				$paytable .= '<tr><th>'.$v['name'].'</th><td>'.number_format($consumemoney).'</td></tr>';
			}
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

			$totals += $consumemoney;
			if($key=='50'){
				$paytable_sub = '<div id="ipay_detail" style="text-align:left;'.($toexcel?'':'display:none;').'"><table border="1" cellpadding="1" cellspacing="1" style="border-collapse:collapse">';
				foreach($ipay_data as $x=>$y){
					$paytable_sub .= '<tr><td style="text-align:left;width:22%">'.$GLOBALS['ini_ipay_paytypes'][$x].'：</td><td style="text-align:left;">'.$y['consumemoney'].'</td></tr>';
				}
				$paytable_sub .= '</table></div>';
				
				if($toexcel){
					$paytable .= '<tr><th colspan="2">'.$val['name'].'</th><td>'.number_format($consumemoney).$paytable_sub.'</td></tr>';
				}else{
					$paytable .= '<tr><th colspan="2">'.$val['name'].'</th><td><a href="javascript:;" onclick="show_ipaydetail();">'.number_format($consumemoney).'</a>'.$paytable_sub.'</td></tr>';
				}
				
			}else{
				$paytable .= '<tr><th colspan="2">'.$val['name'].'</th><td>'.number_format($consumemoney).'</td></tr>';
			}
			
		}
		 
	}

}
$paytable .= '<tr>
<th colspan="2"><strong>总收入</strong></th>
<td><strong>'.number_format($totals).'</strong></td>
</tr></table>';
 


$tpl_vars = array(
		'dateStart' => $dateStart,
		'dateEnd' => $dateEnd,
		'clientid' => $clientid,
		'subclientid' => $subclientid,
		'gameid' => $gameid,
		'name' => $name,
		'paytable' => $paytable,
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
if($toexcel){
	$filename = '消费金额明细.xls';
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=\"$filename\" ");
	header("Content-Transfer-Encoding: binary ");
	//header("Content-Length: ".strlen($paytable));
	$smarty->display('index_'.$ac.'_excel.html');
}else{
	$smarty->display('index_'.$ac.'.html');
}
