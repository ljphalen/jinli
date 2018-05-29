<?php
include 'common.php';

//推荐列表的游戏的有效期检查
$params['ad_type'] = Client_Service_Ad::AD_TYPE_SUBJECT;
$params['status'] =  Client_Service_Ad::AD_STATUS_OPEN;
$startTime = strtotime(date('Y-m-d H', strtotime("-1 hour")));
$endTime = strtotime(date('Y-m-d H'));
$params['end_time'][0] = array('>=',  $startTime);
$params['end_time'][1] = array('<=',  $endTime);
$recommendList = Client_Service_Ad::getsBy($params);
$recommengUpdataFlag = 0;
foreach ($recommendList as $key=>$val){
	if(Client_Service_Ad::gameIdisExistInRecomendGameIds($val['link'])){
		$recommengUpdataFlag = 1;
		break;
	}
}
if($recommengUpdataFlag){
	Client_Service_Ad::updateRecommendListVersionToCache();
}

//推荐列表中图片有效期检查
$params['ad_type'] = Client_Service_Ad::AD_TYPE_RECPIC;
$params['status'] =  Client_Service_Ad::AD_STATUS_OPEN;
$params['end_time'][0] = array('>=',  $startTime);
$params['end_time'][1] = array('<=',  $endTime);
$recommendList = Client_Service_Ad::getsBy($params);
$recommengUpdataFlag = 0;
foreach ($recommendList as $key=>$val){
	if(Client_Service_Ad::gameIdisExistInRecomendGameIds($val['link'])){
		$recommengUpdataFlag = 1;
		break;
	}
}
if($recommengUpdataFlag){
	Client_Service_Ad::updateRecommendListVersionToCache();
}
echo CRON_SUCCESS;
exit;