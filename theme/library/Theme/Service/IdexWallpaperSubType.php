<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_IdexWallpaperSubType {

    private static function _getDao() {
        return Common::getDao("Theme_Dao_IdxWallpaperSubType");
    }

    /**
     * 二级标签下的壁纸id
     * @param String  $targIds=12,34,56
     */
    public static function getWallpaperId($targIds) {

        if (!$targIds) return 0;

        $where = "wallpaper_type_subid in($targIds)";

        return self::_getDao()->getWallpaperIdDao($where);
    }

    /**
     * 壁纸加二级标签
     * @param type $paperId
     * @param type $targId
     */
    public static function addPaperTargs($paperId, $targId) {


        return self::_getDao()->addPaperTargsDao($paperId, $targId);
    }

    /**
     * 删除壁纸已有的二级标签;
     * @param int $paperId 壁纸id号
     * @param int  $targId  二级标签id号(默认全部);
     */
    public static function delPaperTargs($wid) {
        return self::_getDao()->deleteDao($wid);
    }

    /**
     * 壁纸的标签分类
     * @param type $paperId
     *
     */
    public static function getPaperTargs($paperId) {
        $where = " wallpaper_id = $paperId ";
        return self::_getDao()->getPapaprTargsDao($where);
    }

    public static function getPaperTargsWhereIn($paperIds) {
        $where = " wallpaper_id in ($paperIds) ";

        return self::_getDao()->getPapaprTargsDao($where);
    }

}
