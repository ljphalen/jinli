<?php

class Async_Task_Message_Spl {
    private $_params;
    private $_taskId;
    
    public function __construct($params, $taskId="") {
        $this->_params = $params;
        $this->_taskId = $taskId;
    }
    
    public function getParams() {
        return $this->_params;
    }
    
    public function getParam($key) {
        return $this->_params[$key];
    }
    
    public function getTaskId() {
        return $this->_taskId;
    }
    
    public function __toString() {
        return json_encode(array('taskId' => $this->_taskId, 'params' => $this->_params));
    }
    
}

