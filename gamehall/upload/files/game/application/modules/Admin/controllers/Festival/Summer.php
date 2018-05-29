<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Festival_SummerController extends Admin_BaseController {
	
	public $actions = array(
	        'indexUrl' => '/Admin/Festival_Summer/index',
	        'addStep1Url' => '/Admin/Festival_Summer/addStep1',
	        'addStep2Url' => '/Admin/Festival_Summer/addStep2',
	        'addStep3Url' => '/Admin/Festival_Summer/addStep3',
	        'addStep1PostUrl' => '/Admin/Festival_Summer/addStep1Post',
	        'addStep2PostUrl' => '/Admin/Festival_Summer/addStep2Post',
	        'addPostUrl' => '/Admin/Festival_Summer/addPost',
	        'editStep1Url' => '/Admin/Festival_Summer/editStep1',
	        'editStep2Url' => '/Admin/Festival_Summer/editStep2',
	        'editStep3Url' => '/Admin/Festival_Summer/editStep3',
	        'editStep1PostUrl' => '/Admin/Festival_Summer/editStep1Post',
	        'editStep2PostUrl' => '/Admin/Festival_Summer/editStep2Post', 
	        'editPostUrl' => '/Admin/Festival_Summer/editPost',
	        'grantUrl' => '/Admin/Festival_Summer/grant', 
			'rewardUrl' => '/Admin/Festival_Summer/reward',
			'rewardExportUrl' => '/Admin/Festival_Summer/rewardExport',
			'uploadUrl' => '/Admin/Festival_Summer/upload',
	        'uploadPostUrl' => '/Admin/Festival_Summer/upload_post',
	        'uploadImgUrl' => '/Admin/Festival_Summer/uploadImg',
			'getUserDataUrl' => '/Admin/Festival_Summer/getUserData',
			'changeUserData1Url' => '/Admin/Festival_Summer/changeUserData1',
			'changeUserData2Url' => '/Admin/Festival_Summer/changeUserData2',
			'changeUserData3Url' => '/Admin/Festival_Summer/changeUserData3'
	);
	
	public $perpage = 20;
	
	// 前后端字段有一一对应关系
	static $awardItemKeyFG = array(
			"prizeName", "type",
			"probability", "leastDate", "maxQuantity",
			"lotteryNumber", "bigPoster", "smallPoster",
			"validityPeriod", "amount", "leastType",
			"integralNumber"
	);
	static $awardItemKeyBG = array(
			"name", "type",
			"probability", "interval", "count",
			"control", "img_big", "img_small",
			"end_time", "amount", "least_type",
			"amount"
	);
	
	static $awardInfoKeyFG = array(
			"prizeName", "prizeDescript", "task", "continue", "poster"
	);
	static $awardInfoKeyBG = array(
			"name", "info", "condtion", "conti_finish", "img"
	);
	
	/**
	 * 暑假任务(管理首页)
	 */
	public function indexAction(){
	    $page = $this->getInput('page');
	    $input = $this->getInput(array(
	    		Activity_Service_Cfg::NAME, 
	    		Activity_Service_Cfg::STATUS, 
	    		Activity_Service_Cfg::START_TIME, 
	    		Activity_Service_Cfg::END_TIME));
	    
	    if (!isset($input[Activity_Service_Cfg::STATUS])) {
	    	$input[Activity_Service_Cfg::STATUS] = 2; 	// 搜索全部
	    }
	    if ($page < 1) $page = 1;
	    $search = $this->getSearchData($input);
	    list($total, $items) = Activity_Service_Cfg::getList($page, $this->perpage, $search);
	    
	    $this->assign('items', $items);
	    $this->assign('total', $total);
	    $this->assign('search', $input);
	    $url = $this->actions['indexUrl'].'/?' . http_build_query($input) . '&';
	    $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 暑假任务-中奖记录-查看记录
	 */
	public function rewardAction() {
		$input = $this->getInput(array('id', 'key', 'name', 'type'));
		$page = $this->getInput('page');
		
		if ($page < 1) $page = 1;
		list($total, $items) = Activity_Service_RewardLog::getList(
				$page, $this->perpage,
				array(Activity_Service_RewardLog::ACTIVITY_ID=>$input['id'],
						Activity_Service_RewardLog::CUSTOM_KEY=>$input['key'])
		);
		$this->assign('data', $items);
		$this->assign('total', $total);
		
		foreach($input as $key => $value) {
			$this->assign($key, $value);
		}
		$url = $this->actions['rewardUrl'].'/?' . http_build_query($input) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 暑假任务-中奖记录-导出记录
	 */
	public function rewardExportAction(){
		$input = $this->getInput(array('id', 'key', 'name', 'reward', 'type'));
		
		$filename = "活动[{$input['id']}]奖品[{$input['name']}]奖项[{$input['reward']}]发放记录_".date('YmdHis', Common::getTime());
		Util_Csv::putHead($filename);
		
		if (Activity_Service_SummerHoliday::PRIZE_ENTITY != $input['type']) {
			$title = array(array('uuid', '金额', '获得时间'));
		} else {
			$title = array(array('uuid', '兑奖人', '电话号码', '收货地址', '获得时间'));
		}
		Util_Csv::putData($title);
		
		$page=1;
		while(1) {
			list($total, $items) = Activity_Service_RewardLog::getList(
					$page, $this->perpage,
					array(Activity_Service_RewardLog::ACTIVITY_ID=>$input['id'],
							Activity_Service_RewardLog::CUSTOM_KEY=>$input['key'])
			);
			if (!$items) break;
			
			$temp = array();
			foreach ($items as $key=>$item) {
				if (Activity_Service_SummerHoliday::PRIZE_ENTITY != $input['type']) {
					$temp[] = array(
						$item[Activity_Service_RewardLog::UUID],
						$item[Activity_Service_RewardLog::CUSTOM_COUNT],
						date('Y-m-d H:i:s',$item[Activity_Service_RewardLog::CREAYTE_TIME])
					);
				} else {
					$reward = json_decode($item['reward'], true);
					$entity = $reward['entity'];
					$temp[] = array(
							$item[Activity_Service_RewardLog::UUID],
							$entity['contact'],
							$entity['phone'],
							$entity['address'],
							date('Y-m-d H:i:s',$item[Activity_Service_RewardLog::CREAYTE_TIME])
					);
				}
			}
			Util_Csv::putData($temp);
			$page++;
		}
		exit;
	}
	
	
	/**
	 * 暑假任务-添加活动-添加活动-显示
	 */
	public function addStep1Action(){
	    $clientConfig = Common_Service_Version::getClientVersionConfig(Common_Service_Version::VER_1_5_8);
	    $this->assign('clientConfig', $clientConfig);
	}
	
	/**
	 * 暑假任务-修改活动-修改活动-显示
	 */
	public function editStep1Action(){
		$id = $this->getInput(Activity_Service_Cfg::ID);
		$infoData = Activity_Service_Cfg::getBy(array(Activity_Service_Cfg::ID=>$id));
		$clientConfig = Common_Service_Version::getClientVersionConfig(Common_Service_Version::VER_1_5_8);
		$this->assign('clientConfig', $clientConfig);
		$this->assign('infoData', $infoData);
	}
	
	/**
	 * 暑假任务-添加活动-添加活动-提交
	 */
	public function addStep1PostAction(){
		$input = $this->getInput(array(
				Activity_Service_Cfg::NAME,
				Activity_Service_Cfg::START_TIME,
				Activity_Service_Cfg::END_TIME,
				Activity_Service_Cfg::IMG,
				Activity_Service_Cfg::PREHEAT_TIME,
				Activity_Service_Cfg::PREHEAT_IMG,
				Activity_Service_Cfg::EXPLAIN,
				Activity_Service_Cfg::CLIENT_VER,
				Activity_Service_Cfg::STATUS));
		$data = $this->isValidStep1Data($input);
		$this->saveCfgToCache($this->getAddKey(), $data);
		$this->output(0, '继续添加任务');
	}
	
	/**
	 * 暑假任务-修改活动-修改活动-提交
	 */
	public function editStep1PostAction(){
		$input = $this->getInput(array(
				Activity_Service_Cfg::ID,
				Activity_Service_Cfg::NAME,
				Activity_Service_Cfg::START_TIME,
				Activity_Service_Cfg::END_TIME,
				Activity_Service_Cfg::IMG,
				Activity_Service_Cfg::PREHEAT_TIME,
				Activity_Service_Cfg::PREHEAT_IMG,
				Activity_Service_Cfg::EXPLAIN,
				Activity_Service_Cfg::CLIENT_VER,
				Activity_Service_Cfg::STATUS));
		$id = $input[Activity_Service_Cfg::ID];
		$data = $this->isValidStep1Data($input);
		$this->saveCfgToCache($this->getEditKey($id), $data);
		$this->output(0, '继续修改任务');
	}
	
	/**
	 * 暑假任务-添加活动-添加任务-显示
	 */
	public function addStep2Action(){
	    $infoData = $this->getCfgFromCache($this->getAddKey());
	    if(!$infoData){
	        $this->redirect($this->actions['addStep1Url']);
	        exit;
	    }
	    $this->assign(Activity_Service_Cfg::START_TIME, $infoData[Activity_Service_Cfg::START_TIME]);
	    $this->assign(Activity_Service_Cfg::END_TIME, $infoData[Activity_Service_Cfg::END_TIME]);
	}
	
	/**
	 * 暑假任务-修改活动-修改任务-显示
	 */
	public function editStep2Action(){
		$id = $this->getInput(Activity_Service_Cfg::ID);
		$infoData = $this->getCfgFromCache($this->getEditKey($id));
		if(!$infoData){
			$this->redirect($this->actions['editStep1Url']."/?".Activity_Service_Cfg::ID."=".$id);
			exit;
		}
		$this->assign(Activity_Service_Cfg::START_TIME, $infoData[Activity_Service_Cfg::START_TIME]);
		$this->assign(Activity_Service_Cfg::END_TIME, $infoData[Activity_Service_Cfg::END_TIME]);
		
		$infoData = Activity_Service_Cfg::getBy(array(Activity_Service_Cfg::ID=>$id));
		$this->assign('infoData', $infoData);
		
	}
	
	/**
	 * 暑假任务-添加活动-添加任务-提交
	 */
	public function addStep2PostAction(){
		$activity = $this->getInput(Activity_Service_Cfg::ACTIVITY);
		$data = $this->getCfgFromCache($this->getAddKey());
		if (!$data) {
			$this->output(-1, '保存配置失败,修改时间太长,缓存失效.');
		}
		$activity = $this->isValidStep2Data($activity, $data);
		$data[Activity_Service_Cfg::ACTIVITY] = $activity;
		$this->saveCfgToCache($this->getAddKey(), $data);
		$this->output(0, '继续添加奖品');
	}
	
	/**
	 * 暑假任务-修改活动-修改任务-提交
	 */
	public function editStep2PostAction(){
		$activity = $this->getInput(Activity_Service_Cfg::ACTIVITY);
		$id = $this->getInput(Activity_Service_Cfg::ID);
		$data = $this->getCfgFromCache($this->getEditKey($id));
		if (!$data) {
			$this->output(-1, '保存配置失败,修改时间太长,缓存失效.');
		}
		$activity = $this->isValidStep2Data($activity, $data);
		$data[Activity_Service_Cfg::ACTIVITY] = $activity;
		$this->saveCfgToCache($this->getEditKey($id), $data);
		$this->output(0, '继续修改奖品');
	}
	
	/**
	 * 暑假任务-添加活动-添加奖品-显示
	 */
	public function addStep3Action(){
	    $cacheData = $this->getCfgFromCache($this->getAddKey());
	    if(!$cacheData){
	        $this->redirect($this->actions['addStep1Url']);
	        exit;
	    }
	    if(!$cacheData[Activity_Service_Cfg::ACTIVITY]){
	    	$this->redirect($this->actions['addStep2Url']);
	    	exit;
	    }
	}
	
	/**
	 * 暑假任务-修改活动-修改奖品-显示
	 */
	public function editStep3Action(){
		$id = $this->getInput(Activity_Service_Cfg::ID);
		$cacheData = $this->getCfgFromCache($this->getEditKey($id));
		if(!$cacheData){
			$this->redirect($this->actions['editStep1Url']."/?".Activity_Service_Cfg::ID."=".$id);
			exit;
		}
		if(!$cacheData[Activity_Service_Cfg::ACTIVITY]){
			$this->redirect($this->actions['editStep2Url']."/?".Activity_Service_Cfg::ID."=".$id);
			exit;
		}
		$queryData = Activity_Service_Cfg::getBy(array(Activity_Service_Cfg::ID=>$id));
		$reward = $this->_getJSRewardFormat(json_decode($queryData[Activity_Service_Cfg::REWARD], true));
		$this->assign(Activity_Service_Cfg::REWARD, $reward);
		$this->assign(Activity_Service_Cfg::ID, $id);
	}
	
	/**
	 * 暑假任务-添加活动-提交
	 */
	public function addPostAction(){
		$input = $this->_checkAndformatPostInput();
	    $data = $this->getCfgFromCache($this->getAddKey());
	    if(!$data){
	    	$this->output(-1, '保存配置失败,修改时间太长,缓存失效.');
	    }
	    if(!$data[Activity_Service_Cfg::ACTIVITY]){
	    	$this->output(-1, '保存配置失败,前面的页面被再次修改了.');
	    }
	    $data[Activity_Service_Cfg::ACTIVITY_TYPE] = Activity_Service_Cfg::ACTTYPE_SUMMER;
	    $data[Activity_Service_Cfg::START_TIME] = strtotime($data[Activity_Service_Cfg::START_TIME]);
	    $data[Activity_Service_Cfg::END_TIME] = strtotime($data[Activity_Service_Cfg::END_TIME]);
	    $data[Activity_Service_Cfg::PREHEAT_TIME] = strtotime($data[Activity_Service_Cfg::PREHEAT_TIME]);
	    $data[Activity_Service_Cfg::ACTIVITY] = json_encode($data[Activity_Service_Cfg::ACTIVITY]);
	    $data[Activity_Service_Cfg::REWARD] = json_encode($input);
	    $result = Activity_Service_Cfg::insert($data);
	    if($result === false) {
	    	$this->output(-1, '保存配置失败.');
	    }
	    
	    $this->delCfgInCache($this->getAddKey());
	    $this->output(0, '操作成功');
	}
	
	/**
	 * 暑假任务-修改活动-提交
	 */
	public function editPostAction(){
		$input = $this->_checkAndformatPostInput();
		$id = $this->getInput(Activity_Service_Cfg::ID);
	    $data = $this->getCfgFromCache($this->getEditKey($id));
		if(!$data){
	    	$this->output(-1, '保存配置失败,修改时间太长,缓存失效.');
	    }
	    if(!$data[Activity_Service_Cfg::ACTIVITY]){
	    	$this->output(-1, '保存配置失败,前面的页面被再次修改了.');
	    }
	    $data[Activity_Service_Cfg::START_TIME] = strtotime($data[Activity_Service_Cfg::START_TIME]);
	    $data[Activity_Service_Cfg::END_TIME] = strtotime($data[Activity_Service_Cfg::END_TIME]);
	    $data[Activity_Service_Cfg::PREHEAT_TIME] = strtotime($data[Activity_Service_Cfg::PREHEAT_TIME]);
	    $data[Activity_Service_Cfg::ACTIVITY] = json_encode($data[Activity_Service_Cfg::ACTIVITY]);
	    $data[Activity_Service_Cfg::REWARD] = json_encode($input);
	    $result = Activity_Service_Cfg::updateByID($data, $id);
	    if(!$result) {
	    	$this->output(-1, '保存配置失败.');
	    }
	    
	    $this->delCfgInCache($this->getEditKey($id));
	    $this->output(0, '操作成功');
	}
	
	public function grantAction() {
		$id = $this->getInput('id');
	    $infoData = Activity_Service_Cfg::getBy(array(Activity_Service_Cfg::ID=>$id));
	    $rewards = json_decode($infoData[Activity_Service_Cfg::REWARD], true);
	    $rewards = $rewards['reward'];
	    $items = array();
	    foreach ($rewards as $key=>$reward) {
	    	foreach ($reward['awardItem'] as $subKey=>$rewardItem) {
	    		/*
	    		if (Activity_Service_SummerHoliday::isNoPrize($rewardItem)) { 
	    			continue;
	    		}
	    		*/
	    		$constomKey = Activity_Service_SummerHoliday::getLogCustomKey($key, $subKey);
	    		$item = array();
	    		$item['reward_name'] = $reward['name'];
	    		$item['reward_item_name'] = $rewardItem['name'];
	    		$item['type'] = $rewardItem['type'];
	    		$item['total_count'] = $rewardItem['count'];
	    		$item['key'] = $constomKey;
	    		$item['send_count'] = Activity_Service_RewardLog::count(
	    				array(Activity_Service_RewardLog::ACTIVITY_ID=>$id,
	    					Activity_Service_RewardLog::CUSTOM_KEY=>$constomKey)
	    			);
	    		array_push($items, $item);
	    	}
	    }
	    $this->assign('items', $items);
	    $this->assign('id', $id);
	}
	
	//-------------添加图片控件-----------------
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	public function upload_postAction() {
		$ret = Common::upload('img', 'festival');
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
		$ret = Common::upload('imgFile', 'festival');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
	
    //---------首页搜索条件-----------------
    private function getSearchData($input){
        $data = array();
        if($input[Activity_Service_Cfg::NAME]){
            $data[Activity_Service_Cfg::NAME] = array('like', $input[Activity_Service_Cfg::NAME]);
        }
         
        if($input[Activity_Service_Cfg::STATUS] == 0 || $input[Activity_Service_Cfg::STATUS] == 1){
            $data[Activity_Service_Cfg::STATUS] = $input[Activity_Service_Cfg::STATUS];
        }
         
        if($input[Activity_Service_Cfg::START_TIME]){
            $data[Activity_Service_Cfg::START_TIME] = array('>=', strtotime($input[Activity_Service_Cfg::START_TIME]));
        }
         
        if($input[Activity_Service_Cfg::END_TIME]){
            $data[Activity_Service_Cfg::END_TIME] = array('<=', strtotime($input[Activity_Service_Cfg::END_TIME]));
        }
        
        $data[Activity_Service_Cfg::ACTIVITY_TYPE] = Activity_Service_Cfg::ACTTYPE_SUMMER;
        
        return $data;
    }
    
    //-----------------cache存储开始------------------------
    private function getAddKey() {
    	return "summer:add:".$this->userInfo['uid'];
    }
    
    private function getEditKey($id) { // $id 为配置表ID 
    	return "summer::edit:".$id;
    }
    
	private function saveCfgToCache($key, $data) {
		$cache = $this->getCache();
		$cacheData = array(
			'uid' => $this->userInfo['uid'],
			'data' => $data
		);
		$cache->set($key, json_encode($cacheData), 7200);
	}
	
	private function getCfgFromCache($key) {
		$cache = $this->getCache();
		$cacheData = json_decode($cache->get($key), true);
		if ($cacheData && $cacheData['uid'] == $this->userInfo['uid']) {
			return $cacheData['data'];
		}
		return false;
	}
	
	private function delCfgInCache($key) {
		$cache = $this->getCache();
		$cache->delete($key);
	}
	
	private function getCache(){
	    $cacheObj = Cache_Factory::getCache(Cache_Factory::ID_REMOTE_REDIS);
	    return $cacheObj;
	}
	
	//-----------------页面输入数据校验------------------------
	private function isValidStep1Data($input) {
	    if (!$input[Activity_Service_Cfg::NAME]) $this->output(-1, '活动名称不能为空.');
	    if (mb_strlen($input[Activity_Service_Cfg::NAME], 'utf-8') > 20) $this->output(-1, '活动名称太长（大于20个中文字符）');
	    if (!$input[Activity_Service_Cfg::PREHEAT_TIME]) $this->output(-1, '预热时间不能为空.');
	    if (!$input[Activity_Service_Cfg::START_TIME]) $this->output(-1, '活动开始时间不能为空.');
	    if (!$input[Activity_Service_Cfg::END_TIME]) $this->output(-1, '活动结束时间不能为空.');
	    if ($input[Activity_Service_Cfg::END_TIME] < $input[Activity_Service_Cfg::START_TIME]) 
	    	$this->output(-1, '活动开始时间不能大于或等于结束时间.');
	    if ($input[Activity_Service_Cfg::PREHEAT_TIME] > $input[Activity_Service_Cfg::START_TIME]) 
	    	$this->output(-1, '预热时间不能大于开始时间.');
	    
        if ($input[Activity_Service_Cfg::STATUS]) {
            $params[Activity_Service_Cfg::STATUS] = 1;
            if ($input[Activity_Service_Cfg::ID]) {
                $params[Activity_Service_Cfg::ID] = array('!=', $input[Activity_Service_Cfg::ID]);
            }
            $preheatTime = strtotime($input[Activity_Service_Cfg::PREHEAT_TIME]);
            $endTime = strtotime($input[Activity_Service_Cfg::END_TIME]);
            $params[Activity_Service_Cfg::PREHEAT_TIME] = array('<=', $endTime);
            $params[Activity_Service_Cfg::END_TIME] = array('>=', $preheatTime);
            $params[Activity_Service_Cfg::ACTIVITY_TYPE] = array('=', Activity_Service_Cfg::ACTTYPE_SUMMER);
            $items = Activity_Service_Cfg::getsBy($params);
            if ($items) $this->output(-1, '预热时间到结束时间这个区间与其他活动有重叠。');
        }
	    
	    if (!$input[Activity_Service_Cfg::IMG]) $this->output(-1, '活动广告图不能为空.');
	    if (!$input[Activity_Service_Cfg::PREHEAT_IMG]) $this->output(-1, '活动广告图不能为空.');
	    if (!$input[Activity_Service_Cfg::EXPLAIN]) $this->output(-1, '活动说明不能为空.');
	    if (!$input[Activity_Service_Cfg::CLIENT_VER]) $this->output(-1, '游戏大厅参与最低版本不能为空.');
	    return $input;
	}
	
	private function isValidStep2Data($input, $data) {
		if (!$input['name']) $this->output(-1,  "任务名称不能为空.");
		if (mb_strlen($input['name'], 'utf-8') > 15) $this->output(-1, '任务名称太长(>15).' );
		if (!$input['img']) $this->output(-1, "图片不能为空.");
		$startTime = strtotime(date('Y-m-d', strtotime($data[Activity_Service_Cfg::START_TIME])));
		$endTime = strtotime(date('Y-m-d', strtotime($data[Activity_Service_Cfg::END_TIME])));
		for ($cur = $startTime; $cur <= $endTime; $cur+= 86400) {
			/*
			if (($input['day_task'][$cur]['1']['type'] == Activity_Service_SummerHoliday::DOWNLOAD_TASK_TYPE)
			  && ($input['day_task'][$cur]['2']['type'] == Activity_Service_SummerHoliday::DOWNLOAD_TASK_TYPE))
		    {
		    	$this->output(-1, date('Y-m-d', $cur).":不能两个任务都配置成下载.");
		    }
		    */
			for ($i = 1; $i <= 2; $i++) {
				$taskItem = $input['day_task'][$cur][$i];
				$taskName = date('Y-m-d', $cur)."任务".$i.":";
				if (!$taskItem['name']) $this->output(-1, $taskName."名字不能为空.");
				if (mb_strlen($taskItem['name'], 'utf-8') > 18) $this->output(-1, $taskName.'名字太长(>18).' );
				$taskItem['type'] = intval($taskItem['type']);
				if ($taskItem['type'] < 1 || $taskItem['type'] > 2) $this->output(-1, $taskName."类型值不正确.");
				$taskItem['award_type'] = intval($taskItem['award_type']);
				if ($taskItem['award_type'] < 1 || $taskItem['award_type'] > 2)
					$this->output(-1, $taskName."奖励类型不这个正确.");
				$taskItem['award_count'] = intval($taskItem['award_count']);
				if ($taskItem['award_count'] < 1) $this->output(-1, $taskName."面额不能小于0.");
				if ($taskItem['award_type'] == 1) {
					$taskItem['start_time'] = intval($taskItem['start_time']);
					if ($taskItem['start_time'] < 1) $this->output(-1, $taskName."有效期开始时间不能小于1.");
					$taskItem['end_time'] = intval($taskItem['end_time']);
					if ($taskItem['end_time'] < $taskItem['start_time']) $this->output(-1, $taskName."有效期开始时间不能小于1.");
				}
				$taskItem['game_id'] = intval($taskItem['game_id']);
				$gameInfo = Resource_Service_Games::getBy(array('id' => $taskItem['game_id']));
				if (!$gameInfo) {
					$this->output(-1, $taskName."游戏ID({$taskItem['game_id']})不存在.");
				}
				if ($gameInfo['cooperate'] != $taskItem['type']) {
					if ($taskItem['type'] == Resource_Service_Games::COMBINE_GAME) {
						$this->output(-1, $taskName."游戏({$taskItem['game_id']})不是联运网游");
					} /*else {
						$this->output(-1, $taskName."游戏({$taskItem['game_id']})是联运网游");
					} */
				}
				$input['day_task'][$cur][$i] = $taskItem;
			}
			
			if ($input['day_task'][$cur][1]['game_id'] == $input['day_task'][$cur][2]['game_id']) {
				$this->output(-1, date('Y-m-d', $cur).":不能两个任务都配置相同的游戏.");
			}
		}
		return $input;
	}
	
	private function _getJSRewardFormat($rewardConfig) {
		$prizeConfig = array();
		$prize = array();
		for ($i = 0; $i < 3; $i++) {
			$prizeConfig[] = $this->_formatArray($rewardConfig[Activity_Service_Cfg::REWARD][$i+1], self::$awardInfoKeyBG, self::$awardInfoKeyFG);
			$awardItems = $rewardConfig[Activity_Service_Cfg::REWARD][$i+1]["awardItem"];
			$cfgItem = array();
			foreach ($awardItems as $awardItem) {
				$cfgItem[] = $this->_formatArray($awardItem, self::$awardItemKeyBG, self::$awardItemKeyFG);
			}
			$prize[$i] = $cfgItem;
		}
		return array("descript"=>$rewardConfig["info"], "name"=>$rewardConfig["name"], "prize"=>$prize, "prizeConfig"=>$prizeConfig);
	}
	
	private function _checkAndformatPostInput() {
		$rewardConfig = array();
	
		$name = $this->getInput('name');
		if (!$name) $this->output(-1, '活动名称不能为空.');
		$rewardConfig["name"] = $name;
	
		$descript = $this->getInput('descript');
		if (!$descript) $this->output(-1, '活动配置不能为空.');
		$rewardConfig["info"] = $descript;
	
		$prizeConfig = $this->getInput('prizeConfig');
		$prize = $this->getInput('prize');
		for ($i = 0; $i < 3; $i++) {
			$rewardConfig["reward"][$i+1] = $this->_checkAndFormatAwardInfo($prizeConfig[$i], "抽奖".($i+1).":");
	
			$config = $prize[$i];
			if (count($config) < 1) $this->output(-1, "抽奖".($i+1).":"."必须配置奖品.");
	
			$j = 0;
			$probability = 0;
			$lowestAward = 0;
			foreach ($config as $configItem) {
				$j++;
				$awardItem = $this->_checkAndFormatAwardItem($configItem, "抽奖".($i+1). "奖品" .$j.":");
				$rewardConfig["reward"][$i+1]["awardItem"][$j] = $awardItem;
				
				$probability += $awardItem['probability'];
				if (Activity_Service_SummerHoliday::PRIZE_MIN == $awardItem['type']) {
					$lowestAward++;
				}
			}
			if ($probability > Activity_Service_SummerHoliday::PROBABILITY_COUNT) {
				$this->output(-1, "抽奖".($i+1).":"."奖品的概率之和不能大于".Activity_Service_SummerHoliday::PROBABILITY_COUNT.".");
			}
			if ($lowestAward < 1) {
				$this->output(-1, "抽奖".($i+1).":必须配置一个最低奖项.");
			}
			if ($lowestAward > 1) {
				$this->output(-1, "抽奖".($i+1).":最低奖项不能配置多个.");
			}
		}
	
		return $rewardConfig;
	}
	
	private function _formatArray($in, $keysIn, $keysOut) {
		$out = array();
		for($i = 0; $i < count($keysIn); $i++) {
			if (array_key_exists($keysIn[$i], $in)) {
				$out[$keysOut[$i]] = $in[$keysIn[$i]];
			}
		}
		return $out;
	}
	
	private function _checkAndFormatAwardInfo($config, $preInfo) {
		$reward = $this->_formatArray($config, self::$awardInfoKeyFG, self::$awardInfoKeyBG);
		if (!$reward['name']) $this->output(-1, $preInfo.'奖项名称不能为空.');
		if (mb_strlen($reward['name'], 'utf-8') > 9) $this->output(-1, $preInfo.'奖项名称太长（中文字符数量多于9个时，显示会换行）.' );
		if (!$reward['info']) $this->output(-1, $preInfo.'活动说明不能为空.');
		if (!$reward['img']) $this->output(-1, $preInfo.'图片不能为空.');
		$reward['condtion'] = intval($reward['condtion']);
		if ($reward['condtion'] < 1 || $reward['condtion'] > 2) {
			$this->output(-1, $preInfo.'参与条件不合法.');
		}
		if ($reward['condtion'] == Activity_Service_SummerHoliday::REWARD_CONDITION_CONTINUE_FINISHED) {
			$reward['conti_finish'] = intval($reward['conti_finish']);
			if ($reward['conti_finish'] <= 0) {
				$this->output(-1, $preInfo.'参与条件-连续完成天数不能小于等于0.');
			}
		}
		return $reward;
	}
	
	private function _checkAndFormatAwardItem($config, $preInfo) {
		$awardItem = $this->_formatArray($config, self::$awardItemKeyFG, self::$awardItemKeyBG);
		if (!$awardItem['name']) $this->output(-1, $preInfo.'奖品名称不能为空.');
		if (!$awardItem['img_big']) {
			$this->output(-1, $preInfo.'大图不能为空.');
		}
		if (!$awardItem['img_small']) $this->output(-1, $preInfo.'小图不能为空.');
		$awardItem['type'] = intval($awardItem['type']);
		if ($awardItem['type'] < 0 || $awardItem['type'] > 3) {
			$this->output(-1, $preInfo.'奖品类型不正确.');
		}
	
		if (Activity_Service_SummerHoliday::PRIZE_MIN == $awardItem['type']) {
			if ($awardItem['least_type'] < 0 || $awardItem['least_type'] > 1) {
				$this->output(-1, $preInfo.'最低奖项类型不正确.');
			}
			if (1 == $awardItem['least_type']){ // 积分
				$awardItem['amount'] = intval($awardItem['amount']);
				if (!$awardItem['amount']) $this->output(-1, $preInfo.'积分数量不能为空.');
			}
			$awardItem['probability'] = 0;
			$awardItem['interval'] = 0;
			$awardItem['control'] = 0;
			$awardItem['start_time'] = 0;
			$awardItem['end_time'] = 0;
			return $awardItem;
		}
	
		$awardItem['probability'] = intval($awardItem['probability']);
		if ($awardItem['probability'] < 0) $this->output(-1, $preInfo.'奖品概率不能小于0.');
	
		$awardItem['interval'] = intval($awardItem['interval']);
		if ($awardItem['interval'] < 0) $this->output(-1, $preInfo.'奖品最小时间间隔不能小于0.');
	
		$awardItem['count'] = intval($awardItem['count']);
		if ($awardItem['count'] < 0) $this->output(-1, $preInfo.'奖品抽奖次数不能小于0.');
	
		$awardItem['control'] = intval($awardItem['control']);
		if ($awardItem['control'] < 0) $this->output(-1, $preInfo.'抽奖次数达到指定次数不能小于0.');
	
		if (Activity_Service_SummerHoliday::PRIZE_TICKETS == $awardItem['type'] || Activity_Service_SummerHoliday::PRIZE_POINTS == $awardItem['type']) {
			$awardItem['amount'] = intval($awardItem['amount']);
			if (!$awardItem['amount']) $this->output(-1, $preInfo.'奖品数量不能为空.');
		}
		if (Activity_Service_SummerHoliday::PRIZE_TICKETS == $awardItem['type']) {
			$awardItem['end_time'] = intval($awardItem['end_time']);
			if ($awardItem['end_time'] < 1) $this->output(-1, $preInfo.'有效期不能小于1天.');
			$awardItem['start_time'] = 1;
		}
		if (Activity_Service_SummerHoliday::PRIZE_ENTITY == $awardItem['type']) {
			$awardItem['amount'] = 1;
		}
	
		return $awardItem;
	}
	
	// --------------- 测试工具-------------
	public function getUserDataAction() {
		$uuid = $this->getInput('uuid');
		$this->assign('uuid', $uuid);
	}
	public function changeUserData1Action() {
		$uuid = $this->getInput('uuid');
		$day = $this->getInput('day');
		$tid = $this->getInput('tid');
		if (!$uuid || !$day || !tid) {
			$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
		}
		$value = $this->getInput('value');
		if (!$value) {
			$value = 1;
		} else {
			$value = 0;
		}
		
		self::toolLog(__CLASS__, array('uuid'=> $uuid, 'day'=>$day, 'tid'=>$tid, 'value'=>$value));

		$cfg = Activity_Service_SummerHoliday::getEffectionActivity();
		$queryParams[Activity_Service_UserData::UUID] = $uuid;
		$queryParams[Activity_Service_UserData::ACTIVITY_ID] = $cfg['id'];
		$userInfo = Activity_Service_UserData::getBy($queryParams);
		if ($userInfo) {
			$userData = json_decode($userInfo[Activity_Service_UserData::DATA], true);
			self::toolLog(__CLASS__, array('CHG1_DATA_B'=> $userData));
			$userData['day_task'][$day][$tid] = $value;
			self::toolLog(__CLASS__, array('CHG1_DATA_A'=> $userData));
			$data[Activity_Service_UserData::DATA] = json_encode($userData);
			Activity_Service_UserData::updateByID($data, $userInfo['id']);
		}
		else {
			$userData['day_task'][$day][$tid] = $value;
			if ($tid == 1) {
				$userData['day_task'][$day][2] = 0;
			} else {
				$userData['day_task'][$day][1] = 0;
			}
			self::toolLog(__CLASS__, array('CHG1_N_DATA'=> $userData));
			$data[Activity_Service_UserData::ACTIVITY_ID] = $cfg['id'];
			$data[Activity_Service_UserData::UUID] = $uuid;
			$data[Activity_Service_UserData::DATA] = json_encode($userData);
			Activity_Service_UserData::insert($data);
		}
		
		self::toolLog(__CLASS__, "OK");
		$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
	}
	
	public function changeUserData2Action() {
		$uuid = $this->getInput('uuid');
		$tid = $this->getInput('tid');
		if (!$uuid || !tid) {
			$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
		}
		
		self::toolLog(__CLASS__, array('uuid'=> $uuid, 'tid'=>$tid));
	
		$cfg = Activity_Service_SummerHoliday::getEffectionActivity();
		$queryParams[Activity_Service_UserData::UUID] = $uuid;
		$queryParams[Activity_Service_UserData::ACTIVITY_ID] = $cfg['id'];
		$userInfo = Activity_Service_UserData::getBy($queryParams);
		if ($userInfo) {
			$userData = json_decode($userInfo[Activity_Service_UserData::DATA], true);
			self::toolLog(__CLASS__, array('CHG2_DATA_B'=> $userData));
			$userData['reward'][$tid] = ($userData['reward'][$tid] + 1) % 3;
			self::toolLog(__CLASS__, array('CHG2_DATA_A'=> $userData));
			$data[Activity_Service_UserData::DATA] = json_encode($userData);
			Activity_Service_UserData::updateByID($data, $userInfo['id']);
		}
		else {
			$userData['reward'][$tid] = 1;
			self::toolLog(__CLASS__, array('CHG1_N_DATA'=> $userData));
			$data[Activity_Service_UserData::ACTIVITY_ID] = $cfg['id'];
			$data[Activity_Service_UserData::UUID] = $uuid;
			$data[Activity_Service_UserData::DATA] = json_encode($userData);
			Activity_Service_UserData::insert($data);
		}
	
		self::toolLog(__CLASS__, "OK");
		$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
	}
	
	public function changeUserData3Action() {
		$uuid = $this->getInput('uuid');
		if (!$uuid) {
			$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
		}
	
		self::toolLog(__CLASS__, array('uuid'=> $uuid));
	
		$cfg = Activity_Service_SummerHoliday::getEffectionActivity();
		$queryParams[Activity_Service_UserData::UUID] = $uuid;
		$queryParams[Activity_Service_UserData::ACTIVITY_ID] = $cfg['id'];
		$userInfo = Activity_Service_UserData::getBy($queryParams);
		Activity_Service_UserData::deleteByID($userInfo['id']);
		self::toolLog(__CLASS__, "OK");
		$this->redirect($this->actions['getUserDataUrl']."/?uuid=".$uuid);
	}
	
	public static function toolLog($class, $msg) {
		Util_Log::info($class, "summer_tool.log" , $msg);
	}
}