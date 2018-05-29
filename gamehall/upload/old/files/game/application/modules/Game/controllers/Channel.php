<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ChannelController extends Game_BaseController {
	
	public $actions =array(
	                'newGameList' => '/Channel/newGame',
	                'newGameAjax' => '/Channel/newGameAjax',
	                'singleGameList' => '/Channel/singleGame',
	                'singleGameAjax' => '/Channel/singleGameAjax',
	                'webGameList' => '/Channel/webGame',
	                'webGameAjax' => '/Channel/webGameAjax',
	                'crackGameList' => '/Channel/crackGame',
	                'crackGameAjax' => '/Channel/crackGameAjax',
	                'recommendGameList' => '/Channel/recommendGame',
	                'recommendGameAjax' => '/Channel/recommendGameAjax',
	                'gameDetailUrl' => '/index/detail'
	);
	
	public $perpage = 10;
	
	/**
	 * 新游尝鲜
	 * @author yinjiayan
	 */
	public function newGameAction() {
	    list($list, $hasNext, $page, $total) = $this->newGameList();
	    $data = array(
	                    'list' => $list,
	                    'hasNext' => $hasNext,
	                    'curPage' => $page,
	                    'ajaxUrl' => $this->actions['newGameAjax'].'?'.Util_Statist::getCurStatistStr()
	    );
	    $this->assign('data', json_encode($data));
	}
	
	public function newGameAjaxAction() {
	    list($list, $hasNext, $page, $total) = $this->newGameList();
	    $data = array(
	                    'list' => $list,
	                    'hasNext' => $hasNext,
	                    'curPage' => $page,
	                    'ajaxUrl' => $this->actions['newGameAjax'].'?'.Util_Statist::getCurStatistStr()
	    );
	    $this->ajaxOutput($data);
	}
	
	/**
	 * 单机游戏
	 * @author yinjiayan
	 */
	public function singleGameAction() {
	    $data = $this->singleGameList();
	    $adData = $this->getAdList(Client_Service_Ad::AD_TYPE_POSITION_SINGLE);
	    $this->assign('data', json_encode($data));
	    $this->assign('adData', json_encode($adData));
	}
	
	public function singleGameAjaxAction() {
	    $data = $this->singleGameList();
	    $this->ajaxOutput($data);
	}
	
	/**
	 * 火爆网游
	 * @author yinjiayan
	 */
	public function webGameAction() {
	    $data = $this->webGameList();
	    $adData = $this->getAdList(Client_Service_Ad::AD_TYPE_POSITION_WEB);
	    $this->assign('data', json_encode($data));
	    $this->assign('adData', json_encode($adData));
	}
	
	public function webGameAjaxAction() {
	    $data = $this->webGameList();
	    $this->ajaxOutput($data);
	}
	
	/**
	 * 破解游戏
	 * @author yinjiayan
	 */
	public function crackGameAction() {
	    $data = $this->crackGameList();
	    $this->assign('data', json_encode($data));
	    $this->assign('title', '破解游戏');
	}
	
	public function crackGameAjaxAction() {
	    $data = $this->crackGameList();
	    $this->ajaxOutput($data);
	}
	
	/**
	 * 精品推荐
	 * @author yinjiayan
	 */
	public function recommendGameAction() {
	    $data = $this->recommendGameList();
	    $this->assign('data', json_encode($data));
	    $this->assign('title', '精品推荐');
	}
	
	public function recommendGameAjaxAction() {
	    $data = $this->recommendGameList();
	    $this->ajaxOutput($data);
	}
	
	private function newGameList() {
	    $page = $this->getPageInput();
	    $limit = Game_Service_Config::getValue('game_rank_newnum');
	    $perpage = min($this->perpage, $limit);
	    $params = array('status'=> 1,'game_status'=>1,'start_time'=>array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR)));
	    $order = array('start_time' => 'DESC','game_id' => 'DESC');
	    list($total, $newgames) = Client_Service_Taste::getList($page, $perpage, $params, $order);
	    $list = array();
	    $baseIndex = ($page-1) * $perpage + 1;
	    foreach($newgames as $key=>$value) {
	        $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
	        if (!$game) {
	        	continue;
	        }
	        $taste = Client_Service_Taste::getTasteGame(array('game_id'=>$value['game_id']));
			$list[] = array(
			                'name' => html_entity_decode($game['name']),
			                'size'=>$game['size'].'M',
			                'info'=>html_entity_decode($game['resume']),
			                'typeName' => $game['category_title'],
			                'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
			                'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
			                'imgUrl' => $game['img'],
			                'date'=> date("m月d日",$taste['start_time']) 
			);
	    }
	    $hasNext = $perpage * $page < $total;
	    return array($list, $hasNext, $page, $total);
	}
	
	private function singleGameList() {
        $page = $this->getPageInput();
		list($total, $signleGames) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
		$list = array();
		$baseIndex = ($page-1) * $this->perpage + 1;
		foreach ($signleGames as $key=>$value) {
		    $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
		    if (!$game) {
		        continue;
		    }
		    $list[] = array(
		                    'name' => $game['name'],
		                    'size' => $game['size'].'M',
		                    'info' => $game['resume'],
		                    'typeName' => $game['category_title'],
		                     'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
			                'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
		                    'imgUrl' => $game['img'],
		    );
		}
		$hasNext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		return $data = array(
		                'list'=>$list, 
		                'hasNext'=>$hasNext, 
		                'curPage'=>$page,
		                'ajaxUrl' => $this->actions['singleGameAjax'].'?'.Util_Statist::getCurStatistStr()
		);
	}
	
	private function getAdList($hits) {
	    $param = array(
	                    'ad_type'=>1,
	                    'status'=>1,
	                    'hits'=>$hits
	    );
	    list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 6, $param);
        $adList = array();
        $attachPath = Common::getAttachPath();
	    foreach($ad as $key=>$value){
	        $statistSrc = $hits == Client_Service_Ad::AD_TYPE_POSITION_SINGLE ? 'pcglist' : 'olglist';
	        $statistSrc .= '_ad'.$value['id'].'I'.($key+1);
	        $adUrl = $this->getAdUrl($value['ad_ptype'], $value['link'], $statistSrc);
	        if($adUrl){
	            $adList[] = array(
	                            'name'=>html_entity_decode($value['title']),
	                            'imgUrl'=>$attachPath.$value['img'],
	                            'href'=>$adUrl
	            );
	        }
	    }
	    return array('data'=>$adList);
	}
	
	private function getAdUrl($adLinkType, $link, $statistSrc) {
	    switch ($adLinkType) {
	    	case Client_Service_Ad::ADLINK_TYPE_GAMEID :
	    	    $gameId = $link;
	    	    $game = Resource_Service_Games::getGameAllInfo(array('id'=>intval($gameId)), '', Common::getAttachPath());
	    	    if(!$game['status']) return false;
	    	    return Util_Statist::getGameDetailUrl($gameId, $statistSrc);
    	    case Client_Service_Ad::ADLINK_TYPE_CATEGOTY :
    	        $categotyId = $link;
    	        $category = Resource_Service_Attribute::getBy(array('id'=>$categotyId));
			    if(!$category['status']) return false;
			    $pid = $category['parent_id'];
			    if (!$pid) {
			        $pid = $categotyId;
			        $categotyId = 0;
			    }
    	        return Util_Statist::getCategoryDetailUrl($categotyId, $pid, $statistSrc);
	        case Client_Service_Ad::ADLINK_TYPE_SUBJECT :
	            $subjectId = $link;
	            $subject = Client_Service_Subject::getSubject($subjectId);
	            if(!$subject['status']) return false;
	            return Util_Statist::getSubjectDetailUrl($subjectId, $statistSrc);
            case Client_Service_Ad::ADLINK_TYPE_LINK :
                return Util_Statist::getOutLinkUrl($link, $statistSrc);
            case Client_Service_Ad::ADLINK_TYPE_ACTIVITY :
                $activityId = $link;
                $params['start_time'] = array('<=',Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
                $params['status'] = 1;
                $params['id'] = $activityId;
                $orderBy = array('sort'=>'DESC','start_time'=>'DESC');
                list(, $hd) = Client_Service_Hd::getList(1, 1, $params, $orderBy);
                $hd = $hd[0];
                if(!$hd['status']) return false;
                return Util_Statist::getActivityDetailUrl($activityId, $statistSrc);
	    }
	    return false;
	}
	
	private function webGameList() {
	    $page = $this->getPageInput();
	    list($total, $webGames) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>2, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
	    $list = array();
	    $baseIndex = ($page-1) * $this->perpage + 1;
	    foreach ($webGames as $key=>$value) {
            $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
            if (!$game) {
                continue;
            }
            $list[] = array(
                        'name' => $game['name'],
                        'size' => $game['size'].'M',
                        'info' => $game['resume'],
                        'typeName' => $game['category_title'],
                        'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
		                'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
                        'imgUrl' => $game['img'],
            );
	    }
	    $hasNext = $page * $this->perpage < $total;
	    return $data = array(
	                    'list'=>$list,
	                    'hasNext'=>$hasNext,
	                    'curPage'=>$page,
	                    'ajaxUrl' => $this->actions['webGameAjax'].'?'.Util_Statist::getCurStatistStr()
	    );
	}
	
	private function crackGameList() {
	    $page = $this->getPageInput();
	    $orderBy = array('sort'=>'DESC', 'game_id'=>'DESC');
	    $categoryParams['parent_id'] = (ENV=='product')?'157':'139';
	    $categoryParams['game_status'] = 1;
	    list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $categoryParams, $orderBy);
	    $list = array();
	    $baseIndex = ($page-1) * $this->perpage + 1;
	    foreach ($games as $key=>$value) {
	        $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
	        if (!$game) {
	            continue;
	        }
	        $list[] = array(
	                        'name' => $game['name'],
	                        'size' => $game['size'].'M',
	                        'info' => $game['resume'],
	                        'typeName' => $game['category_title'],
	                        'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
			                'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
	                        'imgUrl' => $game['img'],
	        );
	    }
	    $hasNext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
	    return $data = array(
	                    'list'=>$list,
	                    'hasNext'=>$hasNext,
	                    'curPage'=>$page,
	                    'ajaxUrl' => $this->actions['crackGameAjax'].'?'.Util_Statist::getCurStatistStr()
	    );
	}
	
	private function recommendGameList() {
	    $page = $this->getPageInput();
	    $id = $this->getInput('id');
	    list($total, $games) = Game_Service_H5HomeDataHandle::getGameList($page, $this->perpage, array("recommend_id" => $id));
	    $list = array();
	    $baseIndex = ($page-1) * $this->perpage + 1;
	    foreach ($games as $key=>$value) {
	        $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
	        if (!$game) {
	            continue;
	        }
	        $list[] = array(
	                        'name' => $game['name'],
	                        'size' => $game['size'].'M',
	                        'info' => $game['resume'],
	                        'typeName' => $game['category_title'],
	                         'href' => Util_Statist::getGameDetailUrl($game['id'], '', $key+$baseIndex),
			                'download' => Util_Statist::getDownloadUrl($game['id'], $game['link'], $key+$baseIndex),
	                        'imgUrl' => $game['img'],
	        );
	    }
	    $hasNext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
	    return $data = array(
	                    'list'=>$list,
	                    'hasNext'=>$hasNext,
	                    'curPage'=>$page,
	                    'ajaxUrl' => $this->actions['recommendGameAjax'].'?id='.$id.'&'.Util_Statist::getCurStatistStr()
	    );
	}
}
