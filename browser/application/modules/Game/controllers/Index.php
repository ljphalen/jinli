<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class IndexController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/index/index',
		'detailUrl' => '/index/detail/'
	);

	public $perpage = 5;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		list($ad_total, $ads) = Browser_Service_Ad::getCanUseAds(1, 10, array('ad_type'=>2));
		$this->assign('ads', $ads);
		$this->assign('ad_total', $ad_total);

		list($total, $games) = Browser_Service_Game::getList(1, $this->perpage, array('status'=>1));
		$hasnext = (ceil((int) $total / $this->perpage) - (1)) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list($total, $games) = Browser_Service_Game::getList($page, $this->perpage, array('status'=>1));
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		$temp = array();
		foreach($games as $key=>$value) {
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['name'] = $value['name'];
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['link'] = $value['link'];
			$temp[$key]['alink'] = urldecode($this->actions['detailUrl']. '?id=' . $value['id']);
			$temp[$key]['img'] = urldecode($webroot . '/attachs/'.$value['img']);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	/**
	 * get game ads
	 */
	public function adsAction(){
		list($ad_total, $ads) = Browser_Service_Ad::getCanUseAds(1, 10, array('ad_type'=>2));
		$this->output(0, '', $ads);
	}
	
	/**
	 * 
	 * get game detail info
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Browser_Service_Game::getGame($id);
		$this->assign('info', $info);
		list(, $gimgs) = Browser_Service_GameImg::getList(0,10, array('game_id'=>$id));
		$this->assign('gimgs', $gimgs);
		
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$gimgs));
		}
	}
}
