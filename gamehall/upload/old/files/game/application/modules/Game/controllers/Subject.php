<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Game_BaseController {
	
	public $actions =array(
				'index' => '/subject/index',
	            'listAjax' => '/subject/listAjax',
				'detailUrl' => '/index/detail/',
	            'moreGameAjax' => '/subject/moreGameAjax/',
			    'subDetailUrl' => '/subject/detail/',
			    'tjUrl'=>'/index/tj'
			);
	public $perpage = 10;
	
	public static $viewTplType = array(
	                Client_Service_Subject::SUBTYPE_LIST => array(1 => 'white', 0 => 'blue'),
	                Client_Service_Subject::SUBTYPE_CUSTOM => array(1 => 'multicolorTwo', 0 => 'multicolor'),
	);
	
	/**
	 * subject list
	 */
	public function indexAction() {
	    list($data, $hasNext, $page) = $this->getSubjectList();
        $this->assign('data', json_encode(array(
                        'list' => $data, 
                        'hasNext' => $hasNext, 
                        'curPage' => $page, 
                        'ajaxUrl' => $this->actions['listAjax'].'?'.Util_Statist::getCurStatistStr()
        ))); 
	}
	
	public function listAjaxAction() {
	    list($data, $hasNext, $page) = $this->getSubjectList();
	    $this->ajaxOutput(array('list' => $data, 'hasNext' => $hasNext, 'curPage' => $page));
	}
	
    private function getSubjectList() {
        $page = $this->getPageInput();
        $params['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
        $startTime = strtotime(date('Y-m-d H:00:00'));
        $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
        $params['start_time'] = array('<=', $startTime);
        $params['end_time'] = array('>=', $endTime);
        list($total, $subjectList) = Client_Service_Subject::getList($page, $this->perpage, $params);
        $data = array();
        $attachPath = Common::getAttachPath();
        foreach ($subjectList as $subject) {
            $item = array(
                            'name' => $subject['title'],
                            'date' => date('Y-m-d', $subject['start_time']),
                            'imgUrl' => $attachPath. $subject['icon'],
                            'type' => $subject['view_tpl'],
                            'info' => strip_tags(html_entity_decode($subject['resume'])),
                            'href' => Util_Statist::getSubjectDetailUrl($subject['id'])
            );
            $data[] = $item;
        }
        $hasNext = $this->perpage * $page < $total;
        return array($data, $hasNext, $page, $total);
    }
	
	public function detailAction(){
	    $subjectId = $this->getInput('id');
	    $subject = Client_Service_Subject::getSubject($subjectId);
	    $title = $subject['title'];
	    $subType = $subject['sub_type'];
	    $data = array();
	    $attachPath = Common::getAttachPath();
	    $data['topic'] = array(
	                    'name' => $subject['title'],
	                    'date' => date('Y-m-d', $subject['start_time']),
	                    'imgUrl' => $attachPath. $subject['img'],
	                    'info' => strip_tags(html_entity_decode($subject['resume']))
	    );
	    if ($subType == Client_Service_Subject::SUBTYPE_LIST) {
	        $data['topicInfo'] = $this->getSubjectGameList($subject);
	    } else if ($subType == Client_Service_Subject::SUBTYPE_CUSTOM) {
	        $data['hot'] = $this->getRecommendSubject($subjectId);
	        $data['topicInfo'] = $this->getCustomSubjectDetail($subject);
	    }
	    $this->assign('data', json_encode($data));
	    $this->assign('title', $title);
	}
	
	public function moreGameAjaxAction() {
	    $subjectId = $this->getInput('id');
	    $subject = Client_Service_Subject::getSubject($subjectId);
	    $data = $this->getSubjectGameList($subject);
	    $this->ajaxOutput($data);
	}
	
	private function getSubjectGameList($subject) {
	    $id = $subject['id'];
	    $attachPath = Common::getAttachPath();
	    $page = $this->getPageInput();
	    $params = array('subject_id'=>$id, 'game_status'=>1);
	    if($subject['status'] == Client_Service_Subject::SUBJECT_STATUS_OPEN) {
	        list($total, $subjectGames) = Client_Service_SubjectGames::getPageDistinctGameList($page, $this->perpage, $params);
	    }
	    $subjectGameIds = Common::resetKey($subjectGames, 'game_id');
	    $resourceIds = array_keys($subjectGameIds);
	    foreach($resourceIds as $gameId) {
	        $info = Resource_Service_GameData::getGameAllInfo($gameId);
	        $temp = array(
	                        'name'=>html_entity_decode($info['name']),
	                        'stars' => $info['web_star'],
	                        'size'=>$info['size'].'M',
	                        'info'=>html_entity_decode($info['resume']),
	                        'href'=>Util_Statist::getGameDetailUrl($gameId),
	                        'download'=>Util_Statist::getDownloadUrl($info['id'], $info['link']),
	                        'imgUrl'=>urldecode($info['img']),
	        );
	        $list[] = $temp;
	    }
	    $hasNext = $page * $this->perpage < $total;
	    $data = array(
	                    'list'=>$list, 
	                    'hasNext'=>$hasNext, 
	                    'curPage'=>$page, 
	                    'total'=>$total,
	                    'type' => self::$viewTplType[$subject['sub_type']][$subject['view_tpl']],
	                    'ajaxUrl' => $this->actions['moreGameAjax'].'?id='.$id.'&'.Util_Statist::getCurStatistStr()
	    );
	    
	    return $data;
	}
	
	private function getRecommendSubject($curSubjectId) {
	    $params['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
	    $startTime = strtotime(date('Y-m-d H:00:00'));
	    $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
	    $params['start_time'] = array('<=', $startTime);
	    $params['end_time'] = array('>=', $endTime);
	    $allSubject = Client_Service_Subject::getsBy($params);
	    $recommendSubjects = array();
	    $total = count($allSubject);
	    if ($total < 3) {
	    	return array();
	    }
	    $recommendSubjects[0] = $allSubject[$total - 1];
	    for($i=0; $i<$total; $i++) {
	        if ($allSubject[$i]['id'] == $curSubjectId) {
	            if($i == $total - 1) {
	                $recommendSubjects[1] = $allSubject[0];
	            } else {
	                $recommendSubjects[1] = $allSubject[$i + 1];
	            }
	            break;
	        } else {
	            $recommendSubjects[0] = $allSubject[$i];
	        }
	    }
	    $data = array();
	    $attachPath = Common::getAttachPath();
	    foreach ($recommendSubjects as $subject) {
	        $item = array(
	                        'name' => $subject['title'],
	                        'imgUrl' => $attachPath. $subject['icon'],
	                        'href' => Util_Statist::getSubjectDetailUrl($subject['id'])
	        );
	        $data[] = $item;
	    }
	    return $data;
	}
	
    private function getCustomSubjectDetail($subject){
		$subjectId = $subject['id'];
		$subjectSubItems = Client_Service_SubjectItems::getsBySubId($subjectId);
		$params = array(
		                'subject_id' => $subjectId, 
		                'game_status' => Resource_Service_Games::STATE_ONLINE
		);
		$subjectGames = Client_Service_SubjectGames::getSubjectGamesBy($params);
		$itemGames = array();
		foreach ($subjectGames as $subjectGame) {
		    $itemGames[$subjectGame['item_id']][] = $subjectGame;
		}
		
        $list = array();
		foreach ($subjectSubItems as $item) {
		    $itemId = $item['item_id'];
		    $tplType = $item['view_tpl'];
		    if ($tplType == 1) {
		    	$gameId = $itemGames[$itemId][0]['game_id'];
		    	$game = Resource_Service_GameData::getGameAllInfo($gameId);
		    	if(!$game) {
		    	    continue;
		    	}
		    	$list[] = array(
	                       'name' => $item['title'],
	                       'stars' => $game['web_star'],
	                       'size' => $game['size'],
		    	           'language' => $game['language'],
	                       'type' => $game['category_title'],
	                       'info' => $item['resume'],
	                       'href' => Util_Statist::getGameDetailUrl($game['id']),
	                       'download' => Util_Statist::getDownloadUrl($game['id'], $game['link']),
	                       'imgUrl' => $game['img'],
		    	           'posters' => array($game['simgs'][0], $game['simgs'][1])
                );
		    } else if ($tplType == 6) {
		    	$gameList = array();
		    	$games = $itemGames[$itemId];
		    	foreach ($games as $value) {
		    	    $game = Resource_Service_GameData::getGameAllInfo($value['game_id']);
		    	    if (!$game) {
		    	    	continue;
		    	    }
		    	    $gameList[] = array(
		    	                    'name' => $game['name'],
		    	                    'info' => $value['resume'],
		    	                    'href' => Util_Statist::getGameDetailUrl($game['id']),
		    	                    'download' => Util_Statist::getDownloadUrl($game['id'], $game['link']),
		    	                    'imgUrl' => $game['img'],
		    	    );
		    	}
		    	if ($gameList) {
		    	    $list[] = array(
		    	                    'name' => $item['title'],
		    	                    'type' => 'list',
		    	                    'info' => $item['resume'],
		    	                    'data' => $gameList
		    	    );
		    	}
		    }
		}
		$data = array(
		                'list' => $list,
		                'type' => self::$viewTplType[$subject['sub_type']][$subject['view_tpl']]
		);
		return $data;
	}
	
}
