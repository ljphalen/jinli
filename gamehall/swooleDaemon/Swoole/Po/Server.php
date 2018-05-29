<?php

class Swoole_Po_Server {
    public $swoole;
    public $socket;

    public function __construct($swoole, $socket) {
        $this->socket = $socket;
        $this->swoole = $swoole;
    }
    
    public function setMasterPid($master_pid) {
        $masterPidFile = $this->getFilePath('master_pid');
        if (file_exists($masterPidFile)) {
            file_put_contents($masterPidFile, $master_pid);
        }
    }
    
    public function setManagerPid($manager_pid) {
        $managerPidFile = $this->getFilePath('manager_pid');
        if (file_exists($managerPidFile)) {
            file_put_contents($managerPidFile, $manager_pid);
        }
    }

    public function getMasterPid() {
        $masterPidFile = $this->getFilePath('master_pid');
        $pid = false;
        if (file_exists($masterPidFile)) {
            $pid = file_get_contents($masterPidFile);
        }
        return $pid;
    }
    
    public function getManagerPid() {
        $managerPidFile = $this->getFilePath('manager_pid');
        $pid = false;
        if (file_exists($managerPidFile)) {
            $pid = file_get_contents($managerPidFile);
        }
        return $pid;
    }
    
    public function getRunLogFile() {
        $runLog = $this->getFilePath('run_log');
        return $runLog;
    }
    
    public function getGameBaseDir() {
        return $this->swoole['game_base_dir'];
    }

    public function runLog($msg) {
//         echo $msg . PHP_EOL;
        $runLog = $this->getFilePath('run_log') . date("Y-m-d");
        error_log('['.date("Y-m-d H:i:s")."]\t".$msg . PHP_EOL, 3, $runLog);
    }
    
    public function log($msg) {
        $dataLog = $this->getFilePath('data_log') . date("Y-m-d");
        error_log($msg . PHP_EOL, 3, $dataLog);
    }
    
    
    private function getFilePath($field) {
        if(strpos($this->swoole[$field], "/") !== 0) {
            $this->swoole[$field] = SWOOLE_BASE_PATH . '/'. $this->swoole[$field];
        }
        return $this->swoole[$field];
    }
    
}

