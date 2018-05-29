<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Super_BaseController{
	
public $actions = array(
		'listUrl' => '/category/index',
		'detailUrl' => '/category/detail/',
		'indexlUrl' => '/super/game/detail/',
		'tjUrl' => '/super/game/tj'
	);

	public $perpage = 8;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$this->setSource();
		$title = '游戏分类';
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		//首页bannel
		list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ptype'=>1));
		$this->assign('bannel', $bannel[0]);
		
		//游戏分类
		list(, $categorys) = Game_Service_Category::getList(1, 30, array('status'=>1));
		
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->assign('title', $title);
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('categorys', $categorys);
		$this->assign('configs', $configs); 
		$this->assign('source', $this->getSource());
		
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		//游戏分类
		list(, $categorys) = Game_Service_Category::getList($page, $this->perpage, array('status'=>1));
		
		$webroot = Common::getWebRoot();
		
		
		$temp = array();
		foreach($games as $key=>$value) {
			if($value['id'] < 10){
				$t_id = 'CATEGORY0'.$value['id'];
			} else {
				$t_id = 'CATEGORY'.$value['id'];
			}
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] =  urldecode($this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$t_id.'&t_bi='.$this->getSource());
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		//get games list
		if($id == '0' || !$id){          //所有分类   
			list($total, $games) = Game_Service_Game::getCanUseGames(1, $this->perpage, array('status'=>1));
			$this->assign('title', '全部游戏');
		} else {
			list($total, $games) = Game_Service_Game::getCanUseGames(1, $this->perpage, array('category_id'=>intval($id),'status'=>1));
			$info = Game_Service_Category::getCategory(intval($id));
			$this->assign('title', $info['title']);
		}
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
	
		if ($page < 1) $page = 1;
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('games', $games);
		$this->assign('page', $page);
		$this->assign('id', $id);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * subject json list
	 */
	public function moreCtAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) {
			if($id == '0' || !$id){
				$intersrc = 'CATEGORY00';
			} else {
				$intersrc = 'CATEGORY'.$id;
			}
		}
		$page = intval($this->getInput('page'));
	
		if ($page < 1) $page = 1;
		
	    //客户端
		$configs = Game_Service_Config::getAllConfig();
		//get games list
		if($id == '0' || !$id){          //所有游戏   
			list($total, $games) = Game_Service_Game::getCanUseGames($page, $this->perpage, array('status'=>1));
		}  else {
			list($total, $games) = Game_Service_Game::getCanUseGames($page, $this->perpage, array('category_id'=>$id,'status'=>1));
		}
		$temp = array();
		foreach($games as $key=>$value) {
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['name'] = $value['name'];
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], 'GAME', $value['link']);
			$temp[$key]['alink'] = urldecode($this->actions['indexlUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
		}

		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
}
