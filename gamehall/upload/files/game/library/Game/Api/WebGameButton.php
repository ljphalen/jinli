<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游频道导航栏
 * @author wupeng
 */
class Game_Api_WebGameButton {
    
    public static function getButtonConfig() {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_BUTTON);
        $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false){
            $data = self::getButtonData();
            $apcu->set($key, $data, 60);
        }
        return $data;
    }
    
    private static function getButtonData() {
        $searchParams = array('module' => Client_Service_Navigation::WEB_GAME, 'status' => Client_Service_Navigation::STATUS_OPEN);
        $sortParams = array('sort' => 'desc');
        $navigationList = Client_Service_Navigation::getNavigationListBy($searchParams, $sortParams);
        $config = array();
        foreach ($navigationList as $navigation) {
            $param = json_decode($navigation['param'], true);
            if(! $param) {
                $param = array('title' => $navigation['title']);
            }
            if($navigation['icon_url']) {
                $navigation['icon_url'] = Common::getAttachPath() . $navigation['icon_url'];
            }else{
                $navigation['icon_url'] = '';
            }
            $config[] = array(
                'title' => $navigation['title'],
                'viewType' => $navigation['view_type'],
                'iconUrl' => $navigation['icon_url'],
                'param' => $param
            );
        }
        $data = array("items" => $config);
        return $data;
    }
    
    private static function updateButtonCache() {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_BUTTON);
        $data = self::getButtonData();
        Util_CacheKey::updateCache($api, func_get_args(), $data, Util_Api_Cache::WEEK);
    }
	
}
