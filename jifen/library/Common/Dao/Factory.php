<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Common_Dao_Factory {
	static $instances;
	
	/**
	 * dao工厂
	 * 
	 * @param string $serviceName
	 * @return object
	 */
	static public function getDao($daoName) {
		$key = md5($daoName);
		if (isset(self::$instances[$key])) return self::$instances[$key];
		$cacheName = str_replace("_Dao_", "_Cache_", $daoName);
		if (class_exists($cacheName)) {
			self::$instances[$key] = new $cacheName();
		} else {
			self::$instances[$key] = new $daoName();
		}
		return self::$instances[$key];
	}
}