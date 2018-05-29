<?php
include 'common.php';
/**
 * push msg
 */

$queue = Common::getQueue();
$cache = Common::getCache();
$msg_id = $queue->noRepeatPop('push_msg');

//当前并发数
$queue_num  = intval($cache->get('queue_num'));

$msg = Gou_Service_Msg::getMsg(intval($msg_id));

if($msg && $msg['status'] == 0 && $queue_num <= 5) {
	//更新该消息的发送状态,发送中
	$update_ret = Gou_Service_Msg::updateMsg(array('status'=>1), $msg['id']);
	$cache->increment('queue_num');
	
	//检测token
	$token = Gou_Service_Config::getValue('push_token');
	$expired = Gou_Service_Config::getValue('push_token_expired');
	
	if(!$token || !$expired || $expired <= Common::getTime()) {
		//获取token
		$result = Api_Gionee_Push::getToken();
		if($result) {
			Gou_Service_Config::setValue('push_token', $result['authToken']);
			Gou_Service_Config::setValue('push_token_expired', $result['expired']);
			$token = Gou_Service_Config::getValue('push_token');
			$expired = Gou_Service_Config::getValue('push_token_expired');
		}else {
			Common::log(array('msg'=> '未获取到token'), 'push.log');
		}
	}
	
	//判断是否是定向push
	$push_imei = $queue->len('imei_'.$msg['id']);
	
	if (!$push_imei) {
		pushall($msg, $token);
	} else {
		push($msg, $token);
	}
	
	//更新状态
	Gou_Service_Msg::updateMsg(array('status'=>0), $msg['id']);
	if($queue_num > 0) $cache->decrement('queue_num');
}

function push($msg, $token) {
	$queue = Common::getQueue();
	$need = 20;
	$total = 0;
	$max_len = $queue->len('imei_'.$msg['id']);
	$imeis = array();
	do {
		
		$imei_len = $queue->len('imei_'.$msg['id']);
		$imei = $queue->noRepeatPop('imei_'.$msg['id']);
		
		$rid = Gou_Service_Rid::getBy(array('imei'=>$imei));
		if ($rid) {
			//array_push imei
			array_push($imeis, $rid['rid']);
			$total++;
		}
		
		//send
		if($imeis) {
			if ($total == min($max_len,$need)) {
				//push
				$content = json_encode(array('title'=>$msg['title'], 'content'=>$msg['content'], 'url'=>html_entity_decode($msg['url'])));
				$push_result = Api_Gionee_Push::pushMsgBatch($imeis, $token, $msg['id'], $content);
				if($push_result) {
					$result_rids = $push_result['success_ids'][0]['rids'];
					Gou_Service_Pushlog::updateByRids(array('status'=>1), $result_rids, $msg['id']);
				}
				
				$max_len = $queue->len('imei_'.$msg['id']);
				$total = 0;
				$imeis = array();
				sleep(2);
			}
		}
		
	} while ($imei_len - 1 > 0);
}

function pushall($msg, $token) {
	$page = 1;
	//每次发送数量
	$number = 20;
	do {
		list($total, $rids) = Gou_Service_Rid::getList($page, $number);
		$rids = Common::resetKey($rids, 'rid');
		$rid = array_keys($rids);
	
		/* $rid = array();
			foreach ($rids as $key=>$value) {
		$ret = Api_Gionee_Push::checkRid($value['rid'], $token);
		if($ret) {
		Gou_Service_Pushlog::addPushlog(array('rid'=>$value['rid'], 'msg_id'=>$msg['id']));
		$rid[] = $value['rid'];
		}
		} */
	
		if($rid) {
			$content = json_encode(array('title'=>$msg['title'], 'content'=>$msg['content'], 'url'=>html_entity_decode($msg['url'])));
			$push_result = Api_Gionee_Push::pushMsgBatch($rid, $token, $msg['id'], $content);
			if($push_result) {
				$result_rids = $push_result['success_ids'][0]['rids'];
				Gou_Service_Pushlog::updateByRids(array('status'=>1), $result_rids, $msg['id']);
			}
		}
	
		$page++;
		sleep(2);
	} while ($total>($page * $number));
}

