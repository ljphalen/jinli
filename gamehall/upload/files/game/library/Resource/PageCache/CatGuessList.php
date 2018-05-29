<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_PageCache_CatGuessList
 * @author fanch
 *
 */
class Resource_PageCache_CatGuessList{
    const SORTTYPE_HOT = 'hot';
    const SORTTYPE_NEW = 'new';

    /**
     * @param $params
     * @return array
     */
    public static function getPage($params) {
        $guessData = Client_Service_Guess::getGamesByImCrc($params['imcrc']);
        if ($guessData) {
            list($gameIds, $hasNext, $total) = self::getGuessGames($params, $guessData);
        } else {
            list($gameIds, $hasNext, $total) = self::getDefaultGames($params);
        }
        $data = self::getGamesData($gameIds);
        return array($data, $hasNext, $total);
    }

    /**
     * @param $gameIds
     * @return array
     */
    private static function getGamesData($gameIds){
        if(!$gameIds) return array();
        $pageData = Resource_Service_GameListData::getList($gameIds);
        return $pageData;
    }

    /**
     * @param $params
     * @param $guessData
     * @return array
     */
    private static function getGuessGames($params, $guessData) {
        $gameIds = array();
        $total = 0;
        $hasNext = false;
        $searchParams = array('status' => 1);
        $orderParams = self::getOrderParams($params);
        $guessGameIds = explode(',', $guessData['game_ids']);
        $searchParams['id'] = array('IN', $guessGameIds);
        list($total, $games) = Resource_Service_Games::getList($params['page'], $params['perPage'], $searchParams, $orderParams);
        if($games) {
            $games = Common::resetKey($games, 'id');
            $gameIds = array_keys($games);
            $hasNext = (ceil((int) $total / $params['perPage']) - ($params['page'])) > 0 ? true : false;
        }
        return array($gameIds, $hasNext, $total);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getDefaultGames($params) {
        $gameIds = array();
        $total = 0;
        $hasNext = false;
        $orderParams = self::getOrderParams($params);
        $defaultParams = array('game_status' => 1, 'status' => 1);
        list($total, $games) = Client_Service_Game::geGuesstList($params['page'], $params['perPage'], $defaultParams, $orderParams);
        if($games) {
            $games = Common::resetKey($games, 'game_id');
            $gameIds = array_keys($games);
            $hasNext = (ceil((int) $total / $params['perPage']) - ($params['page'])) > 0 ? true : false;
        }
        return array($gameIds, $hasNext, $total);
    }

    /**
     * @param $params
     * @return array
     */
    private static function getOrderParams($params){
        $orderParams = array();
        //最新 按照上线时间倒序
        if($params['sortType'] == self::SORTTYPE_NEW){
            $orderParams = array('online_time'=>'DESC');
        }
        //最热 按照下载总量倒序
        if($params['sortType'] == self::SORTTYPE_HOT){
            $orderParams = array('downloads'=>'DESC','id'=>'DESC');
        }
        return $orderParams;
    }
}