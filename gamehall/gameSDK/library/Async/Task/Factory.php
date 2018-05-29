<?php

class Async_Task_Factory {
    private static $instances = array();

    public static function getInstance($className) {
        $keyName = $className;
        if (isset(self::$instances[$keyName])) {
            return self::$instances[$keyName];
        }
        if (! class_exists($className)) {
            return null;
        }
        self::$instances[$keyName] = new $className();
        return self::$instances[$keyName];
    }
}
