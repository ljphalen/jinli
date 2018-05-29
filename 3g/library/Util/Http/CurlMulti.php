<?php
if (!defined('BASE_PATH')) exit ('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
final class Util_Http_CurlMulti {
    public $conns = array();
    public $mh    = null;

    public function __construct() {
        $this->mh = curl_multi_init();
    }

    public function addHandler($key, $handler) {
        $this->conns [$key] = $handler;

        // 创建多个curl语柄
        curl_multi_add_handle($this->mh, $handler);
    }

    /**
     *
     * @param unknown_type $url
     * @param unknown_type $callback
     * @param unknown_type $data
     * @param unknown_type $method
     */
    public function execute() {
        $active = null;
        $result = array();
        // 执行批处理句柄
        do {
            $mrc = curl_multi_exec($this->mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->mh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($this->conns as $k => $conn) {
            curl_error($this->conns [$k]);
            $res [$k] = curl_multi_getcontent($this->conns [$k]); // 获得返回信息
            // $header[$k]=curl_getinfo($conn);//返回头信息
            curl_close($this->conns [$k]); // 关闭语柄
            curl_multi_remove_handle($this->mh, $this->conns [$k]); // 释放资源
        }
        curl_multi_close($this->mh);
        return $res;
    }
}