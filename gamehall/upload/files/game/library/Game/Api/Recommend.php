<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐列表
 * Game_Api_Recommend
 * @author wupeng
 */
class Game_Api_Recommend {
    
	const LIST_PER_PAGE = 10;

    /**获取客户端推荐列表*/
    public static function getClientRecommend($page, $device, $systemVersion, $clientVersion) {
        $data = array();
        $systemVersionResult = Common::compareSysytemVersion($systemVersion);
        if($systemVersionResult){
            $systemVersion = Game_Service_RecommendNew::GREATE_SYSTEM_VERSION;
        }else{
            $systemVersion = Game_Service_RecommendNew::LESS_SYSTEM_VERSION;
        }
        if(Common::isAfterVersion($clientVersion, '1.5.8')) {
            $clientVersion = "1.5.8";
        }else{
            $clientVersion = "1.5.7";
        }
        $group = Resource_Service_Pgroup::getGroupByDevice($device);
        $data = self::getClientRecommendCacheData($page, $group, $systemVersion, $clientVersion);
        return $data;
    }

    private static function getClientRecommendCacheData($page, $group, $systemVersion, $clientVersion) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_LIST);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getClientRecommendRedisData($page, $group, $systemVersion, $clientVersion);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }

    private static function getClientRecommendRedisData($page, $group, $systemVersion, $clientVersion) {
        $keyParams = array($group, $systemVersion, $clientVersion);
        $recommend = Game_Cache_RecommendNew::getInstance();
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
        foreach ($list as $key => $recList) {
            if($recList['gameId']) {
                $gameIds[] = $recList['gameId'];
                $gameInfo = $gameInfoList[$recList['gameId']];
                if(! $gameInfo) continue;
                unset($gameInfo['viewType']);
                $recList = array_merge($recList, $gameInfo);
            }elseif($recList['gameItems']) {
                $gameList = $recList['gameItems'];
                foreach ($gameList as $gk => $gv) {
                    $gameId = $gv['gameId'];
                    if(! $gameId) continue;
                    $gameInfo = $gameInfoList[$gameId];
                    if(! $gameInfo) continue;
                    $gv = array_merge($gv, $gameInfo);
                    $gameList[$gk] = $gv;
                }
                $recList['gameItems'] = $gameList;
            }else{
                continue;
            }
            $list[$key] = $recList;
        }
        return $list;
    }
    
    private static function getGameInfoList($list) {
        $gameIds = array();
        foreach ($list as $key => $recList) {
            if($recList['gameId']) {
                $gameIds[] = $recList['gameId'];
                continue;
            }
            $gameList = $recList['gameItems'];
            if (! $gameList) continue;
            foreach ($gameList as $gk => $gv) {
                $gameId = $gv['gameId'];
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
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $searchParams['game_status'] = Game_Service_RecommendNew::GAME_OPEN_STATUS ;
                list($gameTotal, $gameList) = Game_Service_RecommendGames::getRecommendGameList(1, Game_Service_RecommendNew::SHOW_NUM, $searchParams);
                $gameList = Common::resetKey($gameList, 'game_id');
                $recommend['listGames'] = array(
                    'total' => $gameTotal,
                    'list' => $gameList
                );
            } elseif ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
                $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
                $recommend['listImage'] = $img;
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
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $data = self::makeListData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
                $data = self::makeImgData($recommend);
            }
            if (! $data) {
                continue;
            }
            $page = ceil($length / self::LIST_PER_PAGE);
            $pageData[$index] = $data;
            $pageList[$page][] = $index++;
            $page = ceil($length / self::LIST_PER_PAGE);
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $length ++;
            }
        }
        return array($pageList, $pageData);
    }
    
    private static function makeListData($recommend) {
        $list = $recommend['listGames'];
        $gameTotal = $list['total'];
        $gameList = $list['list'];
        $gameInfoListData = self::fillRecommendListGameInfoData($gameList);
        $webroot = Yaf_Application::app()->getConfig()->webroot;
        $params = array(
            'url' => $webroot .'/Api/Local_Home/recommendGameList?recommendId=',
            'contentId' => $recommend['id'],
            'gameId' => '',
            'title' => html_entity_decode($recommend['title'], ENT_QUOTES),
        );
        
        if($recommend['list_tpl'] == Game_Service_RecommendNew::LIST_VERTICAL) {
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

    private function fillRecommendListGameInfoData($gameList){
        if(!is_array($gameList)){
            return false;
        }
        foreach ($gameList as $value) {
            $gameData[] = array(
                'ad_id'=>$value['recommend_id'],
                'gameId'=>$value['game_id'],
            );
        }
        return $gameData;
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

    /**
     * 推荐列表查看更多游戏
     * @param unknown $recommendId
     * @param unknown $page
     */
    public static function getClientRecommendGamesList($recommendId, $page) {
        $searchParams = array(
            'id' => $recommendId,
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $recommendListResult = Game_Service_RecommendNew::getRecommendnewBy($searchParams);
        if ($page < 1) $page = 1;
        $totalCount = 0;
        $hasnext = false;
        $pageData = array();
        if($recommendListResult) {
            $gameParams['recommend_id'] = $recommendId;
            $gameParams['game_status'] = Game_Service_RecommendNew::GAME_OPEN_STATUS ;
            list($total, $gameList) = Game_Service_RecommendGames::getRecommendGameList($page, self::LIST_PER_PAGE, $gameParams);
            $pageData =  self::fillRecommendListGameInfoData($gameList);
            $gameList = Common::resetKey($gameList, 'game_id');
            $gameIds = array_keys($gameList);
            $list = Resource_Service_GameListData::getList($gameIds);
            $gameList = Resource_Service_GameListFormat::output($list);
            $gameList = Common::resetKey($gameList, 'gameid');
            foreach ($pageData as $key => $item) {
                $gameId = $item['gameId'];
                $gameInfo = $gameList[$gameId];
                $item = array_merge($item, $gameInfo);
                $pageData[$key] = $item;
            }
            $totalCount = ceil($total / self::LIST_PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$totalCount);
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientRecommendCacheData() {
        $groupData = array();
        $groupVersionData = array();
        $list = self::reloadRecommendListData();
        $recommend = Game_Cache_RecommendNew::getInstance();
        $keys = $recommend->getValidListArgs();
        foreach ($keys as $key => $args) {
            if(count($args) != 3) {
                $recommend->updateClientPageList($args, array(), array());
                continue;
            }
            $group = $args[0];
            $systemVersion = $args[1];
            $clientVersion = $args[2];
            if(isset($groupData[$group])) {
                $groupList = $groupData[$group];
            }else{
                $groupList = self::getGroupRecommendData($list, $group);
                $groupData[$group] = $groupList;
            }
            Yaf_Registry::set("androidVersion", $systemVersion);
            Yaf_Registry::set("apkVersion", $clientVersion);
            $key = $group.'_'.$systemVersion.'_'.$clientVersion;
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
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
                $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
                if($img['link_type'] == Game_Service_Util_Link::LINK_CONTENT && $img['link'] == $gameId) {
                    return true;
                }
            } elseif ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $gameList = Game_Service_RecommendGames::getRecommendGamesBy($searchParams);
                $gameList = array_slice($gameList, 0, 6);
                $gameList = Common::resetKey($gameList, 'game_id');
                if($gameList[$gameId]) {
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
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array('recommend_id' => $recommend['id']);
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
                $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
                if($img['link_type'] == $linkType && $img['link'] == $link) {
                    return true;
                }
            }
        }
        return false;
    }
    
    private static function getRecommendListData() {
        $recommend = Game_Cache_RecommendNew::getInstance();
        $list = $recommend->getDataList();
        if($list === false) {
            $list = self::getRecommendData();
            $recommend->updateDataList($list);
        }
        return $list;
    }

    private static function reloadRecommendListData() {
        $recommend = Game_Cache_RecommendNew::getInstance();
        $list = self::getRecommendData();
        $recommend->updateDataList($list);
        return $list;
    }

}
