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
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
        $data = Util_CacheForApi::getCache($api, func_get_args());
        if($data === false) {
            $data = self::getClientBannerData($clientVersion);
            Util_CacheForApi::updateCache($api, func_get_args(), $data);
        }
        return $data;
    }
    
    private static function getClientBannerData($clientVersion) {
        $bannerList = self::getBannerData();
        $data = self::makeClientBannerData($bannerList, func_get_args());
        return $data;
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
            $banners[] = array(
                Util_JsonKey::VIEW_TYPE => Game_Api_Util_RecommendListUtil::getViewType($banner['link_type']),
                Util_JsonKey::TITLE => html_entity_decode($banner['title'], ENT_QUOTES),
                Util_JsonKey::CONTENT => html_entity_decode($banner['title'], ENT_QUOTES),
                Util_JsonKey::IMAGE_URL => Common::getAttachPath().$img,
                Util_JsonKey::PARAM => Game_Api_Util_RecommendListUtil::getParams($params),
                Util_JsonKey::SOURCE => "",
                'ad_id' => $banner['id']
            );
        }
        $data['slideItems'] = $banners;
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientBannerCacheData() {
        $bannerList = self::getBannerData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
        $keys = Util_CacheForApi::getValidKeys($api);
        
//         $keys = array();
//         $keys[] = array('args' => array("1.5.5"));
//         $keys[] = array('args' => array("1.5.8"));
//         $keys[] = array('args' => array("1.5.7"));
        
        foreach ($keys as $key => $params) {
            $args = $params['args'];
            $data = self::makeClientBannerData($bannerList, $args);
            Util_CacheForApi::updateCache($api, $args, $data);
        }
    }
    
    /**取旧版本数据结构*/
    public static function getOldVersionBannerData() {
        $ads = array();
        $bannerList = self::getBannerData();
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
        $bannerList = self::getBannerData();
        foreach ($bannerList as $banner) {
            if($banner['link_type'] == Game_Service_Util_Link::LINK_SUBJECT && $banner['link'] == $subjectId) {
                return true;
            }
        }
        return false;
    }
    
    public static function hdIsShowing($hdId) {
        $bannerList = self::getBannerData();
        foreach ($bannerList as $banner) {
            if($banner['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY && $banner['link'] == $hdId) {
                return true;
            }
        }
        return false;
    }
	
}
