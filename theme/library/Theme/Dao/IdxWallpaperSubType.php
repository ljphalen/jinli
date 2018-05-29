<?php

class Theme_Dao_IdxWallpaperSubType extends Common_Dao_Base {

    protected $_name = 'idx_wallpaperimage_to_type_subs';
    protected $_primary = 'id';

    /**
     * 获取二级标签下的壁纸ID 前1000张;
     * @param type $wheres
     * @return type
     */
    public function getWallpaperIdDao($wheres, $page = 1, $num = 1000) {
        $pages = ($page - 1) * $num;
        $sql = sprintf("select * from %s where %s limit %d,%d", $this->_name, $wheres, $pages, $num);
        $res = Db_Adapter_Pdo::fetchAll($sql);
        return $res;
    }

    /**
     * 某张壁纸的二级标签
     * @param String $wheres 查询条件
     * @return Array
     */
    public function getPapaprTargsDao($wheres) {
        $sql = sprintf("select * from %s where %s", $this->_name, $wheres);
        $res = Db_Adapter_Pdo::fetchAll($sql);

        return $res;
    }

    public function addPaperTargsDao($paperId, $targId) {
        $sql = sprintf("insert into %s (wallpaper_id,wallpaper_type_subid)"
                . "VALUES (%s,%s)", $this->_name, $paperId, $targId);


        $res = Db_Adapter_Pdo::execute($sql);

        return $res;
    }

    public function addTargDao($targNmae) {
        $sql = sprintf("insert into %s values(%s)", $this->_name, $targName);


        return Db_Adapter_Pdo::execute($sql);
    }

    public function deleteDao($id) {
        $sql = sprintf("delete  from %s where wallpaper_id = %s", $this->_name, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

}
