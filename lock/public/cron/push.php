<?php
include 'common.php';
/**
 * push msg
 * 精灵自动适配不同分辨率的锁屏，其他锁屏需匹配合适的RID
 */

$queue = Common::getQueue();
$cache = Common::getCache();
$webroot = Yaf_Application::app()->getConfig()->webroot;
$file_id = $queue->noRepeatPop('lock_push_msg');

//当前并发数
$queue_num  = intval($cache->get('queue_num'));

$file = Lock_Service_Lock::getLock(intval($file_id));

$msg = Lock_Service_Msg::getBy();

//每次发送数量
$perpage = Lock_Service_Config::getValue('push_number');
if(!$perpage) $perpage = 20;

if($msg && $queue_num <= 10 && $file) {
	//更新该消息的发送状态,发送中
	$cache->increment('queue_num');
	
	//主题url 1首页  2专题   3详情
	$url = '';
	$action = 'index';
	$push_title = '';
	if($msg['dest'] == 2) {
		$subject = Lock_Service_Subject::getBy();
		$url = $webroot.'/subject?sid='.$subject['id'];
		$action = 'subject';
		$push_title = Util_String::utf82unicode($subject['title']);
	} elseif ($msg['dest'] == 3) {
		if($file['channel_id'] == 1){
			$info = Lock_Service_QiiFile::getBy(array('out_id'=>$file['file_id']));
			$info['title'] =$info['zh_title'];
			$action = 'detail';
		} else {
			$info = Lock_Service_File::getFile($file['file_id']);
		}
		
		$url = sprintf('%s/detail?id=%d&pre=%d&next=%d&update_time=%d&orderby=%s&channel=%d&refer=%s&out_id=%d&version=%d',
				$webroot, $file['id'], 0, 0, $info['update_time'], 'id', $file['channel_id'], 'index', $info['out_id'], '-1');
		$push_title = Util_String::utf82unicode($info['title']);
	} else {
		$url = '';
	}

	//检测token
	$token = Lock_Service_Config::getValue('push_token');
	$expired = Lock_Service_Config::getValue('push_token_expired');

	if(!$token || !$expired || $expired <= Common::getTime()) {
		//获取token
		$result = Api_Gionee_Push::getToken();
		if($result) {
			Lock_Service_Config::setValue('push_token', $result['authToken']);
			Lock_Service_Config::setValue('push_token_expired', $result['expired']);
			$token = Lock_Service_Config::getValue('push_token');
			$expired = Lock_Service_Config::getValue('push_token_expired');
		}else {
			Common::log(array('msg'=> '未获取到token'), 'push.log');
		}
	}

	$page = 1;
	do {
		list($total, $rids) = Lock_Service_Rid::getList($page, 20, array('status'=>1));

		$rid = array();
		foreach ($rids as $key=>$value) {
			$ret = Api_Gionee_Push::checkRid($value['rid'], $token);
			if($ret) {
				Lock_Service_Pushlog::addPushlog(array('rid'=>$value['rid'], 'msg_id'=>$msg['id']));
				$rid[] = $value['rid'];
			}
		}

		if($rid) {
			$content = json_encode(array('title'=>$msg['title'], 'content'=>$msg['content'], 'action'=>$action, 'url'=>$url, 'push_title'=>$push_title));
			$push_result = Api_Gionee_Push::pushMsgBatch($rid, $token, $msg['id'], $content);
			if($push_result) {
				$result_rids = $push_result['success_ids'][0]['rids'];
				Lock_Service_Pushlog::updateByRids(array('status'=>1), $result_rids, $msg['id']);
			}
		}

		$page++;
		sleep(2);
	} while ($total>($page * $perpage));

	if($queue_num > 0) $cache->decrement('queue_num');
}