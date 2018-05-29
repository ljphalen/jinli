<?php

class Async_Spl_Center {
    
    const SUBJECT_STATUS = "subject_status";
    
    
    
    
    
    public static function notify($name, $params, $taskId=0) {
        $subject = Async_Spl_Factory::getSubject($name);
        if ($subject) {
            $spl = new Async_Task_Message_Spl($params, $taskId);
            $subject->notify($spl);
        }else{
            Logger_Game::getLogger(__CLASS__)->error("subject : {$name} not found");
        }
    }
    
}
