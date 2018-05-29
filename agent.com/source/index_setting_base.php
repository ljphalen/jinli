<?php
// smarty
require 'plugin/smarty.php';

$setting = new setting();
$yd_rates = $setting->get_base_rate();



if (isset($_POST['smt'])){
	$yd_rates = array();
	$yd_rates['yd_pay_rate']=post('yd_pay_rate');
	$yd_rates['yd_pay_tax_rate']=post('yd_pay_tax_rate');
	$yd_rates['lt_pay_rate']=post('lt_pay_rate');
	$yd_rates['lt_pay_tax_rate']=post('lt_pay_tax_rate');
	$yd_rates['ipay_pay_rate']=post('ipay_pay_rate');
	
	$setting->set_base_rate($yd_rates);
	alertMsg('修改成功！','index.php?ac='.$ac);
}


$tpl_vars = array(
		'ac' => $ac,
		'actionText' => '对帐单基本信息配置',
		);

// 模板赋值
$smarty->assign($tpl_vars);
$smarty->assign($yd_rates);

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');