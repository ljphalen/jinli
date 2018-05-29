<?php

class Theme_Dao_Wallpapersubtype extends Common_Dao_Base {

    protected $_name = 'wallpaper_type_subs';
    protected $_primary = 'w_subtype_id';

    public function getAll($wheres) {
        $sql = sprintf("select * from %s where %s", $this->_name, $wheres);
        $res = Db_Adapter_Pdo::fetchAll($sql);
        return $res;
    }

    public function delPaperTargsDao($paperId, $targsId) {
        if ($targsId) {
            $sql = sprintf("delete * from %s where wallpaper_id = %s "
                    . "and wallpaper_type_subid=%s", $this->_name, $paperId, $targsId);
        } else {
            $sql = sprintf("delete * from %s where wallpaper_id = %s ", $this->_name, $paperId);
        }
        $res = Db_Adapter_Pdo::fetchAll($sql);

        return $res;
    }

    public function updateTargsDao($setData, $where) {
        $sql = sprintf("update %s set  %s where %s", $this->_name, $setData, $where);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function delTargDao($id) {
        $sql = sprintf("delete from %s where w_subtype_id=%s", $this->_name, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function addTargDao($filed, $data) {
        $sql = sprintf("insert into %s (%s) values(%s)", $this->_name, $filed, $data);
        return Db_Adapter_Pdo::execute($sql);
    }

}
