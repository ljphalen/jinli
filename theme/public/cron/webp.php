<?php
include 'common.php';
/**
 * push msg
 */
$ckey = "theme_webp_id";
$id = intval(Theme_Service_Config::getValue($ckey));
list($total, $result) = Theme_Service_FileImg::getList(0, 20, array('id'=>array('>', $id)));
if (!$total) exit("noting to do.\n");

$path = Common::getConfig("siteConfig", "attachPath");

foreach ($result as $key=>$value) {
	$file = realpath($path.$value["img"]);
	$webp = $file.".webp";
	
	if (file_exists($webp)) {
		$id = $value['id'];
		continue;
	}
	
	if (file_exists($file)) {
		image2webp($file, $webp);
		$id = $value['id'];
		echo "create webp ".$id." done\n";
	}
}
Theme_Service_Config::setValue($ckey, $id);