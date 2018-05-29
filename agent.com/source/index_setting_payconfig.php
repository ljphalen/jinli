<?php
// smarty
require 'plugin/smarty.php';

$setting = new setting();
//取支付方式
$payways = $setting->get_payways();

//取当前渠道自有支付方式
$channel_paytype = $setting->get_channel_paytype();
//取当前我方支付方式
$our_paytype = $setting->get_our_paytype();

$payways_new = array();
$payways_theirs = array();
foreach($payways as $k=>$v){
	if((string)$k=='yd'){
		foreach($v['subs'] as $x=>$y){
			$payways_new[$x] = $y['name'];
		}
	}else{
		$payways_new[$k]=$v['name'];
		
		$payways_theirs[$k]=$v['name'];
	}
}
//print_r($payways_new);

if (isset($_POST['smt'])){
	if(empty($_POST['ours_way'])){
		alertMsg('请选择我方支付方式！');
	}
	//我方支付方式
	$setting->set_our_paytype($_POST['ours_way']);
	
	//渠道自有支付方式
	$payways_theirs = array();
	$i=0;
	foreach($_POST['clientid'] as $k=>$v){
		$clientid = intval($v);
		$payway = trim($_POST['payway'][$k]);
		if($clientid<=0||(string)$payway==='')continue;
		$i++;
		$payways_theirs[$clientid] = $payway;
	}
	
	if(count($payways_theirs)<$i){
		alertMsg('渠道自有支付方式渠道号有重复，请重新输入！');
	}
	
	$setting->set_channel_paytype($payways_theirs);
	alertMsg('修改成功！','index.php?ac='.$ac);
}

//用于新增支付方式
for($i=1;$i<=5;$i++){
	$channel_paytype[] = '';
}
//print_r($payways_theirs);
$tpl_vars = array(
		'ac' => $ac,
		'actionText' => '我方他方支付方式配置',
		'configs' => $payways_new,
		'channel_paytype' => $channel_paytype,
		'payways_theirs' => $payways_theirs,
		'our_paytype' => $our_paytype,
		);



// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');