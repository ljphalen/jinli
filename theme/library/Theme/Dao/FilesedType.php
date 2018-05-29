<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_FilesedType extends Common_Dao_Base {

    protected $_name = 'theme_file_sedtype';
    protected $_primary = 'id';

    /**
     * @param array $file_ids
     * @return multitype:
     */
    public function getByFileIds($file_ids) {
        $sql = sprintf('SELECT * FROM %s WHERE file_id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($file_ids));

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getFileByid($typeid, $limit = 12, $page = 1) {
        $num = ($page - 1) * $limit;
        $sql = sprintf('SELECT * FROM %s WHERE type_id = %s  order by id DESC limit %d, %d '
                , $this->_name, $typeid, $num, $limit);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getAllDao() {
        $sql = sprintf('SELECT * FROM %s WHERE 1 order by sort DESC ', $this->_name);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function delTargDao($id) {
        $sql = sprintf("delete from %s where id=%s", $this->_name, $id);
        return Db_Adapter_Pdo::execute($sql);
    }

    /**
     *
     * @param type
     * @param type
     * @return type
     */
    public function setFileTypeDao($filedname, $values) {
        $sql = sprintf("insert into %s (%s)values(%s)", $this->_name, $filedname, $values);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function updatesubtargDao($where, $setData) {
        $sql = sprintf("update %s set  %s where %s", $this->_name, $setData, $where);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function getByTypeIdsDao($typeids) {
        $sql = sprintf("select * from %s where sedtype_id in(%s)", $this->_name, $typeids);
        return Db_Adapter_Pdo::execute($sql);
    }

}
