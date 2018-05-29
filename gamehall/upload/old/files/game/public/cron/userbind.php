<?php
include 'common.php';
/**
 *用户换帮数据处理 5分钟更新一次 每次出队列头
 * array('uuid'=>$data['u'], 'uname'=>$data['tn']),
 * $item = array(
		'uuid'=>'251C24232B454E1FA5E0E0CA72AAD62F',
		'new_uname'=>'15818712234',
		'old_uname'=>'15818712235'
);
 */
$item = Common::getQueue()->pop('GUQ:uname-bind');
if($item){
	//数据转换
	$item = json_decode($item, true);
	//国庆活动日志修改
	Festival_Service_GuoQing::upBydate(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname']));
	Festival_Service_GuoQingLog::updateLog(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname']));
	echo "Guoqinglog-ok\r\n";
	//礼包日志变更
	Client_Service_Giftlog::updateByGiftLog(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname'],'log_type'=>Client_Service_Giftlog::GRAB_GIFT_LOG));
	echo "Giftlog-ok\r\n";
	//抽奖日志变更
	Client_Service_FateLog::updateByFateLog(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname']));
	echo "FateLog-ok\r\n";
	//评论+评论日志
	Client_Service_Comment::updateByComment(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname']));
	Client_Service_CommentLog::updateLog(array('uname' => $item['new_uname']), array('uname'=>$item['old_uname']));
	echo "Comment-ok\r\n";
	//评分日志
	Resource_Service_Score::updateGameScoreLog(array('user' => $item['new_uname']), array('user'=>$item['old_uname']));
	echo "ScoreLog-ok\r\n";
}
echo CRON_SUCCESS;