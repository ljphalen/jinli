<?php
include 'common.php';
/**
 *update dlv_game_recomend create_time
 */


//@return Client_Dao_BIRecommend

//get bi recommend
$bi_rank = Common::getDao("Client_Dao_BIRecommend");
//$result = $bi_rank->getsBy(array('DAY_ID'=>date('Ymd', strtotime("-2 day"))));
$result = $bi_rank->getAll();

//insert local recommend
$tmp = array();
$lc_rank = Common::getDao("Client_Dao_Recommend");
if($result){
	$clear = $lc_rank->turncateRecommend();
	foreach($result as $key=>$value){
		$tmp['ID'] = '';
		$tmp['GAMEC_RESOURCE_ID'] = $value['GAMEC_RESOURCE_ID'];
		$tmp['GAMEC_RECOMEND_ID'] = $value['GAMEC_RECOMEND_ID'];
		$tmp['CREATE_DATE'] = $value['CREATE_DATE'];
		$result = $lc_rank->insert($tmp);
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
	$title = "【推荐游戏无数据】";
	$body = date("Y-m-d H:i:s",Common::getTime())."当前从BI获取数据为空，请BI确认！"."<br><br><br>";
	$body.="李畅华"."<br>";
	$body.="深圳市金立通信设备有限公司"."<br>";
	$body.="移动互联网运营部"."<br>";
	$body.="地址:深圳市福田区深南大道7888号东海国际中心B座 16楼 "."<br>";
	$body.="MP：13691655852"."<br>";
	foreach($to as $key=>$value){
		Common::sendEmail($title, $body , $value, $author, $type = 'HTML');
	}
}



//mark sync time





echo CRON_SUCCESS;