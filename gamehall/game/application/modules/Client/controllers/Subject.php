<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Client_BaseController {
	
	public $actions =array(
		'index' => '/client/games/index',
		'detailUrl' => '/client/index/detail/',
	    'tjUrl'=>'/client/index/tj',
	    'subDetailUrl' => Client_Service_Subject::CLIENT_URL,
	    'customDetailUrl' => Client_Service_Subject::CLIENT_URL,
	);
	public $perpage = 10;
	
	/**
	 * subject list
	 */
	public function indexAction() {
		$sp = $this->getInput('sp');
		$this->assign('source', $this->getSource());
		$this->assign('sp', $sp);
		$this->assign('cache', Game_Service_Config::getValue('game_client_cache'));
		Common::addSEO($this,'专题');
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$sp = $this->getInput('sp');
		$subject = Client_Service_Subject::getSubject($id);
        if(! $subject) {
            $this->redirect('/Client/Error/index/');
            exit();
        }
        Yaf_Dispatcher::getInstance()->disableView();
		if($subject['sub_type'] == Client_Service_Subject::SUBTYPE_CUSTOM) {
		    if($sp) {
    		    $spArr = Common::parseSp($sp);
    		    $clientVersion = Common::getClientVersion($spArr['game_ver']);
		    }
        	if(Game_Api_Util_SubjectUtil::isSubjectCustomShowToClient($clientVersion)) {
        	    $this->initCustomView($subject);
        	}else{
		        $this->initListView($subject);
		    }
		}else{
		    $this->initListView($subject);
		}
	}
	
	/**
	 * 活动说明
	 */
	public function hdinfoAction() {
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		$info = Client_Service_Subject::getSubject($id);
		$this->assign('info', $info);
	}
	
	/**
	 * subject json list
	 */
	public function moreSjAction(){
	    $id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$intersrc = 'SUBJECT'.$id;
		$page = intval($this->getInput('page'));
		$webroot = Common::getWebRoot();
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    list($total,$subject_game_ids) = Client_Service_SubjectGames::getPageDistinctGameList($page, $this->perpage, array('subject_id'=>$id, 'game_status'=>1));
		$subject_game_ids = Common::resetKey($subject_game_ids, 'game_id');
		$resource_ids = array_keys($subject_game_ids);

		if ($resource_ids) {
			foreach($resource_ids as $key=>$value) {
				$info = Resource_Service_GameData::getGameAllInfo($value);
				if ($info) $games[] = $info;
			}
		}
		
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		foreach($games as $key=>$value) {
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&pc=1&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			if ($checkVer >= 2) {
				//加入评测链接
				$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($value['id']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = ',评测,' . $webroot . '/client/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $this->getSource();
				}
				//附加属性处理
				$attach = array();
				if ($evaluationId)	array_push($attach, '评');
				if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) array_push($attach, '礼');
			}
			
			$data = array(
					'id'=>$value['id'],
					'name'=>$value['name'],
					'resume'=>$value['resume'],
					'size'=>$value['size'].'M',
					'link'=>Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link']),
					'alink'=>urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&pc=1&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
					'img'=>urldecode($value['img']),
					'profile'=>htmlentities(html_entity_decode($value['name'], ENT_QUOTES)).','.$href.','.$value['infpage'],
			);
			
			if ($checkVer >= 2) {
				//js a 标签 data-infpage 参数数据
				$data_info = '游戏详情,'.$href.','.$value['id'];
				$data['profile'] = $evaluationId ? $data_info . $evaluationUrl : $data_info;
				$data['attach'] = ($attach) ? implode(',', $attach) : '';
				$data['device'] = $value['device'];
				$data['data-type'] = 1;
			}
			$temp[] = $data;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
    
	
	private function initListView($subject){
	    $id = intval($this->getInput('id'));
	    $intersrc = $this->getInput('intersrc');
	    $sp = $this->getInput('sp');
	    $this->assign('info', $subject);
	    $page = intval($this->getInput('page'));
	
	    if ($page < 1) $page = 1;
	    //get game_ids
	    $params = array('subject_id'=>$id, 'game_status'=>1);
	    
	    list($total, $subject_games) = Client_Service_SubjectGames::getPageDistinctGameList($page, $this->perpage, $params);
	    $resource_ids = Common::resetKey($subject_games, 'game_id');
	    $resource_ids = array_keys($resource_ids);
	    
	    //判断游戏大厅版本
	    $checkVer = $this->checkAppVersion();
	    if ($resource_ids) {
	        foreach($resource_ids as $key=>$value) {
	            $info = Resource_Service_GameData::getGameAllInfo($value);
	            if ($info){
	                if ($checkVer >= 2) {
	                    //增加评测信息
	                    $info['pc_info'] = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
	                    //增加礼包信息
	                    $info['gift_info'] = Client_Service_IndexAdI::haveGiftByGame($info['id']);
	                }
	                $games[] = $info;
	            }
	        }
	    }
	
	    $this->assign('games', $games);
	    $hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
	    $this->assign('checkver', $checkVer);
	    $this->assign('sp', $sp);
	    $this->assign('hasnext', $hasnext);
	    $this->assign('page', $page);
	    $this->assign('id', $id);
	    $this->assign('total', $total);
	    $this->assign('source', $this->getSource());
	    $this->assign('intersrc', $intersrc);
		Common::addSEO($this, html_entity_decode($subject['title'], ENT_QUOTES));
	    $this->getView()->display("subject/detail.phtml");
	}
	
	private function initCustomView($subject){
    	$sp = $this->getInput('sp');
    	$spArr = Common::parseSp($sp);
		$subjectId = $subject['id'];
		if (! $sp || $subjectId <= 0) {
		    $this->redirect('/Client/Error/index/');
		    exit;
		}
		$items = Client_Service_SubjectItems::getsBySubId($subjectId);
		$gameIds = array();
		$itemGames = array();
		$params = array('subject_id' => $subjectId, 'game_status' => Resource_Service_Games::STATE_ONLINE);
		$games = Client_Service_SubjectGames::getSubjectGamesBy($params);
		foreach ($games as $game) {
		    $itemGames[$game['item_id']][] = $game;
		    $gameId[] = $game['game_id'];
		}
		$gamesInfo = array();
		$gameIds = array_unique($gameId);
		foreach ($gameIds as $gameId) {
		    $game = Resource_Service_GameData::getGameAllInfo($gameId);
		    $gamesInfo[$gameId] = $game;
		}

		$onlineList = $this->getOnlineSubject($sp);
        $this->initLeftRight($subjectId, $onlineList);		
		$this->assign('info', $subject);
		$this->assign('items', $items);
		$this->assign('itemGames', $itemGames);
		$this->assign('gamesInfo', $gamesInfo);
		$this->assign('source', $this->getSource());
		
		Common::addSEO($this, html_entity_decode($subject['title'], ENT_QUOTES));
	    $this->getView()->display("subject/custom.phtml");
	}
	
	private function initLeftRight($subjectId, $subjectList) {
	    $before = null;
	    $after = null;
	    $count = count($subjectList);
	    if($count < 2) {
	        return;
	    }
	    for ($i = 0; $i < $count; $i++) {
	        $subject = $subjectList[$i];
	        if($subject['id'] == $subjectId) {
	            if($i > 0) {
	                $before = $subjectList[$i-1];
	            }
	            if($i+1 < $count) {
	                $after = $subjectList[$i+1];
	            }
	            break;
	        }
	    }
	    if( !$before && !$after) {
	        $before = $subjectList[0];
	        $after = $subjectList[1];
	    }else{
	        if (! $before) {
	            $before = $subjectList[0];
	            if($before['id'] == $subjectId) {
	                $before = $subjectList[$count - 1];
	            }
	        }
	        if (! $after) {
	            $after = $subjectList[$count - 1];
	            if($after['id'] == $subjectId) {
	                $after = $subjectList[0];
	            }
	        }
    	    if($before['id'] == $after['id']) {
    	        return;
    	    }
	    }
	    $this->assign('before', $before);
	    $this->assign('after', $after);
	}
	
	private function getOnlineSubject($sp) {
	    $pgroup = array(0);
	    $spArr = Common::parseSp($sp);
	    $device  = $spArr['device'];
	    $group = Resource_Service_Pgroup::getGroupByDevice($device);
	    if($group > 0) {
	        $pgroup[] = $group;
	    }
	    $searchParams['pgroup'] = array('IN', $pgroup);
	    $searchParams['status'] = 1;

	    $startTime = strtotime(date('Y-m-d H:00:00'));
	    $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
	    $searchParams['start_time'] = array('<=', $startTime);
	    $searchParams['end_time'] = array('>=', $endTime);
	    $list = Client_Service_Subject::getTopList($searchParams, 20);
	    return $list;
	}
	
}
