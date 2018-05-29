<?php
include 'common.php';


list(, $ret) = Client_Service_Gift::getAllGift();
foreach ($ret as $key =>$val ){
	Client_Service_Gift::updataGiftBaseInfoCache($val, $val['id']);
	Client_Service_Gift::updataGiftNumCacheByGiftId($val['id']);
	echo "gift-id:{$val['id']}-ok\r\n";
	if($key % 100 == 0){	
		sleep(1);
	}
}

echo CRON_SUCCESS;
exit;