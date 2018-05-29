<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gou_Dao_Theme
 * @author rainkid
 *
 */
class Theme_Dao_Subject extends Common_Dao_Base {

    protected $_name = 'theme_subject';
    protected $_primary = 'id';

    public function update($data, $value) {
        if (!$data) return false;
        $sql = sprintf('UPDATE %s SET %s  WHERE %s', $this->_name, $data, $value);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function getSubject_byGroupType($wheres) {
        $sql = sprintf("select * from (select * from %s where %s) as a "
                . "group by type_id", $this->_name, $wheres);


        $res = Db_Adapter_Pdo::fetchAll($sql);

        return $res;
    }

}
