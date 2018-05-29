<?php
// smarty
require 'plugin/smarty.php';

$setting = new setting();
$payways = $setting->get_payways();

$payways_thirdpart = array();
foreach($payways as $k=>$v){
	//去除移动、爱贝支付、未知
	$k = (string)$k;
	if($k=='yd'||$k=='50_8180'||$k=='50'||$k=='-1')continue;
	$payways_thirdpart[$k]=$v;
}
//print_r($payways_thirdpart);

if (isset($_POST['smt'])){
	
	if(empty($_POST['payway'])){
		alertMsg('未输入相关配置！');
	}
	
	$payways_new = array();
	$payways_new['yd'] = $payways['yd'];
	$payways_new[50] = $payways[50];
	$payways_new['50_8180'] = $payways['50_8180'];
	$i=0;
	$counts=array();
	foreach($_POST['payway'] as $k=>$v){
		$payway = trim($v);
		$name = trim($_POST['name'][$k]);
		if((string)$payway==='')continue;
		$i++;
		$counts[$payway]=$payway;
		$payways_new[$payway] = array('name'=>$name,'clientid'=>'0');
	}
	$payways_new[-1] = $payways[-1];
	
	if(count($counts)<$i){
		alertMsg('输入支付方式代码有重复，请重新输入！');
	}
	
	//ksort($payways_new);
	//var_export($payways_new);exit;
	$setting->set_payways($payways_new);
	alertMsg('修改成功！','index.php?ac='.$ac);
}

//用于新增支付方式
for($i=1;$i<=5;$i++){
	$payways_thirdpart[] = array();
}

$tpl_vars = array(
		'ac' => $ac,
		'actionText' => '第三方支付方式配置',
		'configs' => $payways_thirdpart,
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');