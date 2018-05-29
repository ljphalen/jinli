<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏列表缓存数据
 * @author fanch
 *
 */
class Resource_Service_GameListData {

    const TASK_GAME_ALL_EXPIRE = 86400;
    const GAME_ID_KEY = 'gameid';

    /**
     * 根据游戏ids构造列表数据
     * @param $gameIds
     * @return array
     */
    public static function buildListData($gameIds){
        if (count($gameIds) < 1) {
            return array();
        }
        $items = array();
        foreach($gameIds as $gameId){
            $data = self::getItemData($gameId);
            if(empty($data)){
                continue;
            }
            $items[] = $data;
        }
        return $items;
    }

    /**
     * 更新单条列表数据到缓存
     * 1.新增游戏|修改游戏
     * 2.后台编辑操作
     * 3.附加属性变更
     * 1) 用户评分
     * 2) 后台添加游戏评测
     * 3) 礼包上线、礼包自动下线、礼包过期(天)
     * 4) 免流量(不做处理)
     * 5) 有奖A券
     * 6) 有奖礼包
     * Resource_Service_GameListData::updateListItem($gameId);
     * @param $gameId
     * @return bool
     */
    public static function updateListItem($gameId){
        $gameData = self::getItemData($gameId);
        $obj = self::getListContent();
        $result = $obj->storeListItem(self::GAME_ID_KEY, $gameData);
        Resource_Index_CategoryList::updateCategoryIdxVersion($gameId);
        return $result;
    }


    /**
     * 根据游戏id合集获取对应的列表数据
     * @param $idList
     * @return array|bool
     */
    public static function getList($gameIds){
        if(!is_array($gameIds)){
            return false;
        }
        $obj = self::getListContent();
        $data = $obj->getContent($gameIds);
        return $data;
    }

    /**
     * 根据索引获取分页数据
     * @param $indexKey
     * @param $page
     * @return mixed
     */
    public static function getPage($indexKey, $page){
        $obj = self::getIndexInstance($indexKey);
        return $obj->getPage($page);
    }

    /**
     * A券活动面向所有游戏赠送的A券总数
     * @return mixed
     */
    public static function getGameAllAcoupon(){
        $cacheKey = Util_CacheKey::TASK_GAME_ALL_ACOUPON;
        $obj = self::getCache();
        $data = $obj->get($cacheKey);
        return ($data) ? $data : 0;
    }

    /**
     * 获取单个游戏列表需要存储的数据
     * @param $gameId
     * @return array
     */
    public static function getItemData($gameId){
        $gameInfo = self::getBasicInfo($gameId);
        if(!$gameInfo){
            return array();
        }
        $data = self::fillBasicData($gameInfo);
        $data = self::fillExtraData($data, $gameId);
        return $data;
    }

    /**
     * 删除列表数据中的单条游戏数据
     * 1下线游戏
     * @param $gameId
     * @return bool
     */
    public static function removeItemData($gameId){
        $obj = self::getListContent();
        $result = $obj->removeFromContent($gameId);
        return $result;
    }

    /**
     * 刷新A券活动类型为全部游戏赠送的A券总额
     * 后台有修改
     * @return bool
     */
    public static function refreshGameRewardAcoupon(){
        $allAccoupon = 0;
        $tasks = Client_Service_TaskHd::getsUnionGamesHds();
        if($tasks) {
            list($allAccoupon, $gameIds) = self::getTaskData($tasks);
            if($gameIds) {
                self::updateRewardAcoupon($gameIds);
            }
        }
        self::saveGameAllAcoupon($allAccoupon);
        return true;
    }

    /**
     * @param $gameIds
     */
    private static function updateRewardAcoupon($gameIds){
        foreach($gameIds as $gameId){
            $gameData = Resource_Service_Games::getBy(array('id'=>$gameId, 'status'=>1));
            if($gameData['cooperate'] != Resource_Service_Games::COMBINE_GAME){
                continue;
            }
            self::updateListItem($gameId);
        }
    }

    /**
     * 填充游戏基本数据
     * @param $gameInfo
     * @return array
     */
    private static function fillBasicData($gameInfo){
        $data = array(
            'gameid' => $gameInfo['gameid'],
            'name' => $gameInfo['name'],
            'resume' => $gameInfo['resume'],
            'package' => $gameInfo['package'],
            'img' => $gameInfo['img'],
            'link' => $gameInfo['link'],
            'size' => $gameInfo['size'],
        	'version'=>$gameInfo['version'],
        	'versionCode'=>$gameInfo['versionCode'],
            'category' => $gameInfo['category'],
            'categoryid' => $gameInfo['categoryid'],
            'hot'=> $gameInfo['hot'],
            'webp' => $gameInfo['webp'],
        	'downloads'=>$gameInfo['downloads'],
        );
        return $data;
    }

    /**
     * 填充游戏附加属性
     * @param $data
     * @param $gameId
     */
    private static function fillExtraData(&$data, $gameId){
        $data['evaluation'] = self::getGameEvaluation($gameId);
        $data['gift'] = self::getGameGift($gameId);
        $data['score'] = self::getGameScore($gameId);
        $data['freedl'] = self::getGameFreedl($gameId);
        $data['rewardAcoupon'] = self::getGameRewardAcoupon($gameId);
        $data['rewardGift'] = self::getGameRewardGift($gameId);
        return $data;
    }

    /**
     * 获取游戏基本数据
     * @param $gameId
     * @return array
     */
    private static function getBasicInfo($gameId){
        $gameData = Resource_Service_Games::getBy(array('id'=>$gameId, 'status'=>1));
        if(!$gameData){
            return array();
        }

        $versionData = self::getGameVersion($gameId);
        if(!$versionData){
            return array();
        }

        list($categoryId, $categoryTitle) = self::getGameCategory($gameId);
        $icon = $gameData['big_img'] ? $gameData['big_img'] : ($gameData['mid_img'] ? $gameData['mid_img'] : $gameData['img']);
        $data = array(
            'gameid' => $gameId,
            'img' => $icon,
            'webp' => $gameData['webp'],
            'name' => html_entity_decode($gameData['name']),
            'resume' => html_entity_decode($gameData['resume']),
            'package' => $gameData['package'],
            'link' => $versionData['link'],
            'size' => $versionData['size'],
        	'version'=>$versionData['version'],
            'versionCode'=>$versionData['version_code'],   
            'category' => $categoryTitle,
            'categoryid' => $categoryId,
            'hot'=> self::getGameHotMark($gameData['hot']),
        	'downloads'=>$gameData['download_status']?$gameData['default_downloads']:$gameData['downloads'],
        );
        
        return $data;
    }

    /**
     * @param $gameId
     * @return array|bool|mixed
     */
    private static function getGameVersion($gameId){
        $versionData = Resource_Service_Games::getGameVersionInfo($gameId);
        if(!$versionData){
            return array();
        }
        return $versionData;
    }

    /**
     * @param $gameId
     * @return string
     */
    private static function getGameCategory($gameId){
        $categoryData =  Resource_Service_GameCategory::getBy(array('game_id'=>$gameId, 'level'=>1, 'status'=>1));
        if(!$categoryData){
            return array('', '');
        }
        $attributeData = Resource_Service_Attribute::getBy(array('id'=>$categoryData['parent_id']));
        return array($attributeData['id'], $attributeData['title']);
    }

    /**
     * @param $id
     * @return int|string
     */
    private static function getGameHotMark($id) {
    	if(!$id) return 0;
    	$ret = Resource_Service_Attribute::get($id);
    	return intval($ret['parent_id']);
    }

    /**
     * 获取游戏评测Id
     * @param $gameId
     * @return int
     */
    private static function getGameEvaluation($gameId){
        $evalutionId = Client_Service_IndexAdI::getEvaluationByGame($gameId);
        return ($evalutionId) ? $evalutionId : 0;
    }

    /**
     * 获取游戏礼包标识
     * @param $gameId
     * @return int
     * 入口检测法刷新游戏礼包
     */
    private static function getGameGift($gameId){
        $result = Client_Service_IndexAdI::haveGiftByGame($gameId);
        $gift = $result ? 1 : 0;
        return $gift;
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
     * 计算有去掉A券活动中面向全部游戏总送的面额
     * @param $gameId
     * @return array
     */
    private static function getGameRewardAcoupon($gameId){
        $gameData = Resource_Service_Games::getBy(array('id'=>$gameId, 'status'=>1));
        $cooperate = $gameData['cooperate'];
        if($cooperate != Resource_Service_Games::COMBINE_GAME){
            return array(0, false);
        }

        $items = Client_Service_TaskHd::getsUnionGamesHds();
        if(!$items){
            return array(0, false);
        }
        $isSendTicket = false;
        $rewordCount = 0;
        $gameAllAcoupon = 0;
        foreach($items as $value){
            $ruleContent = json_decode($value['rule_content'],true);
            if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAME_ALL){
                $isSendTicket = true;
                $gameAllAcoupon += $ruleContent['denomination'];
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT){
                $isGameExistSubject = self::checkGameInSuject($gameId, $value['subject_id']);
                if ($isGameExistSubject) {
                    $isSendTicket = true;
                    $rewordCount += $ruleContent['denomination'];
                }
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST){
                $gameIds = json_decode($value['game_object_addition_info'],true);
                $rewordIds = $gameIds['game_list'];
                if(in_array($gameId, $rewordIds)){
                    $isSendTicket = true;
                    $rewordCount += $ruleContent['denomination'];
                }
            }else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST){
                $gameIds = json_decode($value['game_object_addition_info'],true);
                $rewordIds = $gameIds['game_list'];
                if(!in_array($gameId, $rewordIds)){
                    $isSendTicket = true;
                    $rewordCount += $ruleContent['denomination'];
                }
            }
        }
        self::saveGameAllAcoupon($gameAllAcoupon);
        return array($rewordCount, $isSendTicket);
    }

    /**
     * 保存A券活动面向所有游戏赠送的A券总数
     * @param $data
     */
    private static function saveGameAllAcoupon($data){
        $cacheKey = Util_CacheKey::TASK_GAME_ALL_ACOUPON;
        $obj = self::getCache();
        $obj->set($cacheKey, $data, 2 * self::TASK_GAME_ALL_EXPIRE);
    }

    /**
     * 通过专题id获取游戏id
     * @param $subjectId
     * @return array
     */
    private static function getGameIdsBySubject($subjectId){
        $params['subject_id']  = $subjectId;
        $params['game_status'] = 1;
        list(, $games) = Client_Service_Game::getSubjectBySubjectId($params);
        if(!$games) {
            return array();
        }
        $games = Common::resetKey($games, 'resource_game_id');
        return array_keys($games);
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
     * @param $tasks
     * @param $gameAllAccoupon
     * @param $data
     * @return array
     * 缺少排除游戏
     */
    private static function getTaskData($tasks) {
        $acoupon = 0;
        $gameIdArr = array();
        foreach ($tasks as $value) {
            $ruleContent = json_decode($value['rule_content'], true);
            if ($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAME_ALL) {
                $acoupon += $ruleContent['denomination'];
            } else if($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_SINGLE_SUBJECT) {
                $gameIds = self::getGameIdsBySubject($value['subject_id']);
                $gameIdArr = $gameIdArr + $gameIds;
            } else if ($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST) {
                $listGameIds = json_decode($value['game_object_addition_info'], true);
                $gameIds = $listGameIds['game_list'];
                $gameIdArr = $gameIdArr + $gameIds;
            } else if ($value['game_object'] == Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST) {
                $listGameIds = json_decode($value['game_object_addition_info'], true);
                $gameIds = self::getExcludeGameIds($listGameIds);
                $gameIdArr = $gameIdArr + $gameIds;
            }
        }
        $gameIdArr = array_unique($gameIdArr);
        return array($acoupon, $gameIdArr);
    }

    /**
     * 获取排除后的联运游戏Id
     * @param $excludeGameIds
     * @return array
     */
    private static function getExcludeGameIds($excludeGameIds){
        $params=array(
            'status'=>1,
            'id'=>array('NOT IN',$excludeGameIds),
            'cooperate' => Resource_Service_Games::COMBINE_GAME
        );
        $gameData = Resource_Service_Games::getsBy($params);
        $gameData = Common::resetKey($gameData, 'id');
        $gameIds = array_keys($gameData);
        return $gameIds;
    }

    /**
     * @return mixed
     */
    private static function getListContent(){
        $contentKey = Util_CacheKey::GAME_LIST_CONTENT_KEY;
        return new Cache_ListContent($contentKey);
    }

    /**
     * @param $contentKey
     * @return mixed
     */
    public static function getIndexInstance($indexKey) {
        $contentKey = Util_CacheKey::GAME_LIST_CONTENT_KEY;
        return new Cache_ListIndex($contentKey, $indexKey);
    }

    /**
     * @return object
     * @throws Exception
     */
    private static function getCache() {
        return Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
    }
}