<?php

class Async_Task_Adapter_TimerMutil extends Async_Task_Base {
    
    public function heartbeat(Async_Task_Message_Task $task) {
        $microtime = $task->getParam("microtime");
        $date = date("H:i:s", $microtime);
        list($hour, $minute, $second) = explode(':', $date);
        if($second % 10 != 0) {
            return;
        }
        Cache_Redis::ping();
    }
    
}

