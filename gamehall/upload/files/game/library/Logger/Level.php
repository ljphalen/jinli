<?php
class Logger_Level {
	
	const FATAL = 50000;
	const ERROR = 40000;
	const WARN = 30000;
	const INFO = 20000;
	const DEBUG = 10000;

	private $level;
	private $levelStr;

	private function __construct($level, $levelStr) {
		$this->level = $level;
		$this->levelStr = $levelStr;
	}

	public function equals($other) {
		if($other instanceof Logger_Level) {
			if($this->level == $other->level) {
				return true;
			}
		} else {
			return false;
		}
	}

	public function isGreaterOrEqual($other) {
		return $this->level >= $other->level;
	}

	public function __toString() {
		return $this->levelStr;
	}

	public function toInt() {
		return $this->level;
	}
	

	private static $levelMap;
	public static function getLevelFatal() {
		if(!isset(self::$levelMap[Logger_Level::FATAL])) {
			self::$levelMap[Logger_Level::FATAL] = new Logger_Level(Logger_Level::FATAL, 'FATAL');
		}
		return self::$levelMap[Logger_Level::FATAL];
	}
	
	public static function getLevelError() {
		if(!isset(self::$levelMap[Logger_Level::ERROR])) {
			self::$levelMap[Logger_Level::ERROR] = new Logger_Level(Logger_Level::ERROR, 'ERROR');
		}
		return self::$levelMap[Logger_Level::ERROR];
	}
	
	public static function getLevelWarn() {
		if(!isset(self::$levelMap[Logger_Level::WARN])) {
			self::$levelMap[Logger_Level::WARN] = new Logger_Level(Logger_Level::WARN, 'WARN');
		}
		return self::$levelMap[Logger_Level::WARN];
	}

	public static function getLevelInfo() {
		if(!isset(self::$levelMap[Logger_Level::INFO])) {
			self::$levelMap[Logger_Level::INFO] = new Logger_Level(Logger_Level::INFO, 'INFO');
		}
		return self::$levelMap[Logger_Level::INFO];
	}

	public static function getLevelDebug() {
		if(!isset(self::$levelMap[Logger_Level::DEBUG])) {
			self::$levelMap[Logger_Level::DEBUG] = new Logger_Level(Logger_Level::DEBUG, 'DEBUG');
		}
		return self::$levelMap[Logger_Level::DEBUG];
	}

	public static function toLevel($arg, $defaultLevel = null) {
		if(is_int($arg)) {
			switch($arg) {
				case self::DEBUG: return self::getLevelDebug();
				case self::INFO: return self::getLevelInfo();
				case self::WARN: return self::getLevelWarn();
				case self::ERROR: return self::getLevelError();
				case self::FATAL: return self::getLevelFatal();
				default: return $defaultLevel;
			}
		} else {
			switch(strtoupper($arg)) {
				case 'DEBUG': return self::getLevelDebug();
				case 'INFO': return self::getLevelInfo();
				case 'WARN': return self::getLevelWarn();
				case 'ERROR': return self::getLevelError();
				case 'FATAL': return self::getLevelFatal();
				default: return $defaultLevel;
			}
		}
	}
}
