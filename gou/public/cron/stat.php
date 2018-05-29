<?php
include 'common.php';
/**
 * 点击量统计
 */


//hash入库
$queue = Common::getQueue();
$cache = Common::getCache();

do {
	$hash_data = $queue->noRepeatPop('tjhash');
	if($hash_data) {
		$hash = Stat_Service_ShortUrl::getBy(array('hash'=>$hash_data['hash']));
		if(!$hash){
			$data = array(
					'hash'=>$hash_data['hash'],
					'version_id'=>$hash_data['version_id'],
					'module_id'=>$hash_data['model_id'],
					'channel_id'=>$hash_data['channel_id'],
					'item_id'=>$hash_data['item_id'],
					'url'=>$hash_data['link'],
					'channel_code'=>$hash_data['channel_code'],
					'name'=>$hash_data['name'],
					'create_time'=>Common::getTime()
					);
            echo $hash_data['hash'] . " done.\n";
			$ret = Stat_Service_ShortUrl::add($data);
			if(!$ret) Common::log(array('msg'=>'hash:'.$data['hash'].' insert fail'), 'hash.log');
			
			Common::log(array('hash'=>$data['hash']), 'hash.log');
		} else {
		    Stat_Service_ShortUrl::update(array('create_time'=>Common::getTime()), $hash['id']);
           //echo $hash_data['hash'] . " exists. \n";
			//Common::log(array('msg'=> $hash_data['hash'].'已存在'), 'hash.log');
		}
	}
	
} while ($queue->len('tjhash') > 0);


//点击量统计

do {
	list($tjhash, $uid, $imei, $time, $ip) = $queue->noRepeatPop('tjdata');
	if($tjhash) {
		$tj_hash = Stat_Service_ShortUrl::getBy(array('hash'=>$tjhash));
		
		if($tj_hash) {
           $host = parse_url($tj_hash['url'], PHP_URL_HOST);
			$tj_data = array(
					'hash'=>$tjhash,
					'version_id'=>$tj_hash['version_id'],
					'module_id'=>$tj_hash['module_id'],
					'channel_id'=>$tj_hash['channel_id'],
					'item_id'=>$tj_hash['item_id'],
					'url'=>$tj_hash['url'],
					'host'=>$host,
					'host_id'=>crc32($host),
					'uid'=>$uid,
					'imei'=>$imei,
					'create_time'=>$time,
					'dateline'=>date('Y-m-d', $time),
					'name'=>$tj_hash['name'],
			        'channel_code'=>$tj_hash['channel_code'],
					'ip'=>$ip,
			);
			$ret = Stat_Service_Log::add($tj_data);
			if(!$ret) Common::log(array('msg'=>'log:'.$tjhash.' insert fail'), 'hash.log');
			
			//列表点击量统计
			if($tj_hash['module_id'] == 1 || $tj_hash['module_id'] == 47) Gou_Service_Ad::updateAdTJ($tj_hash['item_id']);
			if($tj_hash['module_id'] == 2) Cod_Service_Guide::updateTJ($tj_hash['item_id']);
			if($tj_hash['module_id'] == 10) Gou_Service_News::updateNewsTJ($tj_hash['item_id']);
			if($tj_hash['module_id'] == 51) Gou_Service_Url::updateTJ($tj_hash['item_id']);
			
          echo "[LOG] " . $tjhash . " done.\n";
		} else {
			Common::log(array('msg'=> $tjhash.'不存在'), 'hash.log');
            echo "[LOG] " . $tjhash . " not exist.\n";
		}	
		
	}

} while ($queue->len('tjdata') > 0);



