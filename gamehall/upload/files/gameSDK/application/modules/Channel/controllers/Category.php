<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Channel_BaseController{
	
	public $actions = array(
		'listUrl' => '/channel/category/index',
		'detailUrl' => '/channel/category/detail/',
		'indexlUrl' => '/channel/index/detail/',
		'tjUrl' => '/channel/index/tj'
	);

	public $perpage = 10;
	public $cacheKey = 'Channel_Category_index';
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		//游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getList($page, $this->perpage, array('at_type'=>1,'status'=>1), array('sort'=>'DESC'));
		//客户端
		foreach($categorys as $key=>$value) {
			$categorys[$key]['game_title'] = Resource_Service_Attribute::getGameData($value['id'], '', $this->filter);
		}
		$hasnext = (ceil((int) $total / 8) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('categorys', $categorys);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('cache', Game_Service_Config::getValue('game_client_cache'));
		
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
	    $page = intval($this->getInput('page'));
	    $intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		//游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getList($page, 8, array('at_type'=>1,'status'=>1), array('sort'=>'DESC'));
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($categorys as $key=>$value) {
			$intersrc = 'CATEGORY'.$value['id'];
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $href);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
			$temp[$key]['data-infpage'] = $value['title'].','.urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$t_id.'&t_bi='.$this->getSource());
			$temp[$key]['game_title'] = Resource_Service_Attribute::getGameData($value['id'], '', $this->filter);
		}
		$hasnext = (ceil((int) $total / 8) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
	    
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//get games list
		if($id == '100'){              //所有分类
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('online_time'=>'DESC');
			$limit = Game_Service_Config::getValue('game_rank_newnum');
			$this->perpage = min($limit, $this->perpage);
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = $limit;
			$this->assign('title', '最新游戏');
		} else {
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			//1.5.6版本标识
			$params['parent_id'] = $id;
			$params['game_status'] = 1;
			//过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $params, $orderBy);
		}
		foreach($games as $key=>$value) {
			
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			if ($info) $resource_games[] = $info;
		} 
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		
		$this->assign('resource_games', $resource_games);
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * subject json list
	 */
	public function moreCtAction(){
		$id = intval($this->getInput('id'));
		
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
	    
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//get games list
		if($id == '100'){              //所有分类
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			//过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('online_time'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = Game_Service_Config::getValue('game_rank_newnum');
			$this->assign('title', '最新游戏');
		} else {
			//过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			//1.5.6版本 一级分类所有数据的标识
			$params['parent_id'] = $id;
			$params['game_status'] = 1;
			list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $params, $orderBy);
		}
		
		$temp = array();
		$webroot = Common::getWebRoot();
		
		$i = 0;
		foreach($games as $key=>$value) {
			$num = $i + (($page - 1) * $this->perpage);
			if ($num >= $total && $id == '101') break;
			
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			$href = urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[] = array(
						'id'=>$info['id'],
						'name'=>$info['name'],
						'resume'=>html_entity_decode($info['resume'], ENT_QUOTES),
						'size'=>$info['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $info['id'], $intersrc, $info['link']),
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($info['img']),
						'profile'=>$info['name'].','.$href.','.$info['id'].','.$info['link'].','.$info['package'].','.$info['size'].','.'Android'.$info['min_sys_version_title'].','.$info['min_resolution_title'].'-'.$info['max_resolution_title']
					);
			$i++;
		}
		
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
}
