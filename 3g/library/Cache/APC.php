<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * apc操作类
 * @author huwei
 *
 */
class Cache_APC {
    static public function get($key) {
        return apc_fetch($key);
    }

    static public function set($key, $val, $ttl = 60) {
        return apc_store($key, $val, $ttl);
    }

    static public function del($key) {
        return apc_delete($key);
    }
}