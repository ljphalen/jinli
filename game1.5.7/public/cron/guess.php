<?php
include 'common.php';
/**
 *从BI拉取 -- 猜你喜欢
 */

//get bi recommend //351474800
$dataPath = Common::getConfig('siteConfig', 'guessPath');
$path = $dataPath.'/dlv_game_recomend_imei.txt';

$lc_guess = Common::getDao("Client_Dao_Guess");
//本地数据清除操作
$lc_guess->turncateGuess();
//在直接导入
$lc_guess->loadInsertData($path);
if(!$lc_guess){
	if(ENV == 'product'){
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
		$title = "【猜你喜欢无数据】";
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
}

//重新设置下次读表写表
$cache = Common::getCache();
$ckey = 'guess_table';
$readTable = $cache->get($ckey);

$cache->set("guess_table", $readTable ? 0 : 1);

echo CRON_SUCCESS;
