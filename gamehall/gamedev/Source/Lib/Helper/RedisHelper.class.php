<?php
class RedisHelper
{
    private static $_redis = null;
	
	public function __construct() {
	    //未初始化Redis
	    if (self::$_redis == null) {
	        //连接Redis服务器
	        self::$_redis = new Redis();
	        self::$_redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
	        self::$_redis->select(C('REDIS_NAME'));
	    }
	}
	
	public function set($key, $value) {
	    self::$_redis->set($key, $value);
	}
	
	public function get($key) {
	    return self::$_redis->get($key);
	}
	
	public function hSet($hkey, $key, $value) {
	    //var_dump($hkey, $key, $value);
	    self::$_redis->hSet($hkey, $key, $value);
	}
	
	public function hGet($hkey, $key=null) {
	    if ($key !== null && $key !== '') { 
	       return self::$_redis->hGet($hkey, $key);
	    }
	    return self::$_redis->hGetAll($hkey);
	}
	
	public function lPush($key, $value) {
	    self::$_redis->lPush($key, $value);
	}
	
	public function rPop($key) {
	    return self::$_redis->rPop($key);
	}
	
	public static function test() {
	    $redis = new Redis();
	    $redis->get();
	    $redis->set();
	    $redis->hSet('h', 'key1', 'hello');
	    $redis->hGet('h', 'key1');
	}
	
}