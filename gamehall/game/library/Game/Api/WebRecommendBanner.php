<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游轮播图
 * Game_Api_WebRecommendBanner
 * @author wupeng
 */
class Game_Api_WebRecommendBanner {
    const V155 = '1.5.5';
    const V160 = '1.6.0';
    
    /**获取客户端轮播图*/
    public static function getClientBanner($clientVersion=self::V155) {
        if(Common::isAfterVersion($clientVersion, self::V160)) {
            $clientVersion = self::V160;
        }else{
            $clientVersion = self::V155;
        }
        $data = self::getClientBannerCacheData($clientVersion);
        return $data;
    }

    private static function getClientBannerCacheData($clientVersion) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_BANNER);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getClientBannerRedisData($clientVersion);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getClientBannerRedisData($clientVersion) {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_BANNER);
        $data = Util_Api_Cache::getCache($api, func_get_args());
        if($data === false) {
            $data = self::getClientBannerData($clientVersion);
            Util_Api_Cache::updateCache($api, func_get_args(), $data);
        }
        $data = self::initGameInfo($data);
        return $data;
    }
    
    private static function initGameInfo($data) {
        $banners = $data['slideItems'];
        $gameIds = array();
        foreach ($banners as $key => $banner) {
            if(! isset($banner['gameId'])) continue;
            $gameIds[$banner['gameId']] = $banner['gameId'];
        }
        $list = Resource_Service_GameListData::getList($gameIds);
        $gameList = Resource_Service_GameListFormat::output($list);
        $gameList = Common::resetKey($gameList, 'gameid');
        foreach ($banners as $key => $banner) {
            if(! isset($banner['gameId'])) continue;
            $gameInfo = $gameList[$banner['gameId']];
            unset($gameInfo['viewType']);
            $banner = array_merge($banner, $gameInfo);
            $banners[$key] = $banner;
        }
        $data['slideItems'] = $banners;
        return $data;
    }
    
    private static function getClientBannerData($clientVersion) {
        $bannerList = self::getRecommendBannerData();
        $data = self::makeClientBannerData($bannerList, $clientVersion);
        return $data;
    }
    
    private static function getBannerData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_GameWebRecBanner::STATUS_OPEN
        );
        $bannerList = Game_Service_GameWebRecBanner::getGameWebRecBannerListBy($searchParams);
        return $bannerList;
    }
    
    private static function makeClientBannerData($bannerList, $clientVersion) {
        $banners = array();
        $data['slideItems'] = $banners;
        $pos = 1;
        $adtype = "ad1";
        foreach ($bannerList as $key => $banner) {
           if ($banner['link_type'] == Game_Service_Util_Link::LINK_URL) { // 链接单独处理
                $params = Game_Api_Util_RecommendListUtil::getParamsByLinkUrl($banner['link'], $banner['title'], $adtype, $pos);
            } else {
                $params = Game_Api_Util_RecommendListUtil::getClientParamsByBanner($banner);
            }
            $pos ++;
            if(! $params) {
                continue;
            }
            if($clientVersion == self::V160) {
                $img = $banner['img_high'];
            }else{
                $img = $banner['img'];
            }
            $tmp = array(
                Util_JsonKey::VIEW_TYPE => Game_Api_Util_RecommendListUtil::getViewType($banner['link_type']),
                Util_JsonKey::TITLE => html_entity_decode($banner['title'], ENT_QUOTES),
                Util_JsonKey::CONTENT => html_entity_decode($banner['title'], ENT_QUOTES),
                Util_JsonKey::IMAGE_URL => Common::getAttachPath().$img,
                Util_JsonKey::PARAM => Game_Api_Util_RecommendListUtil::getParams($params),
                Util_JsonKey::SOURCE => "",
                'ad_id' => $banner['id']
            );
            if($params['gameId']) {
                $gameId = $params['gameId'];
                $tmp['gameId'] = $gameId;
            }
            $banners[] = $tmp;
        }
        $data['slideItems'] = $banners;
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientBannerCacheData() {
        $bannerList = self::reloadRecommendBannerData();
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_BANNER);
        $keys = Util_Api_Cache::getValidKeys($api);
        if(! $keys) return;
        foreach ($keys as $key => $args) {
            if(count($args) != 1) {
                Util_Api_Cache::updateCache($api, $args, array());
                continue;
            }
            $clientVersion = $args[0];
            $data = self::makeClientBannerData($bannerList, $clientVersion);
            Util_Api_Cache::updateCache($api, $args, $data);
        }
    }
    
    public static function gameIsShowing($gameId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_CONTENT, $gameId);
    }
    
    public static function subjectIsShowing($subjectId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_SUBJECT, $subjectId);
    }
    
    public static function hdIsShowing($hdId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_ACTIVITY, $hdId);
    }

    private static function linkIsShowing($linkType, $link) {
        $bannerList = self::getRecommendBannerData();
        foreach ($bannerList as $banner) {
            if($banner['link_type'] == $linkType && $link) {
                return true;
            }
        }
        return false;
    }

    private static function getRecommendBannerData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_DATA);
        $list = Util_CacheKey::getHCache($api, array(), 'banner');
        if($list === false) {
            $list = self::getBannerData();
            Util_CacheKey::updateHCache($api, array(), 'banner', $list);
        }
        return $list;
    }

    private static function reloadRecommendBannerData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_DATA);
        $list = self::getBannerData();
        Util_CacheKey::updateHCache($api, array(), 'banner', $list);
        return $list;
    }

    /**取旧版本数据结构*/
    public static function getOldVersionBannerData() {
        $ads = array();
        $bannerList = self::getRecommendBannerData();
        foreach ($bannerList as $banner) {
            $tmp['id'] = $banner['id'];
            $tmp['sort'] = $banner['sort'];
            $tmp['ad_type'] = 1;
            $tmp['ad_ptype'] = $banner['link_type'];
            $tmp['title'] = $banner['title'];
            $tmp['head'] = $banner['title'];
            $tmp['link'] = $banner['link'];
            $tmp['img'] = $banner['img'];
            $tmp['icon'] = $banner['img'];
            $tmp['img3'] = $banner['img'];
            $tmp['start_time'] = $banner['day_id'];
            $tmp['end_time'] = $banner['day_id'];
            $tmp['status'] = $banner['status'];
            $tmp['hits'] = 2;
            $ads[] = $tmp;
        }
        return $ads;
    }
    
}
