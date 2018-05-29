<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-事件类
 * @author fanch
 *
 */
class Task_EventHandle{

    const LOG_FILE = "task.log";
    const LOG_TAG = 'Task_EventHandle';

    /**
     * 工厂主体方法
     * @param class $event
     * @param array $request
     */
    public static function postEvent(Task_Event $event){
        self::handleEvent($event);
    }

    private static function handleEvent($event){
        $taskInstances = Task_Factory::getTaskInstances($event);

        $debugMsg = array('msg' => "创建有效任务实例", 'event'=>$event);
        Util_Log::debug(self::LOG_TAG, self::LOG_FILE, $debugMsg);

        if($taskInstances) {
            foreach ($taskInstances as $taskObj){
                $taskObj->setRequest($event->mRequest);
                $taskObj->run();
            }
        }

        $debugMsg = array('msg' => "事件任务处理完毕", 'event'=> $event);
        Util_Log::debug(self::LOG_TAG, self::LOG_FILE, $debugMsg);
    }
}
