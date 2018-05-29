<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_InstalleController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Installe/index',
		'addUrl' => '/Admin/Client_Installe/add',
		'addPostUrl' => '/Admin/Client_Installe/add_post',
		'editCtUrl' => '/Admin/Client_Installe/editCt',
		'addCtUrl' => '/Admin/Client_Installe/addCt',
		'editUrl' => '/Admin/Client_Installe/edit',
		'editPostUrl' => '/Admin/Client_Installe/edit_post',
		'deleteUrl' => '/Admin/Client_Installe/delete',
		'deleteIdUrl' => '/Admin/Client_Installe/deleteId',
		'batchUpdateUrl'=>'/Admin/Client_Installe/batchUpdate'
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status', 'gtype'));
		$params = $search = $game_ids = $ids = $name = array();
		
		if ($s['title']) $search['title'] =  $s['title'];
		if ($s['status']) $search['status'] = $params['status'] = $s['status'] - 1;
		if ($s['gtype']) $search['gtype'] = $params['gtype'] = $s['gtype'];
		
		$installe_games = Client_Service_Installe::getIdxGameInstalles();
		$installe_ids = array();
		if($installe_games){
			foreach($installe_games as $k=>$v){
				$game = Resource_Service_Games::getBy(array('id'=> $v['game_id']));
				$installe_ids[$v['installe_id']][] = $game['name'];
			}
			//找出索引表中游戏id
			if($s['title']){
				$game_ids = Resource_Service_Games::installSearch(array('name'=>array('LIKE',$s['title'])));
				$game_ids = Common::resetKey($game_ids, "id");
				$game_ids = array_unique(array_keys($game_ids));
				//找出装机必备的id
				if($game_ids){
					foreach($installe_games as $key=>$val){
						if(in_array($val['game_id'],$game_ids)){
						   $ids[] = $val['installe_id'];
						}
					}
				}
				$ids = array_unique($ids);
				if($ids){
					$params['id'] = array('IN',$ids);
				} else {
					$params['id'] = 0;
				}
			}			
		}
		
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
				
		$this->cookieParams();
		list($total, $installes) = Client_Service_Installe::getList($page, $this->perpage,$params);
		
		
		$this->assign('installes', $installes);
		$this->assign('search', $search);
		$this->assign('groups', $groups);
		$this->assign('installe_ids', $installe_ids);
		$this->assign('total', $total);
		$this->assign('s', $s);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an Installe
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Installe::getInstalle(intval($id));
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$this->assign('groups', $groups);
		$this->assign('info', $info);
		$this->assign('id', $id);
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		$installe_games = Client_Service_Installe::getIdxInstalleByInstalleId($id);
		$installe_games = Common::resetKey($installe_games, "game_id");
		$this->assign('installe_games', $installe_games);
		$installe_games =  array_unique(array_keys($installe_games));
		$search = $resource_games = array();
		if($installe_games){
			$search['id'] = array('IN',$installe_games);
			$search['status'] = 1;
			//list($total, $games) = Resource_Service_Games::getVersionSortGames($page, $this->perpage, $search);
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			$games = Common::resetKey($games, "id");
			
		    foreach($games as $key=>$value) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			    if ($info) $resource_games[] = $info;
		    } 
		    $resource_games = Common::resetKey($resource_games, "id");
		}
		
		$url = $this->actions['editCtUrl'].'/?id='.$id . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('games', $games);
		$this->assign('resource_games', $resource_games);
		$this->assign('total', $total);
		$this->assign('id', $id);
	}
	
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Installe::batchAddByInstalle($info['ids'], $id);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Installe::deleteByInstalleGames($info['ids'],$id);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Installe::batchSortByInstalle($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * get an subjct by Installe_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Installe::getInstalle(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$this->assign('groups', $groups);
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = intval($this->getInput('id'));
		$name = $this->getInput('name');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$info = Client_Service_Installe::getInstalle(intval($id));
		$p_info = Resource_Service_Pgroup::getPgroup(intval($info['gtype']));
		
		$ids = explode(',',$p_info['p_id']);
		$fbl = $tmp = $temp = $params = $s = $search = $s_fbl = array();
		$s['id'] = $id;
		$resolution = Resource_Service_Attribute::getsBy(array('at_type'=>4));
		$resolution = Common::resetKey($resolution, 'id');
		
		$all_games = Resource_Service_Games::getsBy(array('status'=>1));
		$all_games = Common::resetKey($all_games, "id");
		
		$all_ids =  array_unique(array_keys($all_games));

				
		if($ids && $p_info['id'] != 1){
			
			foreach($ids as $key=>$value){
				$t_info = Resource_Service_Type::getResourceType(intval($value));
				$fbl[] = $t_info['resolution'];
			}
			$fbl = array_unique($fbl);
			sort($fbl);

			//挑出适合的分辨率的游戏
			$s_fbl['attribute_id'] = array('IN',$fbl);
			$fbl_games = Resource_Service_Games::getIdxGameResourceResolutionByGameId($s_fbl);

			
			foreach($fbl_games as $key=>$val){
				$tmp[$val['game_id']][] = $val['attribute_id'];
			}
			
			foreach($tmp as $k=>$v){
				//判断机组分辨率数组是否在某个游戏分辨率数组里面
				$diff = array_diff($fbl, $v);
				if(!$diff){
					$temp[] = $k;
				}
			}
			
			$fbl_games = $temp;
			
			if($fbl_games){
				if($name) {
					$params['name'] = array('LIKE',$name);
					$resource_games = Resource_Service_Games::getGameInfoByName($params);
					$resource_games = Common::resetKey($resource_games, "id");
					$name_ids =  array_unique(array_keys($resource_games));
					$s['name'] = $name;
						
					if($name_ids && $fbl_games){
						$intersect_games = array_intersect($fbl_games,$name_ids);
						if($intersect_games){
							$search['game_id'] = array('IN',$intersect_games);
						} else {
							$search['game_id'] = 0;
						}
					} else if(!$name_ids){
						$search['game_id'] = 0;
					}
				}
					
			} else {
				$search['game_id'] = 0;
			}
			
		}  else {
			
			if($name) {
				$params['name'] = array('LIKE',$name);
				$resource_games = Resource_Service_Games::getGameInfoByName($params);
				$resource_games = Common::resetKey($resource_games, "id");
				$name_ids =  array_unique(array_keys($resource_games));
				$s['name'] = $name;
							
				if($name_ids && $all_ids){
					$intersect_games = array_intersect($name_ids,$all_ids);
					if($intersect_games){
						$search['game_id'] = array('IN',$intersect_games);
					} else {
						$search['game_id'] = 0;
					}
				} else if(!$name_ids){
					$search['game_id'] = 0;
				}
			} else {
				$search['game_id'] = array('IN',$all_ids);
			}
			
			
		}
			
		$installe_games = Client_Service_Installe::getIdxInstalleByInstalleId($id);
		$installe_games = Common::resetKey($installe_games, "game_id");
		$installe_games =  array_unique(array_keys($installe_games));
		$search['status'] = 1;
			
		list($total, $games) = Resource_Service_Games::getVersionGames($page, $this->perpage, $search);
		$games = Common::resetKey($games, "game_id");
		$this->assign('total', $total);
		$this->assign('games', $games);
		$this->assign('all_games', $all_games);
		$this->assign('installe_games', $installe_games);
		
		
		$this->assign('id', $id);
		$this->assign('s', $s);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('gtype', 'status', 'update_time'));
		$info = $this->_cookData($info);
		$info['start_time'] = strtotime($info['start_time']);
		$info['update_time'] = Common::getTime();
		$result = Client_Service_Installe::addInstalle($info);
		if (!$result) $this->output(-1, '操作失败');
		$webroot = Common::getWebRoot();
		$this->output(0,'操作成功,请添加游戏',$result);
		exit;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'gtype', 'status', 'update_time'));
		$info = $this->_cookData($info);
		$info['start_time'] = strtotime($info['start_time']);
		$info['update_time'] = Common::getTime();
		$ret = Client_Service_Installe::updateInstalle($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['gtype']) $this->output(-1, '请选择分组.');
		return $info;
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Installe::getInstalle($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Installe::deleteInstalle($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteIdAction() {
		$id = intval($this->getInput('id'));
		$game_id = intval($this->getInput('game_id'));
		$result = Client_Service_Installe::deleteByInstalleId($game_id,$id);
		Client_Service_Installe::updateInstalleDate(intval($id));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
