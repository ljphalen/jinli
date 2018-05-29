<?php

class Swoole_Socket_Message_Response {
    private $_op;
    private $_data;
    private $_taskId;
    
    public function __construct($op, $data, $taskId) {
        $this->_op = $op;
        $this->_data = $data;
        $this->_taskId = $taskId;
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
    
    public function __toString() {
        return $this->_op . " >> " . json_encode($this->_data);
    }
    
}

