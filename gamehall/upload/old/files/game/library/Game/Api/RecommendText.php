<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页文字公告
 * Game_Api_RecommendText
 * @author wupeng
 */
class Game_Api_RecommendText {
    
    /**获取客户端文字公告*/
    public static function getClientText() {
        $data = self::getClientTextCacheData();
        return $data;
    }

    private static function getClientTextCacheData() {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
	    $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_TEXT_AD);
	    $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getClientTextRedisData();
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getClientTextRedisData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_TEXT_AD);
        $data = Util_CacheKey::getCache($api, func_get_args());
        if($data === false) {
            $data = self::getClientTextData();
            Util_CacheKey::updateCache($api, func_get_args(), $data);
        }
        return $data;
    }
    
    private static function getClientTextData() {
        $text = self::getRecommendTextData();
        $data = self::makeClientTextData($text);
        return $data;
    }
    
    private static function getTextData() {
        $day_id = strtotime(date("Y-m-d"));
        $searchParams = array(
            'day_id' => $day_id,
            'status' => Game_Service_RecommendText::STATUS_OPEN
        );
        $text = Game_Service_RecommendText::getRecommendTextBy($searchParams);
        return $text;
    }
    
    private static function makeClientTextData($text) {
        $texts = array();
        $data['activityItem'] = $texts;
        $pos = 1;
        $adtype = "ad3";
        
        if($text) {
           if ($text['link_type'] == Game_Service_Util_Link::LINK_URL) { // 链接单独处理
                $params = Game_Api_Util_RecommendListUtil::getParamsByLinkUrl($text['link'], $text['title'], $adtype, $pos);
            } else {
                $params = Game_Api_Util_RecommendListUtil::getClientParamsByText($text);
            }
            if($params) {
                $pos++;
                $texts[] = array(
                    Util_JsonKey::VIEW_TYPE => $params['viewType'] ? $params['viewType'] : Game_Api_Util_RecommendListUtil::getViewType($text['link_type']),
                    Util_JsonKey::TITLE => html_entity_decode($text['title'], ENT_QUOTES),
                    Util_JsonKey::CONTENT => html_entity_decode($text['title'], ENT_QUOTES),
                    Util_JsonKey::IMAGE_URL => "",
                    Util_JsonKey::PARAM => Game_Api_Util_RecommendListUtil::getParams($params),
                    Util_JsonKey::SOURCE => 'homeevent',
                    'ad_id' => $text['id']
                );
            }
        }
        $data['activityItem'] = $texts;
        return $data;
    }
    
    /**服务端主动刷新缓存*/
    public static function updateClientTextCacheData() {
        $text = self::reloadRecommendTextData();
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_TEXT_AD);
        $data = self::makeClientTextData($text);
        Util_CacheKey::updateCache($api, array(), $data);
    }

    /**取旧版本数据结构*/
    public static function getOldVersionTextData() {
        $ads = array();
        $text = self::getRecommendTextData();
        if($text) {
            $ad['id'] = $text['id'];
            $ad['sort'] = 0;
            $ad['ad_type'] = 8;
            $ad['ad_ptype'] = $text['link_type'];
            $ad['title'] = $text['title'];
            $ad['head'] = $text['title'];
            $ad['link'] = $text['link'];
            $ad['img'] = "";
            $ad['icon'] = "";
            $ad['img3'] = "";
            $ad['start_time'] = $text['day_id'];
            $ad['end_time'] = $text['day_id'];
            $ad['status'] = $text['status'];
            $ad['hits'] = 1;
            $ads [] = $ad;
        }
        return $ads;
    }
    
    public static function gameIsShowing($gameId) {
        $text = self::getRecommendTextData();
        return $text && $text['link_type'] == Game_Service_Util_Link::LINK_CONTENT && $text['link'] == $gameId;
    }
    
    public static function subjectIsShowing($subjectId) {
        $text = self::getRecommendTextData();
        return $text && $text['link_type'] == Game_Service_Util_Link::LINK_SUBJECT && $text['link'] == $subjectId;
    }
    
    public static function hdIsShowing($hdId) {
        $text = self::getRecommendTextData();
        return $text && $text['link_type'] == Game_Service_Util_Link::LINK_ACTIVITY && $text['link'] == $hdId;
    }

    private static function getRecommendTextData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $text = Util_CacheKey::getHCache($api, array(), 'text');
        if($text === false) {
            $text = self::getTextData();
            Util_CacheKey::updateHCache($api, array(), 'text', $text);
        }
        return $text;
    }
    
    private static function reloadRecommendTextData() {
        $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DATA);
        $text = self::getTextData();
        Util_CacheKey::updateHCache($api, array(), 'text', $text);
        return $text;
    }
    
}
