<?php

class Swoole_Socket_Handler {
	
    private $serverInfo = null;
    
    public function __construct(Swoole_Po_Server $serverInfo) {
        $this->serverInfo = $serverInfo;
        Swoole_Socket_Factory::register(new Swoole_Socket_Adapter_Decode(), new Swoole_Socket_Adapter_Encode());
    }
    
    public function onStart($server) {
        $this->serverInfo->setMasterPid($server->master_pid);
        $this->serverInfo->setManagerPid($server->manager_pid);
        $this->serverInfo->runLog('server start, swoole version: ' . SWOOLE_VERSION);
        $this->serverInfo->runLog('master_pid: ' . $server->master_pid);
        $this->serverInfo->runLog('manager_pid: ' . $server->manager_pid);
    }

    public function onReceive($server, $fd, $from_id, $data) {
        $request = Swoole_Socket_Factory::decode($data);
        if(! ($request instanceof Swoole_Socket_Message_Request)) {
            return;
        }
        if($request->getOp() == Swoole_Socket_Util::OP_GAME){//客户端请求
            if($this->isClosed($server)) {
                $server->send($fd, Swoole_Socket_Util::getDefaultReply(0));
                return;
            }
            $message = (string) serialize($request);
            $task_id = $server->task($message);
            $server->send($fd, Swoole_Socket_Util::getDefaultReply());
        }elseif($request->getOp() == Swoole_Socket_Util::OP_COMMAND) {
            $request->setTaskId(null);
            $message = (string) serialize($request);
            $task_id = $server->task($message);
            $server->send($fd, Swoole_Socket_Util::getDefaultReply());
        }
    }

    public function onTask($server, $task_id, $from_id, $data) {
        $request = unserialize($data);
        if(! $request instanceof Swoole_Socket_Message_Request) {
            return "sucess";
        }
        if($request->getOp() == Swoole_Socket_Util::OP_TIMER) {
            $this->executeRequest($request);
        }else if($request->getOp() == Swoole_Socket_Util::OP_GAME) {
            $this->executeRequest($request);
            $this->serverInfo->log((string)$request);
        }else if($request->getOp() == Swoole_Socket_Util::OP_COMMAND) {
            $this->serverInfo->runLog((string)$request);
            $data = $request->getData();
            $op = mb_strtoupper($data['op']);
            if($op == 'CLOSE') {
                $server->table->set(Swoole_Socket_Util::TABLE_KEY_CLOSED, array(Swoole_Socket_Util::TABLE_COLUMN_INTVALUE => 1));
            }else if($op == 'OPEN') {
                $server->table->del(Swoole_Socket_Util::TABLE_KEY_CLOSED);
            }else if($op == 'SHUTDOWN') {
                $this->shutdown();
            }else if($op == 'RELOAD') {
                $this->reload();
            }
        }
        return "sucess";
    }

    public function onWorkerStart($server, $worker_id) {
        if($worker_id >= $server->setting['worker_num']) {
            swoole_set_process_name("game worker task {$worker_id} ");
            $this->serverInfo->runLog("game worker task {$worker_id} ");
        } else {
            swoole_set_process_name("game worker event {$worker_id} ");
            $this->serverInfo->runLog("game worker event {$worker_id} ");
        }
        Swoole_AsynServer::init($this->serverInfo);
        if ($worker_id == 0) {
            self::initTimer($server);
        }
        return "sucess";
    }

    public function onTimer($server, $interval) {
        try {
            $microtime = explode(" ", microtime());
            $this->serverInfo->runLog('心跳: ' . $microtime[1]);
            $request = Swoole_Socket_Util::getTimerRequest($microtime[1]);
            $message = (string) serialize($request);
            $task_id = $server->task($message);
        } catch (Exception $e) {
            $msg = "onTimer Exception : " . json_encode(func_get_args()) . ((string)$e);
            $this->serverInfo->log($msg);
        }
    }

    public function onConnect($server, $fd, $from_id) {
        echo "{$fd} connected " . PHP_EOL;
    }

    public function onClose($server, $fd, $from_id) {
        echo "{$fd} closed " . PHP_EOL;
    }
    
    public function onWorkerStop($server, $worker_id) {
    }

    public function onFinish($server, $task_id, $data) {
    }

    public function onWorkerError($server, $worker_id, $worker_pid, $exit_code) {
        $this->serverInfo->runLog("onWorkerError: " . json_encode(func_get_args()));
    }
    
    private function initTimer($server) {
        $time = microtime(true) * 1000;
        $des = ceil($time / 5000) * 5000;
        $delay = $des - $time;
        //$server->after($delay, array($this, 'startTimer' ), $server);
        $server->after(5000, function(){
            echo "timeout".PHP_EOL;
        });
    }
    
    public function startTimer($server) {
        $server->addtimer(5000);
    }
    
    private function isClosed($server) {
        $tableValue = $server->table->get(Swoole_Socket_Util::TABLE_KEY_CLOSED);
        if($tableValue && $tableValue[Swoole_Socket_Util::TABLE_COLUMN_INTVALUE] == 1) {
            return true;
        }
        return false;
    }
    
    private function executeRequest($request) {
        $taskId = $request->getTaskId();
        $task = $request->getTask();
        $method = $request->getMethod();
        $args = $request->getArgs();
        Async_Task_Center::execute($task, $method, $args, $taskId);
    }

    private function shutdown() {
        $masterId = $this->serverInfo->getMasterPid();
        if (! $masterId) {
            $this->serverInfo->runLog('can not find master pid file');
            return false;
        } elseif (! posix_kill($masterId, SIGTERM)) {
            $this->serverInfo->runLog('send signal to master failed');
            return false;
        }
        $this->serverInfo->runLog('shutdown sucess');
        return true;
    }
    
    private function reload() {
        $this->serverInfo->runLog('reload');
        $managerId = $this->serverInfo->getManagerPid();
        if (! $managerId) {
            $this->serverInfo->runLog('can not find manager pid file');
            return false;
        } elseif (! posix_kill($managerId, SIGUSR1)) {
            $this->serverInfo->runLog('send signal to manager failed');
            return false;
        }
        $this->serverInfo->runLog('reload sucess');
        return true;
    }

}
