<?php

class Async_Timer_Factory {
    private static $instances = null;

    public static function getTimerList() {
        if(is_null(self::$instances)) {
            self::loadTimer();
        }
        return self::$instances;
    }
    
    private static function loadTimer() {
        self::$instances = array();
        $timerPath = __DIR__ . DIRECTORY_SEPARATOR . 'Adapter';
        $classes = Util_ClassLoader::loadClassesFromDir($timerPath);
        
        foreach ($classes as $class) {
            if (! ($class instanceof Async_Timer_Base)) {
                continue;
            }
            $className = get_class($class);
            self::$instances[$className] = $class;
        }
    }
    
}
