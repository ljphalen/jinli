<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_SubjectFile extends Common_Dao_Base {

    protected $_name = 'theme_subject_file';
    protected $_primary = 'id';

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public function getByFileIds($file_ids) {
        $sql = sprintf('SELECT * FROM %s WHERE file_id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($file_ids));
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function insertThemeidsDao($filed, $val) {
        $sql = sprintf('INSERT  INTO %s (%s) VALUES (%s)', $this->_name, $filed, $val);
        return Db_Adapter_Pdo::execute($sql);
    }

}
