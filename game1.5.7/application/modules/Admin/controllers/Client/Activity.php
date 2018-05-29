<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_ActivityController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Activity/index',
		'addUrl' => '/Admin/Client_Activity/add',
		'addLotteryUrl' => '/Admin/Client_Activity/addlottery',
		'addLotteryPostUrl' => '/Admin/Client_Activity/addlottery_post',
		'addPostUrl' => '/Admin/Client_Activity/add_post',
		'editUrl' => '/Admin/Client_Activity/edit',
		'editLotteryUrl' => '/Admin/Client_Activity/editlottery',
		'editLotteryPostUrl' => '/Admin/Client_Activity/editlottery_post',
		'editPostUrl' => '/Admin/Client_Activity/edit_post',
	    'lotterylogUrl' => '/Admin/Client_Activity/log',
		'editlotterylogUrl' => '/Admin/Client_Activity/editlog',
		'editlotteryonelogUrl' => '/Admin/Client_Activity/editonelog',
		'editPostlotteryonelogUrl' => '/Admin/Client_Activity/editonelog_post',
		'exportUrl'=>'/admin/Client_Activity/export',
		'uploadUrl' => '/Admin/Client_Activity/upload',
		'uploadImgUrl' => '/Admin/Client_Activity/uploadImg',
		'uploadPostUrl' => '/Admin/Client_Activity/upload_post',
		'batchUpdateUrl'=>'/Admin/Client_Activity/batchUpdate'
	);
	
	public $perpage = 20;
	public $awards = array(
			1 => '一等奖',
			2 => '二等奖',
			3 => '三等奖',
	);
	public $grant_status = array(
			1 => '已发放',
			0 => '未发放',
	);
	//最大概率百万级的
	public $maxRate = 1000000;
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('status', 'name', 'id'));
		if ($page < 1) $page = 1;
		
		//检查某活动是否添加奖项，没有的话就删除
		list(,$check_hds) = Client_Service_Activity::getAllActivity();
		foreach($check_hds as $key=>$values){
			$rules = Client_Service_Activity::getRule($values['id']);
			if(!$rules){
				Client_Service_Activity::delete($values['id']);
			}
		}
		
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['name']) $params['name'] = array('LIKE',$s['name']);
		if ($s['id']) $params['id'] = $s['id'];
		
		
		
	    list($total, $activityies) = Client_Service_Activity::getList($page, $this->perpage, $params);
		$this->assign('activityies', $activityies);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Activity::get($id);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_type', $this->ad_type);
	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Client_Service_Activity::updateActivityStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Client_Service_Activity::updateActivityStatus($info['ids'], 0);
		} else if($info['action'] =='grant'){
			$ret = Client_Service_Activity::updateLogGrantStatus($info['ids'], 1);
		} else if($info['action'] =='pending'){
			$ret = Client_Service_Activity::updateLogLabelStatus($info['ids'], 1);
		} else if($info['action'] =='nopending'){
			$ret = Client_Service_Activity::updateLogLabelStatus($info['ids'], 0);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Activity::batchSort($info['sort']);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'number', 'name', 'online_start_time',  'online_end_time', 'start_time',  'end_time', 'award_time','img', 'min_version', 'descrip', 'status','message','popup_status'));
		$info = $this->_cookData($info);
		$info['update_time'] = Common::getTime();
		$ret = Client_Service_Activity::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0,'操作成功,请设置奖项',$ret);
		exit;
	}	
	
	public function addlotteryAction() {
		$activity_id = intval($this->getInput('id'));
		$this->assign('activity_id', $activity_id);
		$this->assign('maxRate', $this->maxRate);
	}
	
	public function addlottery_postAction() {
		$activity_id = intval($this->getInput('activity_id'));
		$info = $this->getPost(array('award_name', 'img', 'icon', 'probability', 'space_time', 'num'));
		$data = array();
		foreach($info['award_name'] as $key=>$val){
			$data[] = array(
					'id'=>'',
					'award_name'=>$val,
					'probability'=>$info['probability'][$key],
					'lottery_id'=>($key + 1),
					'activity_id'=>$activity_id,
					'num'=>$info['num'][$key],
					'img'=>$info['img'][$key],
					'icon'=>$info['icon'][$key],
					'space_time'=>$info['space_time'][$key]
			);
			//判断输入项是否为空
			$data[$key] = $this->_cookLotteryData($data[$key],($key+1));
		}
		$sum = 0;
		foreach($info['probability'] as $k=>$v){
			$sum += $v;
		}
		$max = $this->maxRate;
		if($sum > $max) $this->output('-1', "三个奖项的中奖概率不能超过{$max}.");
		$ret = Client_Service_Activity::addMutiFateRule($data);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editlotteryAction() {
		$id = intval($this->getInput('id'));
		$rules = Client_Service_Activity::getRule($id);
		$this->assign('awards', $this->awards);
		$this->assign('activity_id', $id);
		$this->assign('rules', $rules);
		$this->assign('maxRate', $this->maxRate);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editlottery_postAction() {
		$activity_id = intval($this->getInput('activity_id'));
		$info = $this->getPost(array('id','award_name', 'img', 'icon', 'probability', 'space_time', 'num'));		
	    $data = array();
		foreach($info['id'] as $key=>$val){
			$data[$val] = array(
					'id'=>$val,
					'award_name'=>$info['award_name'][$key],
					'probability'=>$info['probability'][$key],
					'lottery_id'=>($key + 1),
					'activity_id'=>$activity_id,
					'num'=>$info['num'][$key],
					'img'=>$info['img'][$key],
					'icon'=>$info['icon'][$key],
					'space_time'=>$info['space_time'][$key]
			);
			//判断输入项是否为空
			$data[$val] = $this->_cookLotteryData($data[$val],($key+1));
		}
		$sum = 0;
		foreach($info['probability'] as $k=>$v){
			$sum += $v;
		}
		$max = $this->maxRate;
		if($sum > $max) $this->output('-1', "三个奖项的中奖概率不能超过{$max}.");
		$ret = Client_Service_Activity::updateFateRule($data);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'number', 'name', 'online_start_time',  'online_end_time', 'start_time',  'end_time', 'award_time','img', 'min_version', array('descrip', '#s_z'), 'status','message','popup_status'));
		$info['descrip'] = str_replace("<br />","",html_entity_decode($info['descrip']));
		$info = $this->_cookData($info);	
		$info['update_time'] = Common::getTime();
		$ret = Client_Service_Activity::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '活动名称不能为空.'); 
		if(!$info['descrip']) $this->output(-1, '活动说明不能为空.');
		//if(!$info['online_start_time']) $this->output(-1, '上线时间不能为空.');
		//if(!$info['online_end_time']) $this->output(-1, '下线时间不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		//if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		//if(!$info['award_time']) $this->output(-1, '兑奖时间不能为空.');
		if(!$info['img']) $this->output(-1, '活动图片不能为空.');
		if($info['min_version']){
			if(strnatcmp($info['min_version'], '1.4.9') <= 0) $this->output(-1, '最低版本不能低于1.5.0');
		}
		if(!$info['number']) $this->output(-1, '抽奖次数不能为空.');
		$info['online_start_time'] = strtotime($info['online_start_time']);
		$info['online_end_time'] = strtotime($info['online_end_time']);
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		$info['award_time'] = strtotime($info['award_time']);
		if(preg_match('/^(-)[0-9]*$/',$info['number'])){
			$this->output(-1, '抽奖次数是正整数，填写非法.');
		}
		//if($info['online_end_time'] <= $info['online_start_time']) $this->output(-1, '下线时间不能小于上线时间.');
		//if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		//if($info['start_time'] < $info['online_start_time']) $this->output(-1, '抽奖开始时间不能早于活动上线时间.');
		//if($info['online_end_time'] < $info['end_time']) $this->output(-1, '抽奖结束时间不能晚于活动下线时间.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookLotteryData($info,$jiangx) {
		if(!$info['award_name']) $this->output(-1, $this->awards[$jiangx].'奖品名称不能为空.');
		if(!$info['img']) $this->output(-1, $this->awards[$jiangx].'图片不能为空.');
		if(!$info['icon']) $this->output(-1, $this->awards[$jiangx].'图标不能为空.');

		$info['probability'] = $this->_checkLotteryData($info['probability'],'probability',$this->awards[$jiangx]);
		$info['space_time'] = $this->_checkLotteryData($info['space_time'],'space_time',$this->awards[$jiangx]);
		$info['num'] = $this->_checkLotteryData($info['num'],'num',$this->awards[$jiangx]);
		$jx_num = Client_Service_FateLog::countFateLogsByLotteryId($info['activity_id'],$jiangx);
		if($info['num'] < $jx_num) $this->output(-1, '当天'.$this->awards[$jiangx].'奖品总数不能小于当天已中奖数.');
		return $info;
	}
	
	private function _checkLotteryData($data,$type,$msg) {
		if($type == "probability"){
			$tip ="中奖概率";
		}else if($type == "space_time"){
			$tip ="发放最小间隔";
		}else if($type == "num"){
			$tip ="最大数量";
		}
		if($data == NULL){
			$this->output(-1, $msg.$tip.'不能为空.');
		}
		if(preg_match('/^-[1-9]\d*$/',$data)){
			$this->output(-1, $msg.$tip.'不能为负整数.');
		}
		if(preg_match('/^-([1-9]\d*\.\d*|0\.\d*[1-9]\d*)$/',$data)){
			$this->output(-1, $msg.$tip.'不能为负小数.');
		}
		return $data;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function logAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('start_time','end_time','status','uname','lottery_id','name'));
		if ($page < 1) $page = 1;
		list(, $activities) = Client_Service_Activity::getAllActivity();
		$activities = Common::resetKey($activities, 'id');
		
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['start_time'] = strtotime(($s['start_time']));
		if ($s['end_time']) $params['end_time'] = strtotime(($s['end_time']));
		if ($s['lottery_id']) $params['lottery_id'] = $s['lottery_id'];
		if ($s['uname'])  $params['uname'] = array('LIKE',$s['uname']);
		if ($s['name'])  {
			$activity_ids = Client_Service_Activity::activitySearch(array('name'=>array('LIKE',$s['name'])));
			$activity_ids = Common::resetKey($activity_ids, "id");
			$activity_ids = array_unique(array_keys($activity_ids));
			if($activity_ids){
				$params['activity_id'] = array('IN',$activity_ids);
			} else {
				$params['activity_id'] = 0;
			}
		}
        list($total, $logs) = Client_Service_FateLog::search($page, $this->perpage,$params);
		
		$this->assign('logs', $logs);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('activities', $activities);
		$this->assign('awards', $this->awards);
		$this->assign('activity_id', $s['id']);
		$url = $this->actions['lotterylogUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editlogAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('start_time','end_time','grant_status','uname','lottery_id','label_status','name'));
		if ($page < 1) $page = 1;
		list(, $activities) = Client_Service_Activity::getAllActivity();
		$activities = Common::resetKey($activities, 'id');
	
		$params = array();
		if ($s['grant_status']) $params['grant_status'] = $s['grant_status'] - 1;
		if ($s['start_time']) $params['start_time'] = strtotime(($s['start_time']));
		if ($s['end_time']) $params['end_time'] = strtotime(($s['end_time']));
		if ($s['uname'])  $params['uname'] = array('LIKE',$s['uname']);
		if ($s['label_status']) $params['label_status'] = $s['label_status'] - 1;
		if ($s['lottery_id']) {
			$params['lottery_id'] = $s['lottery_id'];
		} else {
			$params['lottery_id'] = array('>',0);
		}
		if ($s['name'])  {
			$activity_ids = Client_Service_Activity::activitySearch(array('name'=>array('LIKE',$s['name'])));
			$activity_ids = Common::resetKey($activity_ids, "id");
			$activity_ids = array_unique(array_keys($activity_ids));
			if($activity_ids){
				$params['activity_id'] = array('IN',$activity_ids);
			} else {
				$params['activity_id'] = 0;
			}
		}
		
		list($total, $logs) = Client_Service_FateLog::search($page, $this->perpage,$params);
	
		$this->assign('logs', $logs);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('activities', $activities);
		$this->assign('awards', $this->awards);
		$this->assign('grant_status', $this->grant_status);
		$url = $this->actions['editlotterylogUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editonelogAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_FateLog::getFateLog($id);
		$usr_info = Account_Service_User::getUserInfo(array('uname'=>$info['uname']));
		$act_info = Client_Service_Activity::getRule($info['activity_id']);
		$hd_info  =  Client_Service_Activity::get($info['activity_id']);
		$act_info = Common::resetKey($act_info, 'lottery_id');
		$this->assign('info', $info);
		$this->assign('usr_info', $usr_info);
		$this->assign('hd_info', $hd_info);
		$this->assign('awards', $this->awards);
		$this->assign('act_info', $act_info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editonelog_postAction() {
		$s = $this->getInput(array('name','address','grant_status','id','uname','remark'));
		$info = Client_Service_FateLog::updateFateLogGrantStatus($s['grant_status'],$s['remark'],$s['id']);
		$usr_info = Account_Service_User::updateUserInfo(array('realname'=>$s['name'],'address'=>$s['address']),array('uname'=>$s['uname']));
		if (!$info || !$usr_info) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Get order list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('start_time','end_time','status','uname','lottery_id','label_status','grant_status','log','mang_log'));
		if ($page < 1) $page = 1;
		
		list(, $activities) = Client_Service_Activity::getAllActivity();
		$activities = Common::resetKey($activities, 'id');
				
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['start_time'] = strtotime(($s['start_time']));
		if ($s['end_time']) $params['end_time'] = strtotime(($s['end_time']));
		if ($s['uname']) $params['uname'] = array('LIKE',$s['uname']);
		if ($s['lottery_id']) $params['lottery_id'] = $s['lottery_id'];
		if ($s['label_status']) $params['label_status'] = $s['label_status'] - 1;
		if ($s['grant_status']) $params['grant_status'] = $s['grant_status'] - 1;
		if ($s['lottery_id']) {
			$params['lottery_id'] = $s['lottery_id'];
		} else if($s['mang_log']){
			$params['lottery_id'] = array('>',0);
		}
        list($total, $logs) = Client_Service_FateLog::search($page, 10000, $params);
        
		$file_content = "";
		//抽奖记录
		if($s['log'])  {
			$file_content .= "\"ID\",";
			$file_content .= "\"账号\",";
			$file_content .= "\"用户IMEI\",";
			$file_content .= "\"抽奖时间\",";
			$file_content .= "\"中奖状态\",";
			$file_content .= "\"奖项\",";
			$file_content .= "\"活动名称\",";
			$file_content .= "\r\n";
			
			foreach ($logs as $key=>$value) {
				$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
				$file_content .= "\"	" . $value['id'] . "\",";
				$file_content .= "\"" . $value['uname'] . "\",";
				$file_content .= "\"" . $value['imei'] . "\",";
				$file_content .= "\"" . $create_time . "\",";
				$file_content .= "\"" . ($value['status'] ? '已中奖' : '未中奖') . "\",";
				$file_content .= "\"" . ($value['lottery_id'] ? $this->awards[$value['lottery_id']] : '无') . "\",";
				$file_content .= "\"" . $activities[$value['activity_id']]['name'] . "\",";
				$file_content .= "\r\n";
					
			}
		//奖品管理
		} else if($s['mang_log']){
			$file_content .= "\"ID\",";
			$file_content .= "\"账号\",";
			$file_content .= "\"用户IMEI\",";
			$file_content .= "\"抽奖时间\",";
			$file_content .= "\"活动名称\",";
			$file_content .= "\"奖项\",";
			$file_content .= "\"发放状态\",";
			$file_content .= "\"标记状态\",";
			$file_content .= "\r\n";
				
			foreach ($logs as $key=>$value) {
				$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
				$file_content .= "\"	" . $value['id'] . "\",";
				$file_content .= "\"" . $value['uname'] . "\",";
				$file_content .= "\"" . $value['imei'] . "\",";
				$file_content .= "\"" . $create_time . "\",";
				$file_content .= "\"" . $activities[$value['activity_id']]['name'] . "\",";
				$file_content .= "\"" . ($value['lottery_id'] ? $this->awards[$value['lottery_id']] : '无') . "\",";
				$file_content .= "\"" . ($value['grant_status'] ? '已发放' : '未发放') . "\",";
				$file_content .= "\"" . ($value['label_status'] ? '挂起' : '') . "\",";
				$file_content .= "\r\n";
					
			}
		}
		
		if( Common::browserPlatform()) {
			$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		}
		
	    Util_DownFile::downloadFile(date('Y-m-d H:i:s') . '.csv', $file_content);
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
		$ret = Common::upload('img', 'activity');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    public function uploadImgAction() {
    	$ret = Common::upload('imgFile', 'activity');
    	if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
    	exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
    }
}
