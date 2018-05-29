<?php
include 'common.php';


/**
 * 添加（上线）游戏
 */
function start($appid) {
   	//获取数据
   	$urls = Common::getConfig("apiConfig", ((ENV == 'product') ? 'product_Url' : 'devlope_Url'));
    $url = $urls[1] . '?appid='.$appid;
   	$curl = new Util_Http_Curl($url);
   	$result = $curl->get();
   	$base_tmp =  $tmp = $category = $simg = $device = $labels = array();
   	$tmp = json_decode($result,true);
   	$result = $tmp['data'];
   	if (!$result) return '-1';
   	//数据写入    	
   	if($result){
   		$certificate = $result['safeInfos'] ? serialize($result['safeInfos']) : '';
   		$ret = Resource_Service_Games::updateResourceGamesCertificate($certificate,$appid);
   	}
   	return ($ret) ? '1' : '0';
    
}

//数据融合处理
set_time_limit(0);
$maxApp = 3000;
for($i =1; $i<=$maxApp; $i++){
	$ret = start($i);	 
	switch ($ret) {
		case '-1':
			echo "app:$i--->not found\r\n";
			break;
			case '0':
			echo "app:$i--->fail\r\n";
			break;
			case '1':
			echo "app:$i--->ok\r\n";
			break;
	}
	 
	if(($i%50) ==0) sleep(2);
}
