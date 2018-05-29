<?php

class Theme_Dao_Clockmy extends Common_Dao_Base {

    protected $_name = 'clock_file';
    protected $_primary = 'id';

    public function insert_muti($where, $fileds, $sqlstr = false) {
        $sql = sprintf("insert into %s (%s)values(%s)", $this->_name, $filedname, $values);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::execute($sql);
    }

    public function test($ss) {
        return $ss . "runDao";
    }
    
    /**
    * 
    * 从游戏名称中查找匹配的字符并返回数据
    * @param array $data
    * @param int $value
    */
    public  function getMaxCol($key) {
        $sql = sprintf('SELECT MAX(%s) FROM %s', $key, $this->getTableName());
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
    }
 
}
