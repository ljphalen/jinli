<?php
class Logger_Game {
    private $name;
    private $loggerConfig;
    
	public function __construct($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getLoggerConfig() {
		return $this->loggerConfig;
	}
	
	public function setLoggerConfig(Logger_Config $config) {
	    $this->loggerConfig = $config;
	}
	
	public function debug($message, $throwable = null) {
		$this->log(Logger_Level::getLevelDebug(), $message, $throwable);
	}

	public function info($message, $throwable = null) {
		$this->log(Logger_Level::getLevelInfo(), $message, $throwable);
	}

	public function warn($message, $throwable = null) {
		$this->log(Logger_Level::getLevelWarn(), $message, $throwable);
	}
	
	public function error($message, $throwable = null) {
		$this->log(Logger_Level::getLevelError(), $message, $throwable);
	}
	
	public function fatal($message, $throwable = null) {
		$this->log(Logger_Level::getLevelFatal(), $message, $throwable);
	}

	private function log(Logger_Level $level, $message, $throwable = null) {
	    $config = $this->getLoggerConfig();
	    if(! $level->isGreaterOrEqual($config->getLevel())) {
	        return ;
	    }
	    if(is_array($message) || is_object($message)) {
	        $message = json_encode($message, true);
	    }
        if($throwable) {
            $message .= PHP_EOL;
            $message .= (string) $throwable;
        }
        $message .= PHP_EOL;
		$logContent = date('H:i:s') . '   ' . (string) $level;
		$logContent = $logContent . '   ' . $this->getName() . ':';
		$logContent = $logContent . (string) $message;
		
		$typeSendToFile = 3;
		$filePath = sprintf($config->getFile(), date('Y-m-d'));
        if (! is_file($filePath)) {
            $dir = dirname($filePath);
            if (! is_dir($dir)) {
                mkdir($dir, 0777, true);
                chmod($dir, 0777);
            }
        }
		error_log($logContent, $typeSendToFile, $filePath);
	}
	
	// ******************************************
	// *** Static methods and properties      ***
	// ******************************************
	private static $loggers = array();
	private static $config = NULL;
	
	public static function getLogger($name) {
	    self::initConfig();
	    if(! isset(self::$loggers[$name])) {
	        $logger = new Logger_Game($name);
	        $packages = self::$config->getPackages();
	        $package = self::$config->getRootLogger();
	        $nodeStr = "";
	        $nodes = explode("_", $name);
	        if(count($nodes) > 0) {
	            foreach($nodes as $node) {
	                if($nodeStr) {
	                    $nodeStr .= '_';
	                }
	                $nodeStr .= $node;
	                if(! isset($packages[$nodeStr])) {
	                    continue;
	                }
	                $package = $packages[$nodeStr];
	            }
	        }
	        $config = self::$config->getConfig($package);
	        $logger->setLoggerConfig($config);
	        self::$loggers[$name] = $logger;
	    }
	    return self::$loggers[$name];
	}
	
	public static function getRootLogger() {
	    self::initConfig();
	    $name = self::$config->getRootLogger();
	    return self::getLogger($name);
	}
	
	private static function initConfig() {
	    if(! is_null(self::$config)) {
	        return;
	    }
        self::$config = new Logger_Configuration();
        $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'logger.properties';
        self::$config->convert($path);
	}
	
	
	
}
