<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lyd
 *
 */
class Theme_Service_FilesedType extends Common_Service_Base {
    protected static $name = 'Theme_Dao_FilesedType';
    
    public static function getAll() {
        return self::_getDao()->getAllDao();
    }

    public static function setType($filed, $val) {
        return self::_getDao()->setFileTypeDao($filed, $val);
    }

    public static function updatesubtarg($where, $setdata) {
        return self::_getDao()->updatesubtargDao($where, $setdata);
    }

    public static function delsubTarg($id) {
        return self::_getDao()->delTargDao($id);
    }

    /**
     *
     * @return Theme_Dao_FileImg
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_FilesedType");
    }

}

?>