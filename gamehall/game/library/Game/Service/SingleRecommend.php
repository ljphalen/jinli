<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 单机推荐日程
 * Game_Service_SingleRecommend
 * @author wupeng
 */
class Game_Service_SingleRecommend {

	public static function getSingleRecommendListBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}

	public static function getSingleRecommendBy($searchParams, $sortParams = array()) {
	    return self::getDao()->getBy($searchParams, $sortParams);
	}

	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}

	public static function getSingleRecommend($day_id) {
		if (!$day_id) return null;		
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->getBy($keyParams);
	}

	public static function updateSingleRecommend($data, $day_id) {
		if (!$day_id) return false;
		$data = self::checkField($data);
		if (!is_array($data)) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->updateBy($data, $keyParams);
	}

	public static function updateSingleRecommendBy($data, $searchParams) {
	    $data = self::checkField($data);
	    if (!is_array($data)) return false;
	    return self::getDao()->updateBy($data, $searchParams);
	}

	public static function deleteSingleRecommend($day_id) {
		if (!$day_id) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->deleteBy($keyParams);
	}

	public static function deleteSingleRecommendList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('day_id', $keyList);
	}

	public static function addSingleRecommend($data) {
		if (!is_array($data)) return false;
		$data = self::checkNewField($data);
		return self::getDao()->insert($data);
	}

	public static function addMutiSingleRecommend($list) {
	    if (! $list) return false;
	    foreach ($list as $key => $data) {
	        $list[$key] = self::checkNewField($data);
	    }
	    return self::getDao()->mutiFieldInsert($list);
	}

	public static function checkNewField($data) {
	    $record = array();
		$record['day_id'] = isset($data['day_id']) ? $data['day_id'] : 0;
		$record['create_time'] = isset($data['create_time']) ? $data['create_time'] : time();
		return $record;
	}

	private static function checkField($data) {
	    $record = array();
		if(isset($data['day_id'])) $record['day_id'] = $data['day_id'];
		if(isset($data['create_time'])) $record['create_time'] = $data['create_time'];
		return $record;
	}

	private static function getDao() {
		return Common::getDao("Game_Dao_SingleRecommend");
	}

	public static function saveRecommend($day_id, $bannerList, $recommendList) {
	    Common_Service_Base::beginTransaction();
	    try {
	        self::saveRecommendBanner($day_id, $bannerList);
	        self::saveRecommendList($day_id, $recommendList);
	        Common_Service_Base::commit();
	        return true;
	    } catch (Exception $e) {
	        echo (string)$e;
	        Common_Service_Base::rollBack();
	    }
	    return false;
	}

	private static function saveRecommendBanner($day_id, $bannerList) {
	    $oldBannerList = array();
	    $searchParams = array('day_id' => $day_id);
	    $banners = Game_Service_SingleBanner::getSingleBannerListBy($searchParams);
	    $oldBannerList = Common::resetKey($banners, 'id');
	    foreach ($bannerList as $banner) {
	        if($banner['status'] == Game_Service_SingleList::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($banner['link_type'], $banner['link'])) {
	                $banner['status'] = Game_Service_SingleList::STATUS_CLOSE;
	            }
	        }
	        if($banner['id']) {
	            $oldBanner = $oldBannerList[$banner['id']];
	            unset($oldBannerList[$banner['id']]);
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($banner, $oldBanner);
	            if ($updateParams) {
	                Game_Service_SingleBanner::updateSingleBanner($updateParams, $banner['id']);
	            }
	        }else{
	            Game_Service_SingleBanner::addSingleBanner($banner);
	        }
	    }
	    if($oldBannerList) {
	        $deleteList = array_keys($oldBannerList);
	        Game_Service_SingleBanner::deleteSingleBannerList($deleteList);
	    }
	}

	private static function saveRecommendList($day_id, $recommendList) {
	    $oldRecommendList = array();
	    $searchParams = array('day_id' => $day_id);
	    $recommends = Game_Service_SingleList::getSingleListListBy($searchParams);
	    foreach ($recommends as $recommend) {
	        $oldRecommendList[$recommend['id']] = $recommend;
	    }
	    foreach ($recommendList as $info) {
	        $recommend = $info[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	        if(! $recommend['id']) {
	            $recommendId = Game_Service_SingleList::addSingleList($recommend);
	        }else{
	            $recommendId = $recommend['id'];
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($recommend, $oldRecommendList[$recommendId]);
	            if($updateParams) {
	                Game_Service_SingleList::updateSingleList($updateParams, $recommendId);
	            }
	            unset($oldRecommendList[$recommendId]);
	        }
	        $gameList = $info[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES];
	        $image = $info[Game_Manager_SingleGameRecommend::SINGLEGAME_IMAGE];
	        $giftList = $info[Game_Manager_SingleGameRecommend::SINGLEGAME_GIFT];
	        $alone = $info[Game_Manager_SingleGameRecommend::SINGLEGAME_ALONE];

	        self::saveGameList($recommendId, $gameList);
	        self::saveGiftList($recommendId, $giftList);
	        self::saveImage($recommendId, $image);
	        self::saveAlone($recommendId, $alone);
	    }
	    if($oldRecommendList) {
	        $keyList = array_keys($oldRecommendList);
	        Game_Service_SingleList::deleteSingleListList($keyList);
	        $params = array('recommend_id' => array('IN', $keyList));
	        Game_Service_SingleListGames::deleteSingleListGamesBy($params);
	        Game_Service_SingleListImgs::deleteSingleListImgsBy($params);
	        Game_Service_SingleListGift::deleteSingleListGiftBy($params);
	        Game_Service_SingleListAlone::deleteSingleListAloneBy($params);
	    }
	}

	private static function saveAlone($recommendId, $alone) {
	    $recommend = Game_Service_SingleList::getSingleList($recommendId);
	    $oldAlone = Game_Service_SingleListAlone::getSingleListAloneBy(array('recommend_id' => $recommendId));
	    if(! $oldAlone && ! $alone) return true;
	    if($alone) {
	        if(! $alone['recommend_id']) {
	            $alone['recommend_id'] = $recommendId;
	        }
	        if($recommend['status'] == Game_Service_SingleList::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($alone['link_type'], $alone['link'])) {
	                Game_Service_SingleList::updateSingleList(array('status' => Game_Service_SingleList::STATUS_CLOSE), $recommendId);
	            }
	        }
	        if($alone['id']) {
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($alone, $oldAlone);
	            if ($updateParams) {
	                Game_Service_SingleListAlone::updateSingleListAlone($updateParams, $alone['id']);
	            }
	            if($alone['id'] == $oldAlone['id']) $oldAlone = null;
	        }else{
	            Game_Service_SingleListAlone::addSingleListAlone($alone);
	        }
	    }
	    if($oldAlone) {
	        Game_Service_SingleListAlone::deleteSingleListAlone($oldAlone['id']);
	    }
	    return true;
	}

	private static function saveImage($recommendId, $image) {
	    $recommend = Game_Service_SingleList::getSingleList($recommendId);
	    $oldImg = Game_Service_SingleListImgs::getSingleListImgsBy(array('recommend_id' => $recommendId));
	    if(! $oldImg && !$image) return true;
	    if($image) {
	        if(! $image['recommend_id']) {
	            $image['recommend_id'] = $recommendId;
	        }
	        if($recommend['status'] == Game_Service_SingleList::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($image['link_type'], $image['link'])) {
	                Game_Service_SingleList::updateSingleList(array('status' => Game_Service_SingleList::STATUS_CLOSE), $recommendId);
	            }
	        }
	        if($image['id']) {
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($image, $oldImg);
	            if ($updateParams) {
	                Game_Service_SingleListImgs::updateSingleListImgs($updateParams, $image['id']);
	            }
	            if($image['id'] == $oldImg['id']) $oldImg = null;
	        }else{
	            Game_Service_SingleListImgs::addSingleListImgs($image);
	        }
	    }
	    if($oldImg) {
	        Game_Service_SingleListImgs::deleteSingleListImgs($oldImg['id']);
	    }
	    return true;
	}

	private static function saveGameList($recommendId, $gameList) {
	    $oldList = Game_Service_SingleListGames::getSingleListGamesListBy(array('recommend_id' => $recommendId));
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
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($game, $oldList[$game['game_id']]);
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
	        Game_Service_SingleListGames::deleteSingleListGamesList($deleteList);
	    }
	    if($addList) {
	        Game_Service_SingleListGames::addMutiSingleListGames($addList);
	    }
	    foreach ($updateList as $game) {
	        Game_Service_SingleListGames::updateSingleListGames($game, $game['id']);
	    }
	    return true;
	}

	private static function saveGiftList($recommendId, $giftList) {
	    $oldList = Game_Service_SingleListGift::getSingleListGiftListBy(array('recommend_id' => $recommendId));
	    if(! $oldList && ! $giftList) return true;
	    $oldList = Common::resetKey($oldList, 'gift_id');
	    $addList = array();
	    $updateList = array();
	    foreach ($giftList as $gift) {
	        if(! $gift['recommend_id']) {
	            $gift['recommend_id'] = $recommendId;
	        }
	        $giftInfo = Client_Service_Gift::getGift($gift['gift_id']);
	        if(! $giftInfo) continue;
	        if($giftInfo['status'] == Client_Service_Gift::GIFT_STATE_OPENED) {
	            $gift['gift_status'] = Client_Service_Gift::GIFT_STATE_OPENED;
	        }else{
	            $gift['gift_status'] = Client_Service_Gift::GIFT_STATE_CLOSEED;
	        }
	        if($oldList[$gift['gift_id']]) {
	            $updateParams = Game_Manager_SingleGameRecommend::getUpdateParams($gift, $oldList[$gift['gift_id']]);
	            if ($updateParams) {
	                $updateList[] = $gift;
	            }
	            unset($oldList[$gift['gift_id']]);
	        }else{
	            $addList[] = $gift;
	        }
	    }
	    if($oldList) {
	        $deleteList = Common::resetKey($oldList, 'id');
	        $deleteList = array_keys($deleteList);
	        Game_Service_SingleListGift::deleteSingleListGiftList($deleteList);
	    }
	    if($addList) {
	        Game_Service_SingleListGift::addMutiSingleListGift($addList);
	    }
	    foreach ($updateList as $gift) {
	        Game_Service_SingleListGift::updateSingleListGift($gift, $gift['id']);
	    }
	    return true;
	}

    public static function deleteRecommendListByDayId($day_id) {
        Common_Service_Base::beginTransaction();
        try {
            Game_Service_SingleBanner::deleteSingleBannerBy(array('day_id' => $day_id));
            $recommendList = Game_Service_SingleList::getSingleListListBy(array('day_id' => $day_id));
            if($recommendList) {
                $recommendList = Common::resetKey($recommendList, 'id');
                $recommendIdList = array_keys($recommendList);
                $searchParams = array('recommend_id' => array('IN', $recommendIdList));
                Game_Service_SingleListAlone::deleteSingleListAloneBy($searchParams);
                Game_Service_SingleListGames::deleteSingleListGamesBy($searchParams);
                Game_Service_SingleListGift::deleteSingleListGiftBy($searchParams);
                Game_Service_SingleListImgs::deleteSingleListImgsBy($searchParams);
            }
            Game_Service_SingleList::deleteSingleListBy(array('day_id' => $day_id));
            Game_Service_SingleEditLog::deleteSingleEditLogBy(array('day_id' => $day_id));
            Game_Service_SingleRecommend::deleteSingleRecommend($day_id);
            Common_Service_Base::commit();
            return true;
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
        }
        return false;
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
	        $flg = self::addSingleRecommend($data);
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
	    $bannerList = Game_Service_SingleBanner::getSingleBannerListBy($searchParams);
	    foreach ($bannerList as $banner) {
	        $banner['day_id'] = $to_day_id;
	        unset($banner['id']);
	        unset($banner['create_time']);
	        $newBannerList[] = $banner;
	    }
	    if($newBannerList) {
	        $flg = Game_Service_SingleBanner::addMutiSingleBanner($newBannerList);
	    }
	    return $flg;
	}

	private static function copyList($day_id, $to_day_id) {
	    $searchParams = array('day_id' => $day_id);
	    $recommendList = Game_Service_SingleList::getSingleListListBy($searchParams);
	    if($recommendList) {
	        foreach ($recommendList as $recommend) {
	            $games = Game_Service_SingleListGames::getSingleListGamesListBy(array("recommend_id" => $recommend['id']));
	            $giftList = Game_Service_SingleListGift::getSingleListGiftListBy(array("recommend_id" => $recommend['id']));
	            $img = Game_Service_SingleListImgs::getSingleListImgsBy(array("recommend_id" => $recommend['id']));
	            $alone = Game_Service_SingleListAlone::getSingleListAloneBy(array("recommend_id" => $recommend['id']));
	            $recommend['day_id'] = $to_day_id;
	            unset($recommend['id']);
	            unset($recommend['create_time']);
	            $recommendId = Game_Service_SingleList::addSingleList($recommend);
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
	                $flg = Game_Service_SingleListGames::addMutiSingleListGames($newRecommendGameList);
	                if(! $flg) return false;
	            }
	            
	            $newRecommendGiftList = array();
	            foreach ($giftList as $gift) {
	                $gift['recommend_id'] = $recommendId;
	                unset($gift['id']);
	                $newRecommendGiftList[] = $gift;
	            }
	            if($newRecommendGiftList) {
	                $flg = Game_Service_SingleListGift::addMutiSingleListGift($newRecommendGiftList);
	                if(! $flg) return false;
	            }
	            
	            if($img) {
	                $img['recommend_id'] = $recommendId;
	                unset($img['id']);
	                $flg = Game_Service_SingleListImgs::addSingleListImgs($img);
	                if(! $flg) return false;
	            }
	            
	            if($alone) {
	                $alone['recommend_id'] = $recommendId;
	                unset($alone['id']);
	                $flg = Game_Service_SingleListAlone::addSingleListAlone($alone);
	                if(! $flg) return false;
	            }
	        }
	    }
	    return true;
	}
	
	public static function updateGameStatus($gameId, $status) {
	    $listFlg = Game_Api_SingleRecommendList::gameIsShowing($gameId);
	    //更新推荐列表中游戏的状态字段
	    Game_Service_SingleListGames::updateGameStatus($gameId, $status);
	    if($status == Resource_Service_Games::STATE_OFFLINE) {
	        $bannerFlg =Game_Api_SingleRecommendBanner::gameIsShowing($gameId);
	        $searchParams['link_type'] = Game_Service_Util_Link::LINK_CONTENT;
	        $searchParams['link'] = $gameId;
	        $data = array('status' => Game_Service_SingleList::STATUS_CLOSE);
	        Game_Service_SingleBanner::updateSingleBannerBy($data, $searchParams);
	        
	        $imgList = Game_Service_SingleListImgs::getSingleListImgsListBy($searchParams);
	        $aloneList = Game_Service_SingleListAlone::getSingleListAloneListBy($searchParams);
	        if($imgList || $aloneList) {
                $aloneIdList = Common::resetKey($aloneList, 'recommend_id');
                $imgIdList = Common::resetKey($imgList, 'recommend_id');
                $idList = array_merge(array_keys($aloneIdList), array_keys($imgIdList));
	            $searchParams = array('id' => array('IN', $idList));
	            Game_Service_SingleList::updateSingleListBy($data, $searchParams);
	        }
	        if ($bannerFlg) {
	            Game_Api_SingleRecommendBanner::updateClientBannerCacheData();
	        }
	    }
	    if ($listFlg) {
	        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
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
	    $bannerFlg = $bannerFlg = Game_Api_SingleRecommendBanner::subjectIsShowing($subjectId);
	    $recommendFlg = Game_Api_SingleRecommendList::subjectIsShowing($subjectId);
	    
	    $data = array('status' => Game_Service_SingleList::STATUS_CLOSE);
	    Game_Service_SingleBanner::updateSingleBannerBy($data, $searchParams);
	    $imgList = Game_Service_SingleListImgs::getSingleListImgsListBy($searchParams);
	    if($imgList) {
            $imgIdList = Common::resetKey($imgList, 'recommend_id');
            $idList = array_keys($imgIdList);
            $searchParams = array('id' => array('IN', $idList));
	        Game_Service_SingleList::updateSingleListBy($data, $searchParams);
	    }
	    if ($bannerFlg) {
	        Game_Api_SingleRecommendBanner::updateClientBannerCacheData();
	    }
	    if ($recommendFlg) {
	        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
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
	    $bannerFlg = Game_Api_SingleRecommendBanner::hdIsShowing($hdId);
	    $recommendFlg = Game_Api_SingleRecommendList::hdIsShowing($hdId);

	    $data = array('status' => Game_Service_SingleList::STATUS_CLOSE);
	    Game_Service_SingleBanner::updateSingleBannerBy($data, $searchParams);
	    $imgList = Game_Service_SingleListImgs::getSingleListImgsListBy($searchParams);
	    $aloneList = Game_Service_SingleListAlone::getSingleListAloneListBy($searchParams);
	    if($imgList || $aloneList) {
            $aloneIdList = Common::resetKey($aloneList, 'recommend_id');
            $imgIdList = Common::resetKey($imgList, 'recommend_id');
            $idList = array_merge(array_keys($aloneIdList), array_keys($imgIdList));
            $searchParams = array('id' => array('IN', $idList));
	        Game_Service_SingleList::updateSingleListBy($data, $searchParams);
	    }
	    if ($bannerFlg) {
	        Game_Api_SingleRecommendBanner::updateClientBannerCacheData();
	    }
	    if ($recommendFlg) {
	        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
	    }
	}
	
	/**
	 * 礼包关闭要更新推荐状态
	 */
	public static function updateGiftStatus($giftId, $status) {
	    $listFlg = Game_Api_SingleRecommendList::giftIsShowing($giftId);
	    $data = array('gift_status' => $status);
	    $searchParams = array('gift_id' => $giftId);
	    Game_Service_SingleListGift::updateSingleListGiftBy($data, $searchParams);
	    if ($listFlg) {
	        Game_Api_SingleRecommendList::updateClientRecommendCacheData();
	    }
	}
	
}
