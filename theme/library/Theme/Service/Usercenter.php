<?php

class Theme_Service_Usercenter extends Common_Service_Base{
    protected static $name = 'Theme_Dao_Usercenter';

    private static function _getDao() {
        return Common::getDao("Theme_Dao_Usercenter");
    }

    public static function setUsername($data, $sqlstr = FALSE) {

        $fileds = implode(",", array_keys($data));
        $values = implode(",", array_values($data));

        return self::_getDao()->setDataDao($fileds, $values, $sqlstr);
    }

    public static function getUserinfo($uid) {

        $where = "user_sys_id = '$uid' limit 1";
        return self::_getDao()->getDataDao($where, "*");
    }

    public static function updatelogtime($uid, $username = '') {
        if ($username) {
            $data = "user_telent_time=" . time() . ",user_rname='$username'";
        } else {
            $data = "user_telent_time=" . time();
        }

        $where = "user_sys_id='$uid'";
        return self::_getDao()->updataDao($data, $where);
    }

}
