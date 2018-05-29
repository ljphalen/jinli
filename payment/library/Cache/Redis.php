<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Redis操作类
 * @author ljp
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
	 * 选择redis库
	 * @param unknown_type $db
	 * @return unknown
	 */
	public function select($db = 0) {
		return $this->cache->select($db);;
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
	 * 指定key是否存在
	 * @param string $key
	 * @return 1 存在 0 不存在
	 */
	public function exists($key){
		$key = $this->_getKey($key);
		return $this->cache->exists($key);
	}
	/**
	 * 设置key过期方法
	 * @param unknown $key
	 * @param unknown $timeout
	 */
	public function expire($key, $timeout){
		$key = $this->_getKey($key);
		return $this->cache->expire($key, $timeout);
	}

	//检测某元素是否在序列中
	public function sismember($key,$value){
		$key = $this->_getKey($key);
		return $this->cache->sismember($key,$value);
	}
	
	/**
	 * hash set 向名称为h的hash中添加元素
	 * @param unknown_type $h
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public  function hSet($h, $key, $value, $expire=0){
		$h = $this->_getKey($h);
		$res =$this->cache->hSet($h,$key,$value);
		if ($expire) $this->cache->expire($h, $expire);
		return $res;
	}
	

	/**
	 * hash get 名称为h的hash中key1对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hGet($h, $key){
		$h = $this->_getKey($h);
		return $this->cache->hGet($h, $key);
	}
	
	/**
	 * hash key 自增
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hIncrBy($h, $key, $step = 1, $expire = 0){
		$h = $this->_getKey($h);
	    $ret = $this->cache->hIncrBy($h, $key, $step);
	    if ($expire) {
	    	$this->cache->expire($key, $expire);
	    }
	    return $ret;
	}
		
	/**
	 * hash 返回名称为h的hash中所有的键（field）及其对应的value
	 * @param unknown_type $h
	 */
	public  function hGetAll($h){
		$h = $this->_getKey($h);
		return $this->cache->hGetAll($h);
	}

	
	/**
	 * hash 返回名称为key的hash中所有键
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hKeys($h){
		$h = $this->_getKey($h);
		return $this->cache->hKeys($h);
	}
	
	/**
	 * 返回名称为h的hash中所有键对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hVals($h){
		$h = $this->_getKey($h);
		return $this->cache->hVals($h);
	}
	
	/**
	 * 删除名称为h的hash中键为key1的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hDel($h, $key){
		$h = $this->_getKey($h);
		return $this->cache->hDel($h, $key);
	}
	
	/**
	 * 名称为h的hash中是否存在键名字为a的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hExists($h, $key){
		$h = $this->_getKey($h);
		return $this->cache->hExists($h, $key);
	}
}
