<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GuessController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/client/guess/index',
		'indexlUrl' => '/client/index/detail/',
		'tjUrl' => '/client/index/tj'
	);

	public $perpage = 10;

	
	public function indexAction(){
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$imei = crc32(trim($imei));
		
		Common::addSEO($this,'猜你喜欢');
		
		if ($page < 1) $page = 1;
		$resource_games = array();
		
		$currentTime = Common::getTime();
		$startTime = '1435680000'; //2015/7/1 0:0:0
		$endTime = '1436716799'; //2015/7/12 23:59:59
		
		if($currentTime >= $startTime && $currentTime <= $endTime){
		    $gues = 1;
		    $games = array();
		    list($total, $games) = Resource_Service_Attribute::guessTemporaryList($page);
		} else {
    		//list($total, $games) = Client_Service_Guess::getList(1, $this->perpage, $params, array('imcrc'=>'DESC'));
    		$gues = Client_Service_Guess::getGamesByImCrc( $imei );
    	    $ids = explode(',',$gues['game_ids']);
    	    if($gues){
    				list($total, $games) = Resource_Service_Games::search(1, $this->perpage, array('id'=>array('IN', $ids),'status'=>1));
    	    }
    		//如果猜你喜欢没有数据家用默认的代替
    		if(!$gues){
    			list($total, $games) = Client_Service_Game::geGuesstList(1, $this->perpage, array('game_status'=>1,'status'=>1));
    		}
		}
		
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion(); 
		foreach($games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array("id"=>($gues ? $value['id'] : $value['game_id'])));
			if ($info) {
				if ($checkVer >= 2) {
					//增加评测信息
					$info['pc_info'] = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
					//增加礼包信息
					$info['gift_info'] = Client_Service_IndexAdI::haveGiftByGame($info['id']);
				}
				
				$resource_games[] = $info;
			}
			
		} 
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('checkver', $checkVer);
		$this->assign('sp', $sp);
		$this->assign('resource_games', $resource_games);
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * subject json list
	 */
	public function moreAction(){
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$imei = crc32(trim($imei));
		
		if ($page < 1) $page = 1;
		$resource_games = array();
		
		$params['imcrc'] = $imei;
		if ($page < 1) $page = 1;
		$resource_games = array();
		$currentTime = Common::getTime();
		$startTime = '1435680000'; //2015/7/1 0:0:0
		$endTime = '1436716799'; //2015/7/12 23:59:59
		
		if($currentTime >= $startTime && $currentTime <= $endTime){
		    $gues = 1;
		    $games = array();
		    list($total, $games) = Resource_Service_Attribute::guessTemporaryList($page);
		} else {
    		//get games list
    		$gues = Client_Service_Guess::getGamesByImCrc( $imei );
    		$ids = explode(',',$gues['game_ids']);
    	    if($gues){
    				list($total, $games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $ids),'status'=>1));
    	    }
    		
    		//如果猜你喜欢没有数据家用默认的代替
    		if(!$gues){
    			list($total, $games) = Client_Service_Game::geGuesstList($page, $this->perpage, array('game_status'=>1,'status'=>1));
    		}
		}
		
		
		$temp = array();
		$webroot = Common::getWebRoot();
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		
		foreach($games as $key=>$value) {
				
		
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>($gues ? $value['id'] : $value['game_id'])));
			$intersrc = 'glike_GID'.$info['id'];
				
			$href = urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&pc=1&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			
			if ($checkVer >= 2) {
				//加入评测链接
				$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = ',评测,' . $webroot . '/client/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $this->getSource();
				}
				//附加属性处理
				$attach = array();
				if ($evaluationId)	array_push($attach, '评');
				if (Client_Service_IndexAdI::haveGiftByGame($info['id'])) array_push($attach, '礼');
			}
			
			$data = array(
						'id'=>$info['id'],
						'name'=>$info['name'],
						'resume'=>$info['resume'],
						'size'=>$info['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $info['id'], $intersrc, $info['link']),
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&intersrc=glike_GID'.$info['id'].'&t_bi='.$this->getSource()),
						'img'=>urldecode($info['img']),
						'profile' => $info['name'].','.$href.','.$info['id'].','.$info['link'].','.$info['package'].','.$info['size'].','.'Android'.$info['min_sys_version_title'].','.$info['min_resolution_title'].'-'.$info['max_resolution_title'],
			);

			if ($checkVer >= 2) {
				//js a 标签 data-infpage 参数数据
				$data_info = '游戏详情,'.$href.','.$info['id'];
				$data['profile'] = $evaluationId ? $data_info . $evaluationUrl : $data_info;
				$data['attach'] = ($attach) ? implode(',', $attach) : '';
				$data['device'] = $info['device'];
				$data['data-type'] = 1;
			}
			$temp[] = $data;
		
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
}
