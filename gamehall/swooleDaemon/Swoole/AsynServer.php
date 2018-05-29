<?php

class Swoole_AsynServer {
    
    private static $serverInfo;
    
    public static function init(Swoole_Po_Server $serverInfo) {
        self::$serverInfo = $serverInfo;
        self::yafStart();
    }
    
    private static function yafStart() {
        define("BASE_PATH", self::$serverInfo->getGameBaseDir() . '/');
        define ("APP_PATH", BASE_PATH . "application/");
        define("ENV", get_cfg_var('YAF_ENV'));
        define("DEFAULT_MODULE", 'Admin');
        
        define ("APP_PATH", BASE_PATH . "application/");
        define("ENV", get_cfg_var('YAF_ENV'));
        define("DEFAULT_MODULE", 'Admin');
        $app = new Yaf_Application(BASE_PATH. "configs/application.ini", ENV);
        $response = $app->bootstrap()->run();
        
        $application = new Yaf_Application(BASE_PATH. "configs/application.ini", ENV);
        $application->bootstrap()->execute(function() {
            self::$serverInfo->runLog("Yaf started...");
        });
        
    }
    
}

