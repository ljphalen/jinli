<?php
include 'common.php';
/**
 * 从BI拉取 ---  最新榜
 */

//get bi rank
$bi_totlerank = Common::getDao("Client_Dao_BINewRank");
$result = $bi_totlerank->getsBy(array('day_id'=>date('Ymd', strtotime("-2 day"))));
//$result = $bi_totlerank->getAll();

//insert local rank
$lc_totlerank = Common::getDao("Client_Dao_NewRank");
if($result){
	//把今天的数据全部加入表
	foreach($result as $key=>$value){
		$tmp['day_id'] = $value['day_id'];
		$tmp['game_id'] = $value['game_id'];
		$tmp['rank_rate'] = $value['rank_rate'];
		$ret = $lc_totlerank->insert($tmp);
	}
	//插入成功
	if($ret){
		//清除3天前的数据
		$clear = $lc_totlerank->delByBeforDay(date('Ymd', strtotime("-3 day")));
	}
} else {
	
	$to =array(
			'huyk@gionee.com',
	        'wanghj@gionee.com',
			'jiangjc@gionee.com',
			'xiehx@gionee.com',
			'liuyp@gionee.com',
			'fanch@gionee.com',
			'lichanghua@gionee.com',
			'luojiapeng@gionee.com',
			'xiangyuan@gionee.com',
			'songcc@gionee.com',
			'zhongqh@gionee.com',
			'panfei@gionee.com',
			'huangab@gionee.com',
			'chenzhan@gionee.com',
			'guohb@gionee.com',
	);
	$title = "【最新榜无数据】";
	$body = date("Y-m-d H:i:s",Common::getTime())."当前从BI获取数据为空，请BI确认！"."<br><br><br>";
	$body.="李畅华"."<br>";
	$body.="深圳市金立通信设备有限公司"."<br>";
	$body.="移动互联网运营部"."<br>";
	$body.="地址:深圳市福田区深南大道7008号阳光高尔夫大厦15楼 "."<br>";
	$body.="MP：13691655852"."<br>";
	foreach($to as $key=>$value){
		Common::sendEmail($title, $body , $value, $author, $type = 'HTML');
	}
}


//mark sync time





echo CRON_SUCCESS;