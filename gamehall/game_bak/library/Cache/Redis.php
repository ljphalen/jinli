<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Redis操作类
 * @author rainkid
 *
 */
class Cache_Redis implements  Cache_Interface {
	protected $readCache;
	protected $writeCache;
	protected $cacheConfig = '';
	
	const TAG_READ = "read";
	const TAG_WRITE = "write";
	const TAG_HOST = "host";
	const TAG_POST = "port";
	
	static $sRedisConnList = array();
	
	/**
	 * 
	 * 构造函数
	 * 
	 */
	public function __construct($cacheConfig) {
	    $this->cacheConfig = $cacheConfig;
		return $this;
	}
	
	private function _getCacheByTag($TagReadOrWrite = self::TAG_READ) {
	    if ($TagReadOrWrite == self::TAG_READ) {
	        $cache = &$this->readCache;
	    }
	    else {
	        $cache = &$this->writeCache;
	    }
	    
	    if (!$cache) {
	        $this->_connectCache($cache, $TagReadOrWrite);
	    }
	     
	    return $cache;
	}
	
	private function _connectCache(&$cache, $tagReadOrWrite) {
	    $connConfig = $this->cacheConfig;
	    
	    if (isset($this->cacheConfig[$tagReadOrWrite])) {
	        $connConfig = $this->cacheConfig[$tagReadOrWrite];
	        if (self::TAG_READ == $tagReadOrWrite) {
	            $key = array_rand($connConfig);
	            $connConfig = $connConfig[$key];
	        }
	    }
	    
	    if (isset($connConfig[self::TAG_HOST]) && isset($connConfig[self::TAG_POST])) {
	        $key = $connConfig[self::TAG_HOST]. ':' . $connConfig[self::TAG_POST];
	        if (isset(self::$sRedisConnList[$key])) {
	            $cache = self::$sRedisConnList[$key];
	            return;
	        }
	        
	        $cache = new Redis();
	        try {
	            $cache->connect($connConfig[self::TAG_HOST], $connConfig[self::TAG_POST]);
	            self::$sRedisConnList[$key] = $cache;
	        } catch (Exception $e) {
	            throw new Exception('redis配置错误或者服务器繁忙'); 
	        }
	    }
	    else {
	        throw new Exception('redis配置有误');
	    }
	}
	
	protected function getReadCache() {
	    return $this->_getCacheByTag(self::TAG_READ);
	}
	
	protected function getWriteCache() {
	    return $this->_getCacheByTag(self::TAG_WRITE);
	}
	
	/**
	 * 选择redis库
	 * @param unknown_type $db
	 * @return unknown
	 */
	public function select($db = 0) {
		return $this->getReadCache()->select($db);;
	}
	
	/**
	 * 从内存获取数据 
	 *
	 */
	public function get($key) {
		if (!is_array($key)) return $this->_getSingleValue($key);
		$temp = $this->getReadCache()->mget($key);
		$result = array();
		foreach ($temp as $val) {
			if (!$val) continue;
			$result[] = json_decode($val, true);
		}
		return $result;
	}
	
	/**
	 * 
	 * 单个key
	 * @param unknown_type $key
	 */
	private function _getSingleValue($key) {
		$temp = $this->getReadCache()->get($key);
		if ($temp === false) return false;
		return json_decode($temp, true);
	}
	
	/**
	 * 设置值到内存中
	 */
	public function set($key, $value, $expire = 0) {
		$value = json_encode($value);
		if (!$expire) $result = $this->getWriteCache()->set($key, $value);
		else $this->getWriteCache()->setex($key, $expire, $value);
		return $result;
	}
	
	/**
	 * 
	 * 自增长
	 * @param unknown_type $key
	 * @param unknown_type $step
	 */
	public function increment($key, $step = 1) {
		return $this->getWriteCache()->incr($key, $step);
	}
	
	/**
	 * 
	 * 递减
	 * @param string $key
	 * @param int $step
	 */
	public function decrement($key, $step = 1) {
		return $this->getWriteCache()->decr($key, $step);
	}
	
	/**
	 * 
	 * 删除内存$key
	 */
	public function delete($key) {
		return $this->getWriteCache()->delete($key);
	}
	
	/**
	 * 
	 * 分页取列表
	 * @param unknown_type $key
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */
	public function get_list_range($key, $limit, $offset) {
		return $this->getReadCache()->lGetRange($key, $limit, $limit + $offset - 1);
	}

	/**
	 * 清内存
	 * 
	 */
	public  function flush() {
		return $this->getWriteCache()->flushDB();
	}
	
	/**
	 * 指定key是否存在
	 * @param string $key
	 * @return 1 存在 0 不存在
	 */
	public function exists($key){
		return $this->getReadCache()->exists($key);
	}
	/**
	 * 设置key过期方法
	 * @param unknown $key
	 * @param unknown $timeout
	 */
	public function expire($key, $timeout){
		return $this->getWriteCache()->expire($key, $timeout);
	}

	//检测某元素是否在序列中
	public function sismember($key,$value){
		return $this->getReadCache()->sismember($key,$value);
	}
	
	/**
	 * hash set 向名称为h的hash中添加元素
	 * @param unknown_type $h
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public  function hSet($h, $key, $value, $expire=0){
		$res =$this->getWriteCache()->hSet($h,$key,$value);
		if ($expire) {
		    $ttl = $this->getReadCache()->ttl($h);
		    if (-1 == $ttl) {
		      $this->getWriteCache()->expire($h, $expire);
		    }
		}
		return $res;
	}
	

	/**
	 * hash get 名称为h的hash中key1对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hGet($h, $key){
		return $this->getReadCache()->hGet($h, $key);
	}
	
	/**
	 * hash key 自增
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hIncrBy($h, $key, $step = 1, $expire = 0){
	    $ret = $this->getWriteCache()->hIncrBy($h, $key, $step);
	    if (($expire) && ($ret == $step)) { 
	        /* 
	         * 说明：“$ret == $step”“近似”实现过期时间不重复设置，实际
	         * 上N个不同的Key会设置N次。
	         * 用ttl($h)判断是否有过期时间更准确，但会增加与redis的交互。 
	         */
	    	$this->getWriteCache()->expire($h, $expire);
	    }
	    return $ret;
	}
		
	/**
	 * hash 返回名称为h的hash中所有的键（field）及其对应的value
	 * @param unknown_type $h
	 */
	public  function hGetAll($h){
		return $this->getReadCache()->hGetAll($h);
	}
	
	/**
	 * hash hMset 向名称为h的hash中添加键值队性数组元素
	 * @param unknown $h
	 * @param unknown $data
	 */
	public  function hMset($h, $data, $expire = 0){
		$res =  $this->getWriteCache()->hMset($h, $data);
		if ($expire) {
			$ttl = $this->getReadCache()->ttl($h);
			if (-1 == $ttl) {
				$this->getWriteCache()->expire($h, $expire);
			}
		}
		return $res;
	}
	
	/**
	 * hash hMget 从名为$hash的hash中返回$keys指定的字段值
	 * @param string $hash
	 * @param array $keys
	 */
	public  function hMget($hash, $keys){
		return $this->getWriteCache()->hMget($hash, $keys);
	}

	/**
	 * hash 返回名称为key的hash中所有键
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hKeys($h){
		return $this->getReadCache()->hKeys($h);
	}
	
	/**
	 * 返回名称为h的hash中所有键对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hVals($h){
		return $this->getReadCache()->hVals($h);
	}
	
	/**
	 * 删除名称为h的hash中键为key1的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hDel($h, $key){
		return $this->getWriteCache()->hDel($h, $key);
	}
	
	/**
	 * 名称为h的hash中是否存在键名字为a的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hExists($h, $key){
		return $this->getReadCache()->hExists($h, $key);
	}
}
