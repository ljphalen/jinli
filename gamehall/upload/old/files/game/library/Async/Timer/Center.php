<?php

class Async_Timer_Center {
    
    public static function heartbeat($hour, $minute, $second) {
        $timerList = Async_Timer_Factory::getTimerList();
        foreach ($timerList as $executer) {
            try {
                $executer->heartbeat($hour, $minute, $second);
            } catch (Exception $e) {
                Logger_Game::getLogger(__CLASS__)->error(get_class($executer) . " heartbeat error", $e);
            }
        }
    }
    
}

