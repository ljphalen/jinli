<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Ad_RecommendlistController
 * @author wupeng,chengyi
 */
class Ad_RecommendlistController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad_Recommendlist/index',
		'editUrl' => '/Admin/Ad_Recommendlist/edit',
		'editPostUrl' => '/Admin/Ad_Recommendlist/editPost',
		'copyUrl' => '/Admin/Ad_Recommendlist/copy',

	    'bannerAddUrl' => '/Admin/Ad_Recommendbanner/add',
	    'bannerEditUrl' => '/Admin/Ad_Recommendbanner/edit',
	    'bannerDeleteUrl' => '/Admin/Ad_Recommendbanner/delete',

	    'recommendAddUrl' => '/Admin/Ad_Recommendnew/add',
	    'recommendEditUrl' => '/Admin/Ad_Recommendnew/edit',
	    'recommendDeleteUrl' => '/Admin/Ad_Recommendnew/delete',
	    'recommendAddRankUrl' => '/Admin/Ad_Recommendnew/rankAdd',
	    'recommendEditRankUrl' => '/Admin/Ad_Recommendnew/rankEdit',
	    'recommendDeleteRankUrl' => '/Admin/Ad_Recommendnew/rankDelete',
	    
	    'recommendAddHdUrl' => '/Admin/Ad_Recommendnew/otherAdd?type=hd',
	    'recommendAddNewUrl' => '/Admin/Ad_Recommendnew/otherAdd?type=new',
	    'recommendEditHdAndNew' => '/Admin/Ad_Recommendnew/otherEdit',
	    'recommendDeleteHdAndNewUrl' => '/Admin/Ad_Recommendnew/otherDelete',
	    
	    
	    'editlogUrl' => '/Admin/Ad_Recommendlist/editlog',
	    
	    'infoUrl' => '/Admin/Ad_Recommendlist/info',
		'textSetUrl' => '/Admin/Ad_Recommendlist/textSet'
	);
	
	public $perpage = 20;
	public $mRankType = array();
	
	/**
	 * 列表界面
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$date = $this->getInput('date');
		$sortParams = array('day_id' => 'DESC');
		$searchParams = array();
		if(! $date) $date = date("Y-m");
		$startTime = strtotime($date);
		$endTime = strtotime("+1 months", strtotime($date));
        
		$searchParams['day_id'][] = array(">=", $startTime);
		$searchParams['day_id'][] = array("<", $endTime);
		$list = Game_Service_H5RecommendList::getRecommendListsBy($searchParams, $sortParams);
		
		$list = $this->initList($list, $startTime);
		$lastMonthDay = strtotime("-1 day", strtotime($date));
		$lastList = Game_Service_H5RecommendList::getRecommendList($lastMonthDay);
		$this->assign('date', $startTime);
		$this->assign('before', $lastList != 0);
		$this->assign('list', $list);
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
		$dayId = $this->getInput('day_id');
		$dayId = strtotime($dayId);
		$from = $this->getInput('from');
		Game_Service_H5HomeAutoHandle::initID($dayId);
		if($from) {
		    $status = Game_Service_H5HomeAutoHandle::cleanAllOldTmpData($dayId);
		}
		if(Common::getTime() >= $dayId+60*60*24-1) {
		    $isFalse = true;
		} else {
		    $isFalse = false;
		}
		$data = array(
		    'success' => true, 'msg' => "", 'data' => array(),
		);
		$searchParams = array('day_id' => $dayId);
		$bannerList = Game_Service_H5RecommendBanner::getRecommendBannersBy($searchParams);
		$data['data']['roll'] = $this->initBannerInfo($bannerList);
		$recommendList = Game_Service_H5RecommendNew::getsBy(array('day_id' => $dayId));
		$data['data']['recommend'] = $this->initRecommend($recommendList);
		$this->assign('data', $data);
		$log = Game_Service_H5RecommendEditLog::getRecommendEditLogByDayId($dayId);
		if($log) {
		    $user = Admin_Service_User::getUser($log['uid']);
		    $log['username'] = $user['username'];
		}
		$this->assign('log', $log);
		$this->assign('isFalse', $isFalse);
		$this->assign('day_id', $dayId);
		$this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	/**
	 * 提交编辑内容
	 */
	public function editPostAction() {
		$dayId = $this->getInput('day_id');
		$banner = html_entity_decode($this->getInput('banner'));
		$recommend = html_entity_decode($this->getInput('recommend'));
		$banner = json_decode($banner);
		$recommend = json_decode($recommend);
        
		$sort = count($banner);
		$bannerSort = array();
		foreach ($banner as $id) {
		    $bannerSort[$id] = ($sort--);
		}
		
		$sort = count($recommend);
		$recommendSort = array();
		foreach ($recommend as $id) {
		    $recommendSort[$id] = ($sort--);
		}
		Game_Service_H5RecommendBanner::updateListSort($banner, $bannerSort);
		Game_Service_H5RecommendNew::updateListSort($recommend, $recommendSort);
		$changeTmpData = Game_Service_H5HomeAutoHandle::handleTmpDataToSave();
		
		if($changeTmpData) {
		    $this->saveEdit($dayId);
		    Common::getCache()->delete(Util_CacheKey::HOME_H5_INDEX);
		    $this->output(0, '保存成功.');
		} else {
		    $this->output(0, '保存失败.');
		}
	}
	
	private function saveEdit($dayId) {
	    $editInfo = Game_Service_H5RecommendList::getRecommendList($dayId);
	    if (! $editInfo) {
	        $requestData = array();
	        $requestData['day_id'] = $dayId;
	        $requestData['create_time'] = Common::getTime();
	        Game_Service_H5RecommendList::addRecommendList($requestData);
	    }
	    $this->addLog($dayId);
	    
	}
	
	public function copyAction() {
		$dayId = $this->getInput('day_id');
		$type = $this->getInput('type');
		$toDayId = strtotime($dayId);
		$data = Game_Service_H5RecommendList::getRecommendList($toDayId);
		if($type == 1 && !$data) {
		    Game_Service_H5HomeAutoHandle::initID($toDayId);
		    Game_Service_H5HomeAutoHandle::cleanAllOldTmpData();
		    $fromDayId = strtotime("-1 day", $toDayId);
		    Game_Service_H5HomeAutoHandle::copyDayToDay($fromDayId, $toDayId);
		}
	    $this->redirect("edit?day_id=".$dayId);
	}
	
	public function editlogAction() {
	    $perpage = $this->perpage;
	    $page = intval($this->getInput('page'));
	    $dayId = intval($this->getInput('day_id'));
	    if ($page < 1) $page = 1;
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('id' => 'DESC');
	    list($total, $list) = Game_Service_H5RecommendEditLog::getPageList($page, $this->perpage, $searchParams, $sortParams);
	    foreach ($list as $key => $value) {
	        $user = Admin_Service_User::getUser($value['uid']);
	        $list[$key]['username'] = $user['username'];
	    }
	    $requestData=array();
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $list);
	    $this->assign('total', $total);
	    $url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
	    $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function infoAction() {
	}
	
	private function initList($list, $beginTime) {
	    $dataList = array();
	    $list = Common::resetKey($list, 'day_id');
	    $days = date('t', strtotime("+1 month -1 day", $beginTime));
	    for ($i = 0; $i < $days; $i++) {
	        $time = strtotime("+{$i} day", $beginTime);
	        if (! isset($list[$time])) {
	            $dataList[$time] = array();
	        }else{
	            $dataList[$time] = $list[$time];
	        }
	    }
	    return $dataList;
	}
	
	private function addLog($dayId) {
	    $log = array(
	        'day_id' => $dayId,
	        'uid' => $this->userInfo['uid'],
	        'create_time' => Common::getTime(),
	    );
	    Game_Service_H5RecommendEditLog::addRecommendEditLog($log);
	}
	
	private function initBannerInfo($bannerList) {
	    $bannerInfo = array();
	    $sort = 4;
	    foreach ($bannerList as $banner) {
	        $tmp = array();
	        $tmp['sort'] = $sort--;
	        if($banner) {
	            $tmp['id'] = $banner['id'];
	            $tmp['name'] = $banner['title'];
	            $tmp['href'] = Common::getAttachPath(). $banner['img'];
	        }
	        $bannerInfo[] = $tmp;
	    }
	    return $bannerInfo;
	}
	
	/**设置游戏名称*/
	private function initRecommend($recommendList) {
	    $recommendData = array();
	    $this->mRankType = Game_Service_Rank::getH5OpenRankType();
	    foreach ($recommendList as $recommend) {
            $h5HomeListInfo = array(
                'id' => $recommend['id'], 'name' => $recommend['title'],
                'typeName' => Game_Service_H5RecommendNew::$rec_type[$recommend['rec_type']],
            );
	        if($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_LIST) {
                $h5HomeListInfo['list'] = array();
	            $gamesInfo = Game_Service_H5RecommendGames::getGames($recommend["id"]);
	            $h5HomeListInfo = $this->gameIndexList($gamesInfo, $h5HomeListInfo);
	        } elseif($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_RANK) {
	            $rankInfo = Game_Service_H5RecommendRank::getRankByRecommendId($recommend['id']);
	            $h5HomeListInfo = $this->rankIndexList($rankInfo, $h5HomeListInfo);
	        } elseif($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_NEW) {
	            $newsInfo = Game_Service_H5RecommendNewHd::getNewsByRecommendId($recommend['id']);
	            $h5HomeListInfo = $this->newsIndexList($newsInfo, $h5HomeListInfo);
	        } elseif($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_ACTIVE) {
	            $newsInfo = Game_Service_H5RecommendNewHd::getNewsByRecommendId($recommend['id']);
	            $h5HomeListInfo = $this->activeIndexList($newsInfo, $h5HomeListInfo);
	        }else{
	            $imgInfo = Game_Service_H5RecommendImgs::getRecommendImgsBy(array('recommend_id' => $recommend["id"]));
	            if($imgInfo) {
	                $h5HomeListInfo = $this->imgIndexList($imgInfo, $h5HomeListInfo);
	            }
	        }
                if($h5HomeListInfo) {
                    $recommendData[] = $h5HomeListInfo;
                }
	    }
	    return $recommendData;
	}
	
	private function imgIndexList($imgInfo, $h5HomeListInfo) {
            $h5HomeListInfo['href'] = Common::getAttachPath().$imgInfo['img'];
            $h5HomeListInfo['linkId'] = $imgInfo['link'];
            $h5HomeListInfo['typeName'] = $h5HomeListInfo['typeName'] . '-' . Game_Service_Util_Link::$linkType[$imgInfo['link_type']];
            return $h5HomeListInfo;
	}
	
	private function gameIndexList($gamesInfo, $h5HomeListInfo) {
	    $index=0;
	    foreach ($gamesInfo as $game) {
	        if($index < Game_Service_H5RecommendNew::SHOW_NUM) {
	            $h5HomeListInfo['list'][] = self::gameInfo($game['game_id']);
	            $index++;
	        }
	        $gameInfo = Resource_Service_GameData::getBasicInfo($game['game_id']);
	        if($gameInfo['name']) {
	            $gameName[] = $gameInfo['name'];
	        }
	    }
	    $h5HomeListInfo['names'] = implode(',',$gameName);
	    $h5HomeListInfo['type'] = 'gameList';
	    return $h5HomeListInfo;
	}
	
	private function activeIndexList($recommend, $h5HomeListInfo) {
	    $ids = common::resetKey($recommend, 'list_id');
	    if(!$ids) return '';
	    $newList = Client_Service_Hd::getHdByIds(array('id' => array('IN',array_keys($ids))));
	    foreach($newList as $key => $newInfo) {
	        $newNameList[] = $newInfo['title'];
	        $h5HomeListInfo['activityName'] = mb_strlen($newInfo['title'], 'utf-8') > 6 ? mb_substr($newInfo['title'], 0, 6, 'utf-8') : $newInfo['title'];
	        $h5HomeListInfo['info'] = mb_substr($newInfo['resume'], 0, 15, 'utf-8');
	        $h5HomeListInfo['date'] = date('Y-m-d', $newInfo['start_time']);
	        $h5HomeListInfo['href'] = Common::getAttachPath() . $newInfo['img'];
	    }
	    $h5HomeListInfo['names'] = implode(',', $newNameList);
	    $h5HomeListInfo['type'] = 'active';
	    return $h5HomeListInfo;
	}
	
	
	private function newsIndexList($recommend, $h5HomeListInfo) {
	    $ids = common::resetKey($recommend, 'list_id');
	    if(!$ids) return '';
	    $newList = Client_Service_News::getNewsByIds(array('id' => array('IN',array_keys($ids)), 'ntype' => Client_Service_News::ARTICLE_TYPE_NEWS));
	    foreach($newList as $key => $newInfo) {
	        $newData = array(
	            'name' => mb_strlen($newInfo['title'], 'utf-8') > 6 ? mb_substr($newInfo['title'], 0, 6, 'utf-8') : $newInfo['title'],
	            'href' => Common::getAttachPath() . $newInfo['thumb_img'],
	            'resume' => mb_substr($newInfo['resume'], 0, 15, 'utf-8'),
	            'link' => $newInfo['link']
	        );
	        $newNameList[] = $newInfo['title'];
	        $h5HomeListInfo['list'][] = $newData;
	    }
	    $h5HomeListInfo['names'] = implode(',', $newNameList);
	    $h5HomeListInfo['type'] = 'news';
	    return $h5HomeListInfo;
	}
	
	private function rankIndexList($recommend, $h5HomeListInfo) {
	    list($total, $gameList) = Game_Service_Rank::getGameListByType($recommend['rank_type'], 1, Game_Service_H5RecommendRank::INDEX_RANK_SHOWNUM);
	    foreach($gameList as $key => $gameInfo) {
	        $gameData = array(
	            'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
	            'size' => $gameInfo['size'],
	            'type' => mb_substr($gameInfo['category_title'], 0, 4, 'utf-8'),
	            'href' => $gameInfo['img'],
	            'link' => $gameInfo['link']
	        );
	        $h5HomeListInfo['list'][] = $gameData;
	    }
	    $h5HomeListInfo['name'] = $this->mRankType[$recommend['rank_type']];
	    $h5HomeListInfo['type'] = 'rank';
	    return $h5HomeListInfo;
	}
	
	private function gameInfo($gameId) {
	    $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
	    $gameInfo['infpage'];
	    $gameData = array(
	        'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
	        'size' => $gameInfo['size'],
	        'type' => mb_substr($gameInfo['category_title'], 0, 4, 'utf-8'),
	        'href' => $gameInfo['img'],
	        'link' => $gameInfo['link']
	    );
	    $extra = Resource_Service_GameData::getExtraInfo($gameId);
	    if($extra['freedl']) {
	        $gameData['butType'] = "download-prize";
	        $gameData['butName'] = "免流量";
	    }else if($extra['reward']) {
	        $gameData['butType'] = "download-prize";
	        $gameData['butName'] = "有奖下载";
	    }else{
	        $gameData['butType'] = "download";
	        $gameData['butName'] = "下载";
	    }
	    return $gameData;
	}

	public function textSetAction() {
	    $settingKey 	= 'WELFARE_TASK_CONFIG';
	    if($_SERVER['REQUEST_METHOD'] == 'POST') {
	        $input = $this->getInput('switch');
	        $res = Game_Service_Config::setValue($settingKey, $input);
	        if($res) {
	            $this->output(0,'设置成功');
	        }
	        $this->output(-1,'设置失败');
	    }
	    $config = Game_Service_Config::getValue($settingKey);
	    $this->assign('config', $config);
	}
	
	
}
