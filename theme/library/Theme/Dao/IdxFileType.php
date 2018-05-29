<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_IdxFileType extends Common_Dao_Base {

    protected $_name = 'idx_file_type';
    protected $_primary = 'id';

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public function getByFileIdsDao($file_ids) {
        $sql = sprintf('SELECT * FROM %s WHERE file_id in %s ', $this->_name, Db_Adapter_Pdo::quoteArray($file_ids));

        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getFileByidDao($typeid, $limit = 12, $page = 1) {
        $num = ($page - 1) * $limit;
        $sql = sprintf('SELECT * FROM %s WHERE type_id = %s  order by id DESC limit %d, %d '
                , $this->_name, $typeid, $num, $limit);



        return Db_Adapter_Pdo::fetchAll($sql);
    }

    /**
     *
     * @param type $id 分类id 可是为array(1,22,34)或者int 20;
     * @param type $fid    主题号
     * @return type
     */
    public function setFileTypeDao($id, $fid) {
        if (is_array($id) && count($id) > 1) {
            foreach ($id as $k => $v) {
                if ($k == count($id) - 1) $string .= "select $fid, $v ";
                else $string .= "select $fid, $v union ";
            }
            $sql = sprintf(' insert into %s (file_id,type_id) %s ', $this->_name, $string);
        } else {
            $sql = sprintf('insert into %s (file_id,type_id) values(%d,%d)', $this->_name, $fid, $id);
        }

        return Db_Adapter_Pdo::execute($sql);
    }

}
