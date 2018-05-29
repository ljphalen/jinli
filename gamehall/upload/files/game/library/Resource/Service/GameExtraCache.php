<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏附加属性缓存。
 * 所有数据hash存储
 * @author fanch
 *
 */
class Resource_Service_GameExtraCache extends Common_Service_Base{

    /**
     * 根据游戏id批量获取游戏附加属性
     * @param $gameIds
     * @return array
     */
    public static function getExtraByIds($gameIds){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hMget($cacheKey, $gameIds);
        $cacheData  = self::filterEmptyData($cacheData);
        $diffGameIds = self::getDiffGameIds($cacheData,$gameIds);
        if(count($diffGameIds)){ //tbd
            $diffGameData = self::getGamesExtra($diffGameIds);
            $cacheObj->hMset($cacheKey, $diffGameData);
            $cacheData = $cacheData + $diffGameData;
        }
        return $cacheData;
    }

    /**
     * 删除游戏附加属性hash中指定游戏附加数据
     * 游戏下线执行
     * @param $gameId
     * @return bool
     */
    public static function delExtraById($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheObj->hDel($cacheKey, $gameId);
        return true;
    }
    /**
     * 获取缓存中所有的元素
     * @return mixed
     */
    public static function getAllExtra(){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $result = $cacheObj->hKeys($cacheKey);
        return $result;
    }

    /**
     * 刷新游戏评测附加属性/
     * v1.5.1之前有用
     * 1 添加编辑评测 (运营后台编辑)
     * @param $gameId
     * @return bool
     */
    public static function refreshGameEvaluation($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['evaluation'] = self::getGameEvaluation($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * 刷新游戏礼包附加信息
     * 1 添加礼包激活码 (运营后台编辑|开发者上传)
     * 2 抢礼包-自动下线 (api接口)
     * 3 编辑礼包-执行更新 (运营后台添加)
     * 4 计划任务-礼包过期 (1天执行一次)
     * @param $gameId
     * @return bool
     */
    public static function refreshGameGift($gameId){ //tbd
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['commonGift'] = Client_Service_Gift::getGameCommonGift($gameId);
            $cacheData['vipGift'] = Client_Service_Gift::getGameVipGift($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * 刷新游戏评分附加属性
     * 1 用户提交评分
     * @param $gameId
     * @return bool
     */
    public static function refreshGameScore($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['score'] = self::getGameScore($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * 刷新游戏免流量附加属性
     * 1 计划任务刷新
     * @param $gameId
     * @return bool
     */
    public static function refreshGameFreedl($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['freedl'] = self::getGameFreedl($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * 刷新单个游戏有奖A券附加属性
     * 1 计划任务来刷新
     * 2 活动编辑
     * 3 异步执行
     * @param $gameId
     * @return bool
     */
    public static function refreshGameRewardAcoupon($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['rewardAcoupon'] = self::getGameRewardAcoupon($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * 刷新单个游戏有奖礼包附加属性
     * 1 计划任务(1小时01分执行一次)
     * 2 后台添加编辑
     * 3 激活码领取使用(接口使用)
     * @param $gameId
     * @return bool
     */
    public static function refreshGameRewardGift($gameId){
        $cacheKey = self::getCacheKey();
        $cacheObj = self::getCache();
        $cacheData = $cacheObj->hGet($cacheKey, $gameId);
        if($cacheData){
            $cacheData = json_decode($cacheData, true);
            $cacheData['rewardGift'] = self::getGameRewardGift($gameId);
            $cacheData = json_encode($cacheData);
            $cacheObj->hSet($cacheKey, $gameId, $cacheData);
        }
        return true;
    }

    /**
     * @param $cacheData
     * @param $gameIds
     * @return array
     */
    private static function getDiffGameIds($cacheData,$gameIds) {
        if(empty($cacheData)){
            return $gameIds;
        }
        $cacheIds = array_keys($cacheData);
        $diffGameIds = array_diff($gameIds, $cacheIds);
        return $diffGameIds;
    }

    /**
     * 获取多条游戏扩展数据
     * @param $gameIds
     * @return array
     */
    private static function getGamesExtra($gameIds){
        $data = array();
        foreach($gameIds as $gameId){
            $extraData = self::getExtraData($gameId);
            if(empty($extraData)){
              continue;
            }
            $data[$gameId]= json_encode($extraData);
        }
        return $data;
    }

    /**
     * 游戏单条游戏扩展数据
     * @param $gameId
     * @return array
     */
    private static function getExtraData($gameId){ //tbd
        $game = Resource_Service_GameData::getBasicInfo($gameId);
        if(!$game){
            return array();
        }
        $result = array(
            'evaluation' => self::getGameEvaluation($gameId),
            'commonGift' => Client_Service_Gift::getGameCommonGift($gameId),
            'vipGift' => Client_Service_Gift::getGameVipGift($gameId),
            'score' => self::getGameScore($gameId),
            'freedl' => self::getGameFreedl($gameId),
            'rewardAcoupon' => self::getGameRewardAcoupon($gameId),
            'rewardGift' => self::getGameRewardGift($gameId),
        );
        return $result;
    }

    /**
     * 获取游戏评测标识
     * @param $gameId
     * @return int
     */
    private static function getGameEvaluation($gameId){
        $result = Client_Service_IndexAdI::getEvaluationByGame($gameId);
        $evalution = $result ? 1 : 0;
        return $evalution;
    }

    /**
     * 获取游戏免流量标识
     * @param $gameId
     * @return string
     */
    private static function getGameFreedl($gameId){
        $data = Freedl_Service_Hd::getActivatedItems(array('id' => 'ASC'));
        if(!$data) return "";
        foreach ($data as $value){
            switch($value['htype']){
                case 1 :
                    $flag = Freedl_Service_Hd::checkFreedlGame($value['id'], $gameId);
                    if($flag) $freedlArr[] = 'cmcc19_' . $value['id'];
                    break;
                case 2 :
                    $flag = Freedl_Service_Cugd::checkFreedlGame($gameId);
                    if($flag) $freedlArr[] = 'cu19_' . $value['id'];
                    break;
            }
        }
        $freedlStr = implode('|', $freedlArr);
        return $freedlStr;
    }

    /**
     * 获取游戏评分数据
     *
     * @param $gameId
     * @return int
     */
    private static function getGameScore($gameId){
        $result = Resource_Service_Score::getByScore(array('game_id' => $gameId));
        $score = $result['score'] ? $result['score'] : 0;
        return $score;
    }

    /**
     * 获取游戏有奖A券
     * @param $gameId
     * @return array
     */
    private static function getGameRewardAcoupon($gameId){
        $gameData = Resource_Service_GameCache::getGameInfoFromCache($gameId);
        $cooperate = $gameData['cooperate'];
        if($cooperate != Resource_Service_Games::COMBINE_GAME){
            return array(0, false);
        }

        $items = Client_Service_TaskHd::getsUnionGamesHds();
        if(!$items){
            return array(0, false);
        }
        $rewordCount = 0;
        foreach($items as $value){
            $ruleContent = json_decode($value['rule_content'],true);
            if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAME_ALL){
                $rewordCount += $ruleContent['denomination'];
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT){
                $isGameExistSubject = self::checkGameInSuject($gameId, $value['subject_id']);
                if ($isGameExistSubject) {
                   $rewordCount += $ruleContent['denomination'];
                }
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST){
                $gameIds = json_decode($value['game_object_addition_info'],true);
                $rewordIds = $gameIds['game_list'];
                if(in_array($gameId, $rewordIds)){
                    $rewordCount += $ruleContent['denomination'];
                }
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST){
                $gameIds = json_decode($value['game_object_addition_info'],true);
                $rewordIds = $gameIds['game_list'];
                if(!in_array($gameId, $rewordIds)){
                    $rewordCount += $ruleContent['denomination'];
                }
            }
       }
       $isSendTicket = $rewordCount ? true : false;
      return array($rewordCount, $isSendTicket);
    }

    /**
     * 判断游戏是否在专题中
     * @param int $gameId
     * @param int $subjectId
     * @return boolean
     */
    private static function checkGameInSuject($gameId, $subjectId){
        $ret = Client_Service_Subject::getOnlineSubject($subjectId);
        if(!$ret) return false;
        $params['subject_id']  = $subjectId;
        $params['game_status'] = 1;
        $params['resource_game_id'] = $gameId;
        list(, $games) = Client_Service_Game::getSubjectBySubjectId($params);
        if(!$games) return false;
        return true;
    }

    /**
     * 获取游戏有奖礼包
     * @param $gameId
     * @return array
     */
    private static function getGameRewardGift($gameId){
        $result = array(
            'install'=>0,
            'loginGame' =>0
        );
        $currentTime = strtotime(date('Y-m-d H:00:00'));
        $parmes= array(
            'game_id' => $gameId,
            'status' => Client_Service_GiftActivity::GIFT_STATE_OPENED,
            'effect_start_time' => array('<=', $currentTime),
            'effect_end_time' => array('>', $currentTime),
        );
        $giftActivityInfos = Client_Service_GiftActivity::getsBy($parmes);
        if(!$giftActivityInfos){
            return $result;
        }
        foreach($giftActivityInfos as $key=>$value){
            if($value['activity_type'] == Client_Service_GiftActivity::INSTALL_DOWNLOAD_GAME_SEND_GIFT){
                $result['install']++;
            } else {
                $result['loginGame']++;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return array
     */
    private static function filterEmptyData($data){
        $result = array();
        foreach($data as $key => $item){
            if($item == false){
                continue;
            }
            $result[$key] = $item;
        }
        return $result;
    }

    /**
     *
     * @return string
     */
    private static function getCacheKey(){
        $keyStr = Util_CacheKey::GAMES_EXTRA;
        return $keyStr;
    }

    /**
     * 获取cache实例
     */
    private static function getCache() {
        return Cache_Factory::getCache();
    }
}