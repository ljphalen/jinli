<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lyd
 *
 */
class Theme_Service_IdxFilesedType extends Common_Service_Base{

    protected static $name = "Theme_Dao_IdxFilesedType";

    public static function getsedTypebyFileid($fid) {
        if (!$fid) return "fid not null";
        return self::_getDao()->getsedTypebyFileidDao($fid);
    }

    public static function getsedTypebyFileid_ToArray($fid) {
        if (!$fid) return "fid not null";
        $res = self::_getDao()->getsedTypebyFileid_WhereINDao($fid);

        if (!$res) return null;
        foreach ($res as $k => $v) {
            $tem[$v['file_id']][] = $v['sedtype_id'];
        }

        return $tem;
    }

    public static function getSedTypeByTypeid($typeid = '') {
        $res = self::_getDao()->getByTypeIdsDao($typeid);
        return $res;
    }

    /**
     *
     * @return Theme_Dao_FileImg
     */
    private static function _getDao() {
        return Common::getDao(self::$name);
    }

    public static function setsubTarg($targid, $fileid) {
        if (!$fileid) return "fid not null";
        if (!is_array($targid)) return "targid must array";
        self::delsubTarg($fileid);


        foreach ($targid as $v) {
            $res = self::_getDao()->setsubTargDao($v, $fileid);
            if (!$res) return "error ";
        }
        return $res;
    }

    public static function delsubTarg($fileid) {
        return self::_getDao()->delelbyFilesedTypeDao($fileid);
    }

}

?>