<?php 
include 'common.php';
/**
 * 订单数据
 */
$page = 1;
if (date('w') != 5) exit;
do {
	$users = Gc_Service_User::getUserSliverBetween('30.00', '100.00');
	foreach ($users as $key=>$user) {
		$ret = Api_Gionee_Pay::coinAdd(array(
			'out_uid'=>$user['out_uid'],
			'coin_type'=>'2',
			'coin'=>'20.00',
			'msg'=>'恭喜您获得20银币（注册即送100银币，本周赠送给您20银币）。'));
		$ret = Gc_Service_User::incrementTotalSliver('20.00', $user['out_uid']);
	}
	$page++;
	sleep(2);
} while ($total>($page * 100));

echo CRON_SUCCESS;
?>