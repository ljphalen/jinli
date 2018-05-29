<?php

class Swoole_Socket_Swoole {
    private $handler;
    private $server;

    public function __construct(Swoole_Po_Server $serverInfo) {
        if (! extension_loaded('swoole')) {
            $serverInfo->runLog("no swoole extension. get: https://github.com/matyhtf/swoole");
            exit();
        }

        $socket = $serverInfo->socket;
        $swoole = $serverInfo->swoole;
        
        $this->server = new swoole_server($socket['host'], $socket['port'], $socket['work_mode']);
        $this->server->set(array(
            'reactor_num' => empty($socket['reactor_num']) ? 2 : $socket['reactor_num'],
            'worker_num' => empty($socket['worker_num']) ? 2 : $socket['worker_num'],
            'task_worker_num' => empty($socket['task_worker_num']) ? 2 : $socket['task_worker_num'],
            'task_ipc_mode ' => empty($socket['task_ipc_mode']) ? 3 : $socket['task_ipc_mode'],
            'backlog' => empty($socket['backlog']) ? 128 : $socket['backlog'],
            'max_request' => empty($socket['max_request']) ? 1000 : $socket['max_request'],
            'max_conn' => empty($socket['max_conn']) ? 1000 : $socket['max_conn'],
            'dispatch_mode' => empty($socket['dispatch_mode']) ? 2 : $socket['dispatch_mode'],
            'log_file' => $serverInfo->getRunLogFile(),
            'daemonize' => empty($socket['daemonize']) ? 0 : 1,
            'open_length_check' => empty($socket['open_length_check']) ? false : $socket['open_length_check'],
            'package_length_offset' => empty($socket['package_length_offset']) ? 0 : $socket['package_length_offset'],
            'package_body_offset' => empty($socket['package_body_offset']) ? 4 : $socket['package_body_offset'],
            'package_length_type' => empty($socket['package_length_type']) ? 'N' : $socket['package_length_type']
        ));
        $this->server->table = $this->createTable();
    }
    
    private function createTable() {
        $table = new swoole_table(1024);
        $table->column(Swoole_Socket_Util::TABLE_COLUMN_INTVALUE, swoole_table::TYPE_INT);
        $table->create();
        return $table;
    }
    
    public function setHandler($handler) {
        $this->handler = $handler;
    }

    public function run() {
        $this->server->on('Start', array($this->handler, 'onStart'));
        $this->server->on('Connect', array($this->handler, 'onConnect'));
        $this->server->on('Receive', array($this->handler, 'onReceive'));
        $this->server->on('Close', array($this->handler, 'onClose'));
        $handlerArray = array(
            'onTimer', 
            'onWorkerStart', 
            'onWorkerStop', 
            'onWorkerError',
            'onTask', 
            'onFinish',
        );
        foreach ($handlerArray as $handler) {
            if (method_exists($this->handler, $handler)) {
                $this->server->on(str_replace('on', '', $handler), array(
                    $this->handler,
                    $handler
                ));
            }
        }
        $this->server->start();
    }
}
