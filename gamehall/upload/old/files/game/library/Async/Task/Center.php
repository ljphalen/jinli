<?php

class Async_Task_Center {

    const SUCESS = 0;
    const TASK_NOT_FOUND = 1101;
    const TASK_EXCEPTION = 1102;
    const METHOD_NOT_FOUND = 1103;
    const IGNORE = 1104;
    
    public static function execute($task, $method, $args, $taskId=0) {
        if($taskId) {
            Async_Cache::inputTask($taskId);
            Async_Cache::saveTaskData($taskId, $task, $method, $args);
        }
        $result = 0;
        do{
            $task = Async_Task_Factory::getInstance($task);
            if (! ($task && $task instanceof Async_Task_Base)) {
                $result = self::TASK_NOT_FOUND;
                break;
            }
            if (! method_exists($task, $method)) {
                $result = self::METHOD_NOT_FOUND;
                break;
            }
            if($taskId && $task->singleExecute()) {
                $taskKey = md5(get_class($task) . $method . json_encode($args));
                $time = Async_Cache::getCurrentTime();
                if(Async_Cache::getSingleFlg($taskKey) > $time) {
                    $result = self::IGNORE;
                    break;
                }
                Async_Cache::inputSilgleFlg($taskKey);
            }
            try {
                $message = new Async_Task_Message_Task($args, $taskId);
                call_user_method($method, $task, $message);
            } catch (Exception $e) {
                $result = self::TASK_EXCEPTION;
            }
            if($taskKey) {
                Async_Cache::deleteSilgleFlg($taskKey);
            }
        }while(false);
        if($taskId) {
            Async_Cache::finishTask($taskId, $result);
        }
    }
    
}

