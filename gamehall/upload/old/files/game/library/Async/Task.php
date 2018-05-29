<?php

class Async_Task {

    private $client;
    private static $_instance;
    
    private static function getInstance() {
        if(! self::$_instance) {
            self::$_instance = new Async_Task();
        }
        return self::$_instance;
    }
    
    public static function execute($task, $method, $params = array()) {
        Logger_Game::getLogger(__CLASS__)->info(func_get_args());
        $instance = self::getInstance();
        if($instance->isConnected()) {
            if(! $instance->send($task, $method, $params)) {
                $instance->call($task, $method, $params);
            }
        }else{
            Logger_Game::getLogger(__CLASS__)->info('call');
            $instance->call($task, $method, $params);
        }
    }
    
    public function __construct() {
        if (extension_loaded('swoole')) {
            $this->client = new swoole_client(SWOOLE_SOCK_TCP);
            $this->initConnect();
        }
    }
    
    private function initConnect() {
        $socket = Common::getConfig('swooleConfig', 'socket');
        $this->client->connect($socket['ip'], $socket['port']);
    }
    
    private function isConnected() {
        return $this->client && $this->client->isConnected();
    }
    
    private function call($task, $method, $params) {
        try {
            if(! class_exists($task)) {
                return;
            }
            $class = new $task();
            if(! $class instanceof Async_Task_Base) {
                return;
            }
            Async_Task_Center::execute($task, $method, $params);
        } catch (Exception $e) {
            Logger_Game::getLogger(__CLASS__)->error(func_get_args(), $e);
        }
    }
    
    private function send($task, $method, $params) {
        try {
            $data = $this->getData($task, $method, $params);
            $message = $this->pack($data);
            $this->client->send($message);
            $message = $this->client->recv();
            $message = $this->unpack($message);
            return $message && $message['data'];
        } catch (Exception $e) {
            Logger_Game::getLogger(__CLASS__)->error(func_get_args(), $e);
        }
        return false;
    }

    private function unpack($data) {
        if(!$data) return false;
        $pack = unpack("N", $data);
        $length = $pack[1];
        $msg = substr($data, - $length);
        return json_decode($msg, true);
    }
    
    private function pack($data) {
        $message = json_encode($data);
        return pack("N" , strlen($message) ). $message;
    }
    
    private function getData($class, $method, $params) {
        $tmp = array('task' => $class, 'method' => $method, 'args' => $params);
        $data = array('op' => 'GAME', 'data' => $tmp);
        return $data;
    }
}

