<?php
class Async_WebService {
    /**
     * 在web服务器上运行功能，无需等待响应。切忌滥用。
     * (这种方案有缺陷：
     * 1、需要将php.ini中的ignore_user_abort需要设置为on；
     * 2、需要考虑php的执行最大时间；
     * 所以swoole可能会更好！)
     * @param string $class
     * @param string $method
     * @param array $args
     */
    public static function run($class, $method, $args) {
        try {
            $NewArgs = base64_encode(json_encode($args, true));
            $sign = self::getAsynCallSign($class, $method, $NewArgs);
            $webroot = Common::getWebRoot();
            $url = $webroot . "/Api/Async/call?class={$class}&method={$method}&args={$NewArgs}&sign={$sign}";
            $curl = new Util_Http_Socket($url, 1, false, true);
            if (!$curl->getHttpHandler()) {
                throw new Exception($curl->getError());
            }
            $result = $curl->post();
            usleep(10000);
            $curl->close();
        } catch (Exception $e) {
            Util_Log::err("Async_WebService::run", "Async.log", "exception: {$e}");
            Async_Task_Center::execute($class, $method, $args);
        }
    }
    
    /**
     * 计算异步调用的签名，用于认证请求的合法性
     * @param string $class
     * @param string $method
     * @param string $args
     * @return string
     */
    public static function getAsynCallSign($class, $method, $args) {
        static $crcEx = "qwern@230af8z54ner#2&qw9+_";
        return md5($class . $method . $args . $crcEx);
    }
}