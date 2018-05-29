<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Game_BaseController {
	
	public $actions =array(
			'indexUrl' => '/search/index',
			'outUrl' => '/search/out',
			'detailtUrl' => '/search/detail',
			'engineUrl' => '/search/engine',
			'enginetUrl' => '/search/enginet',
			'tjUrl' => '/index/tj',
	);
	public $perpage = 10;

	/**
	 * 
	 */
	public function indexAction() {
		//关键字
		$apikword = Resource_Service_Games::getKeyword();
		$this->assign('apikword', $apikword[0]);
		//热词列表
		$apihot = Resource_Service_Games::getHots();
		$this->assign('apihot', $apihot);
		
		$intersrc = $this->getInput('intersrc');
		$stype = $this->getInput('stype');
		$this->assign('stype', $stype);
		$this->assign('intersrc', $intersrc);
		$this->assign('source', $this->getSource());
	}
	
	public function outAction() {
		$keyword = html_entity_decode(trim($this->getInput('keyword')));
		$stype = $this->getInput('stype');
		$this->assign('stype', $stype);
		
		//关键字过滤
		$action = true;
		$filter_key = Game_Service_Config::getValue('game_search_filter');
		$filter_key = explode('|', $filter_key);
		if(in_array($keyword, $filter_key)) $action = false; 
		if($keyword && $action){
			$page = intval($this->getInput('page'));
				
			$intersrc = $this->getInput('intersrc');
			$webroot = Common::getWebRoot();
			if ($page < 1) $page = 1;
				
		    //调用迅搜查询
			$localGames = Api_XunSearch_Search::getSearchList($page, $this->perpage, $keyword);
			$localGames = json_decode($localGames, true);
			$temp = array();
		    if($localGames['data']['totalCount'] && $action){
		    	$from ='gn';
				$total = $localGames['data']['totalCount'];
		   		$gameData = $localGames['data']['list'];
				foreach ($gameData as $value ){
					$game = Resource_Service_GameData::getGameAllInfo($value['id']);
					$games[] = $game;
				}
		    } else {
				$baiduApi = new Api_Baidu_Game();
				list($total, $games) = $baiduApi->engineList($keyword,1,$this->perpage);
				$from = 'baidu';
			}
			
			$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
			$this->assign('hasnext', $hasnext);
			$this->assign('page', $page);
			$this->assign('games', $games);
			$this->assign('from', $from);
		}
		
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
		$keyword = html_entity_decode(str_replace(" ","",trim($this->getInput('keyword'))));
		$intersrc = $this->getInput('intersrc');
		$from = $this->getInput('from');
		$stype = $this->getInput('stype');
		$webroot = Common::getWebRoot();
		if ($page < 1) $page = 1;
		if($keyword) {
			$params['name'] = $keyword;
		}
		
	   //调用迅搜查询
		$localGames = Api_XunSearch_Search::getSearchList($page, $this->perpage, $keyword);
		$localGames = json_decode($localGames, true);
		$temp = array();
	    if($localGames['data']['totalCount'] ){
	    	$from ='gn';
			$total = $localGames['data']['totalCount'];
	    	$games = $localGames['data']['list'];
	    }else {
	    	$baiduApi = new Api_Baidu_Game();
	    	list($total, $baidu_games) = $baiduApi->engineList($keyword,$page,$this->perpage);
	    	if($baidu_games){
				$games = Common::resetKey($baidu_games, 'id');
				$from = 'baidu';
			} else {
				$games = '';
			}
	    }
	    
	    foreach($games as $key=>$value){
	    	if($from =='gn'){
	    		$value = Resource_Service_GameData::getGameAllInfo($value['id']);
	    	}
	    	$href = urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$stype.'&from='.$from.'&id=' . $value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
	    	$temp[] = array(
	    			'id'=>$value['id'],
	    			'name'=>$value['name'],
	    			'resume'=>html_entity_decode($value['resume'], ENT_QUOTES),
	    			'size'=>$value['size'].'M',
	    			'link'=> Common::bdurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link'], '', $from, $value['name'], $keyword, $stype),
	    			'alink'=>urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$stype.'&from='.$from.'&id='.$value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
	    			'img'=>($value['from'] ? $value['img'] : urldecode($value['img'])),
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
	
   /**
	 * 
	 * get game detail info
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$from = $this->getInput('from');
		$keyword = $this->getInput('keyword');
		$intersrc = $this->getInput('intersrc');
		$stype = $this->getInput('stype');
		$this->assign('stype', $stype);
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		//资讯评测
		list(, $news) = Client_Service_News::getGameNews(1, 10, array('hot'=>'1','game_id'=>$id,'status'=>1));
		if($from == 'baidu'){
			$baiduApi = new Api_Baidu_Game();
			$info = $baiduApi->getInfo($id, $from);
		} else {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
			$from == 'gn';
		} 
		
		$this->assign('from', $from);
		$this->assign('info', $info);
		$this->assign('news', $news);
		$this->assign('keyword', $keyword);
		$this->assign('intersrc', $intersrc);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$info['img']));
		}
	}
}
