<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐列表
 * Game_Api_Recommend
 * @author wupeng
 */
class Game_Api_Recommend {

	const LIST_PER_PAGE = 10;
	private static $clientVersionList = array('1.5.7', '1.5.8');
	

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
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
        $data = Util_CacheForApi::getCache($api, func_get_args());
        if($data === false) {
            $list = self::getRecommendData();
            $list = self::getGroupRecommendData($list, $group);
            $pageData = self::makePageRecommendData($list);
            $data = $pageData[$page];
            if(is_null($data)) {
                $data = array();
            }
            Util_CacheForApi::updateCache($api, func_get_args(), $data);
        }
        return $data;
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
        $length = 1;
        foreach ($list as $key => $recommend) {
            $data = array();
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $data = self::makeListData($recommend);
            } elseif ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
                $data = self::makeImgData($recommend);
            }
            if (! $data)
                continue;
            $page = ceil($length / self::LIST_PER_PAGE);
            $pageData[$page][] = $data;
            if ($recommend['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
                $length ++;
            }
        }
        
        $data = array();
        $pages = array_keys($pageData);
        $pageSize = count($pageData);
        foreach ($pages as $page) {
            $hasnext = $pageSize > $page ? true : false;
            $data[$page] = array(
                'list' => $pageData[$page], 'hasnext' => $hasnext, 'curpage' => $page
            );
        }
        return $data;
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
        $data = array(
            'viewType' => 'ListView',
            'listItemType' => 'ChannelBanner',
            'viewType' => 'ListView',
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
        foreach ($gameList as $ke=>$va) {
            $gameInfo = Resource_Service_GameData::getBasicInfo($va['game_id']);
            $extalInfo = Resource_Service_GameData::getExtraInfo($va['game_id']);
            if($gameInfo){
                $gameData[] = array(
                    'viewType'=>'GameDetailView',
                    'ad_id'=>$va['recommend_id'],
                    'gameId'=>$va['game_id'],
                    'img'=>$gameInfo['img'],
                    'name'=>html_entity_decode($gameInfo['name'], ENT_QUOTES),
					'resume'=>html_entity_decode($gameInfo['resume'], ENT_QUOTES),
                    'size'=>$gameInfo['size'].'M',
                    'link'=>$gameInfo['link'],
                    'package'=>$gameInfo['package'],
                    'category'=>$gameInfo['category'],
                    'hot'=>$gameInfo['hot'],
                    'score'=>$extalInfo['score'],
                    'freedl'=>$extalInfo['freedl'],
                    'reward'=>$extalInfo['reward'],
                    'attach'=>$extalInfo['attach']
                );
            }
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
            if ($img['ad_ptype'] == Game_Service_Util_Link::LINK_CONTENT) {
                $gameId = $img['link'];
            } else {
                $gameId = $params['gameId'];
            }
            $game = Resource_Service_GameData::getGameAllInfo($gameId);
            $data['gameId'] = $gameId;
            $data['link'] = $game['link'];
            $data['package'] = $game['package'];
            $data['size'] = $game['size'] . "M";
            $data['img'] = $game['img'];
            $data['resume'] = html_entity_decode($game['resume']);
            $data['name'] = $game['name'];
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
            $totalCount = ceil($total / self::LIST_PER_PAGE);
            $hasnext = $totalCount > $page ? true : false;
        }
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$totalCount);
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientRecommendCacheData() {
        $list = self::getRecommendData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
        $keys = Util_CacheForApi::getValidKeys($api);
        
//         $keys = array();
//         $keys[] = array('args' => array(1, 35, "Android4.2.0", "1.5.8"));
//         $keys[] = array('args' => array(1, 1, "Android4.1.0", "1.5.8"));
//         $keys[] = array('args' => array(1, 1, "Android4.2.0", "1.5.8"));
//         $keys[] = array('args' => array(1, 1, "Android4.2.0", "1.5.7"));
        
        $groupData = array();
        $groupVersionData = array();
        foreach ($keys as $key => $params) {
            $args = $params['args'];
            if(count($args) != 4) {
                Util_CacheForApi::updateCache($api, $args, array());
                continue;
            }
            $page = $args[0];
            $group = $args[1];
            $systemVersion = $args[2];
            $clientVersion = $args[3];
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
            $data = isset($pageData[$page]) ? $pageData[$page] : array();
            $args = array($page, $group, $systemVersion, $clientVersion);
            Util_CacheForApi::updateCache($api, $args, $data);
        }
    }
	
    /**
     * 前端请求游戏附加属性的同时检查首页缓存数据是否需要更新
     * @param unknown $newExtrainfo
     */
    public static function checkGameExtrainfo($newExtrainfo) {
        $newData = Common::resetKey($newExtrainfo, 'gameId');
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
        $keys = Util_CacheForApi::getValidKeys($api);
        foreach ($keys as $key => $params) {
            $args = $params['args'];
            $data = Util_CacheForApi::getCache($api, $args);
            if(! $data) continue;
            $updateFlg = self::updateCacheByGameExtraInfo($data, $newData);
            if($updateFlg) {
                Util_CacheForApi::updateCache($api, $args, $data);
            }
        }
    }
    
    private static function updateCacheByGameExtraInfo(&$data, $newData) {
        $updateFlg = false;
        if (! $data['list']) {
            return $updateFlg;
        }
        foreach ($data['list'] as $key => $value) {
            $gameList = $value['gameItems'];
            if (! $gameList) continue;
            foreach ($gameList as $gk => $gv) {
                $extalInfo = $newData[$gv['gameId']];
                if (! $extalInfo) continue;
                if ($gv['score'] != $extalInfo['score'] || $gv['attach'] != $extalInfo['attach'] ||
                    $gv['freedl'] != $extalInfo['freedl'] || $gv['reward'] != $extalInfo['reward']) {
                        $updateFlg = true;
                        $gv['attach'] = $extalInfo['attach'];
                        $gv['freedl'] = $extalInfo['freedl'];
                        $gv['reward'] = $extalInfo['reward'];
                        $gv['score'] = $extalInfo['score'];
                        $data['list'][$key]['gameItems'][$gk] = $gv;
                }
            }
        }
        return $updateFlg;
    }
    
    public static function gameIsShowing($gameId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'rec_type' => Game_Service_RecommendNew::REC_TYPE_LIST,
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array();
            $searchParams['recommend_id'] = $recommend['id'];
            $searchParams['game_id'] = $gameId;
            $counts = Game_Service_RecommendGames::getCounts($searchParams);
            if($counts > 0) {
                return true;
            }
        }
        return false;
    }
    
    public static function subjectIsShowing($subjectId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'rec_type' => Game_Service_RecommendNew::REC_TYPE_IMAGE,
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array();
            $searchParams['recommend_id'] = $recommend['id'];
            $searchParams['link_type'] = Game_Service_Util_Link::LINK_SUBJECT;
            $searchParams['link'] = $subjectId;
            $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
            if($img) {
                return true;
            }
        }
        return false;
    }
    
    public static function hdIsShowing($hdId) {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'rec_type' => Game_Service_RecommendNew::REC_TYPE_IMAGE,
            'status' => Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS,
        );
        $list = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
        foreach ($list as $key => $recommend) {
            $searchParams = array();
            $searchParams['recommend_id'] = $recommend['id'];
            $searchParams['link_type'] = Game_Service_Util_Link::LINK_ACTIVITY;
            $searchParams['link'] = $hdId;
            $img = Game_Service_RecommendImgs::getRecommendImgsBy($searchParams);
            if($img) {
                return true;
            }
        }
        return false;
    }
    
    
}
