<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-事件数据类
 * @author fanch
 *
 */
class Task_Event {
    
    public $mRequest = array();
    public $mName = '';
    
    function __construct($eventName, $request){
        $this->mName = $eventName;
        $this->mRequest = $request;
    }

    function __tostring() {
        return array('mName' => $this->mName,
                     'mRequest' => $this->mRequest);
    }
}
