<?php
class Async_Timer_Adapter_TestSwoole extends Async_Timer_Base {
    
    public function heartbeat($hour, $minute, $second) {
        if(! ($hour == 0 && $minute == 0 && $second == 0)) {
            return;
        }
        Async_Cache::deleteSilgleFlgList();
    }
    
}

