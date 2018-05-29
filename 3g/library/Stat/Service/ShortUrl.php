<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Stat_Service_ShortUrl {

    public static function get($id) {
        return self::_getDao()->get($id);
    }

    /**
     * @return Stat_Dao_ShortUrl
     */
    private static function _getDao() {
        return Common::getDao('Stat_Dao_ShortUrl');
    }
}