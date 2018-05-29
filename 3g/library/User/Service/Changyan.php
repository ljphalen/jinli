<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Changyan {
    /**
     *
     * @return User_Dao_Changyan
     */
    public static function getDao() {
        return Common::getDao("User_Dao_Changyan");
    }
}