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
		'batchUpdateUrl'=>'/Admin/Client_Gift/batchUpdate'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
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
			$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1,$value['id']);    //已经领过激活码数量
			$tmp[$value['id']][] = $remain_gifts;
			$temp[$value['id']][] = $not_gifts;
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('remain_logs', $tmp);
		$this->assign('not_gifts', $temp);
		$this->assign('game_names', $game_names);
		$this->assign('gifts', $gifts);
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
		$codes = explode("<br />",html_entity_decode($info['activation_code']));
		$this->assign('codes', $codes);
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		$this->assign('game_info', $game_info);
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
		$gift_id = $this->getInput('gift_id');
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
			$info = Client_Service_Gift::getGift(intval($gift_id));
			$codes = explode("<br />",html_entity_decode($info['activation_code']));
			foreach($codes as $key=>$value){
				if($value && ($log_info['activation_code'] == $value)){
					$codes[$key] = $activation_code;
				}
			}
			$tmp = array();
			foreach($codes as $k=>$v){
				if($v){
					$tmp[]  = $v;
				}
			}
			$code = implode('<br />',$tmp);
			$ret = Client_Service_Gift::updateActivationCode($code,$gift_id);
			$ret_log = Client_Service_Giftlog::updateActivationCode($activation_code, $id);
			if (!$ret || !$ret_log) $this->output(-1, '操作失败');
			$this->output(0, '操作成功');
		}
		
	}
	
	/**
	 * add activation_code
	 */
	public function addlog_postAction() {
		$gift_id = $this->getInput('gift_id');
		$game_id = $this->getInput('game_id');
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
		$logs = Client_Service_Giftlog::getByGiftId(intval($gift_id));
		$logs = Common::resetKey($logs, 'activation_code');
		$logs = array_unique(array_keys($logs));
		
		$tmp = $temp = array();
		foreach($new_codes as $key=>$value){
			if($value && !in_array($value,$logs)){
				$tmp[] = $value;
				$temp[] = array(
						'id'=>'',
						'gift_id'=>$gift_id,
						'game_id'=>$game_id,
						'uname'=>'',
						'imei'=>'',
						'imeicrc'=>'',
						'activation_code'=>$value,
						'create_time'=>'',
						'status'=>0
				);
			}
		}
		
		$info = Client_Service_Gift::getGift(intval($gift_id));
		$old_codes = explode("<br />",html_entity_decode($info['activation_code']));
		if($tmp){
			$tmp = array_merge($old_codes,$tmp);
		}
		
		$code = implode('<br />',$tmp);
		$ret = Client_Service_Gift::updateActivationCode($code,$gift_id);
		$ret_log = Client_Service_Giftlog::mutiGiftlog($temp);
		if (!$ret || !$ret_log) $this->output(-1, '操作失败');
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
	
	//批量操作
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='open'){
			$ret = Client_Service_Gift::updateGiftStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Gift::updateGiftStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Gift::batchSortByGift($info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'game_id', 'name', 'content', array('activation_code', '#s_zb'), 'method', 'use_start_time', 'use_end_time', 'effect_start_time', 'effect_end_time', 'status','game_status'));
		$info = $this->_cookData($info);
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		if (!$game_info) $this->output(-1, '该游戏id不存在');
		$info['game_status'] = $game_info['status'];
		$codes =explode("<br />",html_entity_decode($info['activation_code']));
		$result = Client_Service_Gift::addGiftGame($info,$codes);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'game_id', 'name', 'content', array('activation_code', '#s_zb'), 'method', 'use_start_time', 'use_end_time', 'effect_start_time', 'effect_end_time', 'status','game_status'));
		$info = $this->_cookData($info);
		
		$game_id = $this->getInput('egame_id');
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		if (!$game_info) $this->output(-1, '该游戏id不存在.');
		$info['game_status'] = $game_info['status'];
		$tmp = array();
		$codes = explode("<br />",html_entity_decode($info['activation_code']));
		foreach($codes as $key=>$value){
			if($value){
				$tmp[] = $value;
			}
		}
		$codes = $tmp;
		
		//查找该礼包的所有激活码
		$logs = Client_Service_Giftlog::getsByGiftId($info['id']);
		$logs = Common::resetKey($logs, 'activation_code');
		$logs = array_unique(array_keys($logs));
		$ret = Client_Service_Gift::updateGiftGame($info, intval($info['id']), $game_id, $codes, $logs);
		if (!$ret) $this->output(-1, '操作失败');
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
		if(!$info['content']) $this->output(-1, '礼包内容不能为空.');
		if(!$info['use_start_time']) $this->output(-1, '开始使用时间不能为空.');
		if(!$info['use_end_time']) $this->output(-1, '结束使用时间不能为空.');
		if(!$info['method']) $this->output(-1, '礼包使用方法不能为空.');
		if(!$info['activation_code']) $this->output(-1, '兑奖码不能为空.');
		$codes = explode("<br />",html_entity_decode($info['activation_code']));
		$codes = array_unique($codes);
		$tmp = array();
		foreach ($codes as $key=>$value) {
			if ($value) $tmp[] = $value;
		}
		if(empty($tmp) && !count($tmp)){
			$this->output(-1, '兑奖码不能为空.');
		}
		
		if(!$info['effect_start_time']) $this->output(-1, '开始生效时间不能为空.');
		if(!$info['effect_end_time']) $this->output(-1, '结束生效时间不能为空.');
		$info['use_start_time'] = strtotime($info['use_start_time']);
		$info['use_end_time'] = strtotime($info['use_end_time']);
		$info['effect_start_time'] = strtotime($info['effect_start_time']);
		$info['effect_end_time'] = strtotime($info['effect_end_time']);
		if($info['use_end_time'] <= $info['use_start_time']) $this->output(-1, '开始使用时间不能小于结束使用时间.');
		if($info['effect_end_time'] <= $info['effect_start_time']) $this->output(-1, '开始生效时间不能小于结束生效时间.');
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
		$s = $this->getInput(array('start_time','end_time','id','uname'));
		$info = Client_Service_Gift::getGift($s['id']);
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		
		$remain_gifts = Client_Service_Giftlog::getGiftlogByStatus(0,$s['id']);
		$not_gifts = Client_Service_Giftlog::getGiftlogByStatus(1,$s['id']);
		
		$params = array();
		if ($s['start_time']) $params['create_time'] = array('>=', strtotime($s['start_time']));
		if ($s['end_time']) $params['create_time'] = array('<=', strtotime($s['end_time']));
		if ($s['start_time'] && $s['end_time']) $params['create_time'] = array(array('>=', strtotime($s['start_time'])), array('<=', strtotime($s['end_time']))) ;
		if ($s['uname']) $params['uname'] = $s['uname'];
		$params['gift_id'] = $id;
		
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

		//查找该礼包的激活码
		$info = Client_Service_Gift::getGift(intval($gift_id));
		$codes = explode("<br />",html_entity_decode($info['activation_code']));
		foreach($codes as $key=>$value){
			if($value && ($log_info['activation_code'] == $value)){
				$codes[$key] = '';
			}
		}
		
		$tmp = array();
		foreach($codes as $k=>$v){
			if($v){
				$tmp[]  = $v;
			}
		}
		$code = implode('<br />',$tmp);
		$ret = Client_Service_Gift::updateActivationCode($code,$gift_id);
		$ret_log = Client_Service_Giftlog::deleteGiftlog($id);
		if (!$ret || !$ret_log) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
