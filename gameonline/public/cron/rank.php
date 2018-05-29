<?php
include 'common.php';
/**
 * 从BI拉取游戏大厅排行榜 -- 下载最多(旧)
 */

//update uv
//@return Client_Dao_BIRank

//get bi rank
$bi_rank = Common::getDao("Client_Dao_BIRank");
$result = $bi_rank->getsBy(array('DAY_ID'=>date('Ymd', strtotime("-2 day"))));
//$result = $bi_rank->getAll();

//insert local rank
$lc_rank = Common::getDao("Client_Dao_Rank");
if($result){
	//把今天的数据全部加入表
	foreach($result as $key=>$value){
		$tmp['DAY_ID'] = $value['DAY_ID'];
		$tmp['GAME_ID'] = $value['GAME_ID'];
		$tmp['DL_TIMES'] = $value['DL_TIMES'];
		$tmp['CRT_TIME'] = $value['CRT_TIME'];
		$ret = $lc_rank->insert($tmp);
	}
	//插入成功
	if($ret){
		//清除3天前的数据
		$clear = $lc_rank->delByBeforDay(date('Ymd', strtotime("-1 month -1 day")));
	}
} else {
    
	$to =array(
			'jiangjc@gionee.com',
			'yaozhuo@gionee.com',
			'wujiahan@gionee.com',
			'xiehx@gionee.com',
			'liuyp@gionee.com',
			'fanch@gionee.com',
			'lichanghua@gionee.com',
			'luojiapeng@gionee.com',
			'xiangyuan@gionee.com',
			'songcc@gionee.com',
			'zhongqh@gionee.com',
			'panfei@gionee.com',
			'liuliangjun@gionee.com',
			'huangab@gionee.com',
			'chenzhan@gionee.com',
			'guohb@gionee.com',
	);
	$title = "【下载最多无数据】";
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