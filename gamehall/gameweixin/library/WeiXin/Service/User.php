<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class WeiXin_Service_User {
    
    /**
     * 同步所有用户信息
     * (该方法执行非常耗时,初始化数据库的时候用)
     * @author yinjiayan
     * @return number
     */
    public static function syncAllUserInfo() {
        $nextOpenId = '';
        $count = 0;
        do {
            list($openIds, $nextOpenId) = WeiXin_Service_Base::getOpenidList($nextOpenId);
            if ($openIds) {
                foreach ($openIds as $openid) {
                    $user = new WeiXin_Server_User($openid);
                    $user->syncInfo();
                    $count ++;
                }
            }
        } while ($nextOpenId);
        return $count;
    }

    public static function syncUserInfoByOpenId($openId) {
        $user = new WeiXin_Server_User($openId);
        $user->syncInfo();
    }
    
    public static function getGroupNameByGroupId($groupId) {
        return WeiXin_Service_Base::getGroupName($groupId);
    }
    
}
