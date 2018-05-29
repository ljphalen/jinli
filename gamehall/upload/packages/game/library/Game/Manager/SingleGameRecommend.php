<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Manager_SingleGameRecommend {

    const BANNER = 'banner';
    const RECOMMEND = 'recommend';

    const SINGLEGAME_INFO = 'info';
    const SINGLEGAME_GAMES = 'games';
    const SINGLEGAME_IMAGE = 'image';
    const SINGLEGAME_ALONE = 'alone';
    const SINGLEGAME_GIFT = 'gift';

    public static function getRecommendBanner($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_INFO);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getHCache($api, $args, self::BANNER);
        return $recommendList;
    }
    
    public static function getRecommendList($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_INFO);
        $args = array($dayId, $userId);
        $recommendList = Util_CacheKey::getHCache($api, $args, self::RECOMMEND);
        if($recommendList === false) {
            $recommendList = array();
        }
        return $recommendList;
    }
    
    public static function updateRecommendBanner($dayId, $userId, $bannerList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_INFO);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, self::BANNER, $bannerList);
    }
    
    public static function updateRecommendList($dayId, $userId, $recommendList) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_INFO);
        $args = array($dayId, $userId);
        self::updateRecommendData($api, $args, self::RECOMMEND, $recommendList);
    }
    
    private static function updateRecommendData($api, $args, $key, $data) {
        Util_CacheKey::updateHCache($api, $args, $key, $data, 86400);
    }
    
    public static function deleteRecommend($dayId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_INFO);
        $args = array($dayId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }
    
    public static function loadRecommendList($dayId, $userId) {
        //从数据库同步数据到缓存
        $bannerList = Game_Service_SingleBanner::getSingleBannerListBy(array('day_id' => $dayId));
        self::updateRecommendBanner($dayId, $userId, $bannerList);
        $recommendListInfo = array();
        $recommendList = Game_Service_SingleList::getSingleListListBy(array('day_id' => $dayId));
        foreach ($recommendList as $key => $recommend) {
            $recommendId = $recommend["id"];
            $searchParams = array('recommend_id' => $recommendId);
            $info = array(self::SINGLEGAME_INFO => $recommend);
            if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_LIST) {
                $games = Game_Service_SingleListGames::getSingleListGamesListBy($searchParams);
                $info[self::SINGLEGAME_GAMES] = $games ? $games : array();
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                $img = Game_Service_SingleListImgs::getSingleListImgsBy($searchParams);
                $info[self::SINGLEGAME_IMAGE] = $img;
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_GIFT) {
                $giftList = Game_Service_SingleListGift::getSingleListGiftListBy($searchParams);
                $info[self::SINGLEGAME_GIFT] = $giftList;
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                $alone = Game_Service_SingleListAlone::getSingleListAloneBy($searchParams);
                $info[self::SINGLEGAME_ALONE] = $alone;
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
        Game_Service_SingleEditLog::addSingleEditLog($data);
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
                $gameId = 0;
                if($banner['link_type'] == Game_Service_Util_Link::LINK_CONTENT) {
                    $gameId = $banner['link'];
                }elseif($banner['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY) {                    
                    $hd = Client_Service_Hd::getHd($banner['link']);                    
                    $gameId = $hd['game_id'];                    
                }
                if($gameId) {
                    $game = Resource_Service_GameData::getGameAllInfo($gameId);
                    $tmp['href'] = $game['img'];
                }
            }
            $bannerInfo[] = $tmp;
        }
        return $bannerInfo;
    }
    
    public static function initRecommend($recommendList) {
        $recommendData = array();
        foreach ($recommendList as $key => $info) {
            $recommend = $info[self::SINGLEGAME_INFO];
            $tmp = array(
                'id' => $key, 'name' => $recommend['title'], 'adId' => $recommend['id'],
                'recType' => Game_Service_SingleList::$rec_type[$recommend['rec_type']],
            );
            $tmp['status'] = $recommend['status'];
            if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_LIST) {
                self::initListData($recommend, $info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                self::initImgData($info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                self::initAloneData($info, $tmp);
            }else if($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_GIFT) {
                self::initGiftData($info, $tmp);
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
        $games = $info[self::SINGLEGAME_GAMES];
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
        $img = $info[self::SINGLEGAME_IMAGE];
        if($img) {
            $tmp['imgUrl'] = Common::getAttachPath().$img['img'];
            $tmp['linkId'] = $img['link'];
            $tmp['typeName'] = Game_Service_Util_Link::$linkType[$img['link_type']];
            $gameId = 0;
            if($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT) {
                $gameId = $img['link'];
            }elseif($img['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY) {
                $hd = Client_Service_Hd::getHd($img['link']);
                $gameId = $hd['game_id'];
            }
            if($gameId) {
                $game = Resource_Service_GameData::getGameAllInfo($gameId);
                $tmp['href'] = $game['img'];
            }
        }
        $tmp['type'] = 'image';
    }
    
    private static function initAloneData($info, &$tmp) {
        $recommend = $info[self::SINGLEGAME_INFO];
        $alone = $info[self::SINGLEGAME_ALONE];
        if($alone) {
            $tmp['names'] = $recommend['content'];
            $tmp['linkId'] = $alone['link'];
            $tmp['typeName'] = Game_Service_Util_Link::$linkType[$alone['link_type']];
            $gameId = 0;
            if($alone['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY) {
                $hd = Client_Service_Hd::getHd($alone['link']);
                $gameId = $hd['game_id'];
            }else{
                $gameId = $alone['link'];
            }
            $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
            $tmp['size'] = $gameInfo['size'];
            $tmp['gameType'] = mb_strlen($gameInfo['category_title'], 'utf-8') > 4 ? mb_substr($gameInfo['category_title'], 0, 4, 'utf-8') : $gameInfo['category_title'];
            $tmp['href'] = $gameInfo['img'];
            if(! $recommend['content']) {
                $tmp['info'] = mb_strlen($gameInfo['resume'], 'utf-8') > 10 ? mb_substr($gameInfo['resume'], 0, 10, 'utf-8') : $gameInfo['resume'];
            }else{
                $tmp['info'] = $tmp['names'];
            }
        }
        $tmp['type'] = 'openList';
    }
    
    private static function initGiftData($info, &$tmp) {
//         unset($tmp['recType']);
//         $tmp['name'] = '热门礼包';
        $tmp['type'] = 'package';
        $tmp['list'] = array();
        $giftList = $info[self::SINGLEGAME_GIFT];
        $counts = 0;
        $gameName = "";
        foreach ($giftList as $gift) {
            if($gameName) $gameName .= ", ";
            $giftInfo = Client_Service_Gift::getGift($gift['gift_id']);
            $game = Resource_Service_GameData::getBasicInfo($giftInfo['game_id']);
            if($counts < 6) {
                $name = mb_strlen($game['name'], 'utf-8') > 6 ? mb_substr($game['name'], 0, 6, 'utf-8') : $game['name'];
                $counts++;
                $tmpGift = array('name' => $name, 'imgUrl' => $game['img'], 'info' => $giftInfo['name']);
                $tmp['list'][] = $tmpGift;
            }
            $gameName .= $giftInfo['name'];
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


	
}