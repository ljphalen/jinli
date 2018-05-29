<?php
include 'common.php';
/**
 * push msg
 */


//专题
$subject = Theme_Service_Subject::getBy(array('is_publish'=>1, 'pre_publish'=>Common::getTime()));
if($subject){
	$queue = Common::getQueue();
	$queue -> noRepeatPush('push_msg' , $subject['id']);
}



