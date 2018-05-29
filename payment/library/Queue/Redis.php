<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * RedisQ操作类
 * @author ljp
 *
 */

class Queue_Redis implements Queue_Base {
	
	/**
	 * 
	 * 自身对象
	 *
	 * @var unknown_type
	 */
	protected $mRedis;
	
	/**
	 * 
	 * 构造函数
	 * @param unknown_type $cacheInfo
	 */
	public function __construct($queueInfo = NULL) {
		$this->mRedis = new Redis();
		if(is_array($queueInfo))
			$this->connectServer($queueInfo);

		return $this;
	}

	/**
	 * 连接缓存服务器 
	 * 
	 * @param mixed $cacheInfo 
	 * @access public
	 * @return void
	 */
	public function connectServer($queueInfo) {
		$this->mRedis->connect($queueInfo['host'], $queueInfo['port']);
	}
	

	
	/**
	 * 
	 * 入队列
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function push($key, $value ,$right = true) {
		$value = is_array($value) ? json_encode($value) : $value;
		return $right ? $this->mRedis->rPush($key, $value) : $this->mRedis->lPush($key, $value);
	}
	
	/**
	 * 
	 * 出队列
	 * @param unknown_type $key
	 */
	public function pop($key , $left = true) {
		return $left ? $this->mRedis->lPop($key) : $this->mRedis->rPop($key);
	}
	
	/**
	 * 
	 * 去重入队列
	 * @param string $key
	 * @param string $value
	 * @param string $prefix
	 */
	public function noRepeatPush($key, $value, $prefix) {
		$ckey = $prefix . ':' . $value;
		if ($this->get($ckey)) return true;
		$this->set($ckey, $value);
		return $this->push($key, $value);
	}
	
	/**
	 * 
	 * 出队列时删除cachekey
	 * @param string $key
	 * @param string $prefix
	 */
	public function noRepeatPop($key, $prefix) {
		$value = $this->pop($key);
		$ckey = $prefix . ':' . $value;
		$this->del($ckey);
		return $value;		
	}

	/**
	 * 
	 * 队列长度
	 * @param unknown_type $key
	 */
	public function len($key) {
		return $this->mRedis->llen($key);
	}

	/**
	 * 
	 * 自增长
	 * @param unknown_type $key
	 */
	public function increment($key) {
		return $this->mRedis->incr($key);
	}

	/**
	 * 
	 * 自增减
	 * @param unknown_type $key
	 */
	public function decrement($key) {
		return $this->mRedis->decr($key);
	}
	
	/**
	 * 
	 * 取出值
	 * @param unknown_type $key
	 */
	public function get($key) {
		return $this->mRedis->get($key);
	}
	/**
	 * 
	 * 返回名称为$key的list中start至end之间的元素（end为 -1 ，返回所有）
	 * @param unknown_type $key
	 * @param integer $start
	 * @param integer $length  要获取的个数，0 表示获取全部
	 */
	public function getList($key, $start, $length = 0) {
		if ($length < 0) {
			$length = 0;
		}
		$start < 0 && $start = 0;
		$end = $start + $length - 1;
		return $this->mRedis->lRange($key, $start, $end);
	}
	/**
	 * 
	 * 清空
	 */
	public function clear() {
		return $this->mRedis->flushAll();
	}
	
	/**
	 * 
	 * 设置值
	 * @param string $key
	 * @param string/array $value
	 */
	public function set($key, $value, $timeOut = 0) {
		$value = is_array($value) ? json_encode($value) : $value;
		$retRes = $this->mRedis->set($key, $value);
		if ($timeOut > 0) $this->mRedis->setTimeout($key, $timeOut);
		return $retRes;
	}
	
	/**
	 * 
	 * 删除值
	 * @param unknown_type $key
	 */
	public function del($key) {
		return $this->mRedis->delete($key);
	}
	
	public function setnx($key, $value) {
		return $this->mRedis->setnx($key, $value);
	}
	
	public function getset($key, $value) {
		return $this->mRedis->getset($key, $value);
	}
	
	public function expire($key, $second) {
		return $this->mRedis->expire($key, $second);
	}
	
	public function keys($key) {
		return $this->mRedis->keys($key);
	}
	
	/**
	 * hash set 向名称为h的hash中添加元素
	 * @param unknown_type $h
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public  function hSet($h, $key, $value){
		  return $this->mRedis->hSet($h, $key, $value);
	}
	
	/**
	 * hash get 名称为h的hash中key1对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hGet($h, $key){
		return $this->mRedis->hGet($h, $key);
	}
	
	/**
	 * hash key 自增
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hIncrBy($h, $key, $value = 1){
		return $this->mRedis->hIncrBy($h, $key, $value);
	}
	
	/**
	 * hash 返回名称为h的hash中所有的键（field）及其对应的value
	 * @param unknown_type $h
	 */
	public  function hGetAll($h){
		var_dump($this->mRedis);
		return $this->mRedis->hGetAll($h);
	}
	
	/**
	 * hash 返回名称为key的hash中所有键
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hKeys($h){
		return $this->mRedis->hKeys($h);
	}
	
	/**
	 * 返回名称为h的hash中所有键对应的value
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hVals($h){
		return $this->mRedis->hVals($h);
	}
	
	/**
	 * 删除名称为h的hash中键为key1的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hDel($h, $key){
		return $this->mRedis->hDel($h, $key);
	}
	
	/**
	 * 名称为h的hash中是否存在键名字为a的域
	 * @param unknown_type $h
	 * @param unknown_type $key
	 */
	public  function hExists($h, $key){
		return $this->mRedis->hExists($h, $key);
	}
	
		
}
