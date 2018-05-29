<?php
include 'common.php';
/**
 * scene_download_count
 */

$files = Lock_Service_QiiFile::getAllFile();
$api_url = Common::getConfig('apiConfig', 'scene_download_count');
foreach ($files as $key=>$value) {
	$url = $api_url.'?sceneCode='.$value['out_id'];
	$content = file_get_contents($url);
	if($content) {
		$content = json_decode($content, true);
		$download_count = intval($content['object']['downloadCount']);
		if($value['down'] != $download_count) {
			Lock_Service_QiiFile::updateBy(array('down'=>$download_count), array('out_id'=>$value['out_id']));
			$lock = Lock_Service_Lock::getBy(array('file_id'=>$value['out_id'], 'channel_id'=>1));
			if($lock) Lock_Service_Lock::updateBy(array('down'=>$download_count), array('file_id'=>$value['out_id'], 'channel'=>1));
		}
	}
}
exit();




