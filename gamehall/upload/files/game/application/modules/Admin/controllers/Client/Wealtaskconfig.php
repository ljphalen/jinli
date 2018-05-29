<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_WealtaskconfigController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Wealtaskconfig/index',
		'dlistUrl' => '/Admin/Client_Wealtaskconfig/dindex',
		'wealTaskDetailUrl'=>'/Admin/Client_Wealtaskconfig/wealTaskDetail',	
		'continueLoginActivityAddUrl' => '/Admin/Client_Wealtaskconfig/continueLoginActivityAdd',
		'continueLoginActivityAddPostUrl' => '/Admin/Client_Wealtaskconfig/continueLoginActivityAddPost',
		'jeditUrl' => '/Admin/Client_Wealtaskconfig/jedit',
		'jPostUrl' => '/Admin/Client_Wealtaskconfig/jedit_post',
		'continueLoginDetailUrl' => '/Admin/Client_Wealtaskconfig/continueLoginDetail',
		'deditUrl' => '/Admin/Client_Wealtaskconfig/dedit',
		'deditPostUrl' => '/Admin/Client_Wealtaskconfig/dedit_post',
		'taskInfoConfigUrl' => '/Admin/Client_Wealtaskconfig/taskInfoConfig',
		'taskInfoConfigPostUrl' => '/Admin/Client_Wealtaskconfig/taskInfoConfigPost',
		'editUrl' => '/Admin/Client_Wealtaskconfig/edit',
		'editPostUrl' => '/Admin/Client_Wealtaskconfig/edit_post',
		'deleteUrl' => '/Admin/Client_Wealtaskconfig/delete',
		'uploadUrl' => '/Admin/Client_Wealtaskconfig/upload',
		'uploadImgUrl' => '/Admin/Client_Wealtaskconfig/uploadImg',
		'uploadPostUrl' => '/Admin/Client_Wealtaskconfig/upload_post',
		'dailyTaskUrl'  =>'/Admin/Client_Wealtaskconfig/dailyTask',
		'dailyTaskEditUrl'  =>'/Admin/Client_Wealtaskconfig/dailyTaskEdit',
		'dailyTaskEditPostUrl'  =>'/Admin/Client_Wealtaskconfig/dailyTaskEditPost',
		'dailyTaskDetailUrl' => '/Admin/Client_Wealtaskconfig/dailyTaskDetail',
		'dailyTaskConfigUrl'=>'/Admin/Client_Wealtaskconfig/dailyTaskConfig',
		'dailyTaskConfigPostUrl'=>'/Admin/Client_Wealtaskconfig/dailyTaskConfigPost',
			
	);
	
	public $perpage = 20;
	public $Weal_versionName = 'Weal_Version';
	public $Task_versionName = 'Task_Version';
	
	/**
	 * 
	 * 福利任务列表
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('status', 'task_name'));
		if ($page < 1) $page = 1;
		
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['task_name']) $params['task_name'] = array('LIKE',$s['task_name']);
		
	    list($total, $result) = Client_Service_WealTaskConfig::getList($page, $this->perpage, $params, array('id'=>'ASC'));
	    foreach($result as $key=>$value){
	    	$ret = Client_Service_TaskStatisticReport::getBy(array('task_type'=>1,'task_sub_type'=>$value['id']));
	    	$tmp[$value['id']]['num'] = $ret['people_number'];
	    	$tmp[$value['id']]['ticket_total'] = $ret['tickets'];
	    }
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('nums', $tmp);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	/**
	 * 福利任务的明细
	 */
	public function wealTaskDetailAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('task_id','uname', 'uuid', 'start_time', 'end_time'));
		if ($page < 1) $page = 1;
	
		$params = $uuidIds = $unameIds = array();
		if ($s['uuid']) {
			$search =  array();
			$search['uuid'] = array('LIKE',$s['uuid']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$uuidIds[] = $value['uuid'];
				}
			}
		}
		if ($s['uname']) {
			$search =  array();
			$search['uname'] = array('LIKE',$s['uname']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$unameIds[] = $value['uuid'];
				}
			}
		}
		
		if(count($uuidIds) && count($unameIds) ){
			$params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
		}elseif(count($uuidIds)){
			$params['uuid'] = array('IN',$uuidIds);
		}elseif(count($unameIds)){
			$params['uuid'] = array('IN',$unameIds);
		}
	
		if($s['start_time']){
			$params['consume_time'][0] = array('>=', strtotime($s['start_time']));
		}
		if($s['end_time']){
			$params['consume_time'][1] = array('<=', strtotime($s['end_time']));
		}
		if($s['task_id']){
			$params['sub_send_type'] = $s['task_id'];
		}
		$params['status'] = 1;
		$params['send_type'] = 1;
	
		list($total, $result) = Client_Service_TicketTrade::getList($page, $this->perpage, $params, array('id'=>'ASC'));
		foreach($result as $key=>$value){
			$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
			$tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
			$tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('userInfo', $tempUserInfo);
		$this->assign('total', $total);
		$url = $this->actions['wealTaskDetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	/**
	 *
	 * 每日任务列表
	 */
	public function dailyTaskAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('status', 'task_name'));
		if ($page < 1) $page = 1;
	
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['task_name']) $params['task_name'] = array('LIKE',$s['task_name']);
	
		list($total, $result) = Client_Service_DailyTaskConfig::getList($page, $this->perpage, $params, array('id'=>'ASC'));
		foreach($result as $key=>$value){
			$ret = Client_Service_TaskStatisticReport::getBy(array('task_type'=>2,'task_sub_type'=>$value['id']));
			if(!$ret) continue;
			$tmp[$value['id']]['num'] = $ret['people_number'];
			$tmp[$value['id']]['points'] = $ret['points'];
			$tmp[$value['id']]['tickets'] = $ret['tickets'];
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('nums', $tmp);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * 每日任务编辑
	 */
	public function dailyTaskEditAction() {
		$id = $this->getInput('id');
		$info = Client_Service_DailyTaskConfig::getByID(intval($id));
		$award_json = json_decode($info['award_json'],true);
		$this->assign('award_json', $award_json);
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * 每日任务编辑提交
	 */
	public function dailyTaskEditPostAction() {
		$info = $this->getPost(array('id', 'task_name', 'resume', 'daily_limit','send_object', 'descript', 'award','date','deadline', 'status', 'create_time', 'points'));
		$info = $this->cookDailyData($info);
		$ret = Client_Service_DailyTaskConfig::update($info, intval($info['id']));
		//设置缓存
		$this->setDailyTaskCache();
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 设置每日任务的缓存
	 */
	private function setDailyTaskCache(){
		Client_Service_DailyTaskConfig::setDailyTaskCache();	
	}
	
	/**
	 * 每日任务的明细
	 */
	public function dailyTaskDetailAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('task_id','uname', 'uuid', 'start_time', 'end_time'));
		if ($page < 1) $page = 1;
	
		$params = $uuidIds = $unameIds = array();
		if ($s['uuid']) {
			$search =  array();
			$search['uuid'] = array('LIKE',$s['uuid']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$uuidIds[] = $value['uuid'];
				}
			}
		}
		if ($s['uname']) {
			$search =  array();
			$search['uname'] = array('LIKE',$s['uname']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$unameIds[] = $value['uuid'];
				}
			}
		}
		
		if(count($uuidIds) && count($unameIds) ){
			$params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
		}elseif(count($uuidIds)){
			$params['uuid'] = array('IN',$uuidIds);
		}elseif(count($unameIds)){
			$params['uuid'] = array('IN',$unameIds);
		}
		
		if($s['start_time']){
			$params['create_time'][0] = array('>=', strtotime($s['start_time']));
		}
		if($s['end_time']){
			$params['create_time'][1] = array('<=', strtotime($s['end_time']));
		}
		if($s['task_id']){
			$params['task_id'] = $s['task_id'];
		}
		$params['status'] = 1;
		
		list($total, $result) = Client_Service_DailyTaskLog::getList($page, $this->perpage, $params, array('id'=>'ASC'));
		foreach($result as $key=>$value){
			$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
			$tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
			$tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];	
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('userInfo', $tempUserInfo);
		$this->assign('total', $total);
		$url = $this->actions['dailyTaskDetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	/**
	 * 
	 * 福利任务编辑
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_WealTaskConfig::get(intval($id));
		$rets = Client_Service_WealTaskConfig::getsBy(array('status'=>1,'id'=>array('<>',6)));
		$award_json = json_decode($info['award_json'],true);
		$this->assign('award_json', $award_json);
		$this->assign('info', $info);
		$this->assign('num', count($rets));
	}
	

	/**
	 * 
	 * 福利任务编辑提交
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'task_name', 'resume', 'subject_id', 'descript', 'award','date','deadline', 'status', 'create_time'));
		$info = $this->_cookData($info);
		$tmp = array();
		if(!$info['award']) $this->output(-1, ' A券不能为空.');
		foreach($info['award'] as $key=>$value){
			if(!trim($value)) $this->output(-1, ' A券面额不能为空.');
			if(!trim($info['date'][$key])) $this->output(-1, ' A券有效期开始时间不能为空.');
			if(!trim($info['deadline'][$key])) $this->output(-1, ' A券有效期结束时间不能为空.');
			if(trim($info['date'][$key]) > trim($info['deadline'][$key])) $this->output(-1, ' A券有效期开始时间不能大于结束时间.');
			if(intval($value) < 1) $this->output(-1, ' A券面额为正整数.');
			if(intval($info['date'][$key]) < 1) $this->output(-1, ' A券有效期开始时间为正整数.');
			if(intval($info['deadline'][$key]) < 1) $this->output(-1, ' A券有效期结束时间为正整数.');
			$tmp[] = array(
					'denomination'  => intval(trim($value)),
					'section_start' => intval(trim($info['date'][$key])),
					'section_end' => intval(trim($info['deadline'][$key])),
					);
		}
		$award_json = json_encode($tmp,true);
		$info['award_json'] = $award_json;
		$ret = Client_Service_WealTaskConfig::update($info, intval($info['id']));
		Game_Service_Config::setValue($this->Weal_versionName, Common::getTime());

		//更新缓存
		Client_Service_WealTaskConfig::setNumData();
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 * 任务说明配置
	 */
	public function taskInfoConfigAction() {
		$configs['game_task_desc'] = Game_Service_Config::getValue('game_task_desc');
		$configs['game_weal_desc'] = Game_Service_Config::getValue('game_weal_desc');
		$configs['gameDailyTaskDesc'] = Game_Service_Config::getValue('gameDailyTaskDesc');
		$this->assign('configs', $configs);
	}
	
	/**
	 * Enter description here ...
	 */
	public function taskInfoConfigPostAction() {
		$config = $this->getPost(array('game_task_desc', 'game_weal_desc', 'gameDailyTaskDesc'));
		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$result = Client_Service_WealTaskConfig::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 连续登录的活动登陆列表
	 * Enter description here ...
	 */
	public function dindexAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('status', 'name'));
		if ($page < 1) $page = 1;
	
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['task_name']) $params['task_name'] = array('LIKE',$s['task_name']);
	

	
		list($total, $result) = Client_Service_ContinueLoginActivityConfig::getList($page, $this->perpage, $params, array('start_time'=>'DESC','id'=>'DESC'));
		
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$url = $this->actions['dlistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 添加连续登录的活动
	 * Enter description here ...
	 */
	public function continueLoginActivityAddAction() {
		
	}
	
	/**
	 * 添加连续登录的活动的提交
	 * Enter description here ...
	 */
	public function continueLoginActivityAddPostAction() {
		$info = $this->getPost(array('task_name', 'img', 'award_type', 'award_1','award_2', 'start_time', 'end_time', 'status', 'create_time'));
		$info = $this->cookHdData($info);
		if($info['award_type'] == 1) $info['award'] = $info['award_1'];
		if($info['award_type'] == 2) $info['award'] = $info['award_2'];
		$info['create_time'] = Common::getTime();
		$result = Client_Service_ContinueLoginActivityConfig::add($info);
		if(!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 编辑连续登录的活动
	 * edit an subject
	 */
	public function deditAction() {
		$id = $this->getInput('id');
		$info = Client_Service_ContinueLoginActivityConfig::get(intval($id));
		if($info['award_type'] == 1) $info['award_1'] = $info['award'];
		if($info['award_type'] == 2) $info['award_2'] = $info['award'];
		$this->assign('info', $info);
	}
	
	/**
	 * 编辑连续登录的活动提交
	 * Enter description here ...
	 */
	public function dedit_postAction() {
		$info = $this->getPost(array('id','task_name', 'img', 'award_type', 'award_1','award_2', 'start_time', 'end_time', 'status', 'create_time'));
		$info = $this->cookHdData($info);
		if($info['award_type'] == 1) $info['award'] = $info['award_1'];
		if($info['award_type'] == 2) $info['award'] = $info['award_2'];
		$result = Client_Service_ContinueLoginActivityConfig::update($info,$info['id']);
		//Game_Service_Config::setValue($this->Task_versionName, Common::getTime());
		if(!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 连续登录奖励配置编辑
	 * Enter description here ...
	 */
	public function jeditAction() {
		$info = Client_Service_ContinueLoginCofig::getByID(1);
		$award_json = json_decode($info['award_json'],true);
		$this->assign('info', $info);
		$this->assign('award_json', $award_json);
		
		
	}
	
	/**
	 * 连续登录奖励配置编辑提交
	 * Enter description here ...
	 */
	public function jedit_postAction() {
		$info = $this->getPost(array('id', 'tips', 'awardPOint','awardTicket', 'date', 'status','send_object'));
		$info = $this->cookConfigData($info);

		$tmp = array();
		foreach($info['send_object'] as $key=>$value){
			if($value == 1){
				$tmp[] = array(
						'send_object'  => intval($info['send_object'][$key]),
						'denomination'  =>intval($info['awardPOint'][$key]),
				);
			}else{
				$tmp[] = array(
						'send_object'  => intval($info['send_object'][$key]),
						'denomination'  => intval($info['awardTicket'][$key]),
						'deadline' => intval($info['date'][$key]),
				);
			}
		
		}
		
		$info['award_json'] = json_encode($tmp,true);
		$result = Client_Service_ContinueLoginCofig::update($info,$info['id']);
		if(!$result) $this->output(-1, '操作失败');
	
		if($info['status'] == 0){
			Client_Service_ContinueLoginActivityConfig::updateBy(array('status'=>0), array('id'=>array('>', 0)));
		
		}
		$this->output(0, '操作成功');
	}
	
	/**
	 * 连续登录奖励的明细列表
	 * Enter description here ...
	 */
	public function continueLoginDetailAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('uname', 'uuid'));
		if ($page < 1) $page = 1;
		$params = $uuidIds = $unameIds = array();
		if ($s['uuid']) {
			$search =  array();
			$search['uuid'] = array('LIKE',$s['uuid']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$uuidIds[] = $value['uuid'];
				}
			}
		}
		if ($s['uname']) {
			$search =  array();
			$search['uname'] = array('LIKE',$s['uname']);
			$users = Account_Service_User::getUsers($search);
			if($users){
				foreach($users as $key=>$value){
					$unameIds[] = $value['uuid'];
				}
			}
		}
		
		if(count($uuidIds) && count($unameIds) ){
			$params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
		}elseif(count($uuidIds)){
			$params['uuid'] = array('IN',$uuidIds);
		}elseif(count($unameIds)){
			$params['uuid'] = array('IN',$unameIds);
		}

		$params['task_id'] = 1;
		list($total, $result) = Client_Service_DailyTaskLog::getList($page, $this->perpage, $params, array('id'=>'ASC'));
		foreach($result as $key=>$value){
			$userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
			$tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
			$tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];
			
		}
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('userInfo', $tempUserInfo);
		$this->assign('total', $total);
		$url = $this->actions['continueLoginDetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	/**
	 * 日常任务的配置
	 */
	private function dailyTaskConfigAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	
	
	}
	
	
	/**
	 * 日常任务的配置
	 */
	private function dailyTaskConfigPostAction() {
		$postParams = array('daily_task_download_category_id',
							'daily_task_share_category_id',
				
		);
		
		$config = $this->getPost($postParams);
		
		if(intval($config['daily_task_download_category_id']) < 1) {
			$this->output(-1, '每日任务下载的分类ID为整型数字.');
		}
		
		if(intval($config['daily_task_share_category_id']) < 1) {
			$this->output(-1, '每日任务分享的内容ID为整型数字.');
		}
		
		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	
	
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['task_name']) $this->output(-1, '任务名称不能为空.');
		if(!$info['descript']) $this->output(-1, ' 任务条件不能为空.');
		if(!$info['resume']) $this->output(-1, ' 任务简述不能为空.');
		if(($info['id'] == 2 || $info['id'] == 3 || $info['id'] == 4 || $info['id'] == 5 || $info['id'] == 6) && !$info['subject_id']) $this->output(-1, ' 专题ID不能为空.');
		if(in_array($info['id'], array('2','3','4','5','6'))){
			$ret = Client_Service_Subject::getOnlineSubject($info['subject_id']);
			if(!$ret){
				$this->output(-1, '专题过期或专题不存在');
			}
		}
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function cookDailyData($info) {
		if(!$info['id']) $this->output(-1, '非法操作.');
		if(!$info['task_name']) $this->output(-1, '任务名称不能为空.');
		if(!$info['descript']) $this->output(-1, ' 任务条件不能为空.');
		if(!$info['resume']) $this->output(-1, ' 任务简述不能为空.');
		if(intval($info['daily_limit']) < 1) $this->output(-1, ' 每日上限的次数不能小于1的整数.');
		
		if($info['send_object'] == 2){
			
			if(!count($info['award'])){
				$this->output(-1, 'A券选项不能为空.');
			}
			
		    foreach($info['award'] as $key=>$value){
				if(!trim($value)){
					$this->output(-1, ' A券面额不能为空.');
				} 
				if(!trim($info['date'][$key])){
					$this->output(-1, ' A券有效期开始时间不能为空.');
				} 
				if(!trim($info['deadline'][$key])){
					$this->output(-1, ' A券有效期结束时间不能为空.');
				} 
				if(trim($info['date'][$key]) > trim($info['deadline'][$key])){
					$this->output(-1, ' A券有效期开始时间不能大于结束时间.');
				} 
				$tmp[] = array(
						'denomination'  => intval(trim($value)),
						'section_start' => trim($info['date'][$key]),
						'section_end' => trim($info['deadline'][$key]),
				);
			 }
			 $award_json = json_encode($tmp);
			 $info['award_json'] = $award_json;
		}else{
			if(intval($info['points']) < 1){
				$this->output(-1, ' 积分数量不能小于1的整数.');
			} 
			$info['points'] = intval($info['points']);
		}
		return $info;
	}
	

	/**
	 *
	 * Enter description here ...
	 */
	private function cookHdData($info) {
		if(!$info['task_name']) $this->output(-1, '任务名称不能为空.');
		if(!$info['img']) $this->output(-1, '弹出框图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或者等于结束时间.');
		if((!$info['award_1'] && $info['award_type'] ==1) || (!$info['award_2'] && $info['award_type'] == 2)) $this->output(-1, '奖励方式不能为空.');
		if($info['award_type']==1 &&  (intval($info['award_1']) > 9 || intval($info['award_1'] < 1)) ){
			$this->output(-1, '奖励方式必须填写1-9正整数.');
		}
		
		if($info['award_type']==2 &&  (intval($info['award_2']) > 9 || intval($info['award_2'] < 1)) ){
			$this->output(-1, '奖励方式必须填写1-9正整数.');
		}
		if(  ($info['award_type'] == 1 && $info['award_1'] < 0)  || ($info['award_type'] == 2 && $info['award_2'] < 0) ){
			$this->output(-1, '奖励方式必须填写正整数.');
		}
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function cookConfigData($info) {
		foreach($info['send_object'] as $key=>$value){
			
			if($value  == 1 && trim($info['awardPOint'][$key] == '' )){
				$this->output(-1, '第'.($key+1).'天奖励积分'.'不能为空.');
			}elseif($value  == 2 && trim($info['awardTicket'][$key] == '')){
				$this->output(-1, '第'.($key+1).'天奖励A券'.'不能为空.'.$info['awardTicket'][$key]);
			}
			
			if($value  == 1){
				$str='积分';
				$denomination = intval($info['awardPOint'][$key]);
			}elseif($value  == 2){
				$str='A券';
				$denomination = intval($info['awardTicket'][$key]);
			}
		
			
			if( $denomination < 0 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$denomination)) {
				$this->output(-1, '第'.($key+1).'天奖励'.$str.'必须填写正整数.');
			}
			if( $value == 2  && !$info['date'][$key] ) {
				$this->output(-1, '第'.($key+1).'天奖励有效期不能为空.');
			}
			if( $value == 2 && ($info['date'][$key] < 0 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$info['date'][$key]))) {
				$this->output(-1, '第'.($key+1).'天奖励有效期必须填写正整数.');
			}
		}
		return $info;
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
		$ret = Common::upload('img', 'weal');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'weal');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
	
	
	
	
}
