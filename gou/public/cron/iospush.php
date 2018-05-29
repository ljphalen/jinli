<?php
include 'common.php';

$queue = Common::getQueue();
$cache = Common::getCache();
$msg_id = $queue->noRepeatPop('ios_push_msg');

//当前并发数
$queue_num  = intval($cache->get('queue_num'));

$msg = Ios_Service_Msg::getMsg(intval($msg_id));

if($msg && $msg['status'] == 0 && $queue_num <= 5) {
    //更新该消息的发送状态,发送中
    $update_ret = Ios_Service_Msg::updateMsg(array('status'=>1), $msg['id']);
    $cache->increment('queue_num');

    //判断是否是定向push
    $push_imei = $queue->len('token_'.$msg['id']);

    if (!$push_imei) {
        pushall($msg);
    } else {
        push_part($msg);
    }

    //更新状态
    Ios_Service_Msg::updateMsg(array('status'=>0), $msg['id']);
    if($queue_num > 0) $cache->decrement('queue_num');
}

/**
 * 定向发送
 * @param unknown_type $msg
 */
function push_part($msg) {
    $queue = Common::getQueue();
    $len = $queue->len('token_'.$msg['id']);

    $imei_len = $queue->len('imei_'.$msg['id']);

    for ($i = 0; $i < $imei_len; $i++) {
        $token_id = $queue->noRepeatPop('imei_'.$msg['id']);
        	
        $token = Ios_Service_Token::getToken($token_id);
        push($msg, $token);
        sleep(1);
    }
}


/**
 * 发送所有用户
 * @param unknown_type $msg
 * @param unknown_type $token
 */
function pushall($msg, $token) {
    $page = 1;
    $perpage = 20;
    do {
        list($total, $tokens) = Ios_Service_Token::getList($page, $perpage);
        foreach ($tokens as $key=>$value) {
            push($msg, $value['token']);
         }        
        $page++;
        sleep(1);
    } while ($total>(($page-1) * $perpage));
}


/**
 * push
 * @param unknown_type $msg
 */
function push($msg, $token) {
    $config = Common::getConfig('apiConfig');
    $config = Common::getConfig('apiConfig');
	$ssh_url = $config['ios_push_ssl_url'];
	$pass = $config['ios_push_pass'];
	$certificate = $config['ios_push_certificate'];

    $deviceToken = $token;
    $message = $msg['content'];
    $badge = 1;
    $sound = 'default';
    $url =  $msg['url'];

    // Construct the notification payload
    $body = array();
    if ($url) $body['url'] = $url;
    $body['title'] = $msg['title'];
	$body['aps']['alert'] = $msg['content'];
	if ($badge) $body['aps']['badge'] = $badge;
	if ($sound) $body['aps']['sound'] = $sound;

    /* End of Configurable Items */
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', BASE_PATH . 'configs/'.$certificate);
    // assume the private key passphase was removed.
    stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);

    // connect to apns
    $fp = stream_socket_client($ssh_url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) {
        Common::log(array('msg'=> "Failed to connect $err $errstr\n"), 'ios_push_err.log');
        return  false;
    }

    // send message
    $payload = json_encode($body);
    $message = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;

    Common::log($payload . "\r\n", 'ios_push.log');
    fwrite($fp, $message);
    fclose($fp);
}
