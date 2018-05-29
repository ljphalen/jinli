<?php

if (!defined('BASE_PATH'))
    exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author CHENGYI
 *
 */
class Client_Service_RankVersion {
    
    const ONLINE_ACTION_RANK = 2;
    const SOARING_RANK = 1;

    public static function getRankDayId($id) {
        $dayInfo = self::getDao()->getBy(array('id' => $id));
        return $dayInfo['current_version_id'];
    }

    /**
     * 
     * @return Client_Dao_RankResult
     */
    private static function getDao() {
        return Common::getDao("Client_Dao_RankVersion");
    }

}
