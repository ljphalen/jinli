<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Apc操作类
 * @author huyuke
 *
 */
class Cache_Apcu implements  Cache_Interface {

    const DEFAULT_EXPIRE = 60; //second
     /**
      * @param mixed $key 使用 apcu_store() 存储的键名, 
      *                   如果传递的是一个数组，则数组中的每个元素的值都被返回
      * @return mixed 失败返回false, 成功返回使用 apcu_store() 存入的值
      */
    public function get($key) {
        return apcu_fetch($key);
    }

     /**
      * @param string $key
      * @param mixed  $value
      * @param int    $expire second
      * @return boolean  success true else false
      */
    public function set($key, $value, $expire = self::DEFAULT_EXPIRE) {
        return apcu_store($key, $value, $expire);
    }

     /**
      * @param string $key
      * @param int    $value
      * @return int   lastest value
      */
    public  function incrBy($key, $step = 1) {
        return apcu_inc($key, $step);
    }

     /**
      * @param string $key
      * @return boolean  success true else false
      */
    public function delete($key) {
        return apcu_delete($key);
    }

     /**
      * 清除用户缓存数据,暂不支持清除系统缓存
      * @return boolean  success true else false
      */
    public function flush() {
         return apcu_clear_cache('user');
    }
}
