<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/category/index',
		'detailUrl' => '/category/detail/',
		'indexlUrl' => '/index/detail/',
		'tjUrl' => '/index/tj'
	);

	public $perpage = 10;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		//首页bannel
		list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ltype'=>11));
		$this->assign('bannel', $bannel[0]);
	    //游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getsortList(1, 8, array('at_type'=>1,'status'=>1));
		//客户端
		foreach($categorys as $key=>$value) {
			$categorys[$key]['game_title'] = Resource_Service_Attribute::getGameData($value['id'],'',$this->filter);
		}
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		$hasnext = (ceil((int) $total / 8) - 1) > 0 ? true : false;
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
	    $intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		//游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getsortList($page, 8, array('at_type'=>1,'status'=>1));
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($categorys as $key=>$value) {
			if(!$intersrc) $intersrc = 'CATEGORY'.$value['id'];
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $href);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
			$temp[$key]['data-infpage'] = $value['title'].','.urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$t_id.'&t_bi='.$this->getSource());
			$temp[$key]['game_title'] = Resource_Service_Attribute::getGameData($value['id'],'',$this->filter);
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	/**
	 * game detail
	 */
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$this->assign('configs', $configs);
		
		$page = intval($this->getInput('page'));
	    
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//get games list
		if($id == '100'){              //所有分类
			$orderBy = array('id'=>'DESC');
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			$orderBy = array('online_time'=>'DESC');
			$limit = Game_Service_Config::getValue('game_num');
			$this->perpage = min($limit, $this->perpage);
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = Game_Service_Config::getValue('game_num');
			$this->assign('title', '最新游戏');
		} else {
			$orderBy = array('sort'=>'DESC', 'game_id'=>'DESC');
			$params['category_id'] = $id;
			$params['game_status'] = 1;
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId($page, $this->perpage, $params, $orderBy);
			$gameAttr = Resource_Service_Attribute::getResourceAttribute($id);
			$this->assign('title', $gameAttr['title']);
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
			$orderBy = array('id'=>'DESC');
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			$orderBy = array('online_time'=>'DESC');
			$limit = Game_Service_Config::getValue('game_num');
			$this->perpage = min($limit, $this->perpage);
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = Game_Service_Config::getValue('game_num');
			$this->assign('title', '最新游戏');
		} else {
			$orderBy = array('sort'=>'DESC', 'game_id'=>'DESC');
			$params['category_id'] = $id;
			$params['game_status'] = 1;
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId($page, $this->perpage, $params, $orderBy);
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
						'resume'=>$info['resume'],
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
	
	/**
	 * 获取前3个游戏的名称
	 * @param unknown_type $category_id
	 * @return string
	 */
	public static function getGameData($category_id) {
		if (!$category_id) $this->output(-1, "");
		//get games list
		$game_title = array();
		$params = array('status'=>1);
		if($category_id == '100'){              //全部游戏
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			$orderBy = array('id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList(1, 3, $params, $orderBy);
		} else if($category_id == '101'){      //最新游戏
			$orderBy = array('create_time'=>'DESC');
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getVersionList(1, 3, $params, $orderBy);
		} else if($category_id == 'caini'){      //猜你喜欢
			$orderBy = array('id'=>'DESC');
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Client_Service_Guess::getList(1, 3, $params, $orderBy);
		} else {
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			$params['category_id'] = $category_id;
			$params['game_status'] = 1;
			//游戏大厅|艾米游戏过滤
			if($this->filter){
				$params['game_id'] = array('NOT IN', $this->filter);
			}
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId(1, 3, $params, $orderBy);
		}
			
		foreach($games as $key=>$value) {
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			if ($info) {
				$game_title[] = $info['name'];
			}
		}
		return implode('、',$game_title);
	}
}
