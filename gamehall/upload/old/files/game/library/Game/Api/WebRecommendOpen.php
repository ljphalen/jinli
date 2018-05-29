<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开服信息
 * Game_Api_WebRecommendOpen
 * @author wupeng
 */
class Game_Api_WebRecommendOpen {
    
	const LIST_PER_PAGE = 10;
	const DAY_SECONDS = 86400;
	
	private static $labelName = array(
	    -2 => '历史',
	    -1 => '昨天',
	    0 => '今天',
	    1 => '明天',
	    2 => '未来三天',
	);

    public static function getOpenListByPage($page) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_OPENLIST);
        $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false) {
            $data = self::getOpenListRedisByPage($page);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }

    private static function getOpenListRedisByPage($page) {
        $open = Game_Cache_GameOpen::getInstance();
        $pageInfo = $open->getClientPageInfo();
        if($pageInfo === false) {
            $data = self::getClientData();
            $open->updateClientPageList($data);
            $pageInfo = $open->getClientPageInfo();
        }
        $pageSize = $pageInfo[Util_Api_ListPage::PAGE_SIZE];
        if($page > $pageSize) {
            $pageData = array();
        }else{
            $pageData = $open->getClientPageList($page);
            $pageData = self::reInitGameInfo($pageData);
        }
        $hasnext = $pageSize > $page ? true : false;
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    private static function reInitGameInfo($pageData) {
        $gameIds = array();
        foreach ($pageData as $gk => $gameItem) {
            $gameIds[] = $gameItem['gameId'];
        }
        $gameIds = array_unique($gameIds);
        $list = Resource_Service_GameListData::getList($gameIds);
        $gameList = Resource_Service_GameListFormat::output($list);
        $gameList = Common::resetKey($gameList, 'gameid');
        
        foreach ($pageData as $gk => $gameItem) {
            $extalInfo = $gameList[$gameItem['gameId']];
            if(! $extalInfo) {
                continue;
            }
            $gameItem = array_merge($gameItem, $extalInfo);
            $pageData[$gk] = $gameItem;
        }
        return $pageData;
    }
    
    private static function getClientData() {
        $pageData = array();
        $openList = self::getOpenList();
        $total = count($openList);
        foreach ($openList as $open) {
            $pageData[] = self::makeClientOpenData($open);
        }
        return $pageData;
    }
    
    private static function makeClientOpenData($open) {
        $gameData = array('gameId' => $open['game_id']);
        $dateTime = date('n-j H:i', $open['open_time']);
        list($date, $time) = split(' ', $dateTime, 2);
	    $openDate = Util_TimeConvert::floor($open['open_time'], Util_TimeConvert::RADIX_DAY);
	    $curDate = Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY);
        $label = intval(($openDate - $curDate) / self::DAY_SECONDS);
        if($label > 2) {
            $label = 2;
        }elseif($label < -2) {
            $label = -2;
        }
        $gameData['date'] = $date;
        $gameData['time'] = $time;
        $gameData['areaName'] = $open['server_name'];
        $gameData['label'] = self::$labelName[$label];
        return $gameData;
    }
    
	private static function getOpenList($reload = false) {
        $open = Game_Cache_GameOpen::getInstance();
        if($reload) {
            $openList=false;
        }else{
            $openList = $open->getDataList();
        }
	    if($openList === false) {
            $openList = self::getOpenListFromDB();
	        $open->updateDataList($openList);
	        $updateGameListFlag = true;
	    }
	    $sortCriteria = array(
	        'open_weight' => array(SORT_ASC, SORT_NUMERIC),
	        'game_id' => array(SORT_DESC, SORT_NUMERIC)
	    );
	    $openList = Util_ArraySort::multiSort($openList, $sortCriteria);
	    if($updateGameListFlag) {//保存游戏的开服信息
	        $gameOpenInfo = array();
	        foreach ($openList as $openInfo) {
	            $gameOpenInfo[$openInfo['game_id']][] = $openInfo['id'];
	        }
	        $open->updateGameOpenList($gameOpenInfo);
	    }
	    return $openList;
	}
	
	private static function getOpenListFromDB() {
	    $curTime = time();
	    $curDate = Util_TimeConvert::floor($curTime, Util_TimeConvert::RADIX_DAY);
	    $startTime = Util_TimeConvert::addDays(-4, $curDate);
	    $endTime = Util_TimeConvert::addDays(5, $curDate);
	    $searchParams['open_time'] = array(
	        array('>=', $startTime),
	        array('<=', $endTime)
	    );
	    $searchParams['status'] = Game_Service_GameOpen::STATUS_OPEN;
	    $searchParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
	    $openList = Game_Service_GameOpen::getGameOpenListBy($searchParams);
	    $dayOpenList = array();
	    foreach ($openList as $key => $open) {
	        $gameId = $open['game_id'];
	        $openDate = strtotime(date('Y-m-d', $open['open_time']));
	        $gameWeight = abs($open['open_time'] - $curTime);
	        $lastGame = $dayOpenList[$openDate][$gameId];
	        if($lastGame && $lastGame['game_weight'] < $gameWeight) {
	            continue;
	        }
	        if($openDate < $curDate) {
	            $weight = $curDate - $open['open_time'] + 5 * 86400;
	        }else{
	            $weight = $open['open_time'] - $curDate;
	        }
// 	        echo $open['game_id'] . '   ' . date('Y-m-d H:i', $open['open_time']) . '    ' . $weight . '     ' . $gameWeight . '   ' .$open['server_name'] . "<BR>";

	        $open['open_weight'] = $weight;
	        $open['game_weight'] = $gameWeight;
	        $dayOpenList[$openDate][$gameId] = $open;
	    }
	    $openList = array();
	    foreach ($dayOpenList as $gameOpen) {
	        foreach ($gameOpen as $open) {
	            unset($open['game_weight']);
	            $openList[] = $open;
	        }
	    }
	    return $openList;
	}
	
	public static function updateOpenListCache() {
        $open = Game_Cache_GameOpen::getInstance();
        self::getOpenList(true);

        $data = self::getClientData();
        $open->updateClientPageList($data);
	}
	
	public static function getNewOpen($gameId) {
        $open = Game_Cache_GameOpen::getInstance();
        $list = $open->getGameOpenList($gameId);
        if($list) {
            return $open->getOpenInfo($list[0]);
        }
	    return null;
	}
	
}
