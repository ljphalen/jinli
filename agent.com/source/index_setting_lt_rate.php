<?php
// smarty
require 'plugin/smarty.php';

$setting = new setting();
$yd_rates = $setting->get_lt_rate();



if (isset($_POST['smt'])){
	$yd_rates = array();
	$i=0;
	foreach($_POST['clientid'] as $k=>$v){
		$payway = trim($v);
		$name = trim($_POST['name'][$k]);
		
		$clientid = trim($v);
		$rate = trim($_POST['rate'][$k]);
		if($clientid===''||$clientid<0||(string)$rate==='')continue;
		$i++;
		$yd_rates[$clientid] = $rate;
	}
	
	if(count($yd_rates)<$i){
		alertMsg('输入渠道号有重复，请重新输入！');
	}
	
	$setting->set_lt_rate($yd_rates);
	alertMsg('修改成功！','index.php?ac='.$ac);
}

//用于新增支付方式
for($i=1;$i<=5;$i++){
	$yd_rates[] = '';
}

$tpl_vars = array(
		'ac' => $ac,
		'actionText' => '联通分成比率配置',
		'configs' => $yd_rates,
		);

// 模板赋值
$smarty->assign($tpl_vars);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');