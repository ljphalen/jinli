<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Manager_WebRecommendList {

    const BANNER = 'banner';
    const RECOMMEND = 'recommend';

    const WEBGAME_INFO = 'info';
    const WEBGAME_GAMES = 'games';
    const WEBGAME_IMAGE = 'image';
    const WEBGAME_OPEN = 'open';
    const WEBGAME_GIFT = 'gift';
    
    public static function getRecommendBanner($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_INFO);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getHCache($api, $args, self::BANNER);
        return $recommendList;
    }
    
    public static function getRecommendList($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_INFO);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getHCache($api, $args, self::RECOMMEND);
        if($recommendList === false) {
            $recommendList = array();
        }
        return $recommendList;
    }
    
    public static function updateRecommendBanner($dayId, $userId, $bannerList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_INFO);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, self::BANNER, $bannerList);
    }
    
    public static function updateRecommendList($dayId, $userId, $recommendList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_INFO);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, self::RECOMMEND, $recommendList);
    }
    
    private static function updateRecommendData($api, $args, $key, $data) {
        Util_CacheKey::updateHCache($api, $args, $key, $data, 86400);
    }
    
    public static function deleteRecommend($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_INFO);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function loadRecommendList($dayId, $userId) {
        //从数据库同步数据到缓存
        $bannerList = Game_Service_GameWebRecBanner::getGameWebRecBannerListBy(array('day_id' => $dayId));
        self::updateRecommendBanner($dayId, $userId, $bannerList);
        $recommendListInfo = array();
        $recommendList = Game_Service_GameWebRec::getGameWebRecListBy(array('day_id' => $dayId));
        foreach ($recommendList as $key => $recommend) {
            $recommendId = $recommend["id"];
            $searchParams = array('recommend_id' => $recommendId);
            $info = array(self::WEBGAME_INFO => $recommend);
            if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_LIST) {
                $games = Game_Service_GameWebRecGames::getGameWebRecGamesListBy($searchParams);
                $info[self::WEBGAME_GAMES] = $games ? $games : array();
            }else if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $img = Game_Service_GameWebRecImgs::getGameWebRecImgsBy($searchParams);
                $info[self::WEBGAME_IMAGE] = $img;
            }else if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
                $openList = Game_Service_GameWebRecOpen::getGameWebRecOpenListBy($searchParams);
                $info[self::WEBGAME_OPEN] = $openList ? $openList : array();
            }
            $recommendListInfo[] = $info;
        }
        self::updateRecommendList($dayId, $userId, $recommendListInfo);
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
        $data = array(
            'day_id' => $day_id,
            'uid' => $userId,
            'create_time' => Common::getTime(),
        );
        Game_Service_GameWebRecEditLog::addGameWebRecEditLog($data);
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
                $tmp['id'] = $key;
                $tmp['name'] = $banner['title'];
                $tmp['status'] = $banner['status'];
                $tmp['imgUrl'] = Common::getAttachPath(). $banner['img'];
            }
            $bannerInfo[] = $tmp;
        }
        return $bannerInfo;
    }
    
    public static function initRecommend($recommendList) {
        $recommendData = array();
        foreach ($recommendList as $key => $info) {
            $recommend = $info[self::WEBGAME_INFO];
            $tmp = array(
                'id' => $key, 'name' => $recommend['title'], 'adId' => $recommend['id'],
                'recType' => Game_Service_GameWebRec::$rec_type[$recommend['rec_type']],
            );
            $tmp['status'] = $recommend['status'];
            if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_LIST) {
                self::initListData($recommend, $info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                self::initImgData($info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
                self::initOpenData($info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_GIFT) {
                self::initGiftData($recommend, $tmp);
            }
            $recommendData[] = $tmp;
        }
        return $recommendData;
    }
    
    private static function initListData($recommend, $info, &$tmp) {
        $tmp['type'] = 'gameList' . ($recommend['template'] + 1);
        $tmp['name'] = $recommend['title'];
        $tmp['template'] = Game_Service_GameWebRec::$list_template[$recommend['template']];
        $tmp['list'] = array();
        $games = $info[self::WEBGAME_GAMES];
        $gameName = "";
        $counts = Game_Service_GameWebRec::$list_template_counts[$recommend['template']];
        foreach ($games as $game) {
            if($gameName) $gameName .= ", ";
            if($counts>0) {
                $tmp['list'][] = self::gameInfo($game['game_id']);
                $counts--;
            }
            $gameInfo = Resource_Service_GameData::getBasicInfo($game['game_id']);
            $gameName .= $gameInfo['name'];
        }
        $tmp['names'] = $gameName;
    }
    
    private static function initImgData($info, &$tmp) {
        $img = $info[self::WEBGAME_IMAGE];
        if($img) {
            $tmp['imgUrl'] = Common::getAttachPath().$img['img'];
            $tmp['linkId'] = $img['link'];
            $tmp['typeName'] = Game_Service_Util_Link::$linkType[$img['link_type']];
        }
        $tmp['type'] = 'image';
    }
    
    private static function initOpenData($info, &$tmp) {
        unset($tmp['recType']);
        $tmp['type'] = 'openList';
        $tmp['name'] = '开服信息';
        $tmp['list'] = array();
        $counts = 0;
        $gameName = "";
        $openList = $info[self::WEBGAME_OPEN];
        foreach ($openList as $open) {
            if($gameName) $gameName .= ", ";
            $openInfo = Game_Service_GameOpen::getGameOpen($open['open_id']);
            $game = Resource_Service_GameData::getBasicInfo($openInfo['game_id']);
            $gameName .= $game['name'];
            if($counts >= 5) {
                continue;
            }
            $counts++;
            $date = date('n-j', $openInfo['open_time']);
            $type = Game_Service_GameOpen::$open_type[$openInfo['open_type']];
            $extalInfo = Resource_Service_GameData::getExtraInfo($openInfo['game_id']);
            $name = mb_strlen($game['name'], 'utf-8') > 6 ? mb_substr($game['name'], 0, 6, 'utf-8') : $game['name'];
            $tmpGift = array(
                'name' => $name, 'type' => $type, 'date' => $date,
                'package' => $extalInfo['attach'] == 1 ? '有' : '无'
            );
            $tmp['list'][] = $tmpGift;
        }
        $tmp['num'] = count($openList);
        $tmp['names'] = $gameName;
    }
    
    private static function initGiftData($recommend, &$tmp) {
        unset($tmp['recType']);
        $tmp['name'] = '热门礼包';
        $tmp['type'] = 'package';
        $tmp['list'] = array();
        $dayId = $recommend['day_id'];
        $hotGiftList = self::getGiftList($dayId);
        $counts = 0;
        $gameName = "";
        foreach ($hotGiftList as $gift) {
            if($gameName) $gameName .= ", ";
            $game = Resource_Service_GameData::getBasicInfo($gift['game_id']);
            if($counts < 6) {
                $name = mb_strlen($game['name'], 'utf-8') > 6 ? mb_substr($game['name'], 0, 6, 'utf-8') : $game['name'];
                $counts++;
                $tmpGift = array('name' => $name, 'imgUrl' => $game['img'], 'info' => $gift['gift_name']);
                $tmp['list'][] = $tmpGift;
            }
            $gameName .= $gift['gift_name'];
        }
        $tmp['names'] = $gameName;
    }
    
    private static function gameInfo($gameId) {
        $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
        $gameData = array(
            'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
            'size' => $gameInfo['size'],
            'type' => mb_strlen($gameInfo['category_title'], 'utf-8') > 4 ? mb_substr($gameInfo['category_title'], 0, 4, 'utf-8') : $gameInfo['category_title'],
            'imgUrl' => $gameInfo['img'],
            'web_star' => $gameInfo['web_star'],
            'info' => mb_strlen($gameInfo['resume'], 'utf-8') > 10 ? mb_substr($gameInfo['resume'], 0, 10, 'utf-8') : $gameInfo['resume'],
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
        $info['gameId'] = $gameId;
        $info['gameName'] = $game['name'];
        $info['gameCategory'] = $game['category_title'];
        $info['gameIcon'] = $game['img'];
        $info['gameSize'] = $game['size'];
        $info['gameVersion'] = $game['version'];
        return $info;
    }

	public static function getGiftList($dayId) {
        $search['effect_start_time'] = array('<=', $dayId);
        $search['effect_end_time'] = array('>=', $dayId);
	    $search['status'] = Client_Service_Gift::GIFT_STATE_OPENED;
        $search['game_status']  = Resource_Service_Games::STATE_ONLINE;
	    $hotGiftList = Client_Service_GiftHot::getListBy($search);
	    return $hotGiftList;
	}
	
	public static function getGiftPageList($page, $limit, $dayId) {
        $search['effect_start_time'] = array('<=', $dayId);
        $search['effect_end_time'] = array('>=', $dayId);
	    $search['status'] = Client_Service_Gift::GIFT_STATE_OPENED;
        $search['game_status']  = Resource_Service_Games::STATE_ONLINE;
        return Client_Service_GiftHot::getList($page, $limit, $search);
	}
	
}