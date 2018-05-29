<?php

if (!defined('BASE_PATH'))
    exit('Access Denied!');

/*
 * CHENGYI
 */
class Game_Service_H5HomeDataHandle {

    const CACHE_SET_IN_MAIN = 1;
    const CACHE_SET_IN_TMP = 0;

    private static function getTableNameConfig($table) {
        $_tmpTableName = array(
            'banner' => 'game_h5_recommend_banner',
            'games' => 'game_h5_recommend_games',
            'hdnew' => 'game_h5_recommend_hdnew',
            'imgs' => 'game_h5_recommend_imgs',
            'rank' => 'game_h5_recommend_rank',
            'list' => 'game_h5_recommend_list',
            'main' => 'game_h5_recommend',
        );
        return $_tmpTableName[$table];
    }

    private static function getTableName($name) {
        $tableName = self::getTableNameConfig($name);
        return self::getDao()->changeTableName($tableName);
    }

    public static function getRecommendBannersBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
        return self::getTableName('banner')->getsBy($searchParams, $sortParams);
    }

    public static function getRecommendListGetsBy($params, $orderBy = array('sort' => 'DESC', 'id' => 'ASC')) {
        if (!is_array($params))
            return false;
        return self::getTableName('main')->getsBy($params, $orderBy);
    }

    public static function getGameList($page = 1, $limit = 10, $params = array()) {
        if ($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;
        $result = self::getTableName('games')->getList($start, $limit, $params, array('sort' => 'desc', 'game_id' => "asc"));
        $total = self::getTableName('games')->count($params);
        return array($total, $result);
    }

    public static function getRankByRecommendId($id) {
        if (!$id)
            return null;
        $keyParams = array('recommend_id' => $id);
        return self::getTableName('rank')->getBy($keyParams);
    }

    public static function getNewsByRecommendId($recommendid) {
        return self::getTableName('hdnew')->getList(0, 5, array('recommend_id' => $recommendid));
    }

    public static function getRecommendImgsBy($searchParams, $sortParams = array()) {
        return self::getTableName('imgs')->getBy($searchParams, $sortParams);
    }

    public static function getGames($id) {
        return self::getTableName('games')->getsBy(array("recommend_id" => $id), array('sort' => 'desc', 'game_id' => "asc"));
    }

    private static function getDao() {
        return Common::getDao("Game_Dao_H5HomeAutoSave");
    }

}
