<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_RankController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Rank/most',
		'newUrl'=>'/Admin/Client_Rank/new',
		'monthUrl'=>'/Admin/Client_Rank/month',
		'setUrl'=>'/Admin/Client_Rank/set',
		'manUrl' => '/Admin/Client_Rank/man',
		'maneditUrl'=>'/Admin/Client_Rank/manedit',
		'monthListUrl' => '/Admin/Client_Rank/list',
		'monthEditUrl'=>'/Admin/Client_Rank/editCt',
		'monthAddUrl'=>'/Admin/Client_Rank/add',
		'weekUrl'=>'/Admin/Client_Rank/week',
		'editUrl'=>'/Admin/Client_Rank/edit',
		'batchUpdateUrl'=>'/Admin/Client_Rank/batchUpdate',

	);
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function mostAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$configs = Game_Service_Config::getAllConfig();
		$start_time = $this->getInput('start_time');
		$search = array();
		if($start_time) {
			$search['start_time'] = strtotime($start_time);
		} else {
			$search['start_time'] = '';
		}
	    list(, $bi_games) = Client_Service_Rank::getMostGames($configs['game_rank_mostnum'],$search['start_time']);
		$resource_ids = Common::resetKey($bi_games, 'GAME_ID');
		$this->assign('bi_games', $resource_ids);
		$resource_ids = array_unique(array_keys($resource_ids));
		
		
		if($resource_ids){
			list($total, $games) = Resource_Service_Games::search(1, $configs['game_rank_mostnum'], array('id'=>array('IN',$resource_ids),'status'=>1));
			foreach($games as $k=>$v){
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
				if($info) $tmp[] = $info;
			}
			
	     }
	    $games = $tmp;
	    $this->assign('games', $games);
	    $this->assign('total', $total);
	    
	    list(, $categorys) = Resource_Service_Attribute::getsortList(1, 100, array('at_type'=>1,'status'=>1));
		//categorys list
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
       
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');


		$subject_ids = Client_Service_Game::getIdxGameClientSubjects();
		$subject_ids = Common::resetKey($subject_ids, 'id');
		
		//游戏分类
	    list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		
		$tmp = array();
		$category_games = Resource_Service_Games::getIdxResourceCategorys();
		foreach($category_games as $key=>$value){
			$tmp[$value['game_id']][] = $value['category_id'];
		}
		
		$category_title = array();
		foreach($tmp as $key=>$val){
			 foreach($val as $key1=>$val1){
			 	$category_title[$key][] = $categorys[$val1]['title'];
			 }
			
		}
		
		//游戏专题
		$game_subjects = array();
		foreach($subject_ids as $k=>$v){
			if (!is_array($game_subjects[$v['resource_game_id']])) $game_subjects[$v['resource_game_id']] = array();
			array_push($game_subjects[$v['resource_game_id']], $subjects[$v['subject_id']]['title']);
		}

		
		$this->assign('games', $games);
		$this->assign('start_time', $start_time);
		$this->assign('game_subjects', $game_subjects);
		$this->assign('category_title', $category_title);
		$url = $this->actions['listUrl'].'/?' . http_build_query($start_time) . '&';
		$this->assign('pager', Common::getPages($total, $page, $configs['game_rank_mostnum'], $url));
		
	}
	
	/**
	 * 
	 */
	public function newAction() {
	    $page = intval($this->getInput('page'));
	    $limit = Game_Service_Config::getValue('game_rank_newnum');
	    list($total, $resource_games) = Resource_Service_Games::getList(1, $limit, array('status'=>1), array('online_time'=>'DESC'));
	    foreach($resource_games as $key=>$value) {
	    	$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
	    	$game_info = Resource_Service_Games::getResourceGames($value['id']);
	    	$info['img'] = $game_info['img'];
	    	if ($info) $games[] = $info;
	    }
	    $this->assign("total", $limit);
	    	    
	    list(, $categorys) =  Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>1));
		//categorys list
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
       
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');


		$subject_ids = Client_Service_Game::getIdxGameClientSubjects();
		$subject_ids = Common::resetKey($subject_ids, 'id');
		
		$category_ids = Resource_Service_Games::getIdxGameResourceCategorys();
		$category_ids = Common::resetKey($category_ids, 'game_id');
		
		//游戏专题
		$game_subjects = array();
		foreach($subject_ids as $k=>$v){
			if (!is_array($game_subjects[$v['resource_game_id']])) $game_subjects[$v['resource_game_id']] = array();
			array_push($game_subjects[$v['resource_game_id']], $subjects[$v['subject_id']]['title']);
		}
		
		//游戏分类
	    list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		
		$tmp = array();
		$category_games = Resource_Service_Games::getIdxResourceCategorys();
		foreach($category_games as $key=>$value){
			$tmp[$value['game_id']][] = $value['category_id'];
		}
		
		$category_title = array();
		foreach($tmp as $key=>$val){
			 foreach($val as $key1=>$val1){
			 	$category_title[$key][] = $categorys[$val1]['title'];
			 }
			
		}

		$this->assign('games', $games);
		$this->assign('game_subjects', $game_subjects);
		$this->assign('category_title', $category_title);
		$this->assign('category_ids', $category_ids);	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function monthAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$configs = Game_Service_Config::getAllConfig();
		$start_time = $this->getInput('start_time');
		$limit = Game_Service_Config::getValue('game_rank_monthnum');
		
		if($start_time) {
			$day_id = date('Ymd', strtotime($start_time));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_MonthRank::getList(1, $limit, $params);
		} else {
			$day_id = date('Ymd', strtotime("-1 day"));
			$params['day_id'] = $day_id;
			list(, $bi_games) = Client_Service_MonthRank::getList(1, $limit,$params);
			if(!$bi_games){
				//BI前一天没有，取BI最后一次数据
				$day_id =  Client_Service_MonthRank::getLastDayId();
				$params['day_id'] = $day_id;
				list(,$bi_games) = Client_Service_MonthRank::getList(1, $limit, $params);
			}
		}
		
		$bi_games = Common::resetKey($bi_games, 'game_id');
		$bi_games = array_unique(array_keys($bi_games));
		
		if($bi_games){
			list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $bi_games),'status'=>1));
		}
			
		foreach($resource_games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			$games[] = $info;
		}
		
		$this->assign('games', $games);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$url = $this->actions['monthUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function weekAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$configs = Game_Service_Config::getAllConfig();
		$limit = Game_Service_Config::getValue('game_rank_weeknum');
	
		$day_id = date('Ymd', strtotime("-1 day"));
		$params['day_id'] = $day_id;
		list($total, $bi_games) = Client_Service_WeekNewRank::getList(1,$limit,$params);
		if(!$bi_games){
			//BI前一天没有，取BI最后１天数据
			$day_id =  Client_Service_WeekNewRank::getLastDayId();
			$params['day_id'] = $day_id;
			list($total, $bi_games) = Client_Service_WeekNewRank::getList(1,$limit,$params);
		}
	
		$bi_games = Common::resetKey($bi_games, 'game_id');
		$bi_games = array_unique(array_keys($bi_games));
	
		if($bi_games){
			list($total, $resource_games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $bi_games),'status'=>1));
		}
		
			
		foreach($resource_games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			$games[] = $info;
		}
	
		$this->assign('games', $games);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$url = $this->actions['weekUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 */
	public function setAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	/**
	 *
	 */
	public function editAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
		$config = $this->getInput(array('game_client_rank','game_rank_newnum','game_rank_mostnum','game_rank_monthnum','game_rank_weeknum','game_rank_cnewnum','game_rank_fastestnum','game_rank_onlineknum','game_rank_pcnum'));
		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
		
	/**
	 * 排序管理
	 */
	public function manAction() {
		$clientRank = Game_Service_Config::getConfigRank('client');
		$h5Rank = Game_Service_Config::getConfigRank('h5');
		$webRank = Game_Service_Config::getConfigRank('web');
		
		$this->assign('client', $clientRank);
		$this->assign('h5', $h5Rank);
		$this->assign('web', $webRank);
		
	}
	
	public function maneditAction() {
		$data = $this->getPost(array('client','h5','web'));
		//保存客户端配置
		$clientRank = $this->_saveRank($data['client']);
		Game_Service_Config::setValue('client_rank_config', json_encode($clientRank));
		//保存h5配置
		$h5Rank = $this->_saveRank($data['h5']);
		Game_Service_Config::setValue('h5_rank_config', json_encode($h5Rank));
		//保存web配置
		$webRank = $this->_saveRank($data['web']);
		Game_Service_Config::setValue('web_rank_config', json_encode($webRank));
		//排行榜配置最后编辑时间(版本)
		Game_Service_Config::setValue('rank_config_time', Common::getTime());
		$this->output(0, '操作成功.');
	}

	/**
	 * 生成配置数据
	 * @param array $data
	 * 
	 */
	private function _saveRank($data){
		$tmp= array();
		foreach ($data['key'] as $value){
			$tmp[$value] = array(
					'sort'=>$data['sort'][$value],
					'status'=>$data['status'][$value],
			);
		}
		return $tmp;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function listAction() {
		$page = intval($this->getInput('page'));
		$hot = $this->getInput('hot');
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
	
		//获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		$client_games = Common::resetKey($client_games, 'id');
	
		$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
		$search = array();
		if($hot == 1){
			$search['hot'] = 1;
			$orderBy = array('online_time'=>'DESC');
		} else if($hot == 2){
			$search['hot'] = 2;
			$orderBy = array('downloads'=>'DESC');
		}
	
		list($total, $result) = Client_Service_Game::getMonthRankList($page, $this->perpage,array('status'=>1,'game_status'=>1),$orderBy);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('client_games', $client_games);
		$this->assign('oline_versions', $oline_versions);
		$url = $this->actions['monthListUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	//批量操作
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Game::batchAddByMonthRank($info['ids']);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Game::deleteMonthRank($info['ids']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Game::sortMonthRankGame($info['ids'], $info['sort']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Game::updateGameMonthRank($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Game::updateGameMonthRank($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('name','id'));
	
		$search = array();
		if ($s['name']) $search['name'] = array('LIKE',$s['name']);
	
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
	
		list(, $guess_games) = Client_Service_Game::getMonthRankByGame(array('status'=>1,'game_status'=>1));
		$guess_games = Common::resetKey($guess_games, 'game_id');
		$this->assign('guess_games', $guess_games);
		$guess_games_ids = array_unique(array_keys($guess_games));
	
		if (count($guess_games_ids)) {
			if($s['id']){
				$resource_games = array_intersect($guess_games_ids,array($s['id']));
				if($resource_games){
					$search['id'] = array('IN',$resource_games);
				} else {
					$search['create_time'] = 0;
				}
			} else {
				$search['id'] =  array('IN',$guess_games_ids);
			}
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$games = Common::resetKey($games, "id");
		}
	
		$this->cookieParams();
		$this->assign('total', $total);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('games', $games);
		$this->assign('s', $s);
		$url = $this->actions['monthEditUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 *
	 * add games
	 */
	public function addAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','status','id', 'category_id'));
		$params = $search = array();
	
		if ($s['name']) $params['name'] = $s['name'];
		if ($s['status']) $params['status'] = $s['status'] - 1;
	
	
		//游戏库分类列表
		list(, $categorys) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>1,'status'=>1));
		$this->assign('categorys', $categorys);
	
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
	
		//获取猜你喜欢默认索引表游戏
		list(, $idx_games) = Client_Service_Game::getMonthRankByGame(array('status'=>1,'game_status'=>1));
		$idx_games = Common::resetKey($idx_games, 'game_id');
		$idx_games = array_keys($idx_games);
	
		//获取本地所有游戏
		$client_games = Resource_Service_Games::getsBy(array('status'=>1));
		if(count($client_games)){
			$client_games = Common::resetKey($client_games, 'id');
			$this->assign('client_games', $client_games);
			$resource_game_ids = array_keys($client_games);
			$this->assign('resource_game_ids', $resource_game_ids);
				
			if ($s['category_id']) {
				$game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id'],'game_status'=>1));
				$game_ids = Common::resetKey($game_ids, 'game_id');
				$ids = array_keys($game_ids);
				if($ids){
					if($s['id']){
						$resource_games = array_intersect($ids,array($s['id']));
						if($resource_games){
							$params['id'] = array('IN',$resource_games);
						} else {
							$params['id'] = 0;
						}
					} else {
						$params['id'] = array('IN',$ids);
					}
				} else {
					$params['id'] = 0;
				}
			}  else {
				if($s['id']){
					$resource_games = array_intersect($resource_game_ids,array($s['id']));
					if($resource_games){
						$params['id'] = array('IN',$resource_games);
					} else {
						$params['id'] = 0;
					}
				} else {
					$params['id'] = array('IN',$resource_game_ids);
				}
			}
	
		}
		list($total, $games) = Resource_Service_Games::adminSearch($page, $this->perpage, $params, array('id'=>'DESC'));
		$this->assign('total', $total);
		$this->cookieParams();
		$this->assign('s', $s);
		$this->assign('oline_versions', $oline_versions);
		$this->assign('idx_games', $idx_games);
		$this->assign('games', $games);
		$url = $this->actions['monthAddUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}

}
