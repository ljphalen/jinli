<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_WallSubject extends Common_Dao_Base {

    protected $_name = 'wallpaper_subject';
    protected $_primary = 'w_subject_id';

    public function getAll($wheres = "", $limits = 15, $pages = 1) {
        $offset = ($pages - 1) * $limits;

        $wheres = $wheres ? $wheres : "1=1 order by $this->_primary DESC";
        $sql_count = sprintf("select count(*) as count from %s where %s ", $this->_name, $wheres);
        $sql = sprintf("select * from %s where %s limit %d, %d", $this->_name, $wheres, $offset, $limits);

        $count = Db_Adapter_Pdo::fetchAll($sql_count);
        $res = Db_Adapter_Pdo::fetchAll($sql);
        $result = array($count[0]["count"], $res);
        return $result;
    }

    public function getsubject_GroupTypeDao($wheres) {
        $sql = sprintf("select * from (select * from %s where %s) as t group by w_subject_type ", $this->_name, $wheres);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getsubject_bywheresDao($wheres) {
        $sql = sprintf("select * from %s where  %s ", $this->_name, $wheres);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getsubject_byid($subjectid) {
        $sql = sprintf("select * from %s where %s = %s ", $this->_name, $this->_primary, $subjectid);


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function insert_into($data = array()) {

        $fileds = $data["fileds"] ? $data['fileds'] : "null";
        $values = $data["values"] ? $data["values"] : "null";
        $sql = sprintf("insert into %s (%s) values ('%s')", $this->_name, $fileds, $values);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_tosubject_img($sid, $data) {
        $sql = sprintf("update %s set w_image= '%s' where %s = %s", $this->_name, $data, $this->_primary, $sid);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_typeId($type_id) {
        $sql = sprintf("update %s set w_subject_type= '%d'+10 where w_subject_type = %d", $this->_name, $type_id, $type_id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_byFileds($data, $where, $true = true) {
        if ($true) {
            $sql = sprintf("update %s set %s  where %s = %d", $this->_name, $data, $this->_primary, $where);
        } else {
            $sql = sprintf("update %s set %s  where %s", $this->_name, $data, $where);
        }


        return Db_Adapter_Pdo::execute($sql);
    }

    public function delSubject($sid) {
        $sql = sprintf("delete from %s where %s = %d", $this->_name, $this->_primary, $sid);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_subjectStatus($sid, $status, $time) {
        if ($time) {
            $sql = sprintf("update %s set w_subject_status = '%d' where w_subject_id = %d", $this->_name, $status, $sid);
        } else {
            $sql = sprintf("update %s set w_subject_status = '%d',w_subject_pushlish_time=%d where w_subject_id = %d", $this->_name, $status, time(), $sid);
        }


        return Db_Adapter_Pdo::execute($sql);
    }

}
