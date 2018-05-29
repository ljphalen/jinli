<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Kingstone_BaseController {
	
	public $actions =array(
				'index' => '/kingstone/games/index',
				'detailUrl' => '/kingstone/index/detail/',
			    'subDetailUrl' => '/kingstone/subject/detail/',
			    'tjUrl'=>'/kingstone/index/tj'
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
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		
		$info = Client_Service_Subject::getSubject($id);
		$this->assign('info', $info);
		$page = intval($this->getInput('page'));
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    list($total,$subject_game_ids) = Client_Service_Game::getSubjectGames(1, $this->perpage, array('subject_id'=>$id,'status'=>1,'game_status'=>1));
	    $resource_ids = Common::resetKey($subject_game_ids, 'resource_game_id');
	    $resource_ids = array_unique(array_keys($resource_ids));
	    
	    //判断游戏大厅版本
	    $checkVer = $this->checkAppVersion();
		if ($resource_ids) {
    		foreach($resource_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
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
	    list($total,$subject_game_ids) = Client_Service_Game::getSubjectGames($page, $this->perpage, array('subject_id'=>$id,'status'=>1,'game_status'=>1));
		$subject_game_ids = Common::resetKey($subject_game_ids, 'resource_game_id');
		$resource_ids = array_unique(array_keys($subject_game_ids));

		if ($resource_ids) {
			foreach($resource_ids as $key=>$value) {
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
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
					$evaluationUrl = ',评测,' . $webroot . '/kingstone/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $this->getSource();
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
					'profile'=>$value['name'].','.$href.','.$value['infpage'],
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
}
