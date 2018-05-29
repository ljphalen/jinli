<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Theme_Dao_FileTypes
 * @author tiansh
 *
 */
class Theme_Dao_WallFileImage extends Common_Dao_Base {

    protected $_name = 'wallpaper_file_images';
    protected $_primary = 'wallpaper_id';

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public function getByFileIds($file_ids, $order = FALSE) {

        if ($order) {
            $sql = sprintf('SELECT * FROM %s WHERE wallpaper_id in (%s) and wallpaper_online_time < ' . time()
                    . ' ORDER BY FIELD (%s,%s) ', $this->_name, $file_ids, $this->_primary, $file_ids);
        } else {
            $sql = sprintf('SELECT * FROM %s WHERE wallpaper_id in (%s) ', $this->_name, $file_ids);
        }


        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getByFileIdsAdmin($file_ids, $order = FALSE) {

        if ($order) {
            $sql = sprintf('SELECT * FROM %s WHERE wallpaper_id in (%s) '
                    . ' ORDER BY FIELD (%s, %s) ', $this->_name, $file_ids, $this->_primary, $file_ids);
        } else {
            $sql = sprintf('SELECT * FROM %s WHERE wallpaper_id in (%s) ', $this->_name, $file_ids);
        }
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function set_into_data($filds = '', $values = '') {
        $sql = sprintf("insert into %s (%s) values ('%s')", $this->_name, $filds, $values);
        return Db_Adapter_Pdo::execute($sql) ? Db_Adapter_Pdo::getLastInsertId() : FALSE;
    }

    public function addCounts($id, $fileds) {
        $sql = sprintf('UPDATE %s SET %s = %s+1 WHERE %s = %s ', $this->getTableName(), $fileds, $fileds, $this->_primary, $id);

        return Db_Adapter_Pdo::execute($sql, array(), false);
    }

    public function get_bywheres($wheres, $filed = "*") {
        $sql = sprintf("select %s  from %s where %s", $filed, $this->_name, $wheres);
        return Db_Adapter_Pdo::fetchAll($sql);
    }

    public function getAllCount($where = '') {

        $wheres = $where ? $this->mk_sqls($where) : " 1=1 ";

        $sql = sprintf("select count(*)as count from %s where %s", $this->_name, $wheres);
        $res = Db_Adapter_Pdo::fetchAll($sql);

        return $res[0]["count"];
    }

    public function del_wallpaper($id) {
        $sql = sprintf("delete  from %s where %s = %s", $this->_name, $this->_primary, $id);
        return Db_Adapter_Pdo::execute($sql);
    }

    public function updateFiled($values, $filed, $id) {
        $sql = sprintf("update %s set %s = '%s'  where %s=%s ", $this->_name, $filed, $values, $this->_primary, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function updatewheres($wheres, $id) {
        $sql = sprintf("update %s set %s  where %s=%s ", $this->_name, $wheres, $this->_primary, $id);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function update_up_status() {
        $sql = sprintf("update %s set wallpaper_up_status = 1  ", $this->_name);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function updateStatus($status, $wid) {
        $sql = sprintf("update %s set wallpaper_status = '%d'  where wallpaper_id=%s ", $this->_name, $status, $wid);

        return Db_Adapter_Pdo::execute($sql);
    }

    public function getAll($data = array()) {

        $data["fileds"] = $data["fileds"] ? $this->mk_sqls($data["fileds"]) : "*";
        $data["where"] = $data["where"] ? $this->mk_sqls($data["where"]) : " 1=1 ";
        $data["sort"] = $data["sort"] ? $this->mk_sqls($data["sort"]) : $this->_primary;
        $data["sort_val"] = $data["sort_val"] ? $this->mk_sqls($data["sort_val"]) : 'DESC';
        $data["num"] = $data["num"] ? $this->mk_sqls($data["num"]) : "20";

        $sql = sprintf("select %s from %s where %s order by %s %s limit %d,%d"
                , $data["fileds"], $this->_name, $data["where"], $data["sort"]
                , $data["sort_val"], $data["limit"], $data['num']
        );

        $res = Db_Adapter_Pdo::fetchAll($sql);
        $count = $this->getAllCount($data['where']);
        $result = array("data" => $res, "count" => $count);
        return $result;
    }

    public function mk_sqls($str) {

        $str = str_replace("and", "", $str);
        $str = str_replace("execute", "", $str);
        $str = str_replace("update", "", $str);
        $str = str_replace("count", "", $str);
        $str = str_replace("chr", "", $str);
        $str = str_replace("mid", "", $str);
        $str = str_replace("master", "", $str);
        $str = str_replace("truncate", "", $str);
        $str = str_replace("char", "", $str);
        $str = str_replace("declare", "", $str);
        $str = str_replace("select", "", $str);
        $str = str_replace("create", "", $str);
        $str = str_replace("delete", "", $str);
        $str = str_replace("insert", "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("\"", "", $str);
        $str = str_replace(" ", "", $str);
        $str = str_replace("or", "", $str);
        $str = str_replace(" = ", "", $str);
        $str = str_replace("%20", "", $str);
        //echo $str;
        return $str;
    }

}
