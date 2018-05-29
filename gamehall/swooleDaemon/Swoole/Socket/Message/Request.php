<?php

class Swoole_Socket_Message_Request {
    private $_op;
    private $_data;
    private $_taskId;
    private $_reveiveTime;
    
    public function __construct($op, $data, $taskId) {
        $this->_op = $op;
        $this->_data = $data;
        $this->_taskId = $taskId;
        $this->_reveiveTime = intval(microtime(true) * 1000);
    }
    
    public function getOp() {
        return $this->_op;
    }
    
    public function getData() {
        return $this->_data;
    }
    
    public function getTaskId() {
        return $this->_taskId;
    }
    
    public function getTask() {
        return $this->_data['task'];
    }
    
    public function getMethod() {
        return $this->_data['method'];
    }
    
    public function getArgs() {
        return $this->_data['args'];
    }
    
    public function setTaskId($taskId) {
        $this->_taskId = $taskId;
    }
    
    public function __toString() {
        return $this->_op . " >> " . json_encode($this->_data) . ($this->_taskId ? "  " . $this->_taskId : "");
    }
    
}

