<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Theme_Dao_Livewallpapersubject extends Common_Dao_Base {

    protected $_name = 'livewallpaper_subject';
    protected $_primary = 'id';

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

}