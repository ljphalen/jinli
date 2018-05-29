<?php 
include 'common.php';
/**
 * 订单数据
 */
$page = 1;
// if (date('w') != 5) exit;
do {
	list($total, $users) = Gou_Service_User::getHasFreezeUser($page, 100);
	foreach ($users as $key=>$user) {
// 		if (!in_array($user['freeze_sliver_coin'], array("40.00/3", "20.00/3"))) continue;
		if (strpos($user['freeze_sliver_coin'], '/') === false) continue;
		Gou_Service_User::laterSliverCoin($user['id']);
	}
	$page++;
	sleep(2);
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;
?>