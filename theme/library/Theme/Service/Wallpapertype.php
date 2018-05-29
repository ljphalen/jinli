<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_Wallpapertype {

    private static function _getDao() {
        return Common::getDao("Theme_Dao_Wallpapertype");
    }

    /**
     * 在线壁纸一级分类标签
     * @param String $where 查询条件;
     *
     */
    public static function getAll($where = "1=1", $orderby = 'order by w_type_id DESC') {
        return self::_getDao()->getAll($where, $orderby);
    }

    public static function delWallpaperType($id) {
        return self::_getDao()->delTargDao($id);
    }

    //分类整理；
    public static function getResAll() {
        $res = self::getAll();
        foreach ($res as $v) {
            $tem[$v['w_type_id']] = $v;
        }
        return $tem;
    }

    /**
     *
     * @param type $targName
     */
    public static function addTarg($arrData) {
        $filedname = "w_type_name,w_type_sort,w_type_time,w_type_image";
        $targName = "'" . $arrData['name'] . "'," . $arrData["sort"] . "," . time() . ",'" . $arrData["img"] . "'";
        return self::_getDao()->addTargDao($filedname, $targName);
    }

    public static function insertTarg($filedname, $targName) {
        return self::_getDao()->addTargDao($filedname, $targName);
    }

    public static function update($setdata, $where) {

        $where = "  w_type_id=$where";
        return self::_getDao()->updateTypeDao($setdata, $where);
    }

}
