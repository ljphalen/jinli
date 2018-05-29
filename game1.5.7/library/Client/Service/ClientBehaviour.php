<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author huyuke
 *
 */
class Client_Service_ClientBehaviour {
    /*
       用户启动游戏大厅时客户端必然会请求的接口:
        1 可升级接口 -> ACTION_GET_HIGH_VER_GAME
        update/update

        2 启动页数据 -> ACTION_GET_START_PAGE
        Start/StarImg

        3 栏目数据 -> ACTION_GET_COLUMN
        Column/nava (1.4.8 - 1.5.0 TAB配置)
        Local_Index/frame (1.5.1 栏目和频道列表)
        Local_Index/column (1.5.2后 栏目和频道列表)

        4 轮播图数据 -> ACTION_GET_FIRST_PAGE
        Api/Indexi/turn (1.4.8 - 1.5.0)
        Local_Home/slideAd(1.5.6轮播图)

        5 首页第一屏数据 -> ACTION_GET_FIRST_PAGE
        Api/Indexi/recomend (1.4.8 - 1.5.0)
        Local_Index/indexAd (1.5.1版本)
        Local_Index/chosen (1.5.2 - 1.5.5)
        Local_Home/listFirstPage (1.5.6)

        6 礼包详情 -> ACTION_GET_GIFT_DETAIL
        Gift/detail

        7 游戏下载 -> ACTION_GET_GAME_INFO_IN_GIFT
        Gift/gameinfo
    */
    
    /*
     * 1.同步时间->ACTION_GET_TIME
     * account/gettime
     * 
     * 2.获取指定游戏礼包列表->ACTION_GIFT_lIST
     * /api/gift/index
     * 
     * 3.获取配置->ACTION_SERVER_CONFIG
     * /api/Local_Index/ServerConfig
     * 
     * 4.获取A币A券->ACTION_MY_BALANCE
     * /api/task/myBalance
     */

    const CACHE_EXPIRE = 432000; //five day
    const AN_HOUR = 3600;

    const CLIENT_HALL = 'gn.com.android.gamehall';
    const CLIENT_SDK = 'com.gionee.gsp';

    const ACTION_GET_HIGH_VER_GAME = 'updateGame';
    const ACTION_GET_START_PAGE = 'startPage';
    const ACTION_GET_COLUMN = 'columnData';
    const ACTION_GET_FIRST_PAGE = 'firstPage';
    
    const ACTION_GET_TIME = 'getTime';
    const ACTION_GIFT_lIST = 'giftList';
    const ACTION_SERVER_CONFIG = 'serverConfig';
    const ACTION_MY_BALANCE = 'myBalance';

    const ACTION_GET_GIFT_DETAIL = 'giftDetail';
    const ACTION_GET_GAME_INFO_IN_GIFT = 'gameInfo';

    private $mCache = null;
    private $mHashKey = null;
    private $mClient = null;
    private $mBehaviourHistory = null;

    function Client_Service_ClientBehaviour($imei, $whichClient = self::CLIENT_HALL) {
        if (!$imei) {
            return;
        }
        
        /*
        $this->mCache = Common::getCache();
        $this->mHashKey = Util_CacheKey::CLIENT_BEHAVIOUR . $imei;
        $this->mClient = $whichClient;
        */
    }

    public function saveBehaviours($actionName) {
        if (!$this->mCache) {
            return false;
        }

        if(!$actionName) {
            return false;
        }

        $field = $actionName;
        $value = Common::getTime();
        $this->mCache->hSet($this->mHashKey, $field, $value, self::CACHE_EXPIRE);   
    }

    public function hasLoadedHome($version) {
        if(!$version) {
            return false;
        }

        if(strcasecmp($this->mClient, self::CLIENT_SDK) == 0) {
            return $this->hasLoadedSdkHome($version);
        } else {
            return $this->hasLoadeHallHome($version);
        }
    }

    public function hasLoadedGiftDetail() {
        $giftDetailActionList[self::ACTION_GET_GIFT_DETAIL] = self::AN_HOUR;
        if(strcasecmp($this->mClient, self::CLIENT_SDK) == 0) {
            $giftDetailActionList[self::ACTION_GIFT_lIST] = self::CACHE_EXPIRE;
            $giftDetailActionList[self::ACTION_MY_BALANCE] = 300;
        } else {
            $giftDetailActionList[self::ACTION_GET_GAME_INFO_IN_GIFT] = 300;
        }
        return $this->hasLoaded($giftDetailActionList);
    }

    private function hasLoadeHallHome($version) {
        $homeActionList[self::ACTION_GET_COLUMN] = self::CACHE_EXPIRE;

        if (strnatcmp($version, '1.4.9.s') >= 0) {
            $homeActionList[self::ACTION_GET_HIGH_VER_GAME] = self::CACHE_EXPIRE;
        }

        if (strnatcmp($version, '1.5.0') >= 0) {
            $homeActionList[self::ACTION_GET_FIRST_PAGE] = self::CACHE_EXPIRE;
        }

        if (strnatcmp($version, '1.5.4') >= 0) {
            $homeActionList[self::ACTION_GET_START_PAGE] = self::CACHE_EXPIRE;
        }

        if($this->hasLoaded($homeActionList)) {
            return true;
        }
        if(!$this->mBehaviourHistory) {
            return false;
        }

        $firstActionTime = $this->getFirstHomeActionTime($this->mBehaviourHistory, $homeActionList);

        if($firstActionTime <= 0) {
            return false;
        }

        $currTime = Common::getTime();
        $isOperateQuickly = ($currTime - $firstActionTime < 150) ? true : false;
        if ($isOperateQuickly) {
            return true;
        } else {
            return false;
        }
    }

    private function hasLoadedSdkHome($version) {
        $homeActionList[self::ACTION_SERVER_CONFIG] = self::CACHE_EXPIRE;
        $homeActionList[self::ACTION_GET_TIME] = self::CACHE_EXPIRE;
        return $this->hasLoaded($homeActionList);
    }

    private function hasLoaded($actionList) {
        if (!$this->mCache) {
            return false;
        }
        if (!($this->mCache->exists($this->mHashKey))) {
           $this->debug('', 'hashKey not exits');
            return false;
        }

        $currTime = Common::getTime();
        if (!$this->mBehaviourHistory) {
            $this->mBehaviourHistory = $this->mCache->hGetAll($this->mHashKey);
        }
        $behaviourHistory = $this->mBehaviourHistory;

        foreach ($actionList as $action => $expire) {
            if (!array_key_exists($action, $behaviourHistory)) {
                $this->debug($action, 'action not exits');
                return false;
            }

            $actionTime = intval($behaviourHistory[$action]);
            if($actionTime < ($currTime - $expire)) {
                $this->debug($action, $action . ' lose effectiveness: ' . $actionTime);
                return false;
            }
        }

        return true;
    }

    private function getFirstHomeActionTime($behaviourHistory, $homeActionList) {
        $firstActionTime = 0;

        $homeActionTimes = array();
        foreach($homeActionList as $key => $value) {
            $actionTime = intval($behaviourHistory[$key]);

            if ($actionTime > 0) {
                $homeActionTimes[] = $actionTime;
            }
        }

        if (count($homeActionTimes) > 0) {
            sort($homeActionTimes);
            $firstActionTime = $homeActionTimes[0];
        }

        return $firstActionTime;
    }

    private function debug($action, $msg, $fileName = '_gift_grab.log') {
        $grabLogStatus = Client_Service_Gift::getGrabGiftLogSwitch();
        if($grabLogStatus){
            $debugMsg['hashKey'] = $this->mHashKey;
            $debugMsg['action'] = $action;
            $debugMsg['msg'] = $msg;
            Common::log($debugMsg, date('Y-m-d'). $fileName);
        }
    }
}
