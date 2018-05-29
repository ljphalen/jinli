<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Game_BaseController {
	
	public $actions =array(
			'indexUrl' => '/search/index',
			'outUrl' => '/search/out',
			'detailtUrl' => '/index/detail',
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
		foreach($apihot as $key => $value) {
			$apiList[$key]['name'] = html_entity_decode($value, ENT_QUOTES);
			$apiList[$key]['href'] = '/search/list?keyword='.trim(html_entity_decode($value, ENT_QUOTES)).'&intersrc=Hsearch&action=2&t_bi='.$this->getSource();
		}
		$this->assign('apihot', json_encode($apiList));
		$intersrc = $this->getInput('intersrc');
		$stype = $this->getInput('stype');
                list(, $keywords) = Resource_Service_Keyword::getCanUseResourceKeywords(0,1,array('ktype'=>2));
                $this->assign('defaultKeyword', trim($keywords[0]['name']));
		$this->assign('stype', $stype);
		$this->assign('intersrc', $intersrc);
	}
	
	public function listAction() {
		if (!$intersrc)	$intersrc = 'SEARCH';
		if ($page < 1) $page = 1;
		$keyword = html_entity_decode($this->getInput('keyword'));
		$intersrc = $this->getInput('intersrc');
		//关键字过滤
		$action = true;
		$filter_key = Game_Service_Config::getValue('game_search_filter');
		$filter_key = explode('|', $filter_key);
		if(in_array($keyword, $filter_key)) $action = false;
		
		$searchOption = array(
				'searchFrom' => Api_Search_Query::Search_From_Default,
				'searchAction' => intval($this->getInput('action'))
		);
		$localGames = Api_Search_Query::getSearchList($page, 10, $keyword, $searchOption);
		
		$this->mOptionSearch = array(
				'localGameList' => $localGames['data'],
				'action' => $action,
				'keyword' => $keyword,
				'page' => $page,
				'perpage' => 10,
				'intersrc' => $intersrc
		);
		
		$searchList =  Api_Search_Query::handleSearchList($this->mOptionSearch, $get['type']);
		$gameList = $this->handleMobileApiH5Data($searchList);
		$hasnext = (ceil((int) $searchList['total'] / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$gameList, 'hasNext'=>$hasnext, 'curPage'=>$page, 'resum'=>$searchList['resum'], 'totalCount'=>$searchList['total'],
		'href' => '/search/list',
		'searchMoreUrl'  => '/Api/Local_Search/searchMore',
		'ajaxUrl' => '/Api/Local_Search/searchList?type=h5&keyword='.$keyword);
		$this->assign('list', json_encode($data));
                $this->assign('keywords', $keyword);
	}
	
	private function handleMobileApiH5Data($searchList) {
		$webroot = Common::getWebRoot();
		foreach($searchList['gamelist'] as $key=>$value){
			if ($searchList['from'] == 'gn') {
				$value = Resource_Service_GameData::getGameAllInfo($value['id']);
			}
			$href = urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$this->getInput('stype').'&from='.$searchList['from'].'&id=' . $value['id'].'&gname='.$value['name'].'&keyword='.$this->mOptionSearch['keyword'].'&intersrc='.$this->mOptionSearch['intersrc'].'&t_bi='.$this->getSource());
			$data = array(
					'id'=>$value['id'],
					'name'=>$value['name'],
					'resume'=>html_entity_decode($value['resume'], ENT_QUOTES),
					'size'=>$value['size'].'M',
					'link'=> Common::bdurl($this->actions['tjUrl'], $value['id'], $this->mOptionSearch['intersrc'], $value['link'], '', $searchList['from'], $value['name'], $this->mOptionSearch['keyword'], $this->getInput('stype')),
					'href'=>urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$this->getInput('stype').'&from='.$searchList['from'].'&id='.$value['id'].'&gname='.$value['name'].'&keyword='.$this->mOptionSearch['keyword'].'&intersrc='.$this->mOptionSearch['intersrc'].'&t_bi='.$this->getSource()),
					'img'=>($value['from'] ? $value['img'] : urldecode($value['img'])),
					'profile'=>$value['name'].','.$href.','.$value['id'].','.$value['link'].','.$value['package'].','.$value['size'].','.'Android'.$value['version'].','.$value['min_resolution'].'-'.$value['max_resolution'],
			);
			$gameList[] = $data;
		}
		return $gameList;
	}
	
	public function outAction() {
		$keyword = html_entity_decode(trim($this->getInput('keyword')));
		$stype = $this->getInput('stype');
		//关键字过滤
		$action = true;
		$filter_key = Game_Service_Config::getValue('game_search_filter');
		$filter_key = explode('|', $filter_key);
		if(in_array($keyword, $filter_key)) $action = false; 
		if($keyword && $action){
			$page = $this->getPage();
			$intersrc = $this->getInput('intersrc');
			$searchOption = array('searchFrom' => Api_Search_Query::Search_From_Web, 'searchAction' => intval($this->getInput('action')));
			$localGames = Api_Search_Query::getSearchList($page, $this->perpage, $keyword, $searchOption);
			
			$optionSearch = array(
			    'localGameList' => $localGames['data'], 
			    'action' => $action, 
			    'keyword' => $keyword, 
			    'page' => $page, 
			    'perpage' => $this->perpage
			);
			$searchList = Api_Search_Query::handleSearchList($optionSearch, 'h5');
			
			$hasnext = (ceil((int) $searchList['total'] / $this->perpage) - 1) > 0 ? true : false;
			$this->assign('hasnext', $hasnext);
			$this->assign('page', $page);
			$this->assign('games', $searchList['gamelist']);
			$this->assign('from', $searchList['from']);
			$this->assign('total', $searchList['total']);
		}
		$this->assign('stype', $stype);
		$this->assign('keywords', $keyword);
		$this->assign('intersrc', $intersrc);
	    $this->assign('source', $this->getSource());
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
		if ($page < 1) $page = 1;
		if($keyword) {
			$params['name'] = $keyword;
		}
		
		$localGames = Api_Search_Query::getSearchList($page, $this->perpage, $keyword);
		
		$optionSearch = array('localGameList' => $localGames['data'], 'action' => 1, 'keyword' => $keyword, 'page' => $page, 'perpage' => $this->perpage);
		$searchList = Api_Search_Query::handleSearchList($optionSearch);
		
		$gameList = $this->handleGameList($searchList, $keyword, $intersrc, $stype );
	    
		$hasnext = (ceil((int) $searchList['total'] / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$gameList, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('games', $searchList['gamelist']);
		$this->assign('keyword', $keyword);
		$this->assign('intersrc', $intersrc);
		$this->assign('total', $searchList['total']);
		$this->assign('source', $this->getSource());
		$this->assign('from', $searchList['from']);
	}
	
	private function getPage() {
	    return intval($this->getInput('page')) < 1 ? 1 : intval($this->getInput('page'));
	}
	
	private function handleGameList($searchList, $keyword, $intersrc, $stype) {
		$webroot = Common::getWebRoot();
		foreach($searchList['gamelist'] as $key=>$value){
			if($searchList['from'] =='gn'){
				$value = Resource_Service_GameData::getGameAllInfo($value['id']);
			}
			$href = urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$stype.'&from='.$searchList['from'].'&id=' . $value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$gameList[] = array(
					'id'=>$value['id'],
					'name'=>$value['name'],
					'resume'=>html_entity_decode($value['resume'], ENT_QUOTES),
					'size'=>$value['size'].'M',
					'link'=> Common::bdurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link'], '', $searchList['from'], $value['name'], $keyword, $stype),
					'alink'=>urldecode($webroot.$this->actions['detailtUrl']. '?stype='.$stype.'&from='.$searchList['from'].'&id='.$value['id'].'&gname='.$value['name'].'&keyword='.$keyword.'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
					'img'=>($value['from'] ? $value['img'] : urldecode($value['img'])),
					'profile'=>$value['name'].','.$href.','.$value['id'].','.$value['link'].','.$value['package'].','.$value['size'].','.'Android'.$value['version'].','.$value['min_resolution'].'-'.$value['max_resolution'],
			);
		}
		return $gameList;
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
