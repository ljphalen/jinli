<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Manager_RecommendList {

    const BANNER = 'banner';
    const TEXT = 'text';
    const DAILY = 'daily';
    const RECOMMEND = 'recommend';

    const RECOMMEND_INFO = 'info';
    const RECOMMEND_GAMES = 'games';
    const RECOMMEND_IMAGE = 'image';
    
    
    public static function getRecommendBanner($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::BANNER);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getCache($api, $args);
        return $recommendList;
    }
    
    public static function getRecommendText($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::TEXT);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getCache($api, $args);
        return $recommendList;
    }
    
    public static function getRecommendDaily($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::DAILY);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getCache($api, $args);
        return $recommendList;
    }
    
    public static function getRecommendList($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::RECOMMEND);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getCache($api, $args);
        if($recommendList === false) {
            $recommendList = array();
        }
        return $recommendList;
    }
    
    public static function updateRecommendBanner($dayId, $userId, $bannerList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::BANNER);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, $bannerList);
    }
    
    public static function updateRecommendText($dayId, $userId, $text) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::TEXT);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, $text);
    }
    
    public static function updateRecommendDaily($dayId, $userId, $daily) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::DAILY);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, $daily);
    }
    
    public static function updateRecommendList($dayId, $userId, $recommendList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::RECOMMEND);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, $recommendList);
    }
    
    private static function updateRecommendData($api, $args, $data) {
        Util_CacheKey::updateCache($api, $args, $data, 86400);
    }
    
    
    
    public static function deleteRecommendBanner($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::BANNER);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function deleteRecommendText($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::TEXT);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function deleteRecommendDaily($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::DAILY);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function deleteRecommendList($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::RECOMMEND, Util_CacheKey::RECOMMEND_INFO.self::RECOMMEND);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function loadRecommendList($dayId, $userId) {
        //从数据库同步数据到缓存
        $searchParams = array('day_id' => $dayId);
        $bannerList = Game_Service_RecommendBanner::getRecommendBannersBy($searchParams);
        
        $searchParams = array('day_id' => $dayId);
        $text = Game_Service_RecommendText::getRecommendTextBy($searchParams);
        
        $daily = Game_Service_RecommendDay::getRecommendByDayId($dayId);
        
        $recommendListInfo = array();
        $recommendList = Game_Service_RecommendNew::getsBy(array('day_id' => $dayId));
        foreach ($recommendList as $key => $recommend) {
            $info = array(self::RECOMMEND_INFO => $recommend);
            if($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $games = Game_Service_RecommendGames::getGames($recommend["id"]);
                if(! $games) {
                    $games = array();
                }
                $info[self::RECOMMEND_GAMES] = $games;
            }else{
                $searchParams = array('recommend_id' => $recommend["id"]);
                $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
                $info[self::RECOMMEND_IMAGE] = $img;
            }
            $recommendListInfo[] = $info;
        }
        
        self::updateRecommendBanner($dayId, $userId, $bannerList);
        self::updateRecommendDaily($dayId, $userId, $daily);
        self::updateRecommendList($dayId, $userId, $recommendListInfo);
        self::updateRecommendText($dayId, $userId, $text);
    }
    
    public static function initMonthList($list, $beginTime) {
        $dataList = array();
        $list = Common::resetKey($list, 'day_id');
        $days = date('t', strtotime("+1 month -1 day", $beginTime));
        for ($i = 0; $i < $days; $i++) {
            $time = strtotime("+{$i} day", $beginTime);
            if (! isset($list[$time])) {
                $dataList[$time] = array();
            }else{
                $dataList[$time] = $list[$time];
            }
        }
        return $dataList;
    }
    
    public static function addLog($day_id, $userId) {
        $log = array(
            'day_id' => $day_id,
            'uid' => $userId,
            'create_time' => Common::getTime(),
        );
        Game_Service_RecommendEditLog::addRecommendEditLog($log);
    }

    /**取需要更新的数据*/
    public static function getUpdateParams($postData, $dbData) {
        $updateParams = array();
        foreach ($postData as $key => $value) {
            if(! isset($dbData[$key])) {
                continue;
            }
            if ($value == $dbData[$key]) {
                continue;
            }
            $updateParams[$key] = $value;
        }
        return $updateParams;
    }

    public static function getNewData($postData, $oldData) {
        foreach ($postData as $key => $value) {
            if ($value == $oldData[$key]) {
                continue;
            }
            $oldData[$key] = $value;
        }
        return $oldData;
    }
    
    public static function initBannerInfo($bannerList) {
        $bannerInfo = array();
        $sort = 4;
        foreach ($bannerList as $key => $banner) {
            $tmp = array();
            $tmp['sort'] = $sort--;
            if($banner) {
                $tmp['id'] = $key+1;
                $tmp['name'] = $banner['title'];
                $tmp['href'] = Common::getAttachPath(). $banner['img1'];
            }
            $bannerInfo[] = $tmp;
        }
        return $bannerInfo;
    }
    
    public static function initTextInfo($text) {
        $notice = array(
            'typeName' => "文字公告",
            'id' => $text['id'],
            'name' => $text['title'],
            'type' => Game_Service_Util_Link::$linkType[$text['link_type']],
            'linkId' => $text['link'],
            'info' => $text['title'],
        );
        return $notice;
    }
    
    public static function initDailyInfo($daily) {
        $dailyInfo = array(
            'typeName' => "每日一荐",
        );
        if(! $daily) {
            return $dailyInfo;
        }
        $game = Resource_Service_GameData::getGameAllInfo($daily['game_id']);
        $dailyInfo['id'] = $daily['id'];
        $dailyInfo['name'] = $daily['title'];
        $dailyInfo['info'] = $daily['content'];
        $dailyInfo['gameId'] = $daily['game_id'];
        $dailyInfo['size'] = $game['size'];
        $dailyInfo['type'] = $game['category_title'];
        $dailyInfo['href'] = $game['img'];
        return $dailyInfo;
    }
    
    /**设置游戏名称*/
    public static function initRecommend($recommendList) {
        $recommendData = array();
        foreach ($recommendList as $key => $info) {
            $recommend = $info[self::RECOMMEND_INFO];
            $tmp = array(
                'id' => $key+1, 'name' => $recommend['title'],
                'typeName' => Game_Service_RecommendNew::$rec_type[$recommend['rec_type']],
            );
            if($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $tmp['list'] = array();
                $games = $info[self::RECOMMEND_GAMES];
                $index=0;
                $gameName = "";
                foreach ($games as $game) {
                    if($gameName) $gameName .= ", ";
                    if($index < Game_Service_RecommendNew::SHOW_NUM) {
                        $tmp['list'][] = self::gameInfo($game['game_id']);
                        $index++;
                    }
                    $gameInfo = Resource_Service_GameData::getBasicInfo($game['game_id']);
                    $gameName .= $gameInfo['name'];
                }
                $tmp['names'] = $gameName;
            }else{
                $searchParams = array('recommend_id' => $recommend["id"]);
                $img = $info[self::RECOMMEND_IMAGE];
                if($img) {
                    $tmp['href'] = Common::getAttachPath().$img['img'];
                    $tmp['linkId'] = $img['link'];
                    $tmp['typeName'] = $tmp['typeName'] . '-' . Game_Service_Util_Link::$linkType[$img['link_type']];
                }
            }
            $recommendData[] = $tmp;
        }
        return $recommendData;
    }
    
    private static function gameInfo($gameId) {
        $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
        $gameData = array(
            'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
            'size' => $gameInfo['size'],
            'type' => $gameInfo['category_title'],
            'href' => $gameInfo['img'],
        );
        $extra = Resource_Service_GameData::getExtraInfo($gameId);
        if($extra['freedl']) {
            $gameData['butType'] = "download-prize";
            $gameData['butName'] = "免流量";
        }else if($extra['reward']) {
            $gameData['butType'] = "download-prize";
            $gameData['butName'] = "有奖下载";
        }else{
            $gameData['butType'] = "download";
            $gameData['butName'] = "下载";
        }
        return $gameData;
    }
    
    /**游戏相关信息*/
    public static function getGameInfo($gameId) {
        $info = array();
        $game = Resource_Service_GameData::getGameAllInfo($gameId);
        $info['gameName'] = $game['name'];
        $info['gameCategory'] = $game['category_title'];
        $info['gameIcon'] = $game['img'];
        $info['gameSize'] = $game['size'];
        $info['gameVersion'] = $game['version'];
        return $info;
    }
    
}