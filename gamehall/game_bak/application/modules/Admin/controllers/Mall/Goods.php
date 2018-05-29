<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Mall_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Mall_Goods/index',
		'addGoodUrl' => '/Admin/Mall_Goods/addGood',
		'addGoodPostUrl' => '/Admin/Mall_Goods/addGoodPost',
		'editGoodUrl' => '/Admin/Mall_Goods/editGood',
		'editGoodPostUrl' => '/Admin/Mall_Goods/editGoodPost',
		'exchangeDetailUrl' => '/Admin/Mall_Goods/exchangeDetail',
		'goodExportUrl' => '/Admin/Mall_Goods/goodExport',
		'addSendPointUrl' => '/Admin/Mall_Goods/addSendPoint',
		'addSendPointPostUrl' => '/Admin/Mall_Goods/addSendPointPost',
		'importPostUrl' => '/Admin/Mall_Goods/import',
		'pointDescIndexUrl' => '/Admin/Mall_Goods/pointIndex',
		'presendIndexUrl' => '/Admin/Mall_Goods/presendIndex',
		'presendDetailUrl' => '/Admin/Mall_Goods/presendDetail',
		'presendDetailExportUrl' => '/Admin/Mall_Goods/presendDetailExport',
		'batchUpdateUrl'=>'/Admin/Mall_Goods/batchUpdate',
		'sendUrl' => '/Admin/Mall_Goods/send',
		'editAddressPostUrl' => '/Admin/Mall_Goods/editAddressPost',
		'addressUrl' => '/Admin/Mall_Goods/address',
		'pointDescPostUrl' => '/Admin/Mall_Goods/pointDescPost',
		'uploadUrl' => '/Admin/Mall_Goods/upload',
		'uploadPostUrl' => '/Admin/Mall_Goods/upload_post',
		'uploadImgUrl' => '/Admin/Mall_Goods/uploadImg',
		'pointShopIndexUrl' => '/Admin/Mall_Goods/index',
		'pointCJIndexUrl' => '/Admin/Point_Prize/index',
	);
	
	public $perpage = 20;
	public $type = array(
			Mall_Service_Goods::MALL_GOODS_TYPE_ACOUPON => 'A券',
			Mall_Service_Goods::MALL_GOODS_TYPE_ENTITY => '实体',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title', 'status', 'type', 'start_time','end_time'));
		$params = $hd_params = $search = $game_ids = $ids = array();
		
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['type']) $params['type'] = $s['type'];
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('<=',strtotime($s['end_time']));
				
		list($total, $goods) = Mall_Service_Goods::getList($page, $this->perpage,$params);
		
		$this->assign('s', $s);
		$this->assign('type', $this->type);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$url = $this->actions['indexUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editGoodAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Goods::get(intval($id));
		$this->assign('info', $info);
		$this->assign('type', $this->type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addGoodAction() {
		$this->assign('type', $this->type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addGoodPostAction() {
		$info = $this->getPost(array('type', 'total_num', 'preson_limit_num', 'remaind_num', 'consume_point', 'title', 'img', 'icon', 'status', 'start_time', 'end_time', 'create_time', 'effect_time'));
		$info = $this->_cookData($info);
		$info['create_time'] = Common::getTime();
		$info['remaind_num'] = $info['total_num'];
		$result = Mall_Service_Goods::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editGoodPostAction() {
		$info = $this->getPost(array('id', 'type', 'total_num', 'preson_limit_num', 'consume_point', 'title', 'img', 'icon', 'status', 'start_time', 'end_time', 'effect_time'));
		$info = $this->_cookData($info);
		$logs = Mall_Service_ExchangeLog::getsBy(array('mall_id'=>$info['id']));
		$nums = 0;
		foreach($logs as $key=>$value){
			$nums += $value['exchange_num'];
		}
		
		if($info['total_num'] < $nums) $this->output(-1, '商品总数不能少于已兑换总数.'); 
		
		//开始事务
		$trans = Common_Service_Base::beginTransaction();
		try {
			$info['remaind_num'] = $info['total_num'] - $nums;
	
			$ret = Mall_Service_Goods::update($info, intval($info['id']));
			if (!$ret) throw new Exception('updateGood fail.', -201);
		
			//事务提交
			if($trans)  Common_Service_Base::commit();
			$this->output(0, '操作成功.');
		} catch (Exception $e) {
			Common_Service_Base::rollBack();
			$this->output(-1, '操作失败');
		}		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function exchangeDetailAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('mall_id','start_time','end_time','uname','status','nickname'));
		$params = $search = $uuids = array();
	
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['mall_id']) $params['mall_id'] = $s['mall_id'];
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('<=',strtotime($s['end_time']));
		
		if($s['uname'] || $s['nickname']){
			if ($s['uname']) $search['uname']  = array('LIKE',trim($s['uname']));
			if ($s['nickname']) $search['nickname']  = array('LIKE',trim($s['nickname']));
			$users = Account_Service_User::getUsers($search);
			$users = Common::resetKey($users, 'uuid');
			$uuids = array_unique(array_keys($users));
			if($uuids){
				$params['uuid'] = array('IN',$uuids);
			} else {
				$params['uuid'] = 0;
			}
		}
		
		list($total, $logs) = Mall_Service_ExchangeLog::getList($page, $this->perpage,$params);
		foreach($logs as $k=>$v){
			$usrInfo  = Account_Service_User::getUserInfo(array('uuid'=>$v['uuid']));
			$info = Mall_Service_Goods::getBy(array('id'=>$v['mall_id']));
			$nicknames[$v['uuid']] = $usrInfo['nickname'];
			$types[$v['id']] = $info['type'];
		}
	
		$this->assign('s', $s);
		$this->assign('logs', $logs);
		$this->assign('nicknames', $nicknames);
		$this->assign('types', $types);
		$this->assign('total', $total);
		$this->assign('page', $page);
		$url = $this->actions['exchangeDetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　导出积分商城兑换记录
	 * Get phrase list
	 */
	public function goodExportAction() {
	$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('mall_id','start_time','end_time','uname','status','nickname'));
		$params = $search = $uuids = array();
	
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['mall_id']) $params['mall_id'] = $s['mall_id'];
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('<=',strtotime($s['end_time']));
		
		if($s['uname'] || $s['nickname']){
			if ($s['uname']) $search['uname']  = array('LIKE',trim($s['uname']));
			if ($s['nickname']) $search['nickname']  = array('LIKE',trim($s['nickname']));
			$users = Account_Service_User::getUsers($search);
			$users = Common::resetKey($users, 'uuid');
			$uuids = array_unique(array_keys($users));
			if($uuids){
				$params['uuid'] = array('IN',$uuids);
			} else {
				$params['uuid'] = 0;
			}
		}
		self::getExportData($page, $params);
	}
	
	
	/**
	 *
	 * @param array $data
	 */
	private  function getExportData($page, $params) {
		//excel-head
		$filename = "兑换记录_".date('YmdHis', Common::getTime());
		Util_Csv::putHead($filename);
		$title = array(array('账号','昵称','兑换时间','兑换数量', '状态', '发放时间'));
		Util_Csv::putData($title);
		//循环分页查询输出
	
		while(1){
			list(, $logs) = Mall_Service_ExchangeLog::getList($page, $this->perpage,$params);
			if (!$logs) break;
			$tmp = array();
			foreach ($logs as $key=>$value) {
				$usrInfo  = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
				$exchange_time = $value['exchange_time'] ? date('Y-m-d H:i:s', $value['exchange_time']) : '';
				$send_time = $value['send_time'] ? date('Y-m-d H:i:s', $value['send_time']) : '';
				if($value['status'] == 0){
					$status = '未发放';
				} else if($value['status'] == 1){
					$status = '已发放';
				}
				
				$tmp[] = array(
						$value['uname'],
						$usrInfo['nickname'],
						$exchange_time,
						$value['exchange_num'],
						$status,
						$send_time
				);
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='send'){           //发放
			$ret = Mall_Service_ExchangeLog::updateLogStatus($info['ids'], 1);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 单个发放
	 */
	public function sendAction() {
		$id = $this->getInput('id');
		$send_time = Common::getTime();
		$ret = Mall_Service_ExchangeLog::update(array('status'=>1,'send_time'=>$send_time), $id);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addressAction() {
		$id = $this->getInput('id');
		$page = $this->getInput('page');
		$info = Mall_Service_ExchangeLog::get(intval($id));
		$this->assign('info', $info);
		$this->assign('page', $page);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editAddressPostAction() {
		$info = $this->getPost(array('id', 'receiver', 'receiverphone', 'address'));
		$info = $this->_cookAddressData($info);
		$ret = Mall_Service_ExchangeLog::update($info, intval($info['id']));
		//更新个人信息表
		$logInfo = Mall_Service_ExchangeLog::get(intval($info['id']));
		$userData['receiver'] = $info['receiver'];
		$userData['receiverphone'] = $info['receiverphone'];
		$userData['address'] = $info['address'];
		$params['uuid'] = $logInfo['uuid'];
		Account_Service_User::updateUserInfo($userData, $params);
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addsendAction() {
	}
	
	/**
	 * 导入
	 */
	public function importAction() {
		$filename = $_FILES['pointReciverCsv']['tmp_name'];
		$tpfilename = $_FILES['pointReciverCsv']['name'];
		if(!empty($filename)) {
echo <<<SCRIPT
			<script>
					window.parent.document.getElementById("sendInputTxt").value = "";
					function _addcsvRow(d) {
						if(window.parent.document.getElementById("sendInputTxt").value != "") {
							window.parent.document.getElementById("sendInputTxt").value += ",";
						}
						window.parent.document.getElementById("sendInputTxt").value += d;
					}
			</script>
SCRIPT;
          self::_readFileData($filename, $tpfilename);
		}
	}
	
	/**
	 * 写文件
	 */
	private static function _readFileData($filename, $tpfilename) {
		$ext = strtolower(end(explode(".", $tpfilename)));
		if($ext != "csv"){
			echo '<script>alert("导入文件格式非法，必须是csv.");</script>';
		} else {
			ob_flush();  flush();
			$file    = $filename;
			$handle  = fopen($file,'r');
			if(!$handle) exit;
			$count = 0;   $dataList = array();
			$title = array('账号');
			while(($row = fgetcsv($handle,1000,",")) !== false) {
				foreach($row as $k => &$v) {
					$v = mb_convert_encoding($v,'utf-8','gbk');
				}
				$dataList[] = $row;
			}
			if(count($dataList) <= 1) exit;
			foreach($dataList as $data) {
				if(empty($data[0])) continue;
				echo '<script>_addcsvRow("'.$data[0].'");</script>';
				ob_flush();  flush();
				$count ++;
			}
			echo '<script>alert("已读取：'.$count.' 条");</script>';
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addSendPointAction() {
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addSendPointPostAction() {
		$info = $this->getPost(array('send_num', 'reason', 'userType', 'is_send_msg', 'operat_account', 'send_time', 'sendInput'));
		$info = $this->_cookPresendData($info);
		$info['send_time'] = Common::getTime();
		$info['operat_account'] = $this->userInfo['username'];
		$unames = explode(',',html_entity_decode($info['sendInput']));
		$result = $this->addPoint($info, $unames);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function presendIndexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('start_time','end_time'));
		$params  = $search = array();
		
		if ($s['start_time']) $params['send_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['send_time'][1] = array('<=', strtotime($s['end_time']));
		
		list($total, $presends) = Mall_Service_Presend::getList($page, $this->perpage,$params);
				
		$this->assign('s', $s);
		$this->assign('presends', $presends);
		$this->assign('total', $total);
		$url = $this->actions['presendIndexUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function presendDetailAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('uname','nickname','presendId'));
		$params  = array();
	
		if ($s['presendId']) $params['presend_id']  =trim($s['presendId']);
		if ($s['uname']) $params['uname']  = array('LIKE',trim($s['uname']));
		if ($s['nickname']) {
			$search['nickname']  = array('LIKE',trim($s['nickname']));
			$users = Account_Service_User::getUsers($search);
			$users = Common::resetKey($users, 'uuid');
			$uuids = array_unique(array_keys($users));
			if($uuids){
				$params['uuid'] = array('IN',$uuids);
			} else {
				$params['uuid'] = 0;
			}
		}
		
	    $info = Mall_Service_Presend::get($s['presendId']);
		list($total, $presendDetails) = Mall_Service_PresendLog::getList($page, $this->perpage, $params);
		$avatars = self::getAvatars($presendDetails);
		
		$this->assign('s', $s);
		$this->assign('info', $info);
		$this->assign('avatars', $avatars);
		$this->assign('presendDetails', $presendDetails);
		$this->assign('total', $total);
		$url = $this->actions['presendDetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　发放明细导出
	 */
	public function presendDetailExportAction() {
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('uname','nickname','presendId'));
		$params  = array();
	
		if ($s['presendId']) $params['presend_id']  =trim($s['presendId']);
		if ($s['uname']) $params['uname']  = array('LIKE',trim($s['uname']));
		if ($s['nickname']) {
			$search['nickname']  = array('LIKE',trim($s['nickname']));
			$users = Account_Service_User::getUsers($search);
			$users = Common::resetKey($users, 'uuid');
			$uuids = array_unique(array_keys($users));
			if($uuids){
				$params['uuid'] = array('IN',$uuids);
			} else {
				$params['uuid'] = 0;
			}
		}
		self::getpresendDetailExportData($page, $params, $info);
	}
	
	/**
	 *
	 * @param array $data
	 */
	private function getpresendDetailExportData($page, $params, $info) {
		//excel-head
		$filename = "兑换记录_".date('YmdHis', Common::getTime());
		Util_Csv::putHead($filename);
		$title = array(array('账号','uuid','昵称'));
		Util_Csv::putData($title);
		//循环分页查询输出
	
		while(1){
			list(, $presendDetails) = Mall_Service_PresendLog::getList($page, $this->perpage, $params);
			if (!$presendDetails) break;
			$tmp = array();
			foreach ($presendDetails as $key=>$value) {
				$usrInfo  = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
				$tmp[] = array(
						$value['uname'],
						$value['uuid'],
						$usrInfo['nickname']
				);
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 *
	 * @param array $data
	 * @return boolean
	 */
	private static function getAvatars($presendDetails) {
	$avatars = array();
	   foreach($presendDetails as $key=>$value){
			$usrInfo = self::getUserData(array('uuid'=>$value['uuid']));
			$avatars[$value['uuid']]['avatar'] = $usrInfo['avatar'];
			$avatars[$value['uuid']]['nickname'] = $usrInfo['nickname'];
		}
		return $avatars;
	}
	
	/**
	 *
	 * @param array $data
	 * @param array $unames
	 * @return boolean
	 */
	public static function addPoint($data, $unames) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = Common_Service_Base::beginTransaction();
		try {
	
			//添加操作记录
			$presendId = self::saveOperateLog($data);
			if (!$presendId) throw new Exception("saveOperateLog fail.", -202);
				
			//添加发放明细
			$ret = self::savePresentDetails($presendId, $unames, $data['userType']);
			if (!$ret) throw new Exception('savePresentDetails fail.', -205);
	
			//更新用户总积分
			$ret = self::updateUsersPoint($data, $unames, $data['userType']);
			if (!$ret) throw new Exception('updateUsersPoint fail.', -207);
			
			if($data['is_send_msg']){
			  //添加人工发放消息
			  $ret = self::saveOperatePointMsg($data['send_num'], $unames, $data['userType']);
			  if (!$ret) throw new Exception('saveOperatePointMsg fail.', -209);
			}
	
			//事务提交
			if($trans)  Common_Service_Base::commit();
			return true;
		} catch (Exception $e) {
			Common_Service_Base::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * @param array $data
	 * @return boolean
	 */
	private  static function saveOperateLog($data) {
		return Mall_Service_Presend::add($data);
	}

	/**
	 *
	 * @param int $presend_id
	 * @param array $unames
	 * @return array $presendLog
	 */
	private  static function savePresentDetails($presendId, $unames, $userType) {
		$presendLog = array();
		foreach($unames as $key=>$value){
			if (!$value) {
				continue;
			}
			
			if($userType == 1){  //导入是手机号
				$accountApi = new Api_Gionee_Account();
				$uuid = $accountApi->getUuidByName($value);
				$uname = $value;
			}  else {            //导入是uuid
				$uuid = $value;
				$usrInfo  = self::getUserData(array('uuid'=>$uuid));
				$uname = $usrInfo['uname'];
			}
		    
			$presendLog[] = array(
					'id'=>'',
					'presend_id'=>$presendId,
					'uuid'=>$uuid,
					'uname' =>$uname,
					'send_time'=>Common::getTime()
				);
		}
		return Mall_Service_PresendLog::mutiInsert($presendLog);
	}
	
	/**
	 *
	 * @param string $uname
	 * @return array
	 */
	private  static function getUserData($parmes) {
		return Account_Service_User::getUserInfo($parmes);
	}
	
	/**
	 *
	 * @param array $data
	 * @param array $unames
	 * @return boolean
	 */
	private  static function updateUsersPoint($data, $unames, $userType) {
		if (!is_array($unames)) return false;
		foreach($unames as $key=>$value){
			if (!$value) {
				continue;
			}
			if($userType == 1){  //导入是手机号
				$accountApi = new Api_Gionee_Account();
				$uuid = $accountApi->getUuidByName($value);
			}  else {            //导入是uuid
				$uuid = $value;
			}
			
			$usrGainPoint = array();
			$usrGainPoint = array(
					'id'=>'',
					'uuid'=>$uuid,
					'gain_type'=>'4',
					'gain_sub_type'=> '',
					'days'=>'',
					'points'=>$data['send_num'],
					'create_time'=>$data['send_time'],
					'update_time'=>$data['send_time'],
					'status'=>1,
			);
			$ret = Point_Service_User::gainPresentUserPoints($usrGainPoint, $uuid, $data['send_num']);
		}
		return true;
	}
	

	/**
	 *
	 * @param array $data
	 * @param array $unames
	 * @return array $presendLog
	 */
	private  static function saveOperatePointMsg($send_num, $unames, $userType) {
		$msg = array();
		foreach($unames as $key=>$value){
			if (!$value) {
				continue;
			}
			if($userType == 1){  //导入是手机号
				$accountApi = new Api_Gionee_Account();
				$uuid = $accountApi->getUuidByName($value);
			}  else {            //导入是uuid
				$uuid = $value;
			}
			$msg = array(
					        'type' =>  107,
							'top_type' =>  100,
							'totype' =>  1,
							'title' =>  '您获得'.$send_num.'积分 ',
							'msg' =>  '恭喜，金立游戏大厅赠送您'.$send_num.'积分奖励！',
							'status' =>  0,
							'start_time' =>  time(),
							'end_time' =>  strtotime('2050-01-01'),
							'create_time' =>  time(),
							'sendInput' =>  $uuid,
			);
		    Common::getQueue()->push('game_client_msg',$msg);
		}
		return true;
	}
	
	/**
	 * 积分说明配置
	 */
	public function pointIndexAction() {
		$gamePointsDesc = Game_Service_Config::getValue('gamePointsDesc');
		$this->assign('gamePointsDesc', $gamePointsDesc);
	}
	
	/**
	 * 积分说明提交
	 * Enter description here ...
	 */
	public function pointDescPostAction() {
		$gamePointsDesc = $this->getPost('gamePointsDesc');
		Game_Service_Config::setValue('gamePointsDesc', $gamePointsDesc);
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '商品名称不能为空.'); 
		if(!$info['img']) $this->output(-1, '商品图片不能为空.');
		if(!$info['icon']) $this->output(-1, '商品icon不能为空.');
		if($info['type'] == 2 && !$info['effect_time']) $this->output(-1, 'A券有效期不能为空.');
		if(!$info['total_num']) $this->output(-1, '商品总数量不能为空.');
		if(!$info['preson_limit_num']) $this->output(-1, '每人兑换商品数量不能为空.');
		if($info['type'] == 1 && $info['preson_limit_num'] >=2) $this->output(-1, '每人兑换商品数量只能为1个.');
		if(!$info['consume_point']) $this->output(-1, '兑换商品消耗积分不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或者等于结束时间.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookPresendData($info) {
		if(!$info['send_num']) $this->output(-1, '发放数量不能为空.');
		if($info['send_num'] < 0 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$info['send_num'])){
			$this->output(-1, '发放数量必须填写正整数.');
		}
		if(!$info['reason']) $this->output(-1, '发放原因不能为空.');
		if(!$info['sendInput']) $this->output(-1, '发放账号不能为空.');
		if(preg_match("/^[0-9]+\s*\，[0-9]+$/",$info['sendInput'])) $this->output(-1, '发放账号不能用中文逗号区隔，请用英文“,”区隔.');
		$unames = explode(',',html_entity_decode($info['sendInput']));
		if(count($unames) > 1000)  $this->output(-1, '导入最多1000条.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookAddressData($info) {
		if(!$info['receiver']) $this->output(-1, '收货人不能为空.');
		if(!$info['receiverphone']) $this->output(-1, '联系人电话不能为空.');
		if(!$info['address']) $this->output(-1, '联系人地址不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$result = Client_Service_Hd::deleteHd($id);
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
	
	
}
