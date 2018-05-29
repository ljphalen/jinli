<?php include 'common.php';


$queue = Common::getQueue();

do {
	$hash_data = $queue->pop('api_log');
	if($hash_data) {
		Gou_Service_ApiLog::add($hash_data);
	}
} while ($queue->len('api_log') > 0);
echo $queue->len('api_log')." log done.\n";
exit;
