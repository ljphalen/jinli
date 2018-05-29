<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Redis操作类
 * @author rainkid
 *
 */
class Cache_Redis implements Cache_Interface {

    protected $cache;
    protected $prefixKey = '';

    /**
     * 构造函数
     */
    public function __construct($cacheConfig, $i = 0) {
        $this->cache = new Redis();
        if (!isset($cacheConfig['host'][$i])) {
            $i = 0;
        }
        if (isset($cacheConfig['host'][$i]) && isset($cacheConfig['port'][$i])) {
            $cacheConfig['host'] = $cacheConfig['host'][$i];
            $cacheConfig['port'] = $cacheConfig['port'][$i];
            $this->connectCache($cacheConfig);
        }
        if (isset($cacheConfig['key-prefix'])) {
            $this->prefixKey = $cacheConfig['key-prefix'];
        }
        return $this;
    }

    /**
     * 连接redis
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
        $temp   = $this->cache->mget($key);
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
     *
     * @param string $key
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
        $key    = $this->_getKey($key);
        $value  = json_encode($value, JSON_UNESCAPED_UNICODE);
        $result = $this->cache->set($key, $value);
        if ($expire) $this->cache->expire($key, $expire);
        return $result;
    }

    /**
     *
     * 自增长
     *
     * @param string $key
     * @param int    $step
     */
    public function increment($key, $step = 1) {
        $key = $this->_getKey($key);
        return $this->cache->incr($key, $step);
    }

    /**
     *
     * 递减
     *
     * @param string $key
     * @param int    $step
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
     *
     * @param string $key
     * @param int    $limit
     * @param int    $offset
     */
    public function get_list_range($key, $limit, $offset) {
        $key = $this->_getKey($key);
        return $this->cache->lGetRange($key, $limit, $limit + $offset - 1);
    }

    public function hIncrBy($key, $val, $step = 1, $expire = 604800) {
        $key = $this->_getKey($key);
        $ret = $this->cache->hIncrBy($key, $val, $step);
        if ($ret <= 2 && $expire) {
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

    public function hSet($key, $name, $val, $expire = 0) {
        $key = $this->_getKey($key);
        $res = $this->cache->hSet($key, $name, $val);
        if ($expire) $this->cache->expire($key, $expire);
        return $res;
    }

    public function hDel($key, $val) {
        $key = $this->_getKey($key);
        return $this->cache->hDel($key, $val);
    }

    /**
     * 组装键值
     *
     * @param string $key
     *
     * @return
     */
    private function _getKey($key) {
        return $this->prefixKey . $key;
    }

    /**
     * 清内存
     */
    public function flush() {
        return $this->cache->flushDB();
    }

    //检测某元素是否在序列中
    public function sismember($key, $value) {
        return $this->cache->sismember($key, $value);
    }

    public function sadd($key, $value) {
        return $this->cache->sadd($key, $value);
    }

    public function smembers($key) {
        return $this->cache->sMembers($key);
    }

    public function expire($key, $expire) {
        $key = $this->_getKey($key);
        $this->cache->expire($key, $expire);
    }
    //
}