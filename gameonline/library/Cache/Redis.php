<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Redis操作类
 * @author rainkid
 *
 */
class Cache_Redis implements  Cache_Interface {
	
	protected $cache;
	protected $prefixKey = '';
	
	/**
	 * 
	 * 构造函数
	 * 
	 */
	public function __construct($cacheConfig) {
		$this->cache = new Redis();
		if (isset($cacheConfig['host']) && isset($cacheConfig['port'])) $this->connectCache($cacheConfig);
		if (isset($cacheConfig['key-prefix'])) $this->prefixKey = $cacheConfig['key-prefix'];
		return $this;
	}
	
	/**
	 * 连接redis
	 * 
	 */
	public function connectCache($cacheInfo) {
		try {
			$this->cache->connect($cacheInfo['host'], $cacheInfo['port']);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * 从内存获取数据 
	 *
	 */
	public function get($key) {
		$key = $this->_getKey($key);
		if (!is_array($key)) return $this->_getSingleValue($key);
		$temp = $this->cache->mget($key);
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
		$temp = $this->cache->get($key);
		if ($temp === false) return false;
		return json_decode($temp, true);
	}
	
	/**
	 * 设置值到内存中
	 */
	public function set($key, $value, $expire = 0) {
		$key = $this->_getKey($key);
		$value = json_encode($value);
		$result = $this->cache->set($key, $value);
		if ($expire) $this->cache->expire($key, $expire);
		return $result;
	}
	
	/**
	 * 
	 * 自增长
	 * @param unknown_type $key
	 * @param unknown_type $step
	 */
	public function increment($key, $step = 1) {
		$key = $this->_getKey($key);
		return $this->cache->incr($key, $step);
	}
	
	/**
	 * 
	 * 递减
	 * @param string $key
	 * @param int $step
	 */
	public function decrement($key, $step = 1) {
		$key = $this->_getKey($key);
		return $this->cache->decr($key, $step);
	}
	
	/**
	 * 
	 * 删除内存$key
	 */
	public function delete($key) {
		$key = $this->_getKey($key);
		return $this->cache->delete($key);
	}
	
	/**
	 * 
	 * 分页取列表
	 * @param unknown_type $key
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */
	public function get_list_range($key, $limit, $offset) {
		$key = $this->_getKey($key);
		return $this->cache->lGetRange($key, $limit, $limit + $offset - 1);
	}
	
	public function hIncrBy($key, $val, $step = 1, $expire = 172800) {
		$key = $this->_getKey($key);
		$ret = $this->cache->hIncrBy($key, $val, $step);
		if ($ret < 2 && $expire) {
			$this->cache->expire($key, $expire);
		}
		return $ret;
	}
	
	public function hGetAll($key) {
		$key = $this->_getKey($key);
		return $this->cache->hGetAll($key);
	}
	
	public function hGet($key, $val) {
		$key = $this->_getKey($key);
		return $this->cache->hGet($key, $val);
	}
	
	public function hSet($key,$domain,$val,$expire=0){
		$key = $this->_getKey($key);
		$res =$this->cache->hSet($key,$domain,$val);
		if ($expire) $this->cache->expire($key, $expire);
		return $res;
	}
	
	/**
	 * 组装键值
	 * @param string $key
	 * @return
	 */
	private function _getKey($key) {
		return $this->prefixKey . $key;
	}
	
	/**
	 * 清内存
	 * 
	 */
	public  function flush() {
		return $this->cache->flushDB();
	}
	
	/**
	 * 设置key过期方法
	 * @param unknown $key
	 * @param unknown $timeout
	 */
	public function expire($key, $timeout){
		
		return $this->cache->expire($key, $timeout);
	}

	//检测某元素是否在序列中
	public function sismember($key,$value){
		$key = $this->_getKey($key);
		return $this->cache->sismember($key,$value);
	}
	
}
