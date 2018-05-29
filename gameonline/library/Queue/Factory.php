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
		if(isset(self::$instances[$queueType])) return self::$instances[$queueType];
		self::$instances[$queueType] = new Queue_Redis($config);
		return self::$instances[$queueType];
	}
}