<?php
include 'common.php';
/**
 *刷新令牌
 */

$topApi = new Api_Top_Service();
$page = 1;
$num=0;

do {
	//刷新令牌
	list($total, $userlist) = Gou_Service_User::getListByTime($page, 100);
	if ($total == 0) break;
	foreach ($userlist as $key=>$value) {
		$result = $topApi->refreshToken($value['taobao_refresh']);
		if($result) {
			$ret = json_decode($result,true);
			$data = array(
					'taobao_session'=>$ret['access_token'],
					'taobao_refresh'=>$ret['refresh_token'],
					'taobao_refresh_time'=>Common::getTime() + $ret['expires_in'],
					'taobao_refresh_expires'=>$ret['re_expires_in']
			);
			Gou_Service_User::updateUser($data, $value['id']);
		}
			
		$num++;
		
	}
	$page++;
	sleep(2);
} while ($total>($page * 100));

echo CRON_SUCCESS;