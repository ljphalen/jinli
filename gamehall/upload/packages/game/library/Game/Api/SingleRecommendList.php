<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 单机推荐列表
 * Game_Api_SingleRecommendList
 * @author wupeng
 */
class Game_Api_SingleRecommendList {

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
	    $api = Util_CacheKey::getApi(Util_CacheKey::SINGLEGAME, Util_CacheKey::SINGLEGAME_LIST);
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
        $recommend = Game_Cache_SingleRecommend::getInstance();
        $pageInfo = $recommend->getClientPageInfo($keyParams);
        if($pageInfo === false) {
            $list = self::getClientRecommendListData();
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

    private static function getClientRecommendListData() {
        $recommend = Game_Cache_SingleRecommend::getInstance();
        $list = $recommend->getDataList();
        if($list === false) {
            $list = self::getDBRecommendData();
            $recommend->updateDataList($list);
        }
        return $list;
    }

    private static function reloadClientRecommendListData() {
        $list = self::getDBRecommendData();
        $recommend = Game_Cache_SingleRecommend::getInstance();
        $recommend->updateDataList($list);
        return $list;
    }
    
    private static function getDBRecommendData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $result = array();
        $list = Game_Service_SingleList::getSingleListListBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                $img = Game_Service_SingleListImgs::getSingleListImgsBy($searchParams);
                if(! $img || ! Game_Api_Util_Link::isShow($img['link_type'], $img['link'])) {
                    continue;
                }
                $recommend['listImage'] = $img;
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_LIST) {
                $searchParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
                $gameList = Game_Service_SingleListGames::getSingleListGamesListBy($searchParams);
                if(! $gameList) continue;
                $gameList = Common::resetKey($gameList, 'game_id');
                $recommend['listGames'] = $gameList;
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_GIFT) {
                $giftList = Game_Service_SingleListGift::getSingleListGiftListBy($searchParams);
                $total = count($giftList);
                if($total == 0) continue;
                $firstPageList = array();
                foreach ($giftList as $gift) {
                    $giftInfo = Client_Service_Gift::getGift($gift['gift_id']);
                    if($giftInfo['status'] != Client_Service_Gift::GIFT_STATE_OPENED || $giftInfo['game_status'] != Resource_Service_Games::STATE_ONLINE) {
                        continue;
                    }
                    if($giftInfo['effect_start_time'] > time() || $giftInfo['effect_end_time'] < time()) {
                        continue;
                    }
                    $firstPageList[$gift['gift_id']] = $giftInfo;
                }
                $recommend['giftList'] = array(
                    'total' => $total,
                    'list' => $firstPageList
                );
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                $alone = Game_Service_SingleListAlone::getSingleListAloneBy($searchParams);
                if(! $alone || ! Game_Api_Util_Link::isShow($alone['link_type'], $alone['link'])) {
                    continue;
                }
                if($alone['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY) {
                    $hd = Client_Service_Hd::getHd($alone['link']);
                    $alone['game_id'] = $hd['game_id'];
                }
                $recommend['listAlone'] = $alone;
            }
            $result[$recommend['id']] = $recommend;
        }
        return $result;
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
    
    /**服务端主动刷新缓存*/
    public static function updateClientRecommendCacheData() {
        $list = self::reloadClientRecommendListData();
        $recommend = Game_Cache_SingleRecommend::getInstance();
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
    
    /**推荐列表查看更多游戏*/
    public static function getClientRecommendGamesList($recommendId, $page) {
        if ($page < 1) $page = 1;
        $hasnext = false;
        $pageData = array();
        $recommend = Game_Service_SingleList::getSingleList($recommendId);
        if($recommend && $recommend['status'] == Game_Service_SingleList::STATUS_OPEN) {
            $gameParams['recommend_id'] = $recommendId;
            $gameParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
            list($total, $gameList) = Game_Service_SingleListGames::getPageList($page, self::LIST_PER_PAGE, $gameParams);
            $gameList = Common::resetKey($gameList, 'game_id');
            $gameIds = array_unique(array_keys($gameList));
            $list = Resource_Service_GameListData::getList($gameIds);
            $list = Resource_Service_GameListFormat::output($list);
            $list = Common::resetKey($list, 'gameid');
            foreach ($gameList as $gameId => $game) {
                $gameData = $list[$gameId];
                $gameData['ad_id'] = $game['recommend_id'];
                $pageData[] = $gameData;
            }
            $totalCount = ceil($total / self::LIST_PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    /**推荐列表查看更多列表*/
    public static function getClientRecommendGiftsList($recommendId, $page, $onLine, $userName) {
        if ($page < 1) $page = 1;
        $hasnext = false;
        $pageData = array();
        $list = self::getClientRecommendListData();
        $recommend = $list[$recommendId];
        if ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_GIFT) {
            $giftList = $recommend['giftList']['list'];
            $total = count($giftList);
            $start = ($page - 1) * self::LIST_PER_PAGE;
            $giftList = array_slice($giftList, $start, self::LIST_PER_PAGE);
            foreach ($giftList as $gift) {
                $giftId = $gift['id'];
                $gameId = $gift['game_id'];
                $tmp = array();
                if($onLine) {
                    $log = Client_Service_Giftlog::getByUnameGiftId($userName, $giftId);
                }
                $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
                $tmp[Util_JsonKey::GAME_ID] = $gameId;
                $tmp[Util_JsonKey::GAME_NAME] = $gameInfo['name'];
                $tmp[Util_JsonKey::ICON_URL] = $gameInfo['img'];
                $tmp[Util_JsonKey::GIFT_ID] = $giftId;
                $tmp[Util_JsonKey::GIFT_NAME] = html_entity_decode($gift['name'], ENT_QUOTES);
                $tmp[Util_JsonKey::GIFT_KEY_TOTAL] = Client_Service_Gift::getGiftTotal($giftId);
                $tmp[Util_JsonKey::GIFT_KEY_REMAINS] = Client_Service_Gift::getGiftRemainNum($giftId);
                $tmp[Util_JsonKey::IS_GRAB] = ($log ? "true" : "false");
                $tmp[Util_JsonKey::GIFT_KEY] = ($log ? $log['activation_code'] : "");
                $pageData[] = $tmp;
            }
            $totalCount = ceil($total / self::LIST_PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    private static function reInitGameInfo($list) {
        $extraInfoList = self::getGameInfoList($list);
        foreach ($list as $key => $item) {
            if($item['gameId']) {
                $gameIds[] = $item['gameId'];
                $gameInfo = $extraInfoList[$item['gameId']];
                unset($gameInfo['viewType']);
                $item = array_merge($item, $gameInfo);
            }elseif(isset($item['gameItems'])) {
                $gameItems = $item['gameItems'];
                foreach ($gameItems as $gk => $gameItem) {
                    $gameId = $gameItem['gameId'];
                    if(! $gameId) continue;
                    $gameInfo = $extraInfoList[$gameId];
                    $gameItem = array_merge($gameItem, $gameInfo);
                    $gameItem['hasGift'] = $gameItem['attach'] ? true : false;
                    $gameItems[$gk] = $gameItem;
                }
                $item['gameItems'] = $gameItems;
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
            $gameItems = $item['gameItems'];
            if(! $gameItems) {
                continue;
            }
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
    
    public static function gameIsShowing($gameId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $list = Game_Service_SingleList::getSingleListListBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                $img = Game_Service_SingleListImgs::getSingleListImgsBy($searchParams);
                if($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT && $img['link'] == $gameId) {
                    return true;
                }
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_LIST) {
                $gameList = Game_Service_SingleListGames::getSingleListGamesListBy($searchParams);
                $gameList = Common::resetKey($gameList, 'game_id');
                if($gameList[$gameId]) {
                    return true;
                }
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                $alone = Game_Service_SingleListAlone::getSingleListAloneBy($searchParams);
                if($alone['link_type'] == Game_Service_Util_Link::LINK_CONTENT && $alone['link'] == $gameId) {
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
        $list = Game_Service_SingleList::getSingleListListBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                $img = Game_Service_SingleListImgs::getSingleListImgsBy($searchParams);
                if($img['link_type'] == $linkType && $img['link'] == $link) {
                    return true;
                }
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                $alone = Game_Service_SingleListAlone::getSingleListAloneBy($searchParams);
                if($alone['link_type'] == $linkType && $alone['link'] == $link) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function giftIsShowing($giftId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_SingleList::STATUS_OPEN,
        );
        $list = Game_Service_SingleList::getSingleListListBy($searchParams);
        foreach ($list as $key => $recommend) {
            if ($recommend['rec_type'] != Game_Service_SingleList::REC_TYPE_GIFT) {
                continue;
            }
            $searchParams = array('recommend_id' => $recommend['id']);
            $giftList = Game_Service_SingleListGift::getSingleListGiftListBy($searchParams);
            $giftList = Common::resetKey($giftList, 'gift_id');
            if($giftList[$giftId]) {
                return true;
            }
        }
        return false;
    }

    private static function makePageRecommendData($list) {
        $pageData = array();
        $pageList = array();
        $length = 1;
        $index = 0;
        foreach ($list as $key => $recommend) {
            $data = array();
            if ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_LIST) {
                $data = self::makeListData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_IMAGE) {
                $data = self::makeImgData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_ALONE) {
                $data = self::makeAloneData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_SingleList::REC_TYPE_GIFT) {
                $data = self::makeGiftListData($recommend);
            }
            if (! $data){
                continue;
            }
            $page = ceil($length / self::LIST_PER_PAGE);
            $pageData[$index] = $data;
            $pageList[$page][] = $index++;
            if ($recommend['rec_type'] != Game_Service_SingleList::REC_TYPE_IMAGE) {
                $length ++;
            }
        }
        return array($pageList, $pageData);
    }

    private static function makeListData($recommend) {
        $gameList = $recommend['listGames'];
        $gameTotal = count($gameList);
        $showCounts = Game_Service_SingleList::$list_template_counts[$recommend['template']];
        $gameInfoListData = array();
        foreach ($gameList as $game) {
            if($showCounts-- <= 0) {
                break;
            }
            $gameData = array('gameId' => $game['game_id']);
            $gameData['ad_id'] = $recommend['id'];
            $gameInfoListData[] = $gameData;
        }
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $params = array(
            'url' => $webroot .'/Api/Local_Singlegame/gameList?id=',
            'contentId' => $recommend['id'],
            'gameId' => '',
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
        );
        
        if($recommend['template'] == Game_Service_SingleList::LIST_VERTICAL) {
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

    private static function makeAloneData($recommend) {
        $alone = $recommend['listAlone'];
        $data = array();
        $params = Game_Api_Util_RecommendListUtil::getClientParams($alone['link_type'], $alone['link'], $recommend['title']);
        if(! $params) {
            return $data;
        }
        $data['listItemType'] = 'SimpleEvent';
        $data['viewType'] = $params['viewType'] ? $params['viewType'] : Game_Api_Util_RecommendListUtil::getViewType($alone['link_type']);
        $data['title'] = html_entity_decode($recommend['title']);
        $data['resume'] = html_entity_decode($recommend['content']);
        $data['ad_id'] = $recommend['id'];
        $data['param'] = Game_Api_Util_RecommendListUtil::getParams($params);
        $gameId = $params['gameId'];
        $gameData = array('gameId' => $gameId);
        $data['gameItems'] = array($gameData);
        $data['total'] = count($data['gameItems']);
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
        $giftListData = array();
        $count = 0;
        foreach ($giftList as $gift) {
            $tmp = array();
            $gameInfo = Resource_Service_GameData::getBasicInfo($gift['game_id']);
            $tmp["gameId"] = $gift['game_id'];
            $tmp["gameName"] = html_entity_decode($gameInfo['name'], ENT_QUOTES);
            $tmp["iconUrl"] = $gameInfo['img'];
            $tmp["giftId"] = $gift['id'];
            $tmp["giftName"] = $gift['name'];
            $giftListData[] = $tmp;
            if(++$count == 6) {
                break;
            }
        }
        if($count < 6) {
            $giftTotal = $count;
        }
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $params = array(
            'url' => $webroot .'/Api/Local_Singlegame/giftList?id=',
            'contentId' => $recommend['id'],
            'gameId' => '',
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
        );
        $data = array(
            'viewType' => 'RecommendGiftView',
            'listItemType' => 'HotGiftList',
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
            'giftItems' => $giftListData,
            'total' => $giftTotal,
            'param' => $params,
        );
        return $data;
    }
    
}
