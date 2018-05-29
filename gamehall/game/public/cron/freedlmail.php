<?php
include 'common.php';
/**
 * 免流量活动流量使用情况-每日凌晨执行
 */

function userWarning($activityId,$user_warning){
	list(,$data) = Freedl_Service_Usertotal::getsBy(array('activity_id'=>$activityId, 'total_consume' => array('>', $user_warning)));
	if(!$data) return '无超过预警用户！';
	foreach ($data as $value){
		$msg .= '用户imsi：' . $value['imsi'] . '  流量使用情况：' . $value['total_consume'].'M，' . '  已超过预警值。 <br/>';
	}
	return $msg;
}
//查询当前正在进行中的活动
$items = Freedl_Service_Hd::getActivatedItems(array('id' => 'DESC'));
if(empty($items)) exit("not found hd\r\n");
foreach ($items as $item){
	//查看整体活动消耗情况
	$title = "【免流量活动：{$item['title']} 使用情况】";
	$body = "免流量活动：{$item['title']} <br/>";
	$body .= "活动时间：" . date("Y-m-d H:i:s",$item['start_time'])." 至 ". date("Y-m-d H:i:s",$item['end_time']) . "<br>";
	$body .= "该活动用户使用的手机流量总计{$item['phone_consume']} M，整体预警额度 {$item['total_warning']} T！"."<br>";
	$body .= "活动流量整体预警情况：";
	$body .= ($item['phone_consume'] > ($item['total_warning'] * 1024 * 1024)) ? '已超过' : '未超过' . '<br><br>';
	$body .= "用户流量预警值：" . $item['user_warning'] . "M <br>";
	$body .= "用户流量预警情况： <br>";
	//查看超过预警值的用户imsi
	$body .= userWarning($item['id'], $item['user_warning']);
	$body .= "<br/>";
	$body .= '游戏大厅服务器-'.date("Y-m-d H:i:s",Common::getTime());
	//收件人格式处理
	if($item['email_warning'] && (stristr($item['email_warning'], '@') != false)){ 
		$to = str_replace('|', ',', $item['email_warning']);
		//发送预警邮件
		Common::sendEmail($title, $body, $to, '', 'HTML');
	}
	echo $title . "\r\n" . $body;
}
echo CRON_SUCCESS;
