<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Ad_BesttjController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Besttj/index',
		'addUrl' => '/Admin/Client_Ad_Besttj/add',
		'addPostUrl' => '/Admin/Client_Ad_Besttj/add_post',
		'editCtUrl' => '/Admin/Client_Ad_Besttj/editCt',
		'addCtUrl' => '/Admin/Client_Ad_Besttj/addCt',
		'editUrl' => '/Admin/Client_Ad_Besttj/edit',
		'editPostUrl' => '/Admin/Client_Ad_Besttj/edit_post',
		'deleteUrl' => '/Admin/Client_Ad_Besttj/delete',
		'deleteIdUrl' => '/Admin/Client_Ad_Besttj/deleteId',
		'configUrl' => '/Admin/Client_Ad_Besttj/config',
		'configPostUrl' => '/Admin/Client_Ad_Besttj/config_post',
		'uploadUrl' => '/Admin/Client_Ad_Besttj/upload',
		'uploadPostUrl' => '/Admin/Client_Ad_Besttj/upload_post',
		'checkUrl' => '/Admin/Client_Ad_Besttj/check',
		'batchUpdateUrl'=>'/Admin/Client_Ad_Besttj/batchUpdate'
	);
	const TMP_EXPIRE = 180;
	
	public $perpage = 20;
	//网络
	public $ntype = array(
			1 => 'WIFI',
			2 => '非WIFI',
			3 => '全部网络',
	);
	//推荐方式
	public $btype = array(
	        3 => '4个',
			2 => '9个',
			1 => '1个',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		//不满足条件的删除/恢复
		$id = intval($this->getInput('id'));
		$opc = $this->getInput('opc');
		$fulfil = $this->getInput('fulfil');
		self::rollBackData($id, $opc, $fulfil);
		
		
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status','start_time','end_time', 'gtype', 'ntype', 'btype'));
		$search = array();
		
		if ($s['title']) $search['title'] =  array('LIKE',$s['title']);
		if ($s['status']) $search['status'] = $s['status'] - 1;
		if ($s['gtype']) $search['gtype'] =  $s['gtype'];
		if ($s['start_time']) $search['start_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $search['start_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['ntype']) $search['ntype'] =  $s['ntype'];
		if ($s['btype']) $search['btype'] =  $s['btype'];
		
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$groups = Common::resetKey($groups, 'id');
		$this->cookieParams();
		list($total, $besttjs) = Client_Service_Besttj::getList($page, $this->perpage,$search);
		
		$besttj_ids = array();
		foreach($besttjs as $key=>$value){
			$bs = Client_Service_Besttj::getIdxBesttjByBesttjId($value['id']);
			foreach($bs as $k=>$v){
				$game_info = Resource_Service_Games::getGameAllInfo(array('id'=>$v['game_id']));
				if($game_info){
					$besttj_ids[$v['besttj_id']][] = $game_info['name'];
				}
			}
		}
		
		$this->assign('besttjs', $besttjs);
		$this->assign('groups', $groups);
		$this->assign('search', $search);
		$this->assign('besttj_ids', $besttj_ids);
		$this->assign('total', $total);
		$this->assign('s', $s);
		$this->assign('ntype', $this->ntype);
		$this->assign('btype', $this->btype);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an Besttj
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Besttj::getBesttj(intval($id));
		list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
		$this->assign('groups', $groups);
		$this->assign('info', $info);
		$this->assign('id', $id);
		$this->assign('ntype', $this->ntype);
		$this->assign('btype', $this->btype);
	}
	
	/**
	 *
	 * edit games
	 */
	public function editCtAction() {
		$id = $this->getInput('id');
		$opc = $this->getInput('opc');
		$page = intval($this->getInput('page'));
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1));
		$categorys_id = Common::resetKey($categorys, 'id');
		
		$info = Client_Service_Besttj::getBesttj(intval($id));
		$this->assign('info', $info);
		$besttj_games = Client_Service_Besttj::getIdxBesttjByBesttjId($id);
		$besttj_games = Common::resetKey($besttj_games, "game_id");
		$this->assign('besttj_games', $this->getBestTjGames($id));
		$besttj_games =  array_unique(array_keys($besttj_games));
		$search = array();
		if($besttj_games){
			
			$search['id'] = array('IN',$besttj_games);
			$search['status'] = 1;
		    list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
			foreach($games as $key=>$value){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
			}
			$oline_versions = common::resetkey($info, 'id');
			//游戏分类
			$tmp = $category_games = $client_games = array();
			$category_games = Resource_Service_Games::getIdxGameResourceCategoryBy(array('game_id'=>array('IN', $besttj_games)));
			foreach($category_games as $key=>$value){
				$tmp[$value['game_id']][] = $value['category_id'];
			}
			$category_title = array();
			foreach($tmp as $key=>$val){
				foreach($val as $key1=>$val1){
					$category_title[$key][] = $categorys_id[$val1]['title'];
				}
					
			}
		}
		$this->assign('category_title', $category_title);
		$this->assign('oline_versions', $oline_versions);
		$url = $this->actions['editCtUrl'].'/?id='.$id . '&opc='.$opc.'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('games', $games);
		$this->assign('total', $total);
		$this->assign('id', $id);
		$this->assign('opc', $opc);
	}
	
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort', 'game_message'));
		$bestInfo = Client_Service_Besttj::getBesttj($id);
		$this->checkGameMessage($info['game_message'], $bestInfo);
		if (!count($info['ids'])) $this->output('-1', '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='add'){
			$ret = Client_Service_Besttj::batchAddByBesttj($info['ids'], $id, $info['game_message']);
		} else if($info['action'] =='delete'){
			$ret = Client_Service_Besttj::deleteByBesttjGames($info['ids'],$id);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Besttj::sortBesttjs($info['sort'], $id);
			$ret = Client_Service_Besttj::batchUpdateMessageByBesttj($id, $info['game_message']);
		} else if($info['action'] =='open'){
			$ret = Client_Service_Besttj::batchUpdate($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Besttj::batchUpdate($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	public function checkGameMessage($gameMessage, $bestInfo) {
	   if($bestInfo['btype'] == '3'){
	       foreach($gameMessage as $key=>$value){
	           if(!$value) {
	               $this->output('-1', '请填写完整游戏描述.');
	           }
	       }
	   }
	}
	
	/**
	 * get an subjct by Besttj_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Besttj::getBesttj(intval($id));
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
		$this->assign('ntype', $this->ntype);
		$this->assign('btype', $this->btype);
	}
	
	/**
	 *
	 * add games
	 */
	public function addCtAction() {
		$id = intval($this->getInput('id'));
		$name = $this->getInput('name');
		$opc = $this->getInput('opc');
		$flag = $this->getInput('flag');
		//设置操作恢复的数据
		self::saveCurrentData($id, $flag);
		$s['id'] = $id;
		$s['opc'] = $opc;
		$s['name'] = $name;
		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$info = Client_Service_Besttj::getBesttj($id);
		$p_info = Resource_Service_Pgroup::getPgroup(intval($info['gtype']));
		$ids = explode(',',$p_info['p_id']);
		
		$search = array();
		if($name) {
		    $name_ids =  $this->getGameInfo($name);
		    if(!$name_ids){
		        $search['id'] = '';
		    }
		
		    if($name_ids){
		        $search['id'] = array('IN',$name_ids);
		    }
		}
		
		$search['status'] = 1;
		list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $search);
		$besttjGames = $this->getBestTjGames($id);
		
		$gameInfo = $currentGameInfo = array();
		foreach($besttjGames['idListNum'] as $k=>$v){
		    $gameInfo[$v] = Resource_Service_GameData::getGameAllInfo($v);
		}
		
		foreach($games as $k=>$v){
		    $currentGameInfo[$v['id']] = Resource_Service_GameData::getGameAllInfo($v['id']);;
		}
		
		$this->assign('total', $total);
		$this->assign('currentGameInfo', $currentGameInfo);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('besttj_games', $this->getBestTjGames($id));
		
		$this->assign('id', $id);
		$this->assign('s', $s);
		$this->assign('opc', $opc);
		$url = $this->actions['addCtUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	
	}
	
	private function getGameInfo($name) {
	    $params['name'] = array('LIKE',$name);
	    $params['status'] = Resource_Service_Games::STATE_ONLINE;
	    $resource_games = Resource_Service_Games::getGameInfoByName($params);
	    $resource_games = Common::resetKey($resource_games, "id");
	    return array_unique(array_keys($resource_games));
	}
	
	private function getBestTjGames($id) {
	    $besttjGames = Client_Service_Besttj::getIdxBesttjByBesttjId($id);
	    $besttjNum = Common::resetKey($besttjGames, "game_id");
	    return array('idListNum' => array_unique(array_keys($besttjNum)), 'List' => $besttjNum);
	}
	
	public function checkAction(){
		$id = intval($this->getInput('id'));
		$info = Client_Service_Besttj::getBesttj($id);
		$curr_games = Client_Service_Besttj::getIdxBesttjByBesttjId($id);
		if ($info['btype'] == 1) {
			if(count($curr_games) >= 2 ){
				$this->output(-1, '你最多只能选择1个游戏');
			}
		} else if($info['btype'] == 2){
			if(count($curr_games) < 9){
				$this->output(-1, '不能少于9个游戏');
			}
		} else if($info['btype'] == 3){
			if(count($curr_games) < 4){
				$this->output(-1, '不能少于4个游戏');
			}
		}
		$this->output(0,'操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'guide', 'gtype','ntype','btype','img', 'status', 'start_time', 'update_time'));
		$info = $this->_cookData($info);
		$info['start_time'] = strtotime($info['start_time']);
		$info['update_time'] = Common::getTime();
		$result = Client_Service_Besttj::addBesttj($info);
		if (!$result) $this->output(-1, '操作失败');
		$webroot = Common::getWebRoot();
		$this->saveBestIdData($result);
		$this->output(0,'操作成功,请添加游戏',$result);
		exit;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'guide', 'gtype','ntype','btype','img', 'status', 'start_time', 'update_time'));
		$info = $this->_cookData($info);
		$info['start_time'] = strtotime($info['start_time']);
		$info['update_time'] = Common::getTime();
		$ret = Client_Service_Besttj::updateBesttj($info, intval($info['id']));
		$bts = Client_Service_Besttj::getBesttjByBesttjId($info['id']);
		if($bts){
			Client_Service_Besttj::updateBesttjByBesttjId($info['id'],$info['status']);
		}
		if (!$ret) $this->output(-1, '操作失败');
		Client_Service_Besttj::updateVersionToCache();
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 *
	 */
	public function configAction() {
		$game_client_bestime = Game_Service_Config::getValue('game_client_bestime');
		$this->assign('game_client_bestime', $game_client_bestime);
	}
	
	/**
	 *
	 */
	public function config_postAction() {
		$bestime = $this->getInput('game_client_bestime');
		Game_Service_Config::setValue('game_client_bestime', $bestime);
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['guide']) $this->output(-1, '导语不能为空.');
		if(!$info['start_time']) $this->output(-1, '生效时间不能为空.');
		if($info['btype'] == 1 && !$info['img']) $this->output(-1, '图片不能为空.');
		return $info;
	}
	
	private function rollBackData($id, $opc, $fulfil) {
	    $cache = $this->getCache();
	    $bestKey = $this->getCacheKey($id);
	    $bestIdKey = $bestKey.'info';
	    $bestId = $cache->get($bestIdKey);
	    if($id && $fulfil == "end"){ //完成后，设置恢复数据
	        $cache->delete($bestKey);
	        Client_Service_Besttj::updateVersionToCache();
	    } else {
	        $oldBestGames = $cache->get($bestKey);
	        $currentGames = Client_Service_Besttj::getIdxBesttjByBesttjId($id);
	        foreach($currentGames as $key=>$value){
	            Client_Service_Besttj::deleteByIdxBesttj(array('besttj_id'=>$id,'id'=>$value['id']));
	        }
	        $ret = Client_Service_Besttj::mutiBesttjGames($oldBestGames);
	        
	        if($bestId){
                Client_Service_Besttj::deleteByBesttj(array('id'=>$bestId));
                $currentAddGames = Client_Service_Besttj::getIdxBesttjByBesttjId($bestId);
                foreach($currentAddGames as $key=>$value){
                    Client_Service_Besttj::deleteByIdxBesttj(array('besttj_id'=>$bestId,'id'=>$value['id']));
                }
	        }
	    }
	    
	    if($bestId){
	        $cache->delete($bestIdKey);
	    }
	}
	
	private function saveBestIdData($id) {
	    if($id){
	        $cache = $this->getCache();
	        $bestKey = $this->getCacheKey($id);
	        $bestIdKey = $bestKey.'info';
	        $cache->set($bestIdKey, $id, self::TMP_EXPIRE);
	    }
	}
	
	private function saveCurrentData($id, $flag) {
	    $oldBestGames = Client_Service_Besttj::getIdxBesttjByBesttjId($id);
	    $cache = $this->getCache();
	    $bestKey = $this->getCacheKey($id);
	    if(count($oldBestGames) && is_array($oldBestGames) && $flag == 'start'){
	        $cache->set($bestKey, $oldBestGames, self::TMP_EXPIRE);
	    }
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Besttj::getBesttj($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Besttj::deleteBesttj($id);
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
		$result = Client_Service_Besttj::deleteByBesttjId($game_id,$id);
		Client_Service_Besttj::updateBesttjDate(intval($id));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'bestj');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	private function getCache(){
	    return Cache_Factory::getCache();
	}
	
	private function getCacheKey($id){
	   return Util_CacheKey::BEST_RECOMMEND.$id.$this->userInfo['username'];
	}
}
