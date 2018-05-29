<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 主题搜索热词;
 * @author Lyd
 *
 */
class Theme_Service_Searchwords {

    public static function getlistWords($where, $fileds = "*") {
        $where = $where ? $where : "1=1";
        $res = self::_getDao()->getDataDao($where, $fileds);
        return $res;
    }

    public static function addWords($data) {
        $fileds = implode(",", array_keys($data));

        $vales = "'" . implode("','", array_values($data)) . "'";

        return self::_getDao()->setDataDao($fileds, $vales);
    }

    //跟新热词;
    public static function editWords($id, $words) {
        $where = "id=$id";
        $words = "words='$words'";
        return self::_getDao()->updataDao($words, $where, true);
    }

    //跟新排序
    public static function editsort($id, $sort) {
        $where = "id=$id";
        $sort = "sort='$sort'";
        return self::_getDao()->updataDao($sort, $where, true);
    }

    public static function delWords($id) {


        return self::_getDao()->delDao($id, true);
    }

    private static function _getDao() {
        return Common::getDao("Theme_Dao_Searchwords");
    }

}
