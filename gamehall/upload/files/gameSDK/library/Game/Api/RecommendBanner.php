<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页轮播图
 * Game_Api_RecommendBanner
 * @author wupeng
 */
class Game_Api_RecommendBanner {
    
    /**获取客户端轮播图*/
    public static function getClientBanner($clientVersion="1.5.5") {
        if(Common::isAfterVersion($clientVersion, '1.5.8')) {
            $clientVersion = "1.5.8";
        }else if(Common::isAfterVersion($clientVersion, '1.5.7')) {
            $clientVersion = "1.5.7";
        }else if(Common::isAfterVersion($clientVersion, '1.5.6')) {
            $clientVersion = "1.5.6";
        }else{
            $clientVersion = "1.5.5";
        }
        $data = self::getClientBannerCacheData($clientVersion);
        return $data;
    }

    private static function getClientBannerCacheData($clientVersion) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getClientBannerRedisData($clientVersion);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getClientBannerRedisData($clientVersion) {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
        $args = func_get_args();
        $data = Util_Api_Cache::getCache($api, $args);
        if($data === false) {
            $data = self::getClientBannerData($clientVersion);
            Util_Api_Cache::updateCache($api, $args, $data);
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
        $data = self::makeClientBannerData($bannerList, func_get_args());
        return $data;
    }
    
    private static function makeClientBannerData($bannerList, $args) {
        $banners = array();
        $data['slideItems'] = $banners;
        if(count($args) < 1) return $data;
        $clientVersion = $args[0];
        if(Common::isAfterVersion($clientVersion, '1.5.7')) {
            $img3 = true;
        }else if(Common::isAfterVersion($clientVersion, '1.5.6')) {
            $img2 = true;
        }
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
            $img = ($img3 && $banner['img3']) ? $banner['img3'] : (($img2 && $banner['img2']) ? $banner['img2'] : $banner['img1']);
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
                $tmp['gameId'] = $params['gameId'];
            }
            $banners[] = $tmp;
        }
        $data['slideItems'] = $banners;
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientBannerCacheData() {
        $bannerList = self::reloadRecommendBannerData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
        $keys = Util_Api_Cache::getValidKeys($api);
        foreach ($keys as $key => $args) {
            if(count($args) != 1) {
                Util_Api_Cache::updateCache($api, $args, array());
                continue;
            }
            $data = self::makeClientBannerData($bannerList, $args);
            Util_Api_Cache::updateCache($api, $args, $data);
        }
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
            $tmp['img'] = $banner['img1'];
            $tmp['icon'] = $banner['img2'];
            $tmp['img3'] = $banner['img3'];
            $tmp['start_time'] = $banner['day_id'];
            $tmp['end_time'] = $banner['day_id'];
            $tmp['status'] = $banner['status'];
            $tmp['hits'] = 1;
            $ads[] = $tmp;
        }
        return $ads;
    }
    
    public static function subjectIsShowing($subjectId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_SUBJECT, $subjectId);
    }
    
    public static function hdIsShowing($hdId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_ACTIVITY, $hdId);
    }
    
    public static function gameIsShowing($gameId) {
        return self::linkIsShowing(Game_Service_Util_Link::LINK_CONTENT, $gameId);
    }
    
    private static function linkIsShowing($linkType, $link) {
        $bannerList = self::getRecommendBannerData();
        foreach ($bannerList as $banner) {
            if($banner['link_type'] == $linkType && $banner['link'] == $link) {
                return true;
            }
        }
        return false;
    }
    
    private static function getBannerData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_RecommendBanner::STATUS_OPEN
        );
        $bannerList = Game_Service_RecommendBanner::getRecommendBannersBy($searchParams);
        return $bannerList;
    }

    private static function getRecommendBannerData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $list = Util_CacheKey::getHCache($api, array(), 'banner');
        if($list === false) {
            $list = self::getBannerData();
            Util_CacheKey::updateHCache($api, array(), 'banner', $list);
        }
        return $list;
    }
    
    private static function reloadRecommendBannerData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $list = self::getBannerData();
        Util_CacheKey::updateHCache($api, array(), 'banner', $list);
        return $list;
    }
    
}
