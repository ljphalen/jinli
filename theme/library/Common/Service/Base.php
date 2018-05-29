<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
abstract class Common_Service_Base {

    public static $webroot;
    public static $webrootdown;
    public static $downloadroot;

    public static function __inits() {
        self::$webroot = Yaf_Application::app()->getConfig()->fontcroot;
        self::$webrootdown = Yaf_Application::app()->getConfig()->webroot;
        self::$downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    /**
     * beginTransaction
     * @return boolean
     */
    public static function beginTransaction() {
        try {
            return Db_Adapter_Pdo::getAdapter()->beginTransaction();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * rollback
     * @return boolean
     */
    public static function rollBack() {
        return Db_Adapter_Pdo::getAdapter()->rollBack();
    }

    /**
     * commit
     * @return boolean
     */
    public static function commit() {
        return Db_Adapter_Pdo::getAdapter()->commit();
    }

    private static function _getDao() {
        $class = get_called_class();
        $daoName = $class::$name;
        return Common::getDao($daoName);
    }

    public static function query($sql, $params = array()) {
        return self::_getDao()->query($sql, $params);
    }

    public static function fetchAll($sql, $params = array()) {
        return self::_getDao()->fetchAll($sql, $params);
    }

    public static function insert($data) {
        return self::_getDao()->insert($data);
    }

    public static function getLastInsertId() {
        return self::_getDao()->getLastInsertId();
    }

    public static function update($data, $value) {
        return self::_getDao()->update($data, $value);
    }

    public static function get($value) {
        return self::_getDao()->get($value);
    }

    public static function getAll($orderBy = array()) {
        return self::_getDao()->getAll($orderBy);
    }

    public static function getBy($params, array $orderBy = array()) {
        return self::_getDao()->getBy($params, $orderBy);
    }

    public static function getsBy($params, $orderBy = array()) {
        return self::_getDao()->getsBy($params, $orderBy);
    }

    public static function getList($start = 0, $limit = 20, array $params = array(), array $orderBy = array()) {
        return self::_getDao()->getList($start, $limit, $params, $orderBy);
    }

    public static function count($params = array()) {
        return self::_getDao()->count($params);
    }

    public static function searchBy($start, $limit, $sqlWhere = 1, $orderBy = array()) {
        return self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
    }

    public static function searchCount($sqlWhere) {
         return self::_getDao()->searchCount($sqlWhere);
    }

    /*
     * 把key=>val 转成sql返回;
     * key 字段名;
     * val 值;
     *
     */

    protected static function mk_sqlInsterByArray(array $arr) {
        if (!$arr) return 'array is null';
        $filed = implode(",", array_keys($arr));
        $values = "'" . implode("','", array_values($arr)) . "'";

        $res ["fileds"] = $filed;
        $res["values"] = $values;
        return $res;
    }

    /*
     * 把key=>val 转成sql返回;
     * key 字段名;
     * val 值;
     *
     */

    protected static function mk_sqlUpdateByArray(array $arr) {
        if (!$arr) return 'array is null';
        foreach ($arr as $k => $v) {
            $str .= "$k = '$v',";
        }
        $res = substr($str, 0, strlen($str) - 1);
        return $res;
    }

    /**
     * 壁纸主题;
     * @param type $subject
     * @return int
     */
    public static function mk_wallsubject_data($subject) {

        self::__inits();
        foreach ($subject as $key => $val) {
            $result[$key]["sid"] = $val["w_subject_id"];
            $result[$key]["last_update_time"] = $val["w_subject_pushlish_time"];
            $result[$key]["sort_time"] = $val["w_subject_pushlish_time"];
            $result[$key]["img"] = self::$webroot . '/attachs/theme' . $val["w_image_face"];
            $result[$key]["title"] = $val["w_subject_name"];
            $result[$key]["type_id"] = $val["type_id"];
            $result[$key]["descrip"] = $val["w_subject_conn"];
            $result[$key]["type"] = $val["w_subject_type"];
            if (!$val["w_subject_sub_type"]) $val["w_subject_sub_type"] ++;
            $result[$key]["catagory"] = $val["w_subject_sub_type"];
            $result[$key]["theme"] = 9;
        }

        return $result;
    }

}
