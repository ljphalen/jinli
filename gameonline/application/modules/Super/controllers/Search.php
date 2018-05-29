<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SearchController extends Super_BaseController{
    
	public $actions = array(
			'listUrl' => '/search/index',
			'detailUrl' => '/search/detail/',
			'indexlUrl' => '/super/game/detail/',
			'tjUrl' => '/super/game/tj'
	);
	public $perpage = 8;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$title = '游戏查询';
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		$this->assign('source', $this->getSource());
		$this->assign('title', $title);
	}
	
	public function searchAction() {
		$title = '游戏查询';
		$name = $this->getInput('keyword');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $games) = Game_Service_Game::getSearchGames(1, $this->perpage, array('name'=>$name));
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
		$this->assign('page', $page);
		$this->assign('keyword', $name);
		$this->assign('total', $total);
		$this->assign('configs', $configs);
		$this->assign('title', $title);
		$this->assign('source', $this->getSource());
		$this->display('index');
		exit;
	}
	
	/**
	 * search json list
	 */
	public function moreAction(){
		$page = intval($this->getInput('page'));
		$name = $this->getInput('keyword');
		if ($page < 1) $page = 1;
	    list($total, $games) = Game_Service_Game::getSearchGames($page, $this->perpage, array('name'=>$name));
		if($games){
			$temp = array();
			foreach($games as $key=>$value) {
				$temp[$key]['id'] = $value['id'];
				$temp[$key]['name'] = $value['name'];
				$temp[$key]['resume'] = $value['resume'];
				$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], 'GAME', $value['link']);
				$temp[$key]['alink'] = urldecode($this->actions['indexlUrl']. '?id=' . $value['id'].'&intersrc=SEARCH&t_bi='.$this->getSource());
				$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
			}
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('keyword', $name);
	}
}
