<?php

class Theme_Dao_IdxFilesedType extends Common_Dao_Base {

    protected $_name = 'idx_file_sedtype';
    protected $_primary = 'id';

    public function getsedTypebyFileidDao($fileid) {
        $sql = sprintf('SELECT * FROM %s WHERE file_id = %s ', $this->_name, $fileid);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getsedTypebyFileid_WhereINDao($fileid) {
        $sql = sprintf('SELECT * FROM %s WHERE file_id in (%s) ', $this->_name, $fileid);

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getByTypeIdsDao($type_ids) {
        $sql = sprintf('SELECT * FROM %s WHERE sedtype_id in (%s) ', $this->_name, $type_ids);


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function setsubTargDao($targid, $fileid) {
        $sql = sprintf('insert into %s (file_id,sedtype_id) values(%d,%d)', $this->_name, $fileid, $targid);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function delelbyFilesedTypeDao($fileid) {
        $sql = sprintf('DELETE FROM %s WHERE %s = %d', $this->getTableName(), "file_id", intval($fileid));
        return Db_Adapter_Pdo::execute($sql);
    }

}
