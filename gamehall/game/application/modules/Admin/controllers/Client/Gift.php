<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_GiftController extends Admin_BaseController {
	
	public $actions = array(
	    'gameListUrl'=>'/Admin/Client_Gift/gamelist',		
		'listByGameUrl' => '/Admin/Client_Gift/listByGame',
		'hotUrl' => '/Admin/Client_Gift/hot',
		'addHotUrl' => '/Admin/Client_Gift/addHot',
		'editHotUrl' => '/Admin/Client_Gift/editHot',
		'addHotPostUrl' => '/Admin/Client_Gift/addHotPost',
		'editHotPostUrl' => '/Admin/Client_Gift/editHotPost',
		'listUrl' => '/Admin/Client_Gift/index',
		'addUrl' => '/Admin/Client_Gift/add',
		'addPostUrl' => '/Admin/Client_Gift/add_post',
		'editUrl' => '/Admin/Client_Gift/edit',
		'editPostUrl' => '/Admin/Client_Gift/edit_post',
		'deleteUrl' => '/Admin/Client_Gift/delete',
		'giftlogUrl' => '/Admin/Client_Gift/log',
		'editLogUrl' => '/Admin/Client_Gift/editlog',
		'addlogUrl' => '/Admin/Client_Gift/addlog',
		'addlogPostUrl' => '/Admin/Client_Gift/addlog_post',
		'editlogPostUrl' => '/Admin/Client_Gift/editlog_post',
		'batchUpdateUrl'=>'/Admin/Client_Gift/batchUpdate',
		'batchUpdateGiftHotUrl' => '/Admin/Client_Gift/batchUpdateGiftHot',
		'batchUpdateGameListUrl'=>'/Admin/Client_Gift/batchUpdateGameList',
	    'asyncWebGameUrl' => '/Admin/Client_Gift/asyncWebGame',
	    'privilegeGiftListUrl' => '/Admin/Client_Gift/privilegeGiftList',
	    'oneOfPrivilegeGiftListUrl' => '/Admin/Client_Gift/oneOfPrivilegeGiftList',
	);
	
	public $perpage = 20;
	
	/**
	 *
	 * 游戏列表界面
	 */
	public function gamelistAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title', 'game_id'));
		$params = $search = $game_ids = $ids = array();
		
		if ($s['game_id']) $params['game_id'] = $s['game_id'];
		if ($s['title']) {
			$search['name']  = array('LIKE',$s['title']);
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
		}	
		$params['game_status']  = Resource_Service_Games::STATE_ONLINE;
		$orderBy= array('game_sort'=>'DESC', 
						'game_id'=>'DESC',
						'sort'=>'DESC',
						'effect_start_time' => 'DESC',
						'id' => 'DESC');
		list($total, $result) = Client_Service_Gift::getGameList($page, $this->perpage, $params, $orderBy);
		foreach($result as $key=>$value){
			$gameId = $value['game_id'];
			$gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
			$gameName[$gameId] = $gameInfo['name'];
			$remainGiftNum[$gameId] = Client_Service_Gift::getGiftNumByGameId($gameId);
		} 
		
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('remainGiftNum', $remainGiftNum);
		$this->assign('gameName', $gameName);
		$this->assign('total', $total);
		$url = $this->actions['gameListUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 批量更新游戏列表操作
	 */
	function batchUpdateGameListAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_Gift::updateGiftStatusByGameId($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Gift::updateGiftStatusByGameId($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Gift::updateGameSortByGameId($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		Client_Service_Gift::updateGiftListVersionToCache();
		Client_Service_Gift::updatePrivilegeGiftListVersionCache();
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 获取游戏的礼包列表页面
	 */
	public function listByGameAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('game_id','status', 'name', 'id'));
		$params = $search = $ids = array();
		if ($s['game_id']) $params['game_id'] = $s['game_id'];
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['name']) $params['name'] = array('LIKE',$s['name']);
		if ($s['id']) $params['id'] = $s['id'];

		$orderBy = array('sort' => 'DESC', 'effect_start_time' => 'DESC', 'id' => 'DESC');
		list($total, $result) = Client_Service_Gift::getList($page, $this->perpage, $params, $orderBy);

		$info = Resource_Service_Games::getBy(array('id'=>$s['game_id']));
		$game_names[$s['game_id']] = $info['name'];

		$tmp = $temp = array();
		//0为剩下的激活码数量,1为已经领过激活码数量
		foreach($result as $key=>$value){
			$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']); //剩下的激活码数量
			$not_gifts    = Client_Service_Giftlog::getGiftlogByStatus(1,$value['id']);    //已经领过激活码数量
			$tmp[$value['id']][] = $remain_gifts;
			$temp[$value['id']][] = $not_gifts;
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('remain_logs', $tmp);
		$this->assign('not_gifts', $temp);
		$this->assign('game_names', $game_names);
		$this->assign('total', $total);
		$url = $this->actions['listByGameUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**
	 * 热门礼包页面
	 */
	public function hotAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('status', 'game_name', 'gift_name', 'gift_id'));
		$searchGift = $searchGame = $gameIds = array();
		if ($s['gift_id']) {
			$searchGift['gift_id'] = $s['gift_id'];
		} else {
			if ($s['status']) $searchGift['status'] = $s['status'] - 1;
			if ($s['gift_name']) $searchGift['gift_name'] = array('LIKE',$s['gift_name']);

			if ($s['game_name']) {
				$searchGame['name']  = array('LIKE',$s['game_name']);
				$searchGame['status']  = Resource_Service_Games::STATE_ONLINE;
				$games = Resource_Service_Games::getGamesByGameNames($searchGame);
				$games = Common::resetKey($games, 'id');
				$gameIds = array_unique(array_keys($games));
				if($gameIds){
					$searchGift['game_id'] = array('IN',$gameIds);
				} else {
					$searchGift['game_id'] = 0;
				}
			}
		}

		$searchGift['game_status']  = Resource_Service_Games::STATE_ONLINE;
		list($total, $hotList) = Client_Service_GiftHot::getList($page, $this->perpage, $searchGift);
		foreach($hotList as $key=>$value){
			$gameId = $value['game_id'];
			$gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
			$gameNames[$gameId] = $gameInfo['name'];
		}

		$remainsKey = $totalKey = array();
		foreach($hotList as $key=>$value){
			$giftId = $value['gift_id'];
			$giftInfo = Client_Service_Gift::getGift($giftId);
			$remainsKey[$value['gift_id']] = Client_Service_Gift::getGiftRemainNum($giftId);;
			$totalKey[$value['gift_id']] = Client_Service_Gift::getGiftTotal($giftId);
			$hotList[$key]['vip_level'] = $giftInfo['vip_level'];
		}
		
		$this->assign('result', $hotList);
		$this->assign('s', $s);
		$this->assign('remains_key', $remainsKey);
		$this->assign('total_key', $totalKey);
		$this->assign('game_names', $gameNames);
		$this->assign('total', $total);
		$url = $this->actions['hotUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	public function addHotAction() {}

	public function addHotPostAction() {
		$info = $this->getPost(array('sort', 'gift_id', 'gift_name', 'effect_start_time', 'effect_end_time', 'status'));
		if (empty($info['gift_id']) || empty($info['gift_name']) 
				|| empty($info['effect_start_time']) || empty($info['effect_end_time'])) {
			$this->output(-1, '礼包ID,名称,热门生效时间为必填字段');
		}
		$info = $this->_addHotCookData($info);
		$gift_info = Client_Service_Gift::getGift($info['gift_id']);
		if (!$gift_info) $this->output(-1, '该礼包id不存在');
		$this->checkHotTime($info, $gift_info);
		$this->checkDuplicateHotGift($info['gift_id']);
		$info['game_id'] = $gift_info['game_id'];
		$info['game_status'] = $gift_info['game_status'];

		$result = Client_Service_GiftHot::addGiftHot($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function editHotAction() {
		$gift_id = intval($this->getInput('id'));
		
		$info = Client_Service_GiftHot::getById($gift_id);	
		$this->assign('info', $info);
		
		$remainGifts = Client_Service_Gift::getGiftRemainNum($info['gift_id']);
		$this->assign('remainGifts', $remainGifts);
	
	}

	private function checkHotTime($hotInfo, $giftInfo) {
		if ($hotInfo['effect_end_time'] > $giftInfo['effect_end_time'] ||
				$hotInfo['effect_start_time'] < $giftInfo['effect_start_time']) {
			$this->output(-1, '热门有效期必须在礼包有效期内');
		}
	}

	private function checkDuplicateHotGift($giftId) {
		$search = array('gift_id' => $giftId);
		$hotGift = Client_Service_GiftHot::getBy($search);
		if ($hotGift) {
			$this->output(-1, '该礼包已经是热门');
		}
	}

	public function editHotPostAction() {
		$id = intval($this->getPost('id'));
		$info = $this->getPost(array('sort', 'gift_id', 'gift_name', 'effect_start_time', 'effect_end_time', 'status'));
		$info = $this->_addHotCookData($info);

		$gift_info = Client_Service_Gift::getGift($info['gift_id']);

		if (!$gift_info) $this->output(-1, '该礼包id不存在');

		$this->checkHotTime($info, $gift_info);

		$oldHotInfo = Client_Service_GiftHot::getById($id);
		if ($oldHotInfo['gift_id'] != $info['gift_id']) {
			$this->checkDuplicateHotGift($info['gift_id']);
		}

		$info['game_id'] = $gift_info['game_id'];
		$info['game_status'] = $info['status'];

		$result = Client_Service_GiftHot::updateById($info, $id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * 礼包列表界面
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status', 'name', 'id'));
		$params = $search = $game_ids = $ids = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['name']) $params['name'] = array('LIKE',$s['name']);
		if ($s['id']) $params['id'] = $s['id'];
		if ($s['title']) {
			$search['name']  = array('LIKE',$s['title']);
			$games = Resource_Service_Games::getGamesByGameNames($search); 
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
		}
		
		list($total, $result) = Client_Service_Gift::getList($page, $this->perpage,$params);
		foreach($result as $key=>$value){
			$info = Resource_Service_Games::getBy(array('id'=>$value['game_id']));
			$game_names[$value['game_id']] = $info['name'];
		}
		
		$tmp = $temp = array();
		//0为剩下的激活码数量,1为已经领过激活码数量
		foreach($result as $key=>$value){
			$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']); //剩下的激活码数量
			$not_gifts    = Client_Service_Giftlog::getGiftlogByStatus(1,$value['id']);    //已经领过激活码数量
			$tmp[$value['id']][] = $remain_gifts;
			$temp[$value['id']][] = $not_gifts;
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('remain_logs', $tmp);
		$this->assign('not_gifts', $temp);
		$this->assign('game_names', $game_names);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Gift::getGift(intval($id));
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		$this->assign('game_info', $game_info);
		$remainGifts = Client_Service_Gift::getGiftRemainNum($id);
		$this->assign('remainGifts', $remainGifts);
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * edit an subject
	 */
	public function editlogAction() {
		$id = $this->getInput('id');
		$log_info = Client_Service_Giftlog::getGiftlog(intval($id));
		$gift_info = Client_Service_Gift::getGift($log_info['gift_id']);
		$game_info = Resource_Service_Games::getResourceGames($log_info['game_id']);
		$this->assign('game_info', $game_info);
		$this->assign('log_info', $log_info);
		$this->assign('gift_info', $gift_info);
	}
	
	
	/**
	 *
	 * edit an subject
	 */
	public function addlogAction() {
		$gift_id = $this->getInput('gift_id');
		$game_id = $this->getInput('game_id');
		$gift_info = Client_Service_Gift::getGift($gift_id);
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		$this->assign('game_info', $game_info);
		$this->assign('gift_info', $gift_info);
	}
	
	/**
	 *
	 * edit an subject
	 */
	public function editlog_postAction() {
		$id = $this->getInput('id');
		$giftId = $this->getInput('gift_id');
		$activation_code = trim($this->getInput('activation_code'));
		if(!$activation_code) $this->output(-1, '兑奖码不能为空.');
		
		$log_info = Client_Service_Giftlog::getGiftlog(intval($id));
		if($log_info['status']) $this->output(-1, '该兑奖码已被领取，不能编辑.');

		//查找该礼包的所有激活码
		$logs = Client_Service_Giftlog::getsByGiftId($id);
		$logs = Common::resetKey($logs, 'activation_code');
		$logs = array_unique(array_keys($logs));
		if(in_array($activation_code,$logs)){
			$this->output(-1, '该兑奖码已存在，不能重复添加.');
		} else {
			$logResult = Client_Service_Giftlog::updateActivationCode($activation_code, $id);			
			
			$giftInfo = Client_Service_Gift::getGift($giftId);
			Client_Service_Gift::updataGiftBaseInfoCache($giftInfo, $giftId);
			Client_Service_Gift::updataGiftNumCacheByGiftId($giftId);
			Client_Service_Gift::updateGiftListVersionToCache($giftInfo['game_id']);
			Client_Service_Gift::updatePrivilegeGiftListVersionCache();
				
			if (!$logResult) $this->output(-1, '操作失败');
			$this->output(0, '操作成功');
		}
		
	}
	
	/**
	 * add activation_code
	 */
	public function addlog_postAction() {
		$giftId = intval($this->getInput('gift_id'));
		$gameId = $this->getInput('game_id');
		$data = $this->getPost(array(array('activation_code', '#s_zb')));
		
		
		$new_codes = explode("<br />",html_entity_decode($data['activation_code']));
		$new_codes = array_unique($new_codes);
		$acodes = array();
		foreach ($new_codes as $k=>$v) {
			if ($v) $acodes[] = $v;
		}
		if(empty($acodes) && !count($acodes)){
			$this->output(-1, '添加兑奖码不能为空.');
		}
		
		//查找该礼包的所有激活码
		$logs = Client_Service_Giftlog::getByGiftId($giftId);
		$logs = Common::resetKey($logs, 'activation_code');
		$logs = array_unique(array_keys($logs));
		
		$tmp = $temp = array();
		$ret = Client_Service_Giftlog::getBy(array('gift_id'=>$giftId,'log_type'=>Client_Service_Giftlog::GRAB_GIFT_LOG),array('send_order'=>'DESC'));
		$maxSendOrder = intval($ret['send_order']);
	
		foreach($new_codes as $key=>$value){
			if($value && !in_array($value,$logs)){
				$maxSendOrder ++;
				$tmp[] = $value;
				$temp[] = array(
						'id'=>'',
				        'log_type'=>Client_Service_Giftlog::GRAB_GIFT_LOG,
						'gift_id'=>$giftId,
						'game_id'=>$gameId,
						'uname'=>'',
						'imei'=>'',
						'imeicrc'=>'',
						'activation_code'=>$value,
						'create_time'=>'',
						'status'=>0,
						'send_order'=>$maxSendOrder
				);
			}
		}
		$ret_log = Client_Service_Giftlog::mutiGiftlog($temp);
		if (!$ret_log) $this->output(-1, '操作失败');
		$GiftInfo = Client_Service_Gift::getGift($giftId);

		Client_Service_Gift::updataGiftBaseInfoCache($GiftInfo, $giftId);
		Client_Service_Gift::updataGiftNumCacheByGiftId($giftId);
        //更新游戏礼包附加属性
        Resource_Service_GameExtraCache::refreshGameGift($gameId);
		
		//礼包列表的缓存
		Client_Service_Gift::updateGiftListVersionToCache($gameId);
		Client_Service_Gift::updatePrivilegeGiftListVersionCache();
		
		$this->output(0, '操作成功');
	
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Gift::getGift(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	
	/**
	 * add form page show
	 */
	public function get_nameAction() {
		$game_id = $this->getInput('game_id');
		$game_info = Resource_Service_Games::getResourceGames($game_id);
		if(!$game_info) $this->output(-1, '该游戏id不存在.');
		$temp = array();
		$temp['name'] = $game_info['name'];
		$this->output(0, '', array('list'=>$temp));
	}
	
	public function getGiftInfoAction() {
		$gift_id = $this->getInput('gift_id');
		$gift_info = Client_Service_Gift::getGift($gift_id);
		if(!$gift_info) $this->output(-1, '该礼包id不存在.');
		if(Client_Service_Gift::GIFT_STATE_OPENED != $gift_info['status']) {
			$this->output(-1, '该礼包已下线.');
		}
		if(Client_Service_Gift::GAME_STATE_ONLINE != $gift_info['game_status']) {
			$this->output(-1, '该礼包的游戏已下线.');
		}
		if (Common::getTime() >= $gift_info['effect_end_time']) {
			$this->output(-1, '该礼包已过期.');
		}
		$temp = array();
		$temp['name'] = html_entity_decode($gift_info['name'], ENT_QUOTES);
		$temp['effect_start_time'] = date('Y-m-d H:i:s', $gift_info['effect_start_time']);
		$temp['effect_end_time'] = date('Y-m-d H:i:s', $gift_info['effect_end_time']);
		$this->output(0, '', array('list'=>$temp));
	}
	
	//批量操作
	public function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='open'){
			foreach($info['ids'] as $key=>$value) {
				$remainGifts = Client_Service_Gift::getGiftRemainNum($value);
				if($remainGifts == 0){
					$this->output(-1, '礼包id为'.$value.'的没有激活码，不能开启');
				}
			}
			$ret = Client_Service_Gift::updateGiftStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Gift::updateGiftStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Gift::batchSortByGift($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		Client_Service_Gift::updateGiftListVersionToCache();
		Client_Service_Gift::updatePrivilegeGiftListVersionCache();
		
		$this->output('0', '操作成功.');
	}
	
	//批量操作
	public function batchUpdateGiftHotAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='open'){
			$ret = Client_Service_GiftHot::updateGiftStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_GiftHot::updateGiftStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_GiftHot::batchSortByGift($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'game_id', 'vip_level','dev_email', 'name', 'content', array('activation_code', '#s_zb'), 'method', 'use_start_time', 'use_end_time', 'effect_start_time', 'effect_end_time', 'status','game_status'));
		$info = $this->_cookData($info);
		$info['status'] = Client_Service_Gift::GIFT_STATE_CLOSEED;
		$gameInfo = Resource_Service_Games::getby(array('id'=>$info['game_id'], 'status'=>Client_Service_Gift::GAME_STATE_ONLINE));
		if (!$gameInfo) $this->output(-1, '该游戏id不存在或游戏已下线');
		$info['game_status'] = Client_Service_Gift::GAME_STATE_ONLINE;
		$info['game_sort'] = $this->getGameSort($info['game_id']);
		$result = Client_Service_Gift::addGiftBaseInfo($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function getGameSort($gameId){
		$giftInfo = Client_Service_Gift::getBy(array('game_id'=>$gameId));
		return $giftInfo['game_sort'] ? $giftInfo['game_sort'] : 0;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 
								'sort', 
								'game_id', 
								'name', 
		                        'vip_level',
		                        'dev_email',
								'content',
								 array('activation_code', '#s_zb'),
								 'method',
								 'use_start_time', 
								 'use_end_time', 
								 'effect_start_time',
								 'effect_end_time',
								 'status',
								 'game_status'));
		$info = $this->_cookData($info);
		
		$oldGameId = $this->getInput('egame_id');
		if ($oldGameId != $info['game_id']) {
			$this->output(-1, '禁止更换游戏');
		}

		$gameInfo = Resource_Service_Games::getResourceGames($info['game_id']);
		if (!$gameInfo) {
			$this->output(-1, '该游戏id不存在.');
		}
		$info['game_status'] = $gameInfo['status'];

		$oldGift = Client_Service_Gift::getGift($info['id']);
		$ret = Client_Service_Gift::updateGiftBaseInfo($info, intval($info['id']));
		if (!$ret) {
			$this->output(-1, '操作失败');
		}

	    if($info['status'] == Client_Service_Gift::GIFT_STATE_CLOSEED){
	    	//更新热门礼包游戏id
	    	Client_Service_GiftHot::updateBy(array('status'=>Client_Service_Gift::GIFT_STATE_CLOSEED),
	    	                                        array('gift_id'=>$info['id'])
	    	                                      );
	    }
        $this->refreshCache($info['game_id']);
	    if($oldGift['status'] != $info['status']) {
	        Client_Service_Gift::updateGiftStatusInAd($info['id'], $info['status']);
	    }
		$this->output(0, '操作成功.');
	}
	


	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		$codes = array();
		if(!$info['name']) $this->output(-1, '礼包名称不能为空.'); 
		if(strpos(html_entity_decode($info["name"]), ",")) $this->output(-1, '礼包名称不能有英文逗号.'); 
		if(!$info['game_id']) $this->output(-1, '游戏ID不能为空.');
		if(!$info['vip_level']) $this->output(-1, '游戏VIP等级不能为空.');
		if(!preg_match("/^[0-9]*[1-9][0-9]*$/",$info['vip_level'])) $this->output(-1, '游戏VIP等级只能为正整数.');
		if($info['vip_level'] < 1 || $info['vip_level'] > 15) $this->output(-1, '游戏VIP等级不能小于1或者大于15.');
		if(!$info['dev_email']) $this->output(-1, '开发者邮箱不能为空.');
		if($info['dev_email'] && !preg_match('/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/', $info['dev_email']) ){
		    $this->output(-1, '请确保你输入的邮箱地址正确');
		}
		if(!$info['content']) $this->output(-1, '礼包内容不能为空.');
		if(!$info['use_start_time']) $this->output(-1, '开始使用时间不能为空.');
		if(!$info['use_end_time']) $this->output(-1, '结束使用时间不能为空.');
		if(!$info['method']) $this->output(-1, '礼包使用方法不能为空.');	
		if(!$info['effect_start_time']) $this->output(-1, '开始生效时间不能为空.');
		if(!$info['effect_end_time']) $this->output(-1, '结束生效时间不能为空.');
		$info['use_start_time'] = strtotime($info['use_start_time']);
		$info['use_end_time'] = strtotime($info['use_end_time']);
		$info['effect_start_time'] = strtotime($info['effect_start_time']);
		$info['effect_end_time'] = strtotime($info['effect_end_time']);

		$info['effect_start_time'] = Util_TimeConvert::floor($info['effect_start_time'], Util_TimeConvert::RADIX_DAY);
		$info['effect_end_time'] = Util_TimeConvert::floor($info['effect_end_time'], Util_TimeConvert::RADIX_DAY) + Util_TimeConvert::SECOND_OF_DAY - 1;

		if($info['use_end_time'] <= $info['use_start_time']) $this->output(-1, '开始使用时间不能大于结束使用时间.');
		if($info['effect_end_time'] <= $info['effect_start_time']) $this->output(-1, '开始生效时间不能大于结束生效时间.');
		return $info;
	}
	
	private function _addHotCookData($info) {
		$codes = array();
		if(!$info['gift_name']) $this->output(-1, '礼包名称不能为空.');
		if(strpos(html_entity_decode($info["gift_name"]), ",")) $this->output(-1, '礼包名称不能有英文逗号.');
		if(!$info['effect_start_time']) $this->output(-1, '开始生效时间不能为空.');
		if(!$info['effect_end_time']) $this->output(-1, '结束生效时间不能为空.');
		$info['sort'] = intval($info['sort']);
		$info['effect_start_time'] = strtotime($info['effect_start_time']);
		$info['effect_end_time'] = strtotime($info['effect_end_time']);

		$info['effect_start_time'] = Util_TimeConvert::floor($info['effect_start_time'], Util_TimeConvert::RADIX_DAY);
		$info['effect_end_time'] = Util_TimeConvert::floor($info['effect_end_time'], Util_TimeConvert::RADIX_DAY) + Util_TimeConvert::SECOND_OF_DAY - 1;

		if($info['effect_end_time'] <= $info['effect_start_time']) $this->output(-1, '开始生效时间不能大于结束生效时间.');
		return $info;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function logAction() {
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('status', 'start_time','end_time','id','uname', 'nickname'));
		$info = Client_Service_Gift::getGift($s['id']);
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		
		$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$s['id']);
		$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1,$s['id']);
		
		$params = array();
		if ($s['start_time']) $params['create_time'] = array('>=', strtotime($s['start_time']));
		if ($s['end_time']) $params['create_time'] = array('<=', strtotime($s['end_time']));
		if ($s['start_time'] && $s['end_time']) $params['create_time'] = array(array('>=', strtotime($s['start_time'])), array('<=', strtotime($s['end_time']))) ;
		if ($s['uname']) {
			$params['uname'] = array('LIKE', $s['uname']);
		} else if ($s['nickname']) {
			$searchUser['nickname'] = array('LIKE', $s['nickname']);
            $userInfo = Account_Service_User::getUserInfo($searchUser);
			$params['uname'] = $userInfo['uname'];
		} else if($s['status']) {
			$params['status'] = $s['status'] - 1;
		}
		$params['gift_id'] = $id;
		$params['log_type'] = Client_Service_Giftlog::GRAB_GIFT_LOG;
		list($total, $logs) = Client_Service_Giftlog::getList($page, $this->perpage, $params, array('id'=>'DESC'));
		
		$this->assign('info', $info);
		$this->assign('game_info', $game_info);
		$this->assign('logs', $logs);
		$this->assign('total', $total);
		$this->assign('remain_gifts', $remain_gifts);
		$this->assign('not_gifts', $not_gifts);
		$this->assign('s', $s);
		$url = $this->actions['giftlogUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
	   	$id = $this->getInput('id');
		$gift_id = $this->getInput('gift_id');		
		$log_info = Client_Service_Giftlog::getGiftlog(intval($id));
		if($log_info['status']) $this->output(-1, '该兑奖码已被领取，不能删除.');
		$ret_log = Client_Service_Giftlog::deleteGiftlog($id);
		if (!$ret_log) $this->output(-1, '操作失败');
		
		$remainGiftNum =  Client_Service_Gift::getGiftRemainNum($gift_id);
		$remainGiftNum = Client_Service_Gift::reduceRemainActivitionCodeCache($gift_id, $remainGiftNum);
		$totalGift = Client_Service_Gift::getGiftTotal($gift_id);
		Client_Service_Gift::reduceTotalActivitionCodeCache($gift_id, $totalGift);
		if($remainGiftNum == 0){
			Client_Service_Gift::updateBy(array('status'=>0), array('id'=>$gift_id ));
			Client_Service_GiftHot::updateBy(array('status'=>0), array('gift_id'=>$gift_id));
			Client_Service_Gift::updataGiftNumCacheByGiftId($gift_id);
			Client_Service_Gift::updateGiftListVersionToCache();
			Client_Service_Gift::updatePrivilegeGiftListVersionCache();
				
		}
		$this->output(0, '操作成功'.$remainGiftNum);
	}
	
	function asyncWebGameAction() {
	    $opened = Game_Api_WebRecommendList::giftIsOpen();
	    if (!$opened) $this->output('-1', '网游频道未开启热门礼包');
	    Async_Task::execute('Async_Task_Adapter_UpdateWebRecCache', 'update');
	    $this->output('0', '操作成功.');
	}
	
	public function privilegeGiftListAction() {
	    $giftLevelList =  User_Config_Vip::$giftName;
	    $vipNameList =  User_Config_Vip::$vipName;
	    foreach($giftLevelList as $giftLevel=>$giftLevelTitle){
	        list($total, $privilegegiftsList) = $this->getOneOfPrivilegeGiftListByUserVipLevel($giftLevel);
	        $privilegeGiftList[$giftLevel] = array(
	                'total' => $total,
	                'remainPrivilegeGiftNum' => $this->getRemainPrivilegeGiftNum($privilegegiftsList),
	                'giftLevelTitle' => $this->getVipTitle($giftLevel),
	        );
	    }
	    $this->assign('vipNameList', $vipNameList);
	    $this->assign('privilegeGiftList', $privilegeGiftList);
	}
	
	private function getVipTitle($giftLevel) {
	    return User_Config_Vip::$giftName[$giftLevel].'('.User_Config_Vip::$vipName[$giftLevel][0].')';
	}
	
	public function oneOfPrivilegeGiftListAction() {
	    $giftLevel = $this->getInput('giftLevel');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('gift_id', 'status','name','giftLevel','vip_level'));
		list($total, $privilegegiftsList) = $this->getOneOfPrivilegeGiftList($s, $page);
		foreach($privilegegiftsList as $key=>$value){
		    $remainGifts = Client_Service_Giftlog::getGiftlogByStatus(0,$value['id']);    //剩下的激活码数量
		    $grabGifts    = Client_Service_Giftlog::getGiftlogByStatus(1,$value['id']);    //已经领过激活码数量
		    $remainArr[$value['id']][] = $remainGifts;
		    $tempArr[$value['id']][] = $grabGifts;
		}
		$giftLevelList =  User_Config_Vip::$giftName;
		$this->assign('remainLogs', $tempArr);
		$this->assign('grabLogs', $remainArr);
	    $this->assign('s', $s);
	    $this->assign('giftLevelList', $giftLevelList);
	    $this->assign('total', $total);
	    $this->assign('privilegegiftsList', $privilegegiftsList);
		$url = $this->actions['oneOfPrivilegeGiftListUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
	}
	
	private function getOneOfPrivilegeGiftList($s, $page) {
	    $total = 0;
	    $params = $privilegegiftsList = array();
	    if ($s['gift_id']) $params['gift_id'] = $s['gift_id'];
	    if ($s['status']) $params['status'] = $s['status'] - 1;
	    if ($s['name']) $params['name'] = array('LIKE', $s['name']);
	    $vipLevelList = User_Config_Vip::getVipListByGiftLevle($s['giftLevel']);
	    if($s['vip_level']){
	        $vipLevel = array_intersect($vipLevelList,array($s['vip_level']));
	        if($vipLevel){
	            $params['vip_level'] = array('IN', $vipLevelList);
	        }
	        return array($total, $privilegegiftsList);
	    } else {
	        $params['vip_level'] = array('IN', $vipLevelList);
	    }
	    
	    $orderBy = array('game_sort'=>'DESC',
	            'game_id'=>'DESC',
	            'sort'=>'DESC',
	            'effect_start_time' => 'DESC',
	            'id' => 'DESC');
	     
	    list($total, $privilegegiftsList) = Client_Service_Gift::getList($page,
	            $this->perpage,
	            $params,
	            $orderBy);
	    return array($total, $privilegegiftsList);
	}
	
	private function getOneOfPrivilegeGiftListByUserVipLevel($giftLevel) {
	    $vipLevelList = User_Config_Vip::getVipListByGiftLevle($giftLevel);
	    list($total, $privilegegiftsList) = $this->getPrivilegeGiftData($vipLevelList);
	    return array($total, $privilegegiftsList);
	}
	
	private function getRemainPrivilegeGiftNum($privilegegiftsList) {
	    $remainPrivilegeGiftNum = 0;
	    if(!$privilegegiftsList){
	        return $remainPrivilegeGiftNum;
	    }
	    
	    foreach($privilegegiftsList as $key=>$value){
	        $giftKeyRemains = Client_Service_Gift::getGiftRemainNum($value['id']);
	        if($giftKeyRemains){
	            $remainPrivilegeGiftNum++;
	        }
	    }
	    
	    return $remainPrivilegeGiftNum;
	}
	
	private function getPrivilegeGiftData($vipLevelList) {
	    $search['vip_level'] = array('IN', $vipLevelList);
	    $total = Client_Service_Gift::count($search);
	    $privilegegiftsList = Client_Service_Gift::getsBy($search);
	   return array($total, $privilegegiftsList);
	}

    private function refreshCache($gameId){
        //更新游戏礼包附加属性
        Resource_Service_GameExtraCache::refreshGameGift($gameId);
        //礼包列表的缓存
        Client_Service_Gift::updateGiftListVersionToCache($gameId);
        Client_Service_Gift::updatePrivilegeGiftListVersionCache();
        
        //异步更新
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteListItem', $gameId);
    }

}
