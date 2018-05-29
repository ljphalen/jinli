<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Game_Service_RecommendList
 * @author wupeng
 */
class Game_Service_RecommendList {
    
	public static function getRecommendListsBy($searchParams = array(), $sortParams = array()) {
	    return self::getDao()->getsBy($searchParams, $sortParams);
	}
	
	public static function getRecommendList($day_id) {
		if (!$day_id) return null;		
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->getBy($keyParams);
	}
	
	public static function updateRecommendList($data, $day_id) {
		if (!$day_id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('day_id' => $day_id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	public static function deleteRecommendListByDayId($day_id) {
        Common_Service_Base::beginTransaction();
        try {
            Game_Service_RecommendBanner::deleteRecommendBannerByDayId($day_id);
            Game_Service_RecommendText::deleteRecommendTextByDayId($day_id);
            Game_Service_RecommendDay::deleteRecommendDayByDayId($day_id);
            Game_Service_RecommendEditLog::deleteRecommendEditLogByDayId($day_id);
            Game_Service_RecommendNew::deleteRecommendnewByDayId($day_id);

            $recommendList = Game_Service_RecommendNew::getRecommendnewsBy(array('day_id' => $day_id));
            if($recommendList) {
                $idList = array();
                foreach ($recommendList as $recommend) {
                    $idList[] = $recommend['id'];
                }
                Game_Service_RecommendGames::deleteRecommendGames($idList);
                Game_Service_RecommendImgs::deleteRecommendImgsListByRecommend($idList);
                Game_Service_RecommendNew::deleteRecommendnewByDayId($day_id);
            }
            $keyParams = array('day_id' => $day_id);
            self::getDao()->deleteBy($keyParams);
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
	        
	        $flg = self::copyText($day_id, $to_day_id);
            if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
            }
	        
	        $flg = self::copyDaily($day_id, $to_day_id);
            if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
            }
	        
	        $flg = self::copyList($day_id, $to_day_id);
            if(! $flg) {
	            Common_Service_Base::rollBack();
	            return false;
            }
	        
	        $data = self::getRecommendList($day_id);
	        $data['day_id'] = $to_day_id;
	        $data['create_time'] = Common::getTime();
	        $flg = self::addRecommendList($data);
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
	    $bannerList = Game_Service_RecommendBanner::getRecommendBannersBy($searchParams);
	     
	    foreach ($bannerList as $banner) {
	        $banner['day_id'] = $to_day_id;
	        $banner['id'] = null;
	        $banner['create_time'] = Common::getTime();
	        $newBannerList[] = $banner;
	    }
	    if($newBannerList) {
	        $flg = Game_Service_RecommendBanner::addMutiRecommendBanner($newBannerList);
	    }
        return $flg;
	}
	
	private static function copyText($day_id, $to_day_id) {
	    $flg = true;
	    $searchParams = array('day_id' => $day_id);
        $text = Game_Service_RecommendText::getRecommendTextBy($searchParams);
        if($text) {
            $text['day_id'] = $to_day_id;
            $text['id'] = null;
            $text['create_time'] = Common::getTime();
            $flg = Game_Service_RecommendText::addRecommendText($text);
        }
        return $flg;
	}
	
	private static function copyDaily($day_id, $to_day_id) {
	    $flg = true;
        $daily = Game_Service_RecommendDay::getRecommendByDayId($day_id);
        if($daily) {
            $daily['day_id'] = $to_day_id;
            $daily['id'] = null;
            $daily['create_time'] = Common::getTime();
            $flg = Game_Service_RecommendDay::addRecommendDay($daily);
        }
        return $flg;
	}
	
	private static function copyList($day_id, $to_day_id) {
	    $searchParams = array('day_id' => $day_id);
	    $recommendList = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
	    if($recommendList) {
	        foreach ($recommendList as $recommend) {
	            $games = Game_Service_RecommendGames::getGames($recommend['id']);
	            $img = Game_Service_RecommendImgs::getRecommendImgsBy(array('recommend_id' => $recommend['id']));
	            $recommend['day_id'] = $to_day_id;
	            $recommend['id'] = null;
	            $recommend['create_time'] = Common::getTime();
	            $id = Game_Service_RecommendNew::addRecommendnew($recommend);
	            if(! $id) {
	                return false;
	            }
	            $newRecommendGameList = array();
	            foreach ($games as $game) {
	                $game['recommend_id'] = $id;
	                $newRecommendGameList[] = $game;
	            }
	            if($newRecommendGameList) {
	                $flg = Game_Service_RecommendGames::addMutiRecommendGames($newRecommendGameList);
	                if(! $flg) return false;
	            }
	            if($img) {
	                $img['recommend_id'] = $id;
	                $img['id'] = null;
	                $flg = Game_Service_RecommendImgs::addRecommendImgs($img);
	                if(! $flg) return false;
	            }
	        }
	    }
	    return true;
	}
	
	public static function saveRecommend($day_id, $bannerList, $text, $daily, $recommendList) {
	    Common_Service_Base::beginTransaction();
	    try {
	        self::saveRecommendBanner($day_id, $bannerList);
            self::saveRecommendText($day_id, $text);
	        self::saveRecommendDaily($day_id, $daily);
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
	    $banners = Game_Service_RecommendBanner::getRecommendBannersBy($searchParams);
	    foreach ($banners as $banner) {
	        $oldBannerList[$banner['id']] = $banner;
	    }
	    foreach ($bannerList as $banner) {
	        if($banner['status'] == Game_Service_RecommendBanner::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($banner['link_type'], $banner['link'])) {
	                $banner['status'] = Game_Service_RecommendBanner::STATUS_CLOSE;
	            }
	        }
	        if($banner['id']) {
	            $oldBanner = $oldBannerList[$banner['id']];
	            unset($oldBannerList[$banner['id']]);
	            $updateParams = Game_Manager_RecommendList::getUpdateParams($banner, $oldBanner);
	            if ($updateParams) {
	                Game_Service_RecommendBanner::updateRecommendBanner($updateParams, $banner['id']);
	            }
	        }else{
	            Game_Service_RecommendBanner::addRecommendBanner($banner);
	        }
	    }
	    foreach ($oldBannerList as $banner) {
	        Game_Service_RecommendBanner::deleteRecommendBanner($banner['id']);
	    }
	}

	private static function saveRecommendText($day_id, $text) {
	    $searchParams = array('day_id' => $day_id);
	    $oldText = Game_Service_RecommendText::getRecommendTextBy($searchParams);
	    if($text) {
	        if($text['status'] == Game_Service_RecommendText::STATUS_OPEN) {
	            if(Game_Service_Util_Link::checkLinkValue($text['link_type'], $text['link'])) {
	                $text['status'] = Game_Service_RecommendText::STATUS_CLOSE;
	            }
	        }
	        if($text['id']) {
	            $updateParams = Game_Manager_RecommendList::getUpdateParams($text, $oldText);
	            if ($updateParams) {
	                Game_Service_RecommendText::updateRecommendText($updateParams, $text['id']);
	            }
	            if($text['id'] == $oldText['id']) $oldText = null;
	        }else{
	            Game_Service_RecommendText::addRecommendText($text);
	        }
	    }
	    if($oldText) {
	        Game_Service_RecommendText::deleteRecommendText($oldText['id']);
	    }
	}

	private static function saveRecommendDaily($day_id, $daily) {
	    $oldDaily = Game_Service_RecommendDay::getRecommendByDayId($day_id);
	    if($daily) {
	        $game = Resource_Service_Games::getResourceByGames($daily['game_id']);
	        $daily['game_status'] = $game ? Resource_Service_Games::STATE_ONLINE : Resource_Service_Games::STATE_OFFLINE;
	        if($daily['status'] == Game_Service_RecommendDay::STATUS_OPEN && $daily['game_status'] == Resource_Service_Games::STATE_OFFLINE) {
	            $daily['status'] = Game_Service_RecommendDay::STATUS_CLOSE;
	        }
	        if($daily['id']) {
	            $updateParams = Game_Manager_RecommendList::getUpdateParams($daily, $oldDaily);
	            if ($updateParams) {
	                Game_Service_RecommendDay::updateRecommendDay($updateParams, $daily['id']);
	            }
	            if($daily['id'] == $oldDaily['id']) $oldDaily = null;
	        }else{
	            Game_Service_RecommendDay::addRecommendDay($daily);
	        }
	    }
	    if($oldDaily) {
	        Game_Service_RecommendDay::deleteRecommendDay($oldDaily['id']);
	    }
	}

	private static function saveRecommendList($day_id, $recommendList) {
	    $oldRecommendList = array();
	    $searchParams = array('day_id' => $day_id);
	    $recommends = Game_Service_RecommendNew::getRecommendnewsBy($searchParams);
	    foreach ($recommends as $recommend) {
	        $oldRecommendList[$recommend['id']] = $recommend;
	    }
	    foreach ($recommendList as $info) {
	        $recommend = $info[Game_Manager_RecommendList::RECOMMEND_INFO];
	        $games = $info[Game_Manager_RecommendList::RECOMMEND_GAMES];
	        $image = $info[Game_Manager_RecommendList::RECOMMEND_IMAGE];
	        if($recommend['id']) {
	            $oldRecommend = $oldRecommendList[$recommend['id']];
	            unset($oldRecommendList[$recommend['id']]);
	            
	            //保存推荐游戏
	            $oldGames = Game_Service_RecommendGames::getGames($recommend['id']);
	            $addList = array();
	            $updateList = array();
	            $oldGamesList = array();
	            foreach ($oldGames as $game) {
	                $oldGamesList[$game['game_id']] = $game;
	            }
	            foreach ($games as $game) {
	                if(! $game['recommend_id']) {
	                    $game['recommend_id'] = $recommend['id'];
	                }
	                $gameInfo = Resource_Service_Games::getResourceByGames($game['game_id']);
	                $game['game_status'] = $gameInfo ? Resource_Service_Games::STATE_ONLINE : Resource_Service_Games::STATE_OFFLINE;
	                if($oldGamesList[$game['game_id']]) {
	                    $updateParams = Game_Manager_RecommendList::getUpdateParams($game, $oldGamesList[$game['game_id']]);
	                    if ($updateParams) {
	                        $updateList[$game['game_id']] = $game;
	                    }
	                    unset($oldGamesList[$game['game_id']]);
	                }else{
	                    $addList[] = $game;
	                }
	            }
	            if($oldGamesList) {
	                $deleteList = array_keys($oldGamesList);
	                Game_Service_RecommendGames::deleteList($recommend['id'], $deleteList);
	            }
	            if($addList) {
	                Game_Service_RecommendGames::addMutiRecommendGames($addList);
	            }
                foreach ($updateList as $game) {
                    if(! isset($game['sort'])) {
                        continue;
                    }
                    Game_Service_RecommendGames::updateRecommendGame(array('sort' => $game['sort']), $game['recommend_id'], $game['game_id']);
                }
	            
	            //保存推荐图片
	            $oldImg = Game_Service_RecommendImgs::getRecommendImgsBy(array('recommend_id' => $recommend['id']));
	            if($image) {
	                if(! $image['recommend_id']) {
	                    $image['recommend_id'] = $recommend['id'];
	                }

	                if($recommend['status'] == Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS) {
	                    if(Game_Service_Util_Link::checkLinkValue($image['link_type'], $image['link'])) {
	                        $recommend['status'] = Game_Service_RecommendNew::RECOMMEND_CLOSE_STATUS;
	                    }
	                }
	                
	                if($image['id']) {
	                    $updateParams = Game_Manager_RecommendList::getUpdateParams($image, $oldImg);
	                    if ($updateParams) {
	                        Game_Service_RecommendImgs::updateRecommendImgs($updateParams, $image['id']);
	                    }
	                    if($image['id'] == $oldImg['id']) $oldImg = null;
	                }else{
	                    Game_Service_RecommendImgs::addRecommendImgs($image);
	                }
	            }
	            if($oldImg) {
	                Game_Service_RecommendImgs::deleteRecommendImgs($oldImg['id']);
	            }
	            $updateParams = Game_Manager_RecommendList::getUpdateParams($recommend, $oldRecommend);
	            if ($updateParams) {
	                Game_Service_RecommendNew::updateRecommendnew($updateParams, $recommend['id']);
	            }
	        }else{
	            $recommendId = Game_Service_RecommendNew::addRecommendnew($recommend);
	            $newRecommendGameList = array();
	            foreach ($games as $game) {
	                $game['recommend_id'] = $recommendId;
	                $game['game_status'] = Resource_Service_Games::STATE_ONLINE;
	                $newRecommendGameList[] = $game;
	            }
	            if($newRecommendGameList) {
	                Game_Service_RecommendGames::addMutiRecommendGames($newRecommendGameList);
	            }
	            if($image) {

	                if($recommend['status'] == Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS) {
	                    if(Game_Service_Util_Link::checkLinkValue($image['link_type'], $image['link'])) {
	                        $updateParams = array('status' => Game_Service_RecommendNew::RECOMMEND_CLOSE_STATUS);
	                        Game_Service_RecommendNew::updateRecommendnew($updateParams, $recommendId);
	                    }
	                }
	                
	                $image['recommend_id'] = $recommendId;
	                $image['id'] = null;
	                Game_Service_RecommendImgs::addRecommendImgs($image);
	            }
	        }
	    }
	    if($oldRecommendList) {
	        $idList = array_keys($oldRecommendList);
	        Game_Service_RecommendNew::deleteRecommendnewByRecommendList($idList);
	        Game_Service_RecommendGames::deleteRecommendGames($idList);
	        Game_Service_RecommendImgs::deleteRecommendImgsListByRecommend($idList);
	    }
	}
	
	public static function addRecommendList($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		return self::getDao()->insert($dbData);
	}
	
	public static function updateGameStatus($gameId, $status) {
	    $dayFlg = Game_Api_RecommendDay::gameIsShowing($gameId);
        $listFlg = Game_Api_Recommend::gameIsShowing($gameId);
	    //更新推荐列表中游戏的状态字段
	    Game_Service_RecommendGames::updateGameStatus($gameId, $status);
	    //更新每日一荐中游戏的状态字段
	    Game_Service_RecommendDay::updateGameStatus($gameId, $status);
        if ($dayFlg) {
            Game_Api_RecommendDay::updateClientDayCacheData();
        }
        if ($listFlg) {
            Game_Api_Recommend::updateClientRecommendCacheData();
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
        $bannerFlg = Game_Api_RecommendBanner::subjectIsShowing($subjectId);
        $recommendFlg = Game_Api_Recommend::subjectIsShowing($subjectId);
        $textFlg = Game_Api_RecommendText::subjectIsShowing($subjectId);
        
        $data['status'] = Game_Service_RecommendBanner::STATUS_CLOSE;
        Game_Service_RecommendBanner::updateRecommendBannerBy($data, $searchParams);
        Game_Service_RecommendText::updateRecommendTextBy($data, $searchParams);
        $imgList = Game_Service_RecommendImgs::getRecommendImgsListBy($searchParams);
        $recommendIdList = array();
        foreach ($imgList as $img) {
            $recommendIdList[] = $img['recommend_id'];
        }
        if($recommendIdList) {
            $searchParams = array('id' => array('IN', $recommendIdList));
            Game_Service_RecommendNew::updateRecommendNewBy($data, $searchParams);
        }
        if ($bannerFlg) {
            Game_Api_RecommendBanner::updateClientBannerCacheData();
        }
        if ($recommendFlg) {
            Game_Api_Recommend::updateClientRecommendCacheData();
        }
        if ($textFlg) {
            Game_Api_RecommendText::updateClientTextCacheData();
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
        $bannerFlg = Game_Api_RecommendBanner::hdIsShowing($hdId);
        $recommendFlg = Game_Api_Recommend::hdIsShowing($hdId);
        $textFlg = Game_Api_RecommendText::hdIsShowing($hdId);
         
        $data['status'] = Game_Service_RecommendBanner::STATUS_CLOSE;
        Game_Service_RecommendBanner::updateRecommendBannerBy($data, $searchParams);
        Game_Service_RecommendText::updateRecommendTextBy($data, $searchParams);
        $imgList = Game_Service_RecommendImgs::getRecommendImgsListBy($searchParams);
        $recommendIdList = array();
        foreach ($imgList as $img) {
            $recommendIdList[] = $img['recommend_id'];
        }
        if($recommendIdList) {
            $searchParams = array('id' => array('IN', $recommendIdList));
            Game_Service_RecommendNew::updateRecommendNewBy($data, $searchParams);
        }
        
        if ($bannerFlg) {
            Game_Api_RecommendBanner::updateClientBannerCacheData();
        }
        if ($recommendFlg) {
            Game_Api_Recommend::updateClientRecommendCacheData();
        }
        if ($textFlg) {
            Game_Api_RecommendText::updateClientTextCacheData();
        }
	}
	
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['day_id'])) $dbData['day_id'] = $data['day_id'];
		if(isset($data['create_time'])) $dbData['create_time'] = $data['create_time'];
		return $dbData;
	}
	
	private static function getDao() {
		return Common::getDao("Game_Dao_RecommendList");
	}
	
}
