<?php
class Logger_Configuration {

    const ROOT_LOGGER_PREFIX = "logger.rootLogger";
    const LOGGER_PACKAGES_PREFIX = "logger.packages.";
    const LOGGER_PREFIX = "logger.config.";
	
    private $rootLogger = "default";
	private $packages = array();
	private $config = array();
	
	private function load($url) {
		if (!file_exists($url)) {
			throw new Exception("File [$url] does not exist.");
		}
		
		$properties = @parse_ini_file($url, true);
		if ($properties === false) {
			$error = error_get_last();
			throw new Exception("Error parsing configuration file: {$error['message']}");
		}
		
		return $properties;
	}
	
	public function convert($path) {
		$properties = $this->load($path);
		if (isset($properties[self::ROOT_LOGGER_PREFIX])) {
		    $this->rootLogger = $properties[self::ROOT_LOGGER_PREFIX];
		    unset($properties[self::ROOT_LOGGER_PREFIX]);
		}
		foreach($properties as $key => $value) {
		    if ($this->beginsWith($key, self::LOGGER_PACKAGES_PREFIX)) {
		        $package = substr($key, strlen(self::LOGGER_PACKAGES_PREFIX));
		        $this->parsePackage($value, $package);
		    }
		    if ($this->beginsWith($key, self::LOGGER_PREFIX)) {
		        $config = substr($key, strlen(self::LOGGER_PREFIX));
		        $this->parseConfig($value, $config);
		    }
		}
	}
	
	private function parsePackage($config, $package) {
	    $this->packages[$package] = trim($config);
	}
	
	private function parseConfig($value, $config) {
		$parts = explode('.', $config);
		if (empty($value) || empty($parts)) {
			return;
		}
		$name = array_shift($parts);
		$subKey = array_shift($parts);
		
		if(! isset($this->config[$name])) {
		    $this->config[$name] = new Logger_Config();
		}
		$method = "set" . ucfirst($subKey);
        if (method_exists($this->config[$name], $method)) {
            return call_user_method($method, $this->config[$name], $value);
        }
	}
	
	private function beginsWith($str, $sub) {
		return (strncmp($str, $sub, strlen($sub)) == 0);
	}
	
	public function getRootLogger() {
	    return $this->rootLogger;
	}
	
	public function getPackages() {
	    return $this->packages;
	}
	
	public function getConfig($name) {
	    return $this->config[$name];
	}
	
}
