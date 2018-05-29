<?php

class Theme_Dao_Wallpaperlive extends Common_Dao_Base {

    protected $_name = 'wallpaperlive_file';
    protected $_primary = 'wallpaperlive_id';

    public function getAll($wheres, $limits, $pages) {
        $offset = ($pages - 1) * $limits;

        $wheres = $wheres ? $wheres : "1=1 order by $this->_primary DESC";
        $sql_count = sprintf("select count(*) as count from %s where %s ", $this->_name, $wheres);
        $sql = sprintf("select * from %s where %s limit %d, %d", $this->_name, $wheres, $offset, $limits);

        $count = Db_Adapter_Pdo::fetchAll($sql_count);
        $res = Db_Adapter_Pdo::fetchAll($sql);
        $result = array($count[0]["count"], $res);
        return $result;
    }

    public function insert_muti($where, $fileds, $sqlstr = false) {
        $sql = sprintf("insert into %s (%s)values(%s)", $this->_name, $filedname, $values);
        if ($sqlstr) echo $sql . "<br/>";
        return Db_Adapter_Pdo::execute($sql);
    }

    public function getByFileIds($file_ids, $time = true, $order = FALSE) {

        if ($order) {
            if ($time) {
                $sql = sprintf('SELECT * FROM %s WHERE wallpaperlive_id in (%s) and wallpaper_online_time < ' . time()
                        . ' ORDER BY FIELD (%s,%s) ', $this->_name, $file_ids, $this->_primary, $file_ids);
            } else {
                $sql = sprintf('SELECT * FROM %s WHERE wallpaperlive_id in (%s) '
                        . ' ORDER BY FIELD (%s,%s) ', $this->_name, $file_ids, $this->_primary, $file_ids);
            }
        } else {
            $sql = sprintf('SELECT * FROM %s WHERE wallpaperlive_id in (%s) ', $this->_name, $file_ids);
        }


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function test($ss) {
        return $ss . "runDao";
    }

}
