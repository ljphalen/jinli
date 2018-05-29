<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_GiftactivityController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Giftactivity/index',
		'addUrl' => '/Admin/Client_Giftactivity/addActivity',
		'addPostUrl' => '/Admin/Client_Giftactivity/addActivityPost',
		'editUrl' => '/Admin/Client_Giftactivity/editActivity',
		'editPostUrl' => '/Admin/Client_Giftactivity/editActivityPost',
	    'logUrl' => '/Admin/Client_Giftactivity/giftActivityLog',
		'deleteUrl' => '/Admin/Client_Giftactivity/deleteActivationCode',
		'batchUpdateUrl'=>'/Admin/Client_Giftactivity/batchUpdate',
	    'hdListUrl' => '/Admin/Client_Hd/index',
	    'importUrl' => '/Admin/Client_Giftactivity/import',
	    'editActivationCodeUrl' => '/Admin/Client_Giftactivity/editActivationCode',
	    'editActivationCodePostUrl' => '/Admin/Client_Giftactivity/editActivationCodePost',
	    'uploadUrl' => '/Admin/Client_Giftactivity/upload',
	    'uploadPostUrl' => '/Admin/Client_Giftactivity/upload_post',
	    'uploadImgUrl' => '/Admin/Client_Giftactivity/uploadImg',
	);
	
	public $perpage = 20;
	
	public $activityType = array(
	        Client_Service_GiftActivity::INSTALL_DOWNLOAD_GAME_SEND_GIFT => '下载安装送礼包',
	        Client_Service_GiftActivity::LOGIN_GAME_SEND_GIFT => '登陆游戏送礼包',
	);
	
	public $giftNumType = array(
	        Client_Service_GiftActivity::SINGLE_GIFT => '单个',
	        Client_Service_GiftActivity::MULTIPLE_GIFT => '多个',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$search = $this->getInput(array('title','activity_type','status', 'gift_num_type', 'name','effect_start_time','effect_end_time'));
		$params = array();
		$params = $this->searchInputParmes($search);
		list($total, $activities) = Client_Service_GiftActivity::getList($page, $this->perpage,$params);
		
		foreach($activities as $key=>$value) {
			$gameInfo = $this->getGameInfo($value['game_id']);
			$gameNames[$value['game_id']] = $gameInfo['name'];
			$activationCodeRemainCount = Client_Service_GiftActivity::getRemainGiftActivationCodeCount($value['id'], $value);
			$giftRemain[$value['id']] = $activationCodeRemainCount;
		}
		
		$this->assign('search', $search);
		$this->assign('gameNames', $gameNames);
		$this->assign('activities', $activities);
		$this->assign('activityType', $this->activityType);
		$this->assign('giftNumType', $this->giftNumType);
		$this->assign('total', $total);
		$this->assign('giftRemain', $giftRemain);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	private function searchInputParmes($search) {
	    $params =  $gameIds = $searchGame = array();
	    if ($search['title']) $params['title'] = array('LIKE',$search['title']);
	    if ($search['activity_type']) $params['activity_type'] = $search['activity_type'];
	    if ($search['status']) $params['status'] = $search['status'] - 1;
	    if ($search['gift_num_type']) $params['gift_num_type'] = $search['gift_num_type'];
	    if ($search['effect_start_time']) $params['effect_start_time'] = array('>=',strtotime($search['effect_start_time']));
	    if ($search['effect_end_time']) $params['effect_end_time'] = array('<=',strtotime($search['effect_end_time']));
	    
	    if ($search['name']) {
	        $searchGame['name']  = array('LIKE',$search['name']);
	        $games = Resource_Service_Games::getGamesByGameNames($searchGame);
	        $gameIds = Common::resetKey($games, 'id');
	        $gameIds = array_unique(array_keys($gameIds));
	        if($gameIds){
	            $params['game_id'] = array('IN',$gameIds);
	        } else {
	            $params['game_id'] = 0;
	        }	
	    }
	    return $params;
	}
	
	/**
	 * import activation code
	 */
	public function importAction() {
	    if(!empty($_FILES['importInputTxt']['tmp_name'])) {
echo <<<SCRIPT
			<script>
					window.parent.document.getElementById("importInputTxt").value = "";
					function _addcsvRow(d) {
						if(window.parent.document.getElementById("importInputTxt").value != "") {
							window.parent.document.getElementById("importInputTxt").value += "\\r\\n";
						}
						window.parent.document.getElementById("importInputTxt").value += d;
					}
        
                    function _showCount(n) {
						window.parent.document.getElementById("gift_number").value = n;
					}
			</script>
SCRIPT;
            $ext = strtolower(end(explode(".", $_FILES['importInputTxt']['name'])));
            if($ext != "txt") {
                echo '<script>alert("导入文件格式非法，必须是txt文件.");</script>';return '';
            }
	        ob_flush();
	        flush();
	        $file    = $_FILES['importInputTxt']['tmp_name'];
	        $handle  = fopen($file,'r');
	        if(!$handle) exit;
	        $count = 0;
	        $dataList = array();
	        while(!feof($handle)) {
                   $row = fgets($handle, 4096);
                   $row = mb_convert_encoding(trim($row), 'utf-8', 'gbk');
                   $dataList[] = $row;
	        }
	        $dataList = array_unique($dataList);
	        if(count($dataList) < 1) exit;
	
	        foreach($dataList as $data) {
	            if(empty($data)) continue;
	            echo '<script>_addcsvRow("'.$data.'");</script>';
	            ob_flush();
	            flush();
	            $count ++;
	        }
	        echo '<script>_showCount("'.$count.'");</script>';
	        echo '<script>alert("导入成功");</script>';
	    }
	}
	
	/**
	 * 
	 * edit Giftactivity
	 */
	public function editActivityAction() {
		$id = $this->getInput('id');
		$info = Client_Service_GiftActivity::getGiftActivity(intval($id));
		$giftActivityLogInfo = Client_Service_GiftActivityLog::getBy(array('gift_id'=>$id),array('id' => 'ASC'));
		$gameInfo = $this->getGameInfo($info['game_id']);
		$this->assign('info', $info);
		$this->assign('giftActivityLogInfo', $giftActivityLogInfo);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('activityType', $this->activityType);
		$this->assign('giftNumType', $this->giftNumType);
		$this->assign('singleGiftType', Client_Service_GiftActivity::SINGLE_GIFT);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addActivityAction() {
	    $this->assign('activityType', $this->activityType);
	    $this->assign('giftNumType', $this->giftNumType);
	}
	
	
	/**
	 * get game name
	 */
	public function getGameNameAction() {
		$gameId = $this->getInput('gameId');
		$gameInfo = $this->checkGameIsExist($gameId);
		if(!$gameInfo) {
		    $this->output(-1, '该游戏id不存在或游戏已下线');
		}
		$game = array();
		$game['name'] = html_entity_decode($gameInfo['name'], ENT_QUOTES);
		$this->output(0, '', array('list'=>$game));
	}

	//batch Operation
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='sort'){
			$ret = Client_Service_GiftActivity::batchActivitySort($info['sort']);
		}
	
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addActivityPostAction() {
		$activityInfo = $this->getPost(array(
		                                  'sort',
		                                  'title',
		                                  'activity_type',
		                                  'game_id',
		                                  'use_start_time', 
		                                  'use_end_time',
		                                  'effect_start_time',
		                                  'effect_end_time', 
		                                  'gift_num_type', 
		                                  'gift_number',
                                          array('importInputTxt', '#s_zb'),
                                          'gift_code',
                                          'content',
		                                  'status',
		                                  'method'));
		$activityInfo = $this->checkInputFieldData($activityInfo, 'add');
		if($activityInfo['gift_num_type'] == Client_Service_GiftActivity::SINGLE_GIFT){
		    $activationCodes = array($activityInfo['gift_code']);
		} else {
		    $activationCodes = explode("<br />",html_entity_decode($activityInfo['importInputTxt']));
		    $activationCodes = $this->getActivationCodes($activationCodes);
		    $activityInfo['gift_number'] = count($activationCodes);
		}
		
		unset($activityInfo['importInputTxt']);
		$activityInfo['create_time'] = Common::getTime();
		$activityInfo['game_status'] = Client_Service_GiftActivity::GAME_STATE_ONLINE;
		
		$result = Client_Service_GiftActivity::addGiftActivity($activityInfo, $activationCodes);
		if (!$result) $this->output(-1, '操作失败');
        $this->refreshCache($activityInfo['game_id']);
		$this->output(0, '操作成功');
	}
	
	private function getActivationCodes($activationCodes) {
	    $activationCodes = array_unique($activationCodes);
	    
	    $acodes = array();
	    foreach ($activationCodes as $k=>$v) {
	        if ($v) {
	            $acodes[] = $v;
	        }
	    }
	    if(empty($acodes) && !count($acodes)){
	        $this->output(-1, '添加兑奖码不能为空.');
	    }
	    return $acodes;
	}
	
	private function assembleGiftActivation($newCodes, $giftId, $gameId, $logs) {
	    $giftLog = array();
	    if (!is_array($newCodes)) return $giftLog;
	    foreach($newCodes as $key=>$value){
	        if($value && !in_array($value,$logs)){
	            $giftLog[] = array(
	                    'id'=>'',
	                    'gift_id'=>$giftId,
	                    'game_id'=>$gameId,
	                    'uuid'=>'',
	                    'uname'=>'',
	                    'imei'=>'',
	                    'imeicrc'=>'',
	                    'activation_code'=>$value,
	                    'create_time'=>'',
	                    'status'=>0,
	            );
	        }
	    }
	    return $giftLog;
	}
	
	private function checkGameIsExist($gameId) {
	    return Resource_Service_Games::getBy(array('id'=>$gameId, 'status'=>1));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editActivityPostAction() {
		$activityInfo = $this->getPost(array(
                                          'id',
		                                  'sort',
		                                  'title',
		                                  'activity_type',
		                                  'game_id',
		                                  'use_start_time', 
		                                  'use_end_time',
		                                  'effect_start_time',
		                                  'effect_end_time', 
		                                  'gift_num_type', 
		                                  'gift_number',
		                                  'status',
		                                  'game_status',
		                                  array('content', '#s_z'),
		                                  array('method', '#s_z')));
		$activityInfo['content'] = str_replace("<br />","",html_entity_decode($activityInfo['content']));
		$activityInfo['method'] = str_replace("<br />","",html_entity_decode($activityInfo['method']));
		$oldActivityInfo = $this->getPost(array('old_gift_number','gift_code'));
		$activityInfo = $this->checkInputFieldData($activityInfo, 'edit');
		$ret = Client_Service_GiftActivity::editGiftActivity($activityInfo, $oldActivityInfo);
		if(!$ret) $this->output(-1, '操作失败');
        $this->refreshCache($activityInfo['game_id']);
		$this->output(0, '操作成功.');
	}
	
	public function giftActivityLogAction() {
	    $giftId = $this->getInput('id');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$input = $this->getInput(array('status', 'start_time','end_time','id','uname', 'nickname'));
		$info = Client_Service_GiftActivity::getGiftActivity($input['id']);
		
		$gameInfo = $this->getGameInfo($info['game_id']);
		list($remainGiftCount, $grabGiftCount) = $this->getGiftCount($giftId);
		$params = $this->getActivationCodeParams($input, $giftId);
		
		list($total, $logs) = Client_Service_GiftActivityLog::getList($page, $this->perpage, $params, array('id'=>'DESC'));
		
		$this->assign('info', $info);
		$this->assign('gameInfo', $gameInfo);
		$this->assign('logs', $logs);
		$this->assign('total', $total);
		$this->assign('remainGiftCount', $remainGiftCount);
		$this->assign('grabGiftCount', $grabGiftCount);
		$this->assign('input', $input);
		$this->assign('singleGiftType', Client_Service_GiftActivity::SINGLE_GIFT);
		$url = $this->actions['logUrl'].'/?' . http_build_query($input) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	private function getGiftCount($giftId) {
	    $remainGiftCount = Client_Service_GiftActivityLog::getGiftLogCount(
	            array('status'=>Client_Service_GiftActivityLog::GIFT_NOT_SEND,
	                  'gift_id'=>$giftId));
	    $grabGiftCount = Client_Service_GiftActivityLog::getGiftLogCount(
	            array('status'=>Client_Service_GiftActivityLog::GIFT_ALREADY_SEND,
	                  'gift_id'=>$giftId));
	    return array($remainGiftCount, $grabGiftCount);
	}
	
	public function editActivationCodeAction() {
	    $giftId = $this->getInput('giftId');
	    $giftActivationCodeId = $this->getInput('giftActivationCodeId');
	    $giftActivityInfo = Client_Service_GiftActivity::getGiftActivity($giftId);
	    $giftActivationCodeInfo = Client_Service_GiftActivityLog::getById($giftActivationCodeId);
	    $gameInfo = $this->getGameInfo($giftActivityInfo['game_id']);
	    $multipActivityType = Client_Service_GiftActivity::MULTIPLE_GIFT;
	    $this->assign('gameInfo', $gameInfo);
	    $this->assign('giftActivationCodeInfo', $giftActivationCodeInfo);
	    $this->assign('giftActivityInfo', $giftActivityInfo);
	}
	
	public function editActivationCodePostAction() {
	    $giftId = $this->getInput('giftId');
	    $giftActivationCodeId = $this->getInput('giftActivationCodeId');
	    $activationCode = trim($this->getInput('activation_code'));
	    if(!$activationCode) $this->output(-1, '兑奖码不能为空.');
	
	    $log = $this->getGiftActivationCodes($giftId,$activationCode);
	    if($log && $log['id'] != $giftActivationCodeId){
	        $this->output(-1, '该兑奖码已存在，不能重复添加.');
	    } else {
	        $retLog = Client_Service_GiftActivityLog::updateBy(
	                array('activation_code'=>$activationCode),
	                array('id'=>$giftActivationCodeId));
	        if (!$retLog) $this->output(-1, '操作失败');
	        $this->output(0, '操作成功');
	    }
	
	}
	
	private function updateGiftActivityCache($giftId) {
	     $giftActivityInfo = Client_Service_GiftActivity::getGiftActivity($giftId);
	     $giftTotal = Client_Service_GiftActivityLog::getGiftLogCount(array('gift_id'=>$giftId));
	     $remainGiftCount = Client_Service_GiftActivity::getRemainGiftActivationCodeCount($giftId, $giftActivityInfo);
	     Client_Service_GiftActivity::updateBy(array('gift_number'=>$giftTotal),array('id'=>$giftId));
	     Client_Service_GiftActivity::updateGiftActivityRemainNumCache($giftId, $remainGiftCount);
	     Client_Service_GiftActivity::updataGiftActivityBaseInfoCache($giftActivityInfo, $giftId);
	}
	
	private function getGiftActivationCodes($giftId,$activationCode) {
	    return Client_Service_GiftActivityLog::getBy(
	            array('activation_code'=>$activationCode,
	                  'gift_id'=>$giftId,
	    ));
	}
	
	private function getActivationCodeParams($input,$giftId) {
	    $params = array();
	    if ($input['start_time']) $params['create_time'] = array('>=', strtotime($input['start_time']));
	    if ($input['end_time']) $params['create_time'] = array('<=', strtotime($input['end_time']));
	    if ($input['start_time'] && $input['end_time']) $params['create_time'] = array(array('>=', strtotime($input['start_time'])), array('<=', strtotime($input['end_time']))) ;
	    if ($input['uname']) {
	        $params['uname'] = array('LIKE', $input['uname']);
	    } else if ($input['nickname']) {
	        $searchUser['nickname'] = array('LIKE', $input['nickname']);
	        $userInfo = Account_Service_User::getUserInfo($searchUser);
	        $params['uname'] = $userInfo['uname'];
	    } else if($input['status']) {
	        $params['status'] = $input['status'] - 1;
	    }
	    $params['gift_id'] = $giftId;
	    return $params;
	}
	
	private function getGameInfo($game_id) {
	    return Resource_Service_GameData::getGameAllInfo($game_id);
	}

	/**
	 * 
	 * Enter description here ...
	 * return array
	 */
	private function checkInputFieldData($info, $actionType) {
	    if(!$info['title']) $this->output(-1, '活动名称不能为空.');
	    $title = html_entity_decode($info['title']);
	    if(preg_match("/<[^>]*>/", $title)) $this->output(-1, '活动名称不能包含特殊标记(< >).');
	    if(!$info['game_id']) $this->output(-1, '游戏ID不能为空.');
	    $this->checkGiftNumber($info, $actionType);
	    $ret = $this->isGiftActivityInvalid($info);
	    if($ret){
	        $this->output(-1, '同类型的活动针对同一个游戏不能在同一时段出现.');
	    }
		if(!$info['use_start_time']) $this->output(-1, '开始使用时间不能为空.');
		if(!$info['use_end_time']) $this->output(-1, '结束使用时间不能为空.');
		if(!$info['content']) $this->output(-1, '礼包内容不能为空.');
		if(!$info['method']) $this->output(-1, '礼包使用方法不能为空.');
		if(!$info['effect_start_time']) $this->output(-1, '开始生效时间不能为空.');
		if(!$info['effect_end_time']) $this->output(-1, '结束生效时间不能为空.');
		$info['use_start_time'] = strtotime($info['use_start_time']);
		$info['use_end_time'] = strtotime($info['use_end_time']);
		$info['effect_start_time'] = strtotime($info['effect_start_time']);
		$info['effect_end_time'] = strtotime($info['effect_end_time']);
		if($info['use_end_time'] <= $info['use_start_time']) $this->output(-1, '开始使用时间不能大于结束使用时间.');
		if($info['effect_end_time'] <= $info['effect_start_time']) $this->output(-1, '开始生效时间不能大于结束生效时间.');
		return $info;
	}
	
	private function checkGiftNumber($info, $actionType) {
        if($info['gift_num_type'] == Client_Service_GiftActivity::SINGLE_GIFT){
           if(!$info['gift_number']) $this->output(-1, '礼包发放数量不能为空.');
           if($actionType == 'add'){
               if(!$info['gift_code']){
                   $this->output(-1, '激活码不能为空.');
               }
           }
           if($actionType == 'edit'){
               $grabCount = $this->getGrabCount($info['id']);
               if($info['gift_number'] < $grabCount){
                   $this->output(-1, '礼包发放数量不能小于已经发放过的数量.');
               }
           }
	   }
	    
	    
	    if($actionType == 'add'){
	        if($info['gift_num_type'] == Client_Service_GiftActivity::MULTIPLE_GIFT){
	            if(!$info['importInputTxt']){
	                $this->output(-1, '礼包内容不能为空.');
	            }
	        }
	    }
	}
	
	private function getGrabCount($giftId) {
	    $grabCount = Client_Service_GiftActivityLog::getGiftLogCount(
	            array('status'=>Client_Service_GiftActivityLog::GIFT_ALREADY_SEND,
	                    'gift_id'=>$giftId));
	    
	    return $grabCount;
	}
	
	private function isGiftActivityInvalid($info) {
	    $giftActivities = Client_Service_GiftActivity::getsBy(
	              array('activity_type'=>$info['activity_type'],
	                    'game_id'=>$info['game_id']));
	    if(!$giftActivities){
	        return false;
	    }
	    $info['effect_start_time'] = strtotime($info['effect_start_time']);
	    $info['effect_end_time'] = strtotime($info['effect_end_time']);
	    foreach($giftActivities as $key=>$value){
	        if($value['id'] == $info['id']) {
	            continue;
	        }
	        if($info['effect_start_time'] <= $value['effect_start_time'] && $value['effect_start_time'] <= $info['effect_end_time']){
	            return true;
	        }
	        if($value['effect_start_time'] <= $info['effect_start_time'] && $info['effect_start_time'] <= $value['effect_end_time']){
	            return true;
	        }
	        if($info['effect_start_time'] <= $value['effect_start_time'] && $value['effect_end_time'] <= $info['effect_end_time']){
	            return true;
	        }
	        if($value['effect_start_time'] <= $info['effect_start_time'] && $info['effect_end_time'] <= $value['effect_end_time']){
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteActivationCodeAction() {
		$giftId = $this->getInput('giftId');
	    $giftActivationCodeId = $this->getInput('giftActivationCodeId');
		$result = Client_Service_GiftActivityLog::delete($giftActivationCodeId);
		
		$activationCodeTotal = Client_Service_GiftActivityLog::getGiftLogCount(array('gift_id'=>$giftId));
		$ret = $giftActivityInfo = Client_Service_GiftActivity::updateBy(
		        array('gift_number'=>$activationCodeTotal),
		        array('id'=>$giftId));
		
		$this->updateGiftActivityCache($giftId);
		if (!$result || !$ret) $this->output(-1, '操作失败');
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
	    $ret = Common::upload('img', 'hd');
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
	    $ret = Common::upload('imgFile', 'hd');
	    if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
	    exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}

    private function refreshCache($gameId){
        Resource_Service_GameExtraCache::refreshGameRewardGift($gameId);
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteListItem', $gameId);
    }
}
