<?php

include 'common.php';
/**
 * push msg
 */
$queue = Common::getQueue();
$cache = Common::getCache();

$per_num = 3000;

//当前并发数
$queue_num = intval($cache->get('queue_num'));

Common::log(array('rids' => "start", "queue_num" => $queue_num), 'push_start.log');
//专题
$subject_id = $queue->noRepeatPop('push_msg');

$send_num = $queue->noRepeatPop('send_num');
if ($send_num < 0) {
    Common::log(array("no_send_num"), 'send_num.log');
    exit();
}
Common::log(array($subject_id), 's_id.log');
if (!$subject_id) {
    Common::log(array("no_push_count_send"), 's_msg.log');
    exit();
} //没有待PUSH的专题
$subject = Theme_Service_Subject::getSubject($subject_id);
if (!subject) {
    Common::log(array("error_sid"), 'e_msg.log');
    exit();
} //专题不存在
$subject_files = Theme_Service_SubjectFile::getBySubjectId($subject_id);

//PUSH消息
$msg = Theme_Service_Msg::getBy();

//分批
//$perpage = Theme_Service_Config::getValue('push_number');
if (!$perpage) $perpage = 20;
$webroot = Yaf_Application::app()->getConfig()->webroot;
Common::log(array('msg' => $msg), 'counts11.log');
Common::log(array("queue_num" => $queue_num,), 'counts12.log');
Common::log(array('subject_files' => $subject_files), 'counts13.log');


if ($msg && $queue_num <= 10 && $subject_files) {
//更新该消息的发送状态,发送中

    $cache->increment('queue_num');

//检测token
    $token = Theme_Service_Config::getValue('push_token');
    $expired = Theme_Service_Config::getValue('push_token_expired');

    Common::log(array("toke" => $token), "tokes.log");
    Common::log(array("expired" => $expired), "expired.log");
    // exit;
    if (!$token || !$expired || $expired <= Common::getTime()) {
//获取token
        $result = Api_Push::getToken();
        if ($result) {
            Theme_Service_Config::setValue('push_token', $result['authToken']);
            Theme_Service_Config::setValue('push_token_expired', $result['expired']);
            $token = Theme_Service_Config::getValue('push_token');
            $expired = Theme_Service_Config::getValue('push_token_expired');
        } else {
            Common::log(array('msg' => '未获取到token'), 'push.log');
        }
    }

//遍历专题中的主题，获取适配的rid
    $rids = array();
    /* foreach ($subject_files as $value) {
      $file = Theme_Service_File::getFile($value['file_id']);
      Common::log(array($file), "files.log");
      $file_rids = Theme_Service_Rid::getsBy(array('sr' => $file['resulution'], 'status' => 1));
      Common::log(array('count' => count($file_rids), 'rids' => $file_rids), 'file_rids.log');
      if ($file_rids) {
      foreach ($file_rids as $keys => $vals) {
      $rids[] = $vals['rid'];
      }
      }
      } */

//分批发送
    $where = array('status' => 1);

    $total = Theme_Service_Rid::getsRidsCount($where);

    Common::log(array('count' => $total), 'counts.log');

    $file_rids = Theme_Service_Rid::getsRids($where, $send_num, $per_num);
    Common::log(array("total" => $total, "start" => $send_num, "per" => $perpage), 'file_rids_parm.log');
    Common::log(array($file_rids), 'file_rids.log');

    if ($file_rids) {
        foreach ($file_rids as $keys => $vals) {
            $rids[] = $vals['rid'];
        }

        for ($start = 0; $start <= $total; $start += $perpage) {
            $rid = array_slice($rids, $start, $perpage);
            if ($rid) {
                foreach ($rid as $value) {
                    // Theme_Service_Pushlog::addPushlog(array('rid' => $value['rid'] , 'msg_id' => $msg['id']));
                    Theme_Service_Pushlog::addPushlog(array('rid' => $value, 'msg_id' => $msg['id']));
                }
                $content = json_encode(array('title' => $subject['title'], 'content' => $msg['content'], 'url' => $webroot . '/subject?sid=' . $subject['id']));
                Api_Push::pushMsgBatch($rid, $token, $msg['id'], $content);
                Common::log(array(count($rid), date("Y-m-d H:i:s", time())), 'sened_com.log');
                Common::log(array("rid" => $rid, "date" => date("Y-m-d H:i:s", time())), 'rides_com.log');
            }
        }
        if ($send_num >= $total) {
            Common::log(array("comp" . $total . "--" . date("Y-m-d H:i:s", time())), 'colse.log');
        } else {
            $tem_num = $send_num + $per_num;
            $queue->noRepeatPush('push_msg', $subject_id);
            $queue->noRepeatPush('send_num', $tem_num);
            Common::log(array($tem_num), 'sened.log');
        }

        if ($queue_num > 0) $cache->decrement('queue_num');
    }
}
