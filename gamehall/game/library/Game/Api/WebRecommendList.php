<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游推荐列表
 * Game_Api_WebRecommendList
 * @author wupeng
 */
class Game_Api_WebRecommendList {
    
	const LIST_PER_PAGE = 10;

    /**获取客户端推荐列表*/
    public static function getClientRecommend($page, $device, $systemVersion) {
        $data = array();
        $systemVersionResult = Common::compareSysytemVersion($systemVersion);
        if($systemVersionResult){
            $systemVersion = Game_Service_RecommendNew::GREATE_SYSTEM_VERSION;
        }else{
            $systemVersion = Game_Service_RecommendNew::LESS_SYSTEM_VERSION;
        }
        $group = Resource_Service_Pgroup::getGroupByDevice($device);
        $data = self::getClientRecommendCacheData($page, $group, $systemVersion);
        return $data;
    }

    private static function getClientRecommendCacheData($page, $group, $systemVersion) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_LIST);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false) {
            $data = self::getClientRecommendRedisData($page, $group, $systemVersion);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getClientRecommendRedisData($page, $group, $systemVersion) {
        $keyParams = array($group, $systemVersion);
        $recommend = Game_Cache_GameWebRec::getInstance();
        $pageInfo = $recommend->getClientPageInfo($keyParams);
        if($pageInfo === false) {
            $list = self::getRecommendListData();
            $list = self::getGroupRecommendData($list, $group);
            list($pageList, $dataList) = self::makePageRecommendData($list);
            $recommend->updateClientPageList($keyParams, $dataList, $pageList);
            $pageInfo = $recommend->getClientPageInfo($keyParams);
        }
        $pageSize = $pageInfo[Util_Api_ListPage::PAGE_SIZE];
        if($page > $pageSize) {
            $pageData = array();
        }else{
            $pageData = $recommend->getClientPageList($page, $keyParams);
            $pageData = self::reInitGameInfo($pageData);
        }
        $hasnext = $pageSize > $page ? true : false;
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    private static function reInitGameInfo($list) {
        $gameInfoList = self::getGameInfoList($list);
        foreach ($list as $key => $item) {
            if($item['gameId']) {
                $gameIds[] = $item['gameId'];
                $gameInfo = $gameInfoList[$item['gameId']];
                if(! $gameInfo) continue;
                unset($gameInfo['viewType']);
                $item = array_merge($item, $gameInfo);
            }elseif(isset($item['gameItems']) || isset($item['serviceInfoItems'])) {
                $itemName = isset($item['gameItems']) ? 'gameItems' : 'serviceInfoItems';
                $gameItems = $item[$itemName];
                foreach ($gameItems as $gk => $gameItem) {
                    $gameId = $gameItem['gameId'];
                    if(! $gameId) continue;
                    $gameInfo = $gameInfoList[$gameId];
                    if(! $gameInfo) continue;
                    $gameItem = array_merge($gameItem, $gameInfo);
                    $gameItem['hasGift'] = $gameItem['attach'] ? true : false;
                    $gameItems[$gk] = $gameItem;
                }
                $item[$itemName] = $gameItems;
            }else{
                continue;
            }
            $list[$key] = $item;
        }
        return $list;
    }
    
    private static function getGameInfoList($list) {
        $gameIds = array();
        foreach ($list as $key => $item) {
            if($item['gameId']) {
                $gameIds[] = $item['gameId'];
                continue;
            }
            if(! (isset($item['gameItems']) || isset($item['serviceInfoItems']))) {
                continue;
            }
            $itemName = isset($item['gameItems']) ? 'gameItems' : 'serviceInfoItems';
            $gameItems = $item[$itemName];
            foreach ($gameItems as $gk => $gameItem) {
                $gameId = $gameItem['gameId'];
                if(! $gameId) continue;
                $gameIds[] = $gameId;
            }
        }
        $gameIds = array_unique($gameIds);
        $list = Resource_Service_GameListData::getList($gameIds);
        $gameList = Resource_Service_GameListFormat::output($list);
        $gameList = Common::resetKey($gameList, 'gameid');
        return $gameList;
    }
    
    private static function getRecommendData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_GameWebRec::STATUS_OPEN,
        );
        $list = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $img = Game_Service_GameWebRecImgs::getGameWebRecImgsBy($searchParams);
                $recommend['listImage'] = $img;
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_LIST) {
                $searchParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
                $showCounts = Game_Service_GameWebRec::$list_template_counts[$recommend['template']];
                list($total, $gameList) = Game_Service_GameWebRecGames::getPageList(1, $showCounts, $searchParams);
                if($total == 0) continue;
                $gameList = Common::resetKey($gameList, 'game_id');
                $recommend['listGames'] = array(
                    'total' => $total,
                    'list' => $gameList
                );
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_GIFT) {
                list($total, $giftList) = Game_Manager_WebRecommendList::getGiftPageList(1, 6, $day_id);
                if($total == 0) continue;
                $total = 7;
                $giftList = Common::resetKey($giftList, 'gift_id');
                $recommend['giftList'] = array(
                    'total' => $total,
                    'list' => $giftList
                );
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
                $searchParams['open_status'] = Game_Service_GameOpen::STATUS_OPEN;
                $openList = Game_Service_GameWebRecOpen::getGameWebRecOpenListBy($searchParams);
                if(! $openList) continue;
                $total = 6;
                $openList = Common::resetKey($openList, 'open_id');
                $recommend['openList'] = array(
                    'total' => $total,
                    'list' => $openList
                );
            }
            $list[$key] = $recommend;
        }
        return $list;
    }
    
    private static function getGroupRecommendData($list, $group) {
        $data = array();
        foreach ($list as $recommend) {
            if($recommend['pgroup'] == 0 || $recommend['pgroup'] == $group) {
                $data[] = $recommend;
            }
        }
        return $data;
    }

    private static function makePageRecommendData($list) {
        $pageData = array();
        $pageList = array();
        $length = 1;
        $index = 0;
        foreach ($list as $key => $recommend) {
            $data = array();
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_LIST) {
                $data = self::makeListData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $data = self::makeImgData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
                $data = self::makeOpenListData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_GIFT) {
                $data = self::makeGiftListData($recommend);
            }
            if (! $data){
                continue;
            }
            $page = ceil($length / self::LIST_PER_PAGE);
            $pageData[$index] = $data;
            $pageList[$page][] = $index++;
            if ($recommend['rec_type'] != Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $length ++;
            }
        }
        return array($pageList, $pageData);
    }

    private static function makeOpenListData($recommend) {
        $list = $recommend['openList'];
        $total = $list['total'];
        $openList = $list['list'];
        $data = array();
        $data['viewType'] = 'ServiceInfoView';
        $data['listItemType'] = 'ServiceInfoList';
        $data['title'] = '开服信息';
        $data['total'] = $total;
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $url = $webroot .'/Api/Local_Webgame/openList';
        $data['param'] = array("url" => $url);
        $data['serviceInfoItems'] = array();
        $counts = 5;
        foreach ($openList as $open) {
            if($counts <=0)break;
            $tmp = array();
            $openInfo = Game_Service_GameOpen::getGameOpen($open['open_id']);
            if(! $openInfo['game_status'] == Resource_Service_Games::STATE_ONLINE) {
                continue;
            }
            $game_id = $openInfo['game_id'];
            $openData = array('gameId' => $game_id);
            $openType = Game_Service_GameOpen::$open_type[$openInfo['open_type']];
            if(! $openType) {
                $openType = "";
            }
            if($openInfo['open_time']) {
                $time = date('n.j', $openInfo['open_time']);
            }else{
                $time = "";
            }
            $openData['ad_id'] = $recommend['id'];
            $openData['status'] = $openType;
            $openData['time'] = $time;
            $data['serviceInfoItems'][] = $openData;
            $counts--;
        }
        if(! $data['serviceInfoItems']) {
            return array();
        }
        return $data;
    }
    
    private static function makeListData($recommend) {
        $list = $recommend['listGames'];
        $gameTotal = $list['total'];
        $gameList = $list['list'];
        
        $gameInfoListData = array();
        foreach ($gameList as $game) {
            $gameData = array('gameId' => $game['game_id']);
            $gameData['ad_id'] = $recommend['id'];
            $gameInfoListData[] = $gameData;
        }
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $params = array(
            'url' => $webroot .'/Api/Local_Webgame/gameList?id=',
            'contentId' => $recommend['id'],
            'gameId' => '',
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
        );
        
        if($recommend['template'] == Game_Service_RecommendNew::LIST_VERTICAL) {
            $listItemType = "VerticalGames";
        }else{
            $listItemType = "HorizontalGames";
        }
        $data = array(
            'viewType' => 'ListView',
            'listItemType' => $listItemType,
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
            'gameItems' => $gameInfoListData,
            'total' => $gameTotal,
            'param' => $params,
        );
        return $data;
    }
    
    private static function makeImgData($recommend) {
        $img = $recommend['listImage'];
        $data = array();
        $params = Game_Api_Util_RecommendListUtil::getClientParams($img['link_type'], $img['link'], $recommend['title']);
        if(! $params) {
            return $data;
        }
        $data['listItemType'] = 'SimpleBanner';
        $data['viewType'] = $params['viewType'] ? $params['viewType'] : Game_Api_Util_RecommendListUtil::getViewType($img['link_type']);
        $data['bannerImg'] = Common::getAttachPath() . $img['img'];
        $data['title'] = html_entity_decode($recommend['title']);
        $data['content'] = html_entity_decode($recommend['title']);
        $data['ad_id'] = $recommend['id'];
        $data['param'] = Game_Api_Util_RecommendListUtil::getParams($params);
        
        if ($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT || $img['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY) {
            if ($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT) {
                $gameId = $img['link'];
            } else {
                $gameId = $params['gameId'];
            }
            $data['gameId'] = $gameId;
        }
        return $data;
    }
    
    private static function makeGiftListData($recommend) {
        $list = $recommend['giftList'];
        $giftTotal = $list['total'];
        $giftList = $list['list'];
        $data = array();
        $data['viewType'] = 'GiftListView';
        $data['listItemType'] = 'HotGiftList';
        $data['title'] = '热门礼包';
        $data['total'] = $giftTotal;
        $data['param'] = array("title" => "热门礼包");
        $data['giftItems'] = array();
        foreach ($giftList as $gift) {
            $tmp = array();
            $gameInfo = Resource_Service_GameData::getBasicInfo($gift['game_id']);
            $tmp["gameId"] = $gift['game_id'];
            $tmp["gameName"] = html_entity_decode($gameInfo['name'], ENT_QUOTES);
            $tmp["iconUrl"] = $gameInfo['img'];
            $tmp["giftId"] = $gift['gift_id'];
            $tmp["giftName"] = $gift['gift_name'];
            $data['giftItems'][] = $tmp;
        }
        return $data;
    }
    
    /**推荐列表查看更多游戏*/
    public static function getClientRecommendGamesList($recommendId, $page) {
        $hasnext = false;
        $pageData = array();
        $recommend = Game_Service_GameWebRec::getGameWebRec($recommendId);
        if($recommend && $recommend['status'] == Game_Service_GameWebRec::STATUS_OPEN) {
            $gameParams['recommend_id'] = $recommendId;
            $gameParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
            list($total, $gameList) = Game_Service_GameWebRecGames::getPageList($page, self::LIST_PER_PAGE, $gameParams);
            $gameList = Common::resetKey($gameList, 'game_id');
            $gameIds = array_unique(array_keys($gameList));
            $list = Resource_Service_GameListData::getList($gameIds);
            $list = Resource_Service_GameListFormat::output($list);
            $list = Common::resetKey($list, 'gameid');
            foreach ($gameList as $gameId => $game) {
                $gameData = $list[$gameId];
                if(! $gameData) continue;
                $gameData['ad_id'] = $game['recommend_id'];
                $pageData[] = $gameData;
            }
            $totalCount = ceil($total / self::LIST_PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientRecommendCacheData() {
        $list = self::reloadRecommendListData();
        $recommend = Game_Cache_GameWebRec::getInstance();
        $keys = $recommend->getValidListArgs();
        $groupData = array();
        $groupVersionData = array();
        Yaf_Registry::set("apkVersion", '1.5.9');
        foreach ($keys as $key => $args) {
            if(count($args) != 2) {
                $recommend->updateClientPageList($args, array(), array());
                continue;
            }
            $group = $args[0];
            $systemVersion = $args[1];
            if(isset($groupData[$group])) {
                $groupList = $groupData[$group];
            }else{
                $groupList = self::getGroupRecommendData($list, $group);
                $groupData[$group] = $groupList;
            }
            Yaf_Registry::set("androidVersion", $systemVersion);
            $key = $group.'_'.$systemVersion;
            if(isset($groupVersionData[$key])) {
                $pageData = $groupVersionData[$key];
            }else{
                $pageData = self::makePageRecommendData($groupList);
                $groupVersionData[$key] = $pageData;
            }
            list($pageList, $dataList) = $pageData;
            $recommend->updateClientPageList($args, $dataList, $pageList);
        }
    }
    
    public static function gameIsShowing($gameId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $list = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $img = Game_Service_GameWebRecImgs::getGameWebRecImgsBy($searchParams);
                if($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT && $img['link'] == $gameId) {
                    return true;
                }
            }
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_LIST) {
                $gameList = Game_Service_GameWebRecGames::getGameWebRecGamesListBy($searchParams);
                $gameList = Common::resetKey($gameList, 'game_id');
                if($gameList[$gameId]) {
                    return true;
                }
            }
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
                $openList = Game_Service_GameWebRecOpen::getGameWebRecOpenListBy($searchParams);
                if(! $openList) continue;
                $openList = Common::resetKey($openList, 'open_id');
                $searchParams = array('game_id' => $gameId, 'id' => array('IN', array_keys($openList)));
                $result = Game_Service_GameOpen::getGameOpenBy($searchParams);
                if($result) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function subjectIsShowing($subjectId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_SUBJECT, $subjectId);
    }
    
    public static function hdIsShowing($hdId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_ACTIVITY, $hdId);
    }
    
    private static function linkIsShowing($linkType, $link) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $list = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_IMAGE) {
                $searchParams = array('recommend_id' => $recommend['id']);
                $img = Game_Service_GameWebRecImgs::getGameWebRecImgsBy($searchParams);
                if($img['link_type'] == $linkType && $img['link'] == $link) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function openIsShowing($openId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $list = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] != Game_Service_GameWebRec::REC_TYPE_OPEN) {
                continue;
            }
            $searchParams = array('recommend_id' => $recommend['id']);
            $openList = Game_Service_GameWebRecOpen::getGameWebRecOpenListBy($searchParams);
            $openList = Common::resetKey($openList, 'open_id');
            if($openList[$openId]) {
                return true;
            }
            break;
        }
        return false;
    }
    
    public static function giftIsOpen() {
        $list = self::getRecommendListData();
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_GIFT) {
                return true;
            }
        }
        return false;
    }
    
    public static function giftIsShowing($giftId) {
        $list = self::getRecommendListData();
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] != Game_Service_GameWebRec::REC_TYPE_GIFT) {
                continue;
            }
            $giftList = $recommend['giftList']['list'];
            if($giftList[$giftId]) {
                return true;
            }
            break;
        }
        return false;
    }

    private static function getRecommendListData() {
        $recommend = Game_Cache_GameWebRec::getInstance();
        $list = $recommend->getDataList();
        if($list === false) {
            $list = self::getRecommendData();
            $recommend->updateDataList($list);
        }
        return $list;
    }

    private static function reloadRecommendListData() {
        $recommend = Game_Cache_GameWebRec::getInstance();
        $list = self::getRecommendData();
        $recommend->updateDataList($list);
        return $list;       
    }
    
    public static function getFirstListGameList() {
        $list = self::getRecommendListData();
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] != Game_Service_GameWebRec::REC_TYPE_LIST) {
                continue;
            }
            $list = $recommend['listGames'];
            $gameList = $list['list'];
            $gameInfoListData = array();
            foreach ($gameList as $game) {
                $gameInfoListData[] = $game['game_id'];
            }
            return $gameInfoListData;
        }
        return array();
    }
    
}
