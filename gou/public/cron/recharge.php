<?php
include 'common.php';
/**
 *话费充值相关：查询余额、补发充值状态 每小时执行一次
 */

//查询余额
$recharge_sms = Gou_Service_Config::getValue('gou_recharge_sms');
if($recharge_sms) {
	$info = Api_Ofpay_Recharge::userInfo();
	if($info['userinfo']['ret_leftcredit'] < 1000.00) Common::sms('13510810201', '欧飞账户余额少于1000元，请及时充值！.');
}


//补发充值状态
$time = COmmon::getTime();
$start_time = $time - 3600*23;
$end_time = $time - 3600*24;
list(,$orders) = Gou_Service_Order::getsBy(array('order_type'=>4, 'rec_status'=>3, 'rec_order_id'=>array('!=', ""),  'create_time'=>array('>=', $start_time), 'create_time'=>array('<=', $end_time)), array('id'=>'DESC'));
if($orders) {
	foreach ($orders as $key=>$value) {
		$ret = Api_Ofpay_Recharge::reissue($value['trade_no']);
		if($ret['msginfo']['retcode'] != 1)  Common::sms('15818682200', '充值订单号'.$value['trade_no'].'状态补发失败！.');
	}
}

//补充
$time = COmmon::getTime();
$s_time = $time - 3600*12;
$e_time = $time - 3600*2;
list(,$orderlists) = Gou_Service_Order::getsBy(array('order_type'=>4, 'rec_status'=>3, 'status'=>2,  'create_time'=>array('>=', $s_time), 'create_time'=>array('<=', $e_time)), array('id'=>'DESC'));
if($orderlists) {
	foreach ($orderlists as $key=>$value) {
		//$ret = Api_Ofpay_Recharge::reissue($value['trade_no']);
		if($ret['msginfo']['retcode'] != 1)  Common::sms('15818682200', '充值订单号'.$value['trade_no'].'未充值！.');
	}
}

echo CRON_SUCCESS;