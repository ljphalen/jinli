<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游推荐信息
 * Game_Service_GameWebRecommend
 * @author wupeng
 */
class Game_Service_GameWebRecommend {
    const BUTTON_CONFIG = 'WEBGAME_BUTTON_CONFIG';

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    
    public static $status_list = array(
        self::STATUS_CLOSE => "关闭",
        self::STATUS_OPEN => "开启"
    );

	public static function getGameWebRecommendListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getGameWebRecommendBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}
	
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	public static function getGameWebRecommend($day_id) {
		if (!$day_id) return null;		
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateGameWebRecommend($data, $day_id) {
		if (!$day_id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteGameWebRecommend($day_id) {
		if (!$day_id) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteGameWebRecommendList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('day_id', $keyList);
	}

	public static function addGameWebRecommend($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkNewField($data);
		return self::getDao()->insert($dbData);
	}

	private static function checkField($data) {
		$dbData = array();
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}

	public static function checkNewField($data) {
	    $record = array();
	    $record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
	    $record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
	    return $record;
	}
	
	private static function getDao() {
		return Common::getDao("Game_Dao_GameWebRecommend");
	}
	
	public static function saveRecommend($day_id, $bannerList, $recommendList) {
	    Common_Service_Base::beginTransaction();
	    try {
	        self::saveRecommendBanner($day_id, $bannerList);
	        self::saveRecommendList($day_id, $recommendList);
	        Common_Service_Base::commit();
	        return true;
	    } catch (Exception $e) {
	        Common_Service_Base::rollBack();
	    }
	    return false;
	}
	
	private static function saveRecommendBanner($day_id, $bannerList) {
	    $oldBannerList = array();
	    $searchParams = array('day_id' => $day_id);
	    $banners = Game_Service_GameWebRecBanner::getGameWebRecBannerListBy($searchParams);
	    foreach ($banners as $banner) {
	        $oldBannerList[$banner['id']] = $banner;
	    }
	    foreach ($bannerList as $banner) {
	        if($banner['status'] == Game_Service_GameWebRecBanner::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($banner['link_type'], $banner['link'])) {
	                $banner['status'] = Game_Service_GameWebRecBanner::STATUS_CLOSE;
	            }
	        }
	        if($banner['id']) {
	            $oldBanner = $oldBannerList[$banner['id']];
	            unset($oldBannerList[$banner['id']]);
	            $updateParams = Game_Manager_WebRecommendList::getUpdateParams($banner, $oldBanner);
	            if ($updateParams) {
	                Game_Service_GameWebRecBanner::updateGameWebRecBanner($updateParams, $banner['id']);
	            }
	        }else{
	            Game_Service_GameWebRecBanner::addGameWebRecBanner($banner);
	        }
	    }
	    foreach ($oldBannerList as $banner) {
	        Game_Service_GameWebRecBanner::deleteGameWebRecBanner($banner['id']);
	    }
	}
	
	private static function saveRecommendList($day_id, $recommendList) {
	    $oldRecommendList = array();
	    $searchParams = array('day_id' => $day_id);
	    $recommends = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
	    foreach ($recommends as $recommend) {
	        $oldRecommendList[$recommend['id']] = $recommend;
	    }
	    foreach ($recommendList as $info) {
	        $recommend = $info[Game_Manager_WebRecommendList::WEBGAME_INFO];
	        $gameList = $info[Game_Manager_WebRecommendList::WEBGAME_GAMES];
	        $image = $info[Game_Manager_WebRecommendList::WEBGAME_IMAGE];
	        $openList = $info[Game_Manager_WebRecommendList::WEBGAME_OPEN];
	        
	        if(! $recommend['id']) {
	            $recommendId = Game_Service_GameWebRec::addGameWebRec($recommend);
	        }else{
	            $recommendId = $recommend['id'];
	            $updateParams = Game_Manager_WebRecommendList::getUpdateParams($recommend, $oldRecommendList[$recommendId]);
	            if($updateParams) {
	                Game_Service_GameWebRec::updateGameWebRec($updateParams, $recommendId);
	            }
	            unset($oldRecommendList[$recommendId]);
	        }
	        self::saveGameList($recommendId, $gameList);
	        self::saveOpenList($recommendId, $openList);
	        self::saveImage($recommendId, $image);
	    }
	    if($oldRecommendList) {
	        $keyList = array_keys($oldRecommendList);
	        Game_Service_GameWebRec::deleteGameWebRecList($keyList);
	        $params = array('recommend_id' => array('IN', $keyList));
	        Game_Service_GameWebRecImgs::deleteGameWebRecImgsBy($params);
	        Game_Service_GameWebRecGames::deleteGameWebRecGamesBy($params);
	        Game_Service_GameWebRecOpen::deleteGameWebRecOpenBy($params);
	    }
	}

	private static function saveImage($recommendId, $image) {
	    $recommend = Game_Service_GameWebRec::getGameWebRec($recommendId);
    	$oldImg = Game_Service_GameWebRecImgs::getGameWebRecImgsBy(array('recommend_id' => $recommendId));
    	if(! $oldImg && !$image) return true;
    	if($image) {
    	    if(! $image['recommend_id']) {
    	        $image['recommend_id'] = $recommendId;
    	    }
    	    if($recommend['status'] == Game_Service_GameWebRec::STATUS_OPEN) {
    	        if(Game_Service_Util_Link::checkLinkValue($image['link_type'], $image['link'])) {
    	            Game_Service_GameWebRec::updateGameWebRec(array('status' => Game_Service_GameWebRec::STATUS_CLOSE), $recommendId);
    	        }
    	    }
    	
    	    if($image['id']) {
    	        $updateParams = Game_Manager_WebRecommendList::getUpdateParams($image, $oldImg);
    	        if ($updateParams) {
    	            Game_Service_GameWebRecImgs::updateGameWebRecImgs($updateParams, $image['id']);
    	        }
    	        if($image['id'] == $oldImg['id']) $oldImg = null;
    	    }else{
    	        Game_Service_GameWebRecImgs::addGameWebRecImgs($image);
    	    }
    	}
    	if($oldImg) {
    	    Game_Service_GameWebRecImgs::deleteGameWebRecImgs($oldImg['id']);
    	}
    	return true;
	}
	
	private static function saveGameList($recommendId, $gameList) {
	    $oldList = Game_Service_GameWebRecGames::getGameWebRecGamesListBy(array('recommend_id' => $recommendId));
	    if(! $oldList && ! $gameList) return true;
	    $oldList = Common::resetKey($oldList, 'game_id');
	    $addList = array();
	    $updateList = array();
	    foreach ($gameList as $game) {
	        if(! $game['recommend_id']) {
	            $game['recommend_id'] = $recommendId;
	        }
	        $gameInfo = Resource_Service_Games::getResourceByGames($game['game_id']);
	        $game['game_status'] = $gameInfo ? Resource_Service_Games::STATE_ONLINE : Resource_Service_Games::STATE_OFFLINE;
	        if($oldList[$game['game_id']]) {
	            $updateParams = Game_Manager_WebRecommendList::getUpdateParams($game, $oldList[$game['game_id']]);
	            if ($updateParams) {
	                $updateList[] = $game;
	            }
	            unset($oldList[$game['game_id']]);
	        }else{
	            $addList[] = $game;
	        }
	    }
	    if($oldList) {
	        $deleteList = Common::resetKey($oldList, 'id');
	        $deleteList = array_keys($deleteList);
	        Game_Service_GameWebRecGames::deleteGameWebRecGamesList($deleteList);
	    }
	    if($addList) {
	        Game_Service_GameWebRecGames::addMutiGameWebRecGames($addList);
	    }
	    foreach ($updateList as $game) {
	        Game_Service_GameWebRecGames::updateGameWebRecGames($game, $game['id']);
	    }
	    return true;
	}
	
	private static function saveOpenList($recommendId, $openList) {
        $oldList = Game_Service_GameWebRecOpen::getGameWebRecOpenListBy(array('recommend_id' => $recommendId));
        if(! $oldList && ! $openList) return true;
        $oldList = Common::resetKey($oldList, 'open_id');
        $addList = array();
        $updateList = array();
        foreach ($openList as $open) {
            if(! $open['recommend_id']) {
                $open['recommend_id'] = $recommendId;
            }
            $openInfo = Game_Service_GameOpen::getGameOpen($open['open_id']);
            $open['open_status'] = $openInfo['status'];
            if($oldList[$open['open_id']]) {
                $updateParams = Game_Manager_WebRecommendList::getUpdateParams($open, $oldList[$open['open_id']]);
                if ($updateParams) {
                    $updateList[] = $open;
                }
                unset($oldList[$open['open_id']]);
            }else{
                $addList[] = $open;
            }
        }
        if($oldList) {
            $deleteList = Common::resetKey($oldList, 'id');
            $deleteList = array_keys($deleteList);
            Game_Service_GameWebRecOpen::deleteGameWebRecOpenList($deleteList);
        }
        if($addList) {
            Game_Service_GameWebRecOpen::addMutiGameWebRecOpen($addList);
        }
        foreach ($updateList as $open) {
            Game_Service_GameWebRecOpen::updateGameWebRecOpen($open, $open['id']);
        }
        return true;
	}
	
	public static function copyRecommendListByDayId($day_id, $to_day_id) {
	    Common_Service_Base::beginTransaction();
	    try {
	        $flg = self::copyBanner($day_id, $to_day_id);
	        if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
	        }
	         
	        $flg = self::copyList($day_id, $to_day_id);
	        if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
	        }
	        
	        $data = array('day_id' => $to_day_id);
	        $flg = self::addGameWebRecommend($data);
	        if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
	        }
	         
	        Common_Service_Base::commit();
	        return true;
	    } catch (Exception $e) {
	        Common_Service_Base::rollBack();
	    }
	    return false;
	}

	private static function copyBanner($day_id, $to_day_id) {
	    $flg = true;
	    $newBannerList = array();
	    $searchParams = array('day_id' => $day_id);
	    $bannerList = Game_Service_GameWebRecBanner::getGameWebRecBannerListBy($searchParams);
	    foreach ($bannerList as $banner) {
	        $banner['day_id'] = $to_day_id;
	        unset($banner['id']);
	        unset($banner['create_time']);
	        $newBannerList[] = $banner;
	    }
	    if($newBannerList) {
	        $flg = Game_Service_GameWebRecBanner::addMutiGameWebRecBanner($newBannerList);
	    }
	    return $flg;
	}
	
	private static function copyList($day_id, $to_day_id) {
	    $searchParams = array('day_id' => $day_id);
	    $recommendList = Game_Service_GameWebRec::getGameWebRecListBy($searchParams);
	    if($recommendList) {
	        foreach ($recommendList as $recommend) {
	            if($recommend['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
	                continue;
	            }
	            $games = Game_Service_GameWebRecGames::getGameWebRecGamesListBy(array("recommend_id" => $recommend['id']));
	            $img = Game_Service_GameWebRecImgs::getGameWebRecImgsBy(array("recommend_id" => $recommend['id']));
	            $recommend['day_id'] = $to_day_id;
	            unset($recommend['id']);
	            unset($recommend['create_time']);
	            $recommendId = Game_Service_GameWebRec::addGameWebRec($recommend);
	            if(! $recommendId) {
	                return false;
	            }
	            $newRecommendGameList = array();
	            foreach ($games as $game) {
	                $game['recommend_id'] = $recommendId;
	                unset($game['id']);
	                $newRecommendGameList[] = $game;
	            }
	            if($newRecommendGameList) {
	                $flg = Game_Service_GameWebRecGames::addMutiGameWebRecGames($newRecommendGameList);
	                if(! $flg) return false;
	            }
	            if($img) {
	                $img['recommend_id'] = $recommendId;
	                unset($img['id']);
	                $flg = Game_Service_GameWebRecImgs::addGameWebRecImgs($img);
	                if(! $flg) return false;
	            }
	        }
	    }
	    return true;
	}
	
	public static function updateGameStatus($gameId, $status) {
	    $listFlg = Game_Api_WebRecommendList::gameIsShowing($gameId);
	    //更新推荐列表中游戏的状态字段
	    Game_Service_GameWebRecGames::updateGameStatus($gameId, $status);
	    if($status == Resource_Service_Games::STATE_OFFLINE) {
	        $bannerFlg = Game_Api_WebRecommendBanner::gameIsShowing($gameId);
	        $searchParams['link_type'] = Game_Service_Util_Link::LINK_CONTENT;
	        $searchParams['link'] = $gameId;
	        $data = array('status' => Game_Service_GameWebRec::STATUS_CLOSE);
	        Game_Service_GameWebRecBanner::updateGameWebRecBannerBy($data, $searchParams);
	        $imgList = Game_Service_GameWebRecImgs::getGameWebRecImgsListBy($searchParams);
	        if($imgList) {
	            $recommendIdList = Common::resetKey($imgList, 'recommend_id');
	            $searchParams = array('id' => array('IN', array_keys($recommendIdList)));
	            Game_Service_GameWebRec::updateGameWebRecBy($data, $searchParams);
	        }
	        if ($bannerFlg) {
	            Game_Api_WebRecommendBanner::updateClientBannerCacheData();
	        }
	    }

	    $searchParams = array('game_id' => $gameId);
	    $data = array('game_status' => $status);
	    Game_Service_GameOpen::updateGameOpenBy($data, $searchParams);

	    if ($listFlg) {
	        Game_Api_WebRecommendList::updateClientRecommendCacheData();
	    }
	    
	    $curTime = time();
	    $curDate = Util_TimeConvert::floor($curTime, Util_TimeConvert::RADIX_DAY);
	    $startTime = Util_TimeConvert::addDays(-4, $curDate);
	    $endTime = Util_TimeConvert::addDays(5, $curDate);
	    $searchParams = array();
	    $searchParams['open_time'] = array(
	        array('>=', $startTime),
	        array('<=', $endTime)
	    );
	    $searchParams['game_id'] = $gameId;
	    $open = Game_Service_GameOpen::getGameOpenBy($searchParams);
	    if($open) {
	        Game_Api_WebRecommendOpen::updateOpenListCache();
	    }
	}
	
	
	
	/**
	 * 专题关闭要更新推荐状态
	 */
	public static function updateSubjectStatus($subjectId, $status) {
	    if($status != Client_Service_Subject::SUBJECT_STATUS_CLOSE) {
	        return;
	    }
	    $searchParams['link_type'] = Game_Service_Util_Link::LINK_SUBJECT;
	    $searchParams['link'] = $subjectId;
	    $bannerFlg = $bannerFlg = Game_Api_WebRecommendBanner::subjectIsShowing($subjectId);
	    $recommendFlg = Game_Api_WebRecommendList::subjectIsShowing($subjectId);
	    
	    $data = array('status' => Game_Service_GameWebRec::STATUS_CLOSE);
	    Game_Service_GameWebRecBanner::updateGameWebRecBannerBy($data, $searchParams);
	    $imgList = Game_Service_GameWebRecImgs::getGameWebRecImgsListBy($searchParams);
	    if($imgList) {
            $recommendIdList = Common::resetKey($imgList, 'recommend_id');
            $searchParams = array('id' => array('IN', array_keys($recommendIdList)));
	        Game_Service_GameWebRec::updateGameWebRecBy($data, $searchParams);
	    }
	    if ($bannerFlg) {
	        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
	    }
	    if ($recommendFlg) {
	        Game_Api_WebRecommendList::updateClientRecommendCacheData();
	    }
	}
	
	/**
	 * 活动关闭要更新推荐状态
	 */
	public static function updateHDStatus($hdId, $status) {
	    if($status != Client_Service_Subject::SUBJECT_STATUS_CLOSE) {
	        return;
	    }
	    $searchParams['link_type'] = Game_Service_Util_Link::LINK_ACTIVITY;
	    $searchParams['link'] = $hdId;
	    $bannerFlg = Game_Api_WebRecommendBanner::hdIsShowing($hdId);
	    $recommendFlg = Game_Api_WebRecommendList::hdIsShowing($hdId);

	    $data = array('status' => Game_Service_GameWebRec::STATUS_CLOSE);
	    Game_Service_GameWebRecBanner::updateGameWebRecBannerBy($data, $searchParams);
	    $imgList = Game_Service_GameWebRecImgs::getGameWebRecImgsListBy($searchParams);

	    if($imgList) {
	        $recommendIdList = Common::resetKey($imgList, 'recommend_id');
	        $searchParams = array('id' => array('IN', array_keys($recommendIdList)));
	        Game_Service_GameWebRec::updateGameWebRecBy($data, $searchParams);
	    }
	    if ($bannerFlg) {
	        Game_Api_WebRecommendBanner::updateClientBannerCacheData();
	    }
	    if ($recommendFlg) {
	        Game_Api_WebRecommendList::updateClientRecommendCacheData();
	    }
	}
	
	
}
