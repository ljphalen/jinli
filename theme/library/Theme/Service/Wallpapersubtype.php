<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_Wallpapersubtype {

    private static function _getDao() {
        return Common::getDao("Theme_Dao_Wallpapersubtype");
    }

    /**
     * 二级分类标签
     * @param String $where 查询条件;
     *
     */
    public static function getAll($where = "1=1") {
        return self::_getDao()->getAll($where);
    }

    public static function addTarg($targName) {
        $field = "w_subtype_name,w_subtype_time";
        $data = "'$targName'," . time();
        return self::_getDao()->addTargDao($field, $data);
    }

    public static function updatesubtarg($setData, $where) {
        return self::_getDao()->updateTargsDao($setData, $where);
    }

    public static function delsubtarg($id) {
        return self::_getDao()->delTargDao($id);
    }

}
