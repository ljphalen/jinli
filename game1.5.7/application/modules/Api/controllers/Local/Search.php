<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_SearchController extends Api_BaseController {

	public $perpage = 10;
	private $_recoGames = array();

	
	public function searchListAction() {
		$page = intval($this->getInput('page'));
		$keyword = html_entity_decode($this->getInput('keyword'));
		$intersrc = $this->getInput('intersrc');
		$data = $this->_searchList($keyword, $page, $intersrc);
		$this->localOutput('','',$data);
	}
	
	public function searchMoreAction() {
		$get = $this->getInput(array('keyword', 'num'));
		$action = true;
		$filter_key = Game_Service_Config::getValue('game_search_filter');
		$filter_key = explode('|', $filter_key);
		if(in_array($keyword, $filter_key)) $action = false;
		
		$searchOption = array(
		    'searchFrom' => Api_Search_Query::Search_From_Android,
		    'searchAction' => intval($this->getInput('action')),
		    'ua' => $this->getInput('sp'),
		    'uuid' => $this->getInput('puuid')
		);
		$localGames = Api_Search_Query::getHotSearchConnectList( $get['keyword'], 6, $searchOption);
		$searchList = $this->handleConnectWordApiData($localGames['data']['list']);
		$this->localOutput('','',$searchList);
	}
	
	/**
	 * 
	 */
	private  function _searchList($keyword, $page, $intersrc) {
		if (!$intersrc)	$intersrc = 'SEARCH';
		$webroot = Common::getWebRoot();
		if ($page < 1) $page = 1;
		
		//关键字过滤
		$action = true;
		$filter_key = Game_Service_Config::getValue('game_search_filter');
		$filter_key = explode('|', $filter_key);
		if(in_array($keyword, $filter_key)) $action = false;
		
		if($this->getInput('ajaxGet')) $this->perpage = 1; 
		
		$searchOption = array(
		    'searchFrom' => Api_Search_Query::Search_From_Android,
		    'searchAction' => intval($this->getInput('action')),
		    'ua' => $this->getInput('sp'),
		     'uuid' => $this->getInput('puuid')
		);
		$localGames = Api_Search_Query::getSearchList($page, $this->perpage, $keyword, $searchOption);
		
		$optionSearch = array(
		    'localGameList' => $localGames['data'],
		    'action' => $action,
		    'keyword' => $keyword,
		    'page' => $page,
		    'perpage' => $this->perpage
		);
		
		$searchList =  Api_Search_Query::handleSearchList($optionSearch);
		
	    $gameList = $this->handleMobileApiData($searchList);
	    
		$hasnext = (ceil((int) $searchList['total'] / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$gameList, 'hasnext'=>$hasnext, 'curpage'=>$page, 'resum'=>$searchList['resum'], 'totalCount'=>$searchList['total']);
		return $data;
		
	}
	
	private function handleConnectWordApiData($searchList) {
	    $gameList = array('gameItems' => array(), 'list' => array());
	    foreach($searchList as $key=>$value){
	        if(count($gameList['gameItems']) > 0) {
	            $gameList['list'][] = array('name' => $value['name']);
	        } else {
	            $attach = array();
				if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) {
					array_push($attach, '1');
				}
    			$gameInfo = Resource_Service_GameData::getGameAllInfo($value['id']);
    	        $data = array(
    	            'img'=>urldecode($gameInfo['img']),
    	            'name'=>html_entity_decode($value['name']),
    	            'resume'=>html_entity_decode($gameInfo['resume']),
    	            'package'=>$gameInfo['package'],
    	            'link'=>$gameInfo['link'],
    	            'gameId'=>$value['id'],
    	            'size'=>$gameInfo['size'].'M',
    	            'category'=> $gameInfo['category_title'],
    	            'hot' => Resource_Service_Games::getSubscript($gameInfo['hot']),
    	            'score'=> $gameInfo['client_star'],
    	            'freedl' => $gameInfo['freedl'],
    	            'viewType' => 'GameDetailView',
    	            'reward' => $gameInfo['reward'],
    	            'attach' => ($attach) ? implode(',', $attach) : '',
    	        );
    	        $gameList['gameItems'][] = $data;
	        }
	    }
	    return $gameList;
	}
	
	private function handleMobileApiData($searchList) {
		foreach($searchList['gamelist'] as $key=>$value){
			if ($searchList['from'] == 'gn') {
				$attach = array();
				if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) {
					array_push($attach, '1');
				}
				$value = Resource_Service_GameData::getGameAllInfo($value['id']);
			}
			$data = array(
					'from'=>$searchList['from'],
					'img'=>urldecode($value['img']),
					'name'=>html_entity_decode($value['name']),
					'resume'=>html_entity_decode($value['resume']),
					'package'=>$value['package'],
					'link'=>$value['link'],
					'gameid'=>$value['id'],
					'size'=>$value['size'].'M',
					'category'=>$searchList['from'] == 'gn' ? $value['category_title'] : $value['category'],
					'attach' => ($attach) ? implode(',', $attach) : '',
					'hot' => Resource_Service_Games::getSubscript($value['hot']),
					'score'=>($searchList['from'] == 'gn' ? $value['client_star'] : 0),
					'freedl' => $value['freedl'],
					'viewType' => 'GameDetailView',
					'reward' => $value['reward']
			);
			$gameList[] = $data;
		}
		return $gameList;
	}
	
	
}