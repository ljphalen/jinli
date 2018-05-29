<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Front_BaseController {
	public $actions =array(
			'listUrl' => '/Front/Search/index',
	);
	public $perpage = 10;

    /**
     * 查找页面
     */
	public function indexAction() {	
		$keyword = html_entity_decode(trim($this->getInput('keyword')));
		$page = intval($this->getInput('page'));	
		if ($page < 1) $page = 1;
		
		$action = $this->getInput('action');
		if($action == 'search'){
			//关键字过滤
			$flag = true;
			$filter = Game_Service_Config::getValue('game_search_filter');
			$filter = explode('|', $filter);
			if(in_array($keyword, $filter)) $flag = false;
			if($keyword && $flag){
				$page = intval($this->getInput('page'));
				if ($page < 1) $page = 1;
			
				$params = array();
				$search = array();
				$params['action'] = $action;
				if($keyword) {
					$params['keyword'] = $keyword;
					$search['name'] = $keyword;
					//过滤掉游戏大厅
				   /* if($this->filter){
						$search['id'] = array('NOT IN', $this->filter);
					} */
	
				}
				
				//获取本地所有游戏
				$params['status'] = 1;
				$search['status'] = 1;
				list($total, $local_games) = Resource_Service_Games::adminSearch($page, $this->perpage, $search);
				$local_games = Common::resetKey($local_games, 'id');
			
				$tmp = array();
				$games = array();
				if(count($local_games)){
					$games = Resource_Service_Games::getGameList($local_games, Common::getAttachPath());
					$from = 'gn';
				} else {
					$baiduApi = new Api_Baidu_Game();
					list($total, $games) = $baiduApi->engineList($keyword,$page,$this->perpage);
					$from = 'baidu';
				}		
				//搜到结果
				if( $total > 0){
					//分页
					$url = $this->actions['listUrl'].'/?'. http_build_query($params).'&';
					$paper = Common::getPages($total, $page, $this->perpage, $url,'',1);
					$this->assign('pager',$paper );
						
					//下载排行
					$this->assign('downgames', $this->getDowloadRank());
					$this->assign('games', $games);
					$this->assign('from', $from);
				}
			}
			//大家都在玩的游戏
			list($playgames_total, $playgames) = Web_Service_Ad::getCanUseNormalAds(1, 6, array('ad_type'=>2));
			$playgames_list = array();
			foreach ($playgames as $key=>$val){
				$info= Resource_Service_Games::getGameAllInfo(array("id"=>$val['link']));
				if($info) {
					$playgames_list[$key] =  $info;
				}
			}
			$this->assign('playgames', $playgames_list);
		}

		$intersrc = $this->getInput('intersrc');
		$this->assign('tj_cku', $this->getInput('cku'));
		$this->assign('tj_channel', $this->getInput('channel'));
		$this->assign('tj_object', $this->getInput('object'));
		$this->assign('tj_intersrc', $intersrc);
		$this->assign('page', $page);
		$this->assign('keyword', $keyword);
		$this->assign('total', $total);
		$this->assign('from', $from);
	}
	
	/**
	 *
	 * get  BAIDU game detail info
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$from = $this->getInput('from');
		$keyword = $this->getInput('keyword');

	
		if($from == 'baidu'){
			$baiduApi = new Api_Baidu_Game();
			$game = $baiduApi->getInfo($id, $from);
		}
		//没有记录跳转
	    if(!$game['id']){
	    	$str  = $this->redirect('/Front/Error/index/');
	    	exit;
	    }
		
		//截取游戏的分类，进行匹配本地游戏库的分类 
		if( $game['category'] == '体育竞技' || $game['category'] == '赛车竞速'){
			$category = mb_substr($game['category'], 2,2,'utf-8');
		}else{
			$category = mb_substr($game['category'], 0,2,'utf-8');
		}

		$category_arr = Resource_Service_Attribute::getResourceAttributeInfo(array('title'=>array('like',$category)));
		if($category_arr['id']){
			$category_id = $category_arr['id'] ;
		}else{
			$category = '';
			$category_arr = Resource_Service_Attribute::getResourceAttributeInfo(array('title'=>array('like',$category)));
			$category_id = $category_arr['id'] ;
		}
		list(,$game_arr) = Resource_Service_Games::getGames(array('category_id'=>$category_id));
		$game_ids = Common::resetKey($game_arr, 'game_id');
		$game_ids = array_unique(array_keys($game_ids));
		if ($game_ids){
			//相关推荐
			$recommIds = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=> array('in',$game_ids)));
			if($recommIds){
				foreach($recommIds as $key=>$value){
					$tmp = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAMEC_RECOMEND_ID']));
					if($tmp) $recommGames[] = $tmp;
				}
			}
		}
		$this->assign('recommGames', $recommGames);
		
		//验证此应用是否安全
		$safe_arr = Common::applyIsSafe(unserialize($game['certificate']));
		$this->assign('safe_arr', $safe_arr);
		$this->assign('from', $from);
		$this->assign('game', $game);
		$this->assign('keyword', $keyword);
		
		//统计参数
		if($from){
			$tj_object = 'gamedetail'.$id.'_'.$from;
			$tj_intersrc = 'gamedetail_gid'.$id;
		}else{
			$tj_object = 'gamedetail'.$id;
			$tj_intersrc = 'gamedetail_gid'.$id;
		}
		$this->assign('tj_object', $tj_object);
		$this->assign('tj_intersrc', $tj_intersrc);
		
		//取得配置文件
		$ami_web_game_share_text = Game_Service_Config::getValue('ami_web_game_share_text');
		$this->assign('ami_web_game_share_text', $ami_web_game_share_text);
	
	}
	
}
