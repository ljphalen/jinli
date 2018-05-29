<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Queue_Factory {
	static $instances;
	
	static public function getQueue($config, $queueType = 'redis') {
		if(!in_array($queueType, array('Memcache', 'Redis'))) $queueType = 'Redis';
		$queueName = 'Queue_' . $queueType;
		$key = md5($queueName.json_encode($config));
		if(isset(self::$instances[$key]) && is_object(self::$instances[$key])) return self::$instances[$key];
		if (!class_exists($queueName)) throw new Exception('empty class name');
		self::$instances[$key] = new $queueName($config);
		return self::$instances[$key];
	}
}