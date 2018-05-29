<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_WallSets extends Common_Dao_Base {

    protected $_name = 'wallpaper_sets';
    protected $_primary = 'set_id';

    public function insert_into($data) {
        $fileds = $data["fileds"] ? $data['fileds'] : "null";
        $values = $data["values"] ? $data["values"] : "null";
        $sql = sprintf("insert into %s (%s) values ('%s')", $this->_name, $fileds, $values);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function getall($wheres = "1=1", $limits = 15, $pages = 1) {


        $offset = ($pages - 1) * $limits;
        $wheres = $wheres ? $wheres : "1=1";
        $sql_count = sprintf("select count(*)as count from %s where %s ", $this->_name, $wheres);
        $sql = sprintf("select * from %s where %s limit %d, %d", $this->_name, $wheres, $offset, $limits);

        $count = Db_Adapter_Pdo::fetchAll($sql_count);
        $res = Db_Adapter_Pdo::fetchAll($sql);

        $result = array($count[0], $res);
        return $result;
    }

    public function update_fileds_by_id($id, $data, $fileds) {
        $sql = sprintf("update %s set %s= '%s'  where set_id=%s ", $this->_name, $fileds, $data, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_fileds_bywhere($setid, $where) {
        $sql = sprintf("update %s set %s  where set_id=%s ", $this->_name, $where, $setid);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function get_sets_bywhere($wheres, $fileds = "*") {
        $sql = sprintf("select %s from %s where %s ", $fileds, $this->_name, $wheres);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function delSets($setid) {
        $sql = sprintf("delete  from %s where %s = %s ", $this->_name, $this->_primary, $setid);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function update_addimgs_toset($id, $imgs) {
        $count = count($imgs);
        $imgs = json_encode($imgs);
        $sql = sprintf("update %s set set_images= '%s' ,set_image_count=%d where set_id=%s ", $this->_name, $imgs, $count, $id);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function getImgBySetid($setid) {
        $sql = sprintf("select * from %s where set_id = %d ", $this->_name, $setid);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function update_setStatus($setid, $status) {
        if ($status == 5) {
            $sql = sprintf("update %s set set_status = '%d'  where set_id=%s ", $this->_name, $status, $setid);
        } else {
            $times = time();
            $sql = sprintf("update %s set set_status = '%d',set_publish_time=$times  where set_id=%s ", $this->_name, $status, $setid);
        }
        return Db_Adapter_Pdo::execute($sql);
    }

}
