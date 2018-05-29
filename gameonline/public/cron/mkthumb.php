<?php
include 'common.php';
/**
 *update pv
 */
set_time_limit(0);
list(, $imgs) = Resource_Service_Img::getAllGameImg();
$attachPath = Common::getConfig('siteConfig', 'attachPath');
foreach($imgs as $key=>$value) {
	$img = $attachPath . $value['img'];
	
	$file = explode(".", basename($img));
	
	$thumb = dirname($img) . "/". basename($img) . "_240x400." . $file[1];
	$ret = Util_Image::makeThumb($img, $thumb, 240, 400);
	if (!$ret) {
		echo $thumb."\n";
		copy($img, $thumb);
	} else {
		echo $thumb."\n";
	}
}

echo CRON_SUCCESS;