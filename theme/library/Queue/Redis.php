<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * RedisQ操作类
 */
class Queue_Redis extends Redis implements Queue_Base {

    /**
     *
     * 自身对象
     *
     * @var unknown_type
     */
    protected $mRedis;

    /**
     * 去重队列key的前缀
     */
    protected $prefix = 'sync:';

    /**
     *
     * 构造函数
     * @param unknown_type $cacheInfo
     */
    public function __construct($queueInfo = NULL) {
        if (is_array($queueInfo)) $this->connectServer($queueInfo);

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
        parent::connect($queueInfo['host'], $queueInfo['port']);
    }

    /**
     *
     * 入队列
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public function push($key, $value, $right = true) {
        $value = is_array($value) ? json_encode($value) : $value;
        return $right ? parent::rPush($key, $value) : parent::lPush($key, $value);
    }

    /**
     *
     * 出队列
     * @param unknown_type $key
     */
    public function pop($key, $left = true) {
        return $left ? parent::lPop($key) : parent::rPop($key);
    }

    /**
     *
     * 去重入队列
     * @param string $key
     * @param string $value
     * @param string $prefix
     */
    public function noRepeatPush($key, $value, $prefix = '') {
        $pos = strpos($key, $this->prefix);
        if (!$prefix) $prefix = $pos === false ? $key : substr($key, strlen($this->prefix) - 1);
        $ckey = $prefix . ':' . $value;
        if ($this->get($ckey)) return true;
        $this->set($ckey, $value);
        if ($pos === false) $key = $this->prefix . $key;
        return $this->push($key, $value);
    }

    /**
     *
     * 出队列时删除cachekey
     * @param string $key
     * @param string $prefix
     */
    public function noRepeatPop($key, $prefix = '') {
        $pos = strpos($key, $this->prefix);
        if (!$prefix) $prefix = $pos === false ? $key : substr($key, strlen($this->prefix) - 1);
        if ($pos === false) $key = $this->prefix . $key;
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
        return $this->llen($key);
    }

    /**
     *
     * 自增长
     * @param unknown_type $key
     */
    public function increment($key) {
        return $this->incr($key);
    }

    /**
     *
     * 自增减
     * @param unknown_type $key
     */
    public function decrement($key) {
        return $this->decr($key);
    }

    /**
     *
     * 取出值
     * @param unknown_type $key
     */
    public function get($key) {
        return parent::get($key);
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
        return parent::lRange($key, $start, $end);
    }

    /**
     *
     * 清空
     */
    public function clear() {
        return parent::flushdb();
    }

    /**
     *
     * 设置值
     * @param string $key
     * @param string/array $value
     */
    public function set($key, $value, $timeOut = 0) {
        $value = is_array($value) ? json_encode($value) : $value;
        $retRes = parent::set($key, $value);
        if ($timeOut > 0) parent::setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     *
     * 删除值
     * @param unknown_type $key
     */
    public function del($key) {
        return parent::delete($key);
    }

    /**
     * 哈希写入
     * @param type $hash
     * @param type $key
     * @param type $value
     * @return type
     */
    public function hset($hash, $key, $value) {
        $value = is_array($value) ? json_encode($value) : $value;
        $res = parent::hSet($hash, $key, $value);
        return $res;
    }

    /**
     * 取哈希
     * @param type $hash
     * @param type $key
     * @return type
     */
    public function hget($hash, $key) {
        return parent::hGet($hash, $key);
    }

}
