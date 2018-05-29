<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SingleController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/channel/single/index',
		'detailUrl' => '/channel/index/detail/',
		'tjUrl' => '/channel/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
    public function indexAction() {
		$page = intval($this->getInput('page'));
		if (!$t_bi) $this->setSource();
		$intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		
		//广告位intersrc
		$intersrc = ($intersrc ? $intersrc . '_adp13' : 'pcg_adp13');
		list(, $ad) = Client_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>1,'status'=>1,'hits'=>3));
		$tmp = array();
		foreach($ad as $key=>$value){
			if($value['ad_ptype'] != 5 ){
				$tmp = $value;break;
			}
		}
		$ad = $tmp;
		$topad = Client_Service_Ad::getDataAd($ad['ad_ptype'],$ad['title'],$ad['link'],$ad['img'],$adsrc,$t_bi);
		
    	list($total, $Web_games) = Client_Service_Channel::getList($page, $this->perpage, array('ctype'=>1, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
    	$game_ids = Common::resetKey($Web_games, 'game_id');
    	$game_ids = array_unique(array_keys($game_ids));
    	if ($game_ids) {
    		foreach($game_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
	    		if ($info) $games[] = $info; 
    		}
    	}
		$this->assign('games', $games);
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('ad', $ad);
		$this->assign('topad', $topad);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction(){
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) $intersrc = "pcg";
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
		
		foreach($games as $key=>$value) {
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[] = array(
						'id'=>$value['id'],
						'name'=>$value['name'],
						'resume'=>$value['resume'],
						'size'=>$value['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link']),
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($value['img']),
						'profile'=>$value['name'].','.$href.','.$value['infpage'],
			);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
		$this->assign('page', $page);
	}
}
