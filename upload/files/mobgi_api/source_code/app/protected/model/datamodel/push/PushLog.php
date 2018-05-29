<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-16 15:53:11
 * $Id: PushLog.php 62100 2015-1-16 15:53:11Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushLogBase');

class PushLog extends PushLogBase{
    
    /**
     * 新增push日志
     * @param type $type
     * @param type $log
     * @param type $operator
     * @param type $response
     * @return type
     */
    public function add($type, $log, $operator, $response){
        $this->type = $type;
        $this->log = $log;
        $this->operator = $operator;
        $this->response = $response;
        $this->createtime = time();
        return $this->insert();
    }
}

