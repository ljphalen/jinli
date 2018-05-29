<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Channel_BaseController {
	
	public $actions =array(
			'indexUrl' => '/channel/search/index',
			'detailtUrl' => '/channel/search/detail',
			'errorUrl' => '/channel/search/error',
			'engineUrl' => '/channel/search/engine',
			'enginetUrl' => '/channel/search/enginet',
			'tjUrl' => '/channel/search/index/tj',
	);
	public $perpage = 10;

	/**
	 * 
	 */
	public function indexAction() {
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$tj = explode('_',$sp);
		Util_Cookie::set("imei", $imei, true, Common::getTime() + strtotime("+1 days"));
		Util_Cookie::set("version", $tj[1], true, Common::getTime() + strtotime("+1 days"));
		$page = intval($this->getInput('page'));
		$keyword = html_entity_decode($this->getInput('keyword'));
		$intersrc = $this->getInput('intersrc');
		$webroot = Common::getWebRoot();
		if ($page < 1) $page = 1;
		
		
		//关键字过滤
		$action = true;
		$filter = Game_Service_Config::getValue('game_search_filter');
		$filter = explode('|', $filter);
		if(in_array($keyword, $filter)) $action = false;
		
		$params = array();
		$search = array();
		if($keyword && $action) {
			$params['name'] = $keyword;
		}
		//获取本地所有游戏
		//过滤
	   /* if($this->filter){
			$params['id'] = array('NOT IN', $this->filter);
		} */
		$params['status'] = 1;
		list($total, $local_games) = Resource_Service_Games::adminSearch(1, $this->perpage, $params);
		$tmp = array();
		$games = array();
		if($local_games && $action){
			$games = Resource_Service_Games::getGameList($local_games, Common::getAttachPath());
			$from = 'gn';
		} else {
			$baiduApi = new Api_Baidu_Game();
			list($total, $baidu_games) = $baiduApi->engineList($keyword,1,$this->perpage);
			if($baidu_games && $action){
				$games = Common::resetKey($baidu_games, 'id');
				$from = 'baidu';
			} else {
				$games = array();
				$this->redirect($webroot. '/channel/search/error?keyword='.$keyword);
				exit;
			}
		}

		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('games', $games);
		$this->assign('keyword', $keyword);
		$this->assign('intersrc', $intersrc);
		$this->assign('total', $total);
		$this->assign('source', $this->getSource());
		$this->assign('from', $from);
	}
	
	/**
	 * 
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		$keyword = html_entity_decode($this->getInput('keyword'));
		$intersrc = $this->getInput('intersrc');
		$from = $this->getInput('from');
		$webroot = Common::getWebRoot();
		if ($page < 1) $page = 1;
		if($keyword) {
			$params['name'] = $keyword;
		}
		
		//过滤
		/*
		if($this->filter){
			$params['id'] = array('NOT IN', $this->filter);
		}*/
		//获取本地所有游戏
		$params['status'] = 1;
		list($total, $local_games) = Resource_Service_Games::adminSearch($page, $this->perpage, $params);
		
		$temp = array();
		if($local_games){
			$games = Resource_Service_Games::getGameList($local_games, Common::getAttachPath());
		} else {
			$baiduApi = new Api_Baidu_Game();
			list($total, $baidu_games) = $baiduApi->engineList($keyword,$page,$this->perpage);
			if($baidu_games){
				$games = Common::resetKey($baidu_games, 'id');
			} else {
				$games = '';
				$this->redirect($webroot.'/channel/error/index');
				exit;
			}
		}
	    
	    foreach($games as $key=>$value){
	    	$href = urldecode($webroot.$this->actions['detailtUrl']. '?from='.$from.'&id=' . $value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
	    	$temp[] = array(
	    			'id'=>$value['id'],
	    			'name'=>$value['name'],
	    			'resume'=>$value['resume'],
	    			'size'=>$value['size'].'M',
	    			'link'=>$href,
	    			'alink'=>urldecode($webroot.$this->actions['detailtUrl']. '?from='.$value['from'].'&id='.$value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
	    			'img'=>$value['img'],
	    			'profile'=>$value['name'].','.$href.','.$value['id'].','.$value['link'].','.$value['package'].','.$value['size'].','.'Android'.$value['version'].','.$value['min_resolution'].'-'.$value['max_resolution'],
	    	);
	    }
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('games', $games);
		$this->assign('keyword', $keyword);
		$this->assign('intersrc', $intersrc);
		$this->assign('total', $total);
		$this->assign('source', $this->getSource());
		$this->assign('from', $from);
	}
	
	
	public static function errorAction() {
	}
	
   /**
	 * 
	 * get game detail info
	 */
	public function detailAction() {
	$id = intval($this->getInput('id'));
		$from = $this->getInput('from');
		$app_version = Util_Cookie::get('version', true);
		$intersrc = $this->getInput('intersrc');
	    if($from == 'baidu'){
			$baiduApi = new Api_Baidu_Game();
			$info = $baiduApi->getInfo($id, $from);
		} else {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
			$from == 'gn';
		}
		list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5));
		$sys_version = Common::resetKey($sys_version, 'id');
		
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		
		$search = $tmp = array();
		$game_ids = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=>$id));
		if($game_ids){
			foreach($game_ids as $key=>$value){
				$tmp = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAMEC_RECOMEND_ID']));
				if($tmp['status']){
					$games[] = $tmp;
				}
			}
		}
		$this->assign('from', $from);
		$this->assign('info', $info);
		$this->assign('sys_version', $sys_version);
		$this->assign('resolution', $resolution);
		$this->assign('games', $games);
		$this->assign('intersrc', $intersrc);
		$this->assign('app_version', $app_version);
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$info['img']));
		}
	}
}