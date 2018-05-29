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
	
		$localGames = Api_Search_Query::getSearchList($page, $this->perpage, $keyword);
		
		$optionSearch = array('localGameList' => $localGames['data'], 'action' => $action, 'keyword' => $keyword, 'page' => $page, 'perpage' => $this->perpage);
		$searchList = Api_Search_Query::handleSearchList($optionSearch);
		
		if(!$searchList['gamelist'] && $searchList['from'] == 'baidu') {
			$this->redirect($webroot. '/channel/search/error?keyword='.$keyword);
			exit;
		}
		
		foreach($searchList['gamelist'] as $key=>$value){
			if ($searchList['from'] == 'gn') {
				$value = Resource_Service_GameData::getGameAllInfo($value['id']);
			}
			$gamelist[] = $value;
		}

		$hasnext = (ceil((int) $searchList['total'] / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('games', $gamelist);
		$this->assign('keyword', $keyword);
		$this->assign('intersrc', $intersrc);
		$this->assign('total', $searchList['total']);
		$this->assign('source', $this->getSource());
		$this->assign('from', $searchList['from']);
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
		
		
		//关键字过滤
		$action = true;
		$filter = Game_Service_Config::getValue('game_search_filter');
		$filter = explode('|', $filter);
		if(in_array($keyword, $filter)) $action = false;
		
		$localGames = Api_Search_Query::getSearchList($page, $this->perpage, $keyword);
		$temp = array();
	    if($localGames['data']['totalCount'] ){
	    	$from ='gn';
			$total = $localGames['data']['totalCount'];
			$games = $localGames['data']['list'];
	    }  else {
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
	    	if ($from == 'gn') {
	    		$value = Resource_Service_GameData::getGameAllInfo($value['id']);
	    	}
	    	$href = urldecode($webroot.$this->actions['detailtUrl']. '?from='.$from.'&id=' . $value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
	    	$temp[] = array(
	    			'id'=>$value['id'],
	    			'name'=>$value['name'],
	    			'resume'=> html_entity_decode($value['resume'], ENT_QUOTES),
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
		$sys_version = Resource_Service_Attribute::getsBy(array('at_type'=>5));
		$sys_version = Common::resetKey($sys_version, 'id');
		
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4));
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