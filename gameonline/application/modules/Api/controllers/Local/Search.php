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
		
		
	    if($keyword && $action) {
			$params['name'] = $keyword;
		}

		/* //过滤
		if($this->filter){
			$params['id'] = array('NOT IN', $this->filter);
		} */
		//获取本地所有游戏
		$params['status'] = 1;
		$orderBy = array('online_time'=>'DESC','id'=>'DESC');
		list($total, $local_games) = Resource_Service_Games::adminSearch($page, $this->perpage, $params, $orderBy);
		
	    $temp = array();
	    if($local_games && $action){
	    	$games = Resource_Service_Games::getGameList($local_games, Common::getAttachPath());
	    	$from = 'gn';
	    } else {
	    	$baiduApi = new Api_Baidu_Game();
	    	list($total, $baidu_games) = $baiduApi->engineList($keyword,$page,$this->perpage);
	    	if($baidu_games){
				$games = Common::resetKey($baidu_games, 'id');
				$resum = "百度应用的游戏可能不适配您的手机";
			} else {
				$games = '';
			}
	    }
	    
	    //判断游戏大厅版本
	    $temp = array();
	    foreach($games as $key=>$value){
	    	
	    	if ($from == 'gn') {
    			//附加属性处理,1:礼包
    			$attach = array();
    			if (Client_Service_IndexAdI::haveGiftByGame($value['id'])) array_push($attach, '1');
	    		$value = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
	    	}
	    	
	    	$data = array(
					'img'=>urldecode($value['img']),
					'name'=>html_entity_decode($value['name']),
					'resume'=>html_entity_decode($value['resume']),
					'package'=>$value['package'],
					'link'=>$value['link'],
					'gameid'=>$value['id'],
					'size'=>$value['size'].'M',
					'category'=>$from == 'gn' ? $value['category_title'] : $value['category'],
					'attach' => ($attach) ? implode(',', $attach) : '',
					'hot' => Resource_Service_Games::getSubscript($value['hot']),
	    			'score'=>($from == 'gn' ? $value['client_star'] : 0),
					'viewType' => 'GameDetailView',
			);	    	
			$temp[] = $data;
	    }
	    
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'resum'=>$resum, 'totalCount'=>$total);
		return $data;
		
	}
	
}