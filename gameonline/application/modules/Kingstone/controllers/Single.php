<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SingleController extends Kingstone_BaseController{
	
	public $actions = array(
		'listUrl' => '/kingstone/single/index',
		'detailUrl' => '/kingstone/index/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
    public function indexAction() {
		$page = intval($this->getInput('page'));
		$sp = $this->getInput('sp');
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		$intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		
		//广告位intersrc
		$adsrc = ($intersrc) ? $intersrc . '_adp13' : 'pcg_adp13';
		list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 1, array('ad_type'=>1,'status'=>1,'hits'=>3));
		$topad = Client_Service_Ad::getDataAd($ad[0]['ad_ptype'],$ad[0]['title'],$ad[0]['link'],$ad[0]['img'],$adsrc,$t_bi);
		
    	list($total, $Web_games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
    	$game_ids = Common::resetKey($Web_games, 'game_id');
    	$game_ids = array_unique(array_keys($game_ids));
    	//判断游戏大厅版本
    	$checkVer = $this->checkAppVersion();
    	if ($game_ids) {
    		foreach($game_ids as $key=>$value) {
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
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('ad', $ad[0]);
		$this->assign('topad', $topad);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction(){
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$intersrc = $this->getInput('intersrc');
		$webroot = Common::getWebRoot();
		
		$games = $Web_games = array();
		list($total, $Web_games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
    	$game_ids = Common::resetKey($Web_games, 'game_id');
    	$game_ids = array_unique(array_keys($game_ids));
    	if ($game_ids) {
    		foreach($game_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
	    		if ($info) $games[] = $info; 
    		}
    	}
    	
    	//判断游戏大厅版本
    	$temp = array();
    	$checkVer = $this->checkAppVersion();
		foreach($games as $key=>$value) {
			$src = ($intersrc) ? $intersrc .'_GID'.$value['id'] : 'pcg'.$value['id'];
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&pc=1&intersrc='.$src.'&t_bi='.$this->getSource());
			if ($checkVer >= 2) {
				//加入评测链接
				$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($value['id']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = ',评测,' . $webroot . '/kingstone/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $src . '&t_bi=' . $this->getSource();
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
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $value['id'].'&pc=1&intersrc='.$src.'&t_bi='.$this->getSource()),
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
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
		$this->assign('page', $page);
	}
}
