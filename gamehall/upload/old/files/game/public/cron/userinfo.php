<?php
include 'common.php';
/**
 *完善用户信息表的uuid数据，执行一次
 * 
 */
$page = 1;
do {
	//只扫上线的游戏
	list($total, $data) = Account_Service_User::getUserInfoList($page , 100);
	if(!$data) break;
	foreach($data as $key=>$value){
		//获取用户主表数据
		$user = Account_Service_User::getUser(array('uname' => $value['uname']));
		if(!$user) continue;
		//添加游戏到联通免流量
		Account_Service_User::updateUserInfo(array('uuid'=>$user['uuid']), array('id'=>$value['id']));
		echo "game-user-info:{$value['id']}-ok\r\n";
	}
	$page++;
	sleep(1);
} while ($total>(($page -1) * 100));

echo CRON_SUCCESS;