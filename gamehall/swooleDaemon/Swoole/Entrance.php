<?php
class Swoole_Entrance {

    private static $serverPath;

    private static $serverConfigPath;

    private static $classPath = array();

    public static function getServerPath() {
        return self::$serverPath;
    }
    
    public static function getServerConfigPath() {
        return self::$serverConfigPath;
    }

    final public static function autoLoader($class) {
        if (isset(self::$classPath[$class])) {
            require self::$classPath[$class];
            return;
        }
        $baseClasspath = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        $libs = array(
            self::$serverPath,
        );
        
        foreach ($libs as $lib) {
            $classpath = $lib . DIRECTORY_SEPARATOR . $baseClasspath;
            if (is_file($classpath)) {
                self::$classPath[$class] = $classpath;
                require "{$classpath}";
                return;
            }
        }
    }

    public static function run($rootPath) {
        self::$serverPath = $rootPath;
        self::$serverConfigPath = self::$serverPath . DIRECTORY_SEPARATOR . 'config';
        
        spl_autoload_register(__CLASS__ . '::autoLoader');
        Swoole_Core_Config::load(self::$serverConfigPath);

        $socket = Swoole_Core_Config::get('socket');
        if (empty($socket)) {
            throw new Exception("socket config empty");
        }
        $swoole = Swoole_Core_Config::get('swoole');
        if (empty($swoole)) {
            throw new Exception("swoole config empty");
        }
        $serverInfo = new Swoole_Po_Server($swoole, $socket);
        
        $server = Swoole_Core_Factory::getInstance('Swoole_Socket_Swoole', $serverInfo);
        $hander = Swoole_Core_Factory::getInstance('Swoole_Socket_Handler', $serverInfo);
        $server->setHandler($hander);
        $server->run();
    }
}

