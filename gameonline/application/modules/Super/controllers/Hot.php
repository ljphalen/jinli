<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class HotController extends Super_BaseController{
	
	public $actions = array(
		'listUrl' => '/index/index',
		'detailUrl' => '/super/game/detail/',
		'tjUrl' => '/super/game/tj'
	);

	public $perpage = 100;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$title = '推荐游戏';
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		//首页轮转广告
		list($ad_total, $ads) = Game_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>1));
		$this->assign('ads', $ads);
		//最新游戏
		$configs = Game_Service_Config::getAllConfig();
		list(, $newGames) = Game_Service_Game::getList($page, $configs['game_num'], array('status'=>1), array('create_time'=>'DESC', 'sort'=>'DESC'));
		//推荐游戏
		list(, $hotGames) = Game_Service_Ad::getCanUseNormalAds(1, $this->perpage, array('ad_type'=>2));
		$hotGames = self::_gameData($hotGames);
		
		$this->assign('subjects', $subjects);
		$this->assign('newGames', $newGames);
		$this->assign('hotGames', $hotGames);
		$this->assign('title', $title);
		$this->assign('source', $this->getSource());
		$webroot = Common::getWebRoot();
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
	}
	
	private static function _gameData(array $data) {
		$tmp = array();
		$n =1;
		foreach($data as $key=>$value){
			if(($key +1) % 4 == 0 && $key <= count($data)){
				for($i=0;$i<4;$i++){
					$tmp[$n][] = $data[$key - 3 + $i];
				}
				$n++;
			}
			if(($key + 1) % 4 !=0 && $key == (count($data)-1)){
				for($j=0;$j< (count($data) % 4);$j++){
					$tmp[$n][] = $data[count($data) - (count($data) % 4) + $j];
				}
			}
		}
		return $tmp;
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list(, $games) = Game_Service_Ad::getCanUseNormalAds($page, $this->perpage, array('ad_type'=>2));
		
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($games as $key=>$value) {
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['name'] = $value['name'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], 'GAME', $value['link']);
			$temp[$key]['alink'] = urldecode($this->actions['detailUrl']. '?id=' . $value['id']);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	
	/**
	 * 
	 * get game detail info
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Game_Service_Game::getGameInfo($id);
		$this->assign('info', $info);
		list(, $gimgs) = Game_Service_GameImg::getList(0,10, array('game_id'=>$id));
		$this->assign('gimgs', $gimgs);
		list($total, $categorys) = Game_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		//game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$this->assign('configs', $configs);
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$gimgs));
		}
	}
}
