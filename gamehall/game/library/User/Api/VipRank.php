<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * vip排行榜
 * User_Api_VipRank
 * @author wupeng
 */
class User_Api_VipRank {
    
	const LIST_PER_PAGE = 10;
	const DAY_SECONDS = 86400;
	
    public static function getRankListByPage($page) {
        $apcu = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_RANK);
        $key = Util_CacheKey::getKey($api, func_get_args());
        $data = $apcu->get($key);
        if ($data === false) {
            $data = self::getRankListRedisByPage($page);
            $apcu->set($key, $data, 60);
        }
        return $data;
    }

    private static function getRankListRedisByPage($page) {
        $rank = User_Cache_VipRank::getInstance();
        $pageInfo = $rank->getClientPageInfo();
        if($pageInfo === false) {
            $data = self::getClientData();
            $rank->updateClientPageList($data);
            $pageInfo = $rank->getClientPageInfo();
        }
        $pageSize = $pageInfo[Util_Api_ListPage::PAGE_SIZE];
        $pageData = array();
        if($page <= $pageSize) {
            $pageList = $rank->getClientPageList($page);
            foreach ($pageList as $tmp) {
                unset($tmp['uuid']);
                $pageData[] = $tmp;
            }
        }
        $hasnext = $pageSize > $page ? true : false;
        $data = array('list'=>$pageData, 'hasnext'=>$hasnext, 'curpage'=>$page);
        return $data;
    }
    
    private static function getClientData() {
        $rankList = Account_Service_User::getVipRankList();
        $pageData = array();
        $attachPath = Common::getAttachPath();
        foreach ($rankList as $rank) {
            $tmp = array();
            $tmp['uuid'] = $rank['uuid'];
            $tmp['vipRanking'] = $rank['vip_rank'];
            $tmp['uname'] = html_entity_decode($rank['nickname']);
            $tmp['vipLevel'] = $rank['vip'];
            $pageData[$rank['uuid']] = $tmp;
        }
        return $pageData;
    }
	
	public static function updateRankListCache() {
        $rank = User_Cache_VipRank::getInstance();
        $data = self::getClientData();
        $rank->updateClientPageList($data);
	}
	
	public static function updateDbRank() {
	    $ret = Account_Service_User::updateVipRank();
	    self::updateRankListCache();
	    if($ret > 0) {//失败步骤
	        Util_Log::err('User_Api_VipRank', 'vipRank.log', '刷新排行榜失败: ' . $ret);
	        return $ret;
	    }
	}
	
	private static function getUserMaxRank() {
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_MAXRANK);
        $key = Util_CacheKey::getKey($api);
        $cache = Cache_Factory::getCache();
        $maxRank = $cache->get($key);
        if($maxRank === false) {
            $maxRank = self::updateUserMaxRank();
        }
        return $maxRank;
	}
	
	public static function updateUserMaxRank() {
	    $maxRank = Account_Service_User::getUserCounts();
        $api = Util_CacheKey::getApi(Util_CacheKey::VIPCENTER, Util_CacheKey::VIPCENTER_MAXRANK);
        $key = Util_CacheKey::getKey($api);
        $cache = Cache_Factory::getCache();
        $result = $cache->set($key, $maxRank, 90000);
        return $maxRank;
	}
	
	public static function getUserVipRankByUUID($uuid) {
	    $userInfo = Account_Service_User::getUserInfo(array('uuid' => $uuid));
	    return self::getUserVipRankByUserInfo($userInfo);
	}
	
	public static function getUserVipRankByUserInfo($userInfo) {
	    $userRank = $userInfo['vip_rank'] ? $userInfo['vip_rank'] : 0;
	    if($userRank > 0 && $userRank <= User_Config_Vip::MAX_RANK) {
	        return $userRank . '名';
	    }
	    $maxRank = self::getUserMaxRank();
	    if($userRank == 0) {
	        $userRank = $maxRank;
	    }
	    $beforeRank = ($maxRank - $userRank) / $maxRank;
	    $userRank = sprintf("%01.2f", $beforeRank);
	    $userRank = $userRank*100;
	    if($userRank < 1) {
	        $userRank = 1;
	    }
	    return '超过'.$userRank.'%玩家';
	}
	
}
