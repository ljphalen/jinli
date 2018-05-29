<?php

class Theme_Dao_Wallpapertype extends Common_Dao_Base {

    protected $_name = 'wallpaper_type';
    protected $_primary = 'w_type_id';

    public function getAll($wheres, $order = "order by w_type_id DESC") {

        $wheres = parent::mk_sqls($wheres);
        $sql = sprintf("select * from %s where %s %s", $this->_name, $wheres, $order);

        $res = Db_Adapter_Pdo::fetchAll($sql);

        return $res;
    }

    public function addTargDao($fildname, $targName) {
        $sql = sprintf("insert into %s (%s)values(%s)", $this->_name, $fildname, $targName);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function delTargDao($id) {
        $sql = sprintf("delete from %s where w_type_id=%s", $this->_name, $id);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function updateTypeDao($setData, $where) {
        $sql = sprintf("update %s set  %s where %s", $this->_name, $setData, $where);


        return Db_Adapter_Pdo::execute($sql);
    }

}
