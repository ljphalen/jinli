<?php

class Swoole_Socket_Util {

    const OP_GAME = 'GAME';
    const OP_TIMER = 'TIMER';
    const OP_REPLY = 'REPLY';
    const OP_COMMAND = 'COMMAND';

    const TABLE_COLUMN_INTVALUE = "intvalue";
    const TABLE_KEY_CLOSED = "CLOSED";
    
    public static function getDefaultReply($result=1) {
        $response = new Swoole_Socket_Message_Response(Swoole_Socket_Util::OP_REPLY, $result, '');
        return Swoole_Socket_Factory::encode($response);
    }

    public static function getTimerRequest($microtime) {
//         $taskId = self::guid();
        $taskId = 0;
        $args = array('microtime' => $microtime);
        $data = array('task' => 'Async_Task_Adapter_Timer', 'method' => 'heartbeat', 'args' => $args);
        $request = new Swoole_Socket_Message_Request(Swoole_Socket_Util::OP_TIMER, $data, $taskId);
        return $request;
    }

    public static function guid() {
        $uuid = '';
        if (function_exists('com_create_guid')) {
            $uuid = com_create_guid();
        } else {
            mt_srand((double) microtime(true) * 10000); // optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $left_curly = chr(123); // "{"
            $right_curly = chr(125); // "}"
            $uuid = $left_curly . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . $right_curly;
        }
        return trim($uuid, '{}');
    }
    
}
