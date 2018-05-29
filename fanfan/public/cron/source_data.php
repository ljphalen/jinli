<?php
include 'common.php';

ini_set('memory_limit', '512M');

$queue = Common::getQueue();

if (!$queue->len("source_data")) {
	$cpUrls = Widget_Service_Cp::getAll();
	foreach ($cpUrls as $v) {
		if (!empty($v['url'])) {
			$queue->noRepeatPush("source_data", $v['id'], "s");
		}
	}
}

$i = 0;
while ($i < 3) {
	$urlId   = $queue->noRepeatPop("source_data", "s");
	$urlInfo = Widget_Service_Cp::get($urlId);
	if ($urlInfo) {
		$data = Widget_Service_Adapter::getData($urlInfo);
		$out = '';
		$ids  = Widget_Service_Adapter::done($data, $urlInfo, $out);
		$num  = count($ids);
		echo $out;
		echo date('Y-m-d H:i:s') . ":$num:" . html_entity_decode($urlInfo['url']) . "  .....done.\n";
	}
	$i++;
}

