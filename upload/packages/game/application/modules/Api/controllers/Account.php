<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AccountController extends Api_BaseController {
	
	/**
	 * 获取Gionee 账号服务器时间
	 */
	public function gettimeAction(){
		$data  = Api_Gionee_Account::getServerTime();
		$this->_response('', array('api' => $data));
	}
	
	
	/**
	 * 获取token值 【token 有效期 5分钟】
	 * @param string h 代表请求的唯一值
	 * @return json token值
	 * 
	 */
	public function gettokenAction(){
		$hash = $this->getInput('h');
		if(!$hash) $this->_response('-1');
		$time = Common::getTime();
		$token = base64_encode(Common::encrypt($hash . ":" . ($time + 300), ENCODE));
		$jsonData = array('api'=>array('token'=>$token));
		$this->_response('', $jsonData);
	}
	
	/**
	 * 获取|刷新图形验证码
	 * @return json 接口返回值
	 */
	public function refreshgvcAction(){
		$data  = Api_Gionee_Account::refreshGVC();
		$this->_response('', array('api' => $data));
	}
	
	/**
	 * 图形验证码(注册-验证)
	 * @param string tn 手机号
	 * @param string vid 获取图形验证码时的返回值
	 * @param string vtx 填写的验证码值
	 * @return json 接口返回值
	 */
	public function reggvcAction(){
		$request = $this->getInput(array('tn','vid','vtx'));
		$tn = $request['tn'];
		$vid = $request['vid'];
		$vtx = $request['vtx'];
		if(!$tn || !$vid || !$vtx) $this->_response('-1');
		$data = Api_Gionee_Account::registerByGvc($tn, $vid, $vtx);
		$this->_response('', array('api' => $data));
	}
	
	/**
	 * 获取短信验证码(注册)
	 * @param string tn 手机号
	 * @param string s  图形验证 返回session 【要求输入图形验证时填写】
	 * @return json 接口返回值
	 */
	public function getsmsAction(){
		$request = $this->getInput(array('tn','s'));
		$tn = $request['tn'];
		$s = ($request['s']) ? $request['s'] : '';
		if(!$tn) $this->_response('-1');
		$data = Api_Gionee_Account::getSmsForRegister($tn, $s);
		$this->_response('', array('api' => $data));
	}
	
	/**
	 * 短信验证码(注册-验证)
	 * @param string tn 手机号
	 * @param string sc 填写的图形验证码的值
	 * @param string s 图形验证 返回session 【要求输入图形验证时填写】
	 * @return json 接口返回值
	 */
	public function regsmsAction(){
		$request = $this->getInput(array('tn','sc','s'));
		$tn = $request['tn'];
		$sc = $request['sc'];
		$s = ($request['s']) ? $request['s'] : '';
		if(!$tn || !$sc) $this->_response('-1');
		$data = Api_Gionee_Account::registerBySmsCode($tn, $sc, $s);
		$this->_response('', array('api' => $data));
	}
	
	/**
	 * 设置密码(注册-完成)
	 * @param string s 短信验证码通过后返回的session
	 * @param string p 用户设置的密码
	 * @param string mode 登录端 【1-客户端 2-web】
	 * @param string sp 客户端sp参数
	 * @return string json 格式的数据
	 * {
	 *	tn: "15818712234",
	 *	u: "7442F8C7A59C4E9E99EDA8C3BE75235B"
	 *	}
	 */
	public function regpassAction(){
		$request = $this->getInput(array('s', 'p', 'mode', 'sp'));
		$s = $request['s'];
		$p = $request['p'];
		$mode = $request['mode'];
		//缺少参数
		if(!$s || !$p || !$mode) $this->_response('-1');
		$time = Common::getTime();
		$response = Api_Gionee_Account::registerByPass($s, $p);
		$result = array('api' => response);
		if($response['u'] && $response['tn']){
			//v1.5.3 增加pk
			$p= Api_Gionee_Account::encode_password($p);
			$pk = Api_Gionee_Account::createPasskey($response['u'], $p);
			//写入注册用户信息 并自动登录
			$reg = array(
				'u'=> $response['u'],
				'pk' => $pk,
				'tn'=> $response['tn'],
				'time'=> $time,
				'sp' => $request['sp'],
				'mode'=> $mode,
			);
			//游戏大厅会员注册
			$act = 1;
			$this->_addUser($reg, $act);
			//写入登录日志
			$log = array('uuid' => $reg['u'], 'uname' => $reg['tn'], 'act' => 2, 'create_time' => $reg['time']);
			if($reg['mode']) $log['mode'] = $reg['mode'];
			if($reg['sp']){
				$sp= $this->_parseSp($reg['sp']);
				$log = array_merge($log, $sp);
			}
			Account_Service_User::addLog($log);
			//加入注册时间 最后登录时间 用户资料
			$nickName = substr_replace($response['tn'], '****', 3, 4);
			$result['account'] = array(
					'uuid' => $response['u'],
					'pk' => $pk,
					'uname'=> $response['tn'],
					'nickname' => html_entity_decode($nickName),
					'sex' => 0,
					'favor_type'=>'',
					'avatar'=>'',
					'regtime' => $time,
					'lltime' => $time
					
			);
		}
		
		$this->_response('', $result);
	}
	
	/**
	 * 账号普通登录
	 * @param string tn 登录的用户名 -手机号
	 * @param string p 登录密码
	 * @param string vid 获取图形验证码返回值
	 * @param string vtx 填写的验证码值
	 * @param string vty 验证码类别 【登录需要验证码时该值会返回，当使用是将返回值传递过来】
	 * @param string token 获取的token 值
	 * @param string h 请求token的 h 值
	 * @param string sp 客户端登录 填写
	 * @param string mode 登录端 【1-客户端 2-web】
	 * @return json 接口返回值 | 错误代码
	 * 
	 */
	public function loginAction(){
		$request = $this->getInput(array('tn', 'p', 'vid', 'vtx', 'vty', 'token', 'h', 'sp', 'mode'));
		$tn = $request['tn'];
		$p = $request['p'];
		$h = $request['h'];
		$token = $request['token'];
		$sp = $request['sp'];
		$mode = $request['mode'];
		//缺少参数
		if(!$tn || !$p || !$mode || !$h || !$token) $this->_response('-1');
		//token过期或异常
		if(!($this->_checkToken($h, $token))) $this->_response('-2');
		
		$spArr = Common::parseSp($sp);
		$imei = $spArr['imei'];
		
		$loginNum = $this->checkLoginNum($imei);
		if(!$loginNum)  $this->_response('-1');
		
		$time = Common::getTime();

		$vid = ($request['vid']) ? $request['vid'] : '';
		$vtx = ($request['vtx']) ? $request['vtx'] : '';
		$vty = ($request['vty']) ? $request['vty'] : '';
		$response = Api_Gionee_Account::auth($tn, $p, $vid, $vtx, $vty);
		$result = array('api' => $response);
		if($response['u']){
			//v1.5.3 增加pk
			$p= Api_Gionee_Account::encode_password($p);
			$data = array(
					'u'=>$response['u'],
					'pk' => Api_Gionee_Account::createPasskey($response['u'], $p), //v1.5.3 增加pk
					'tn'=>$tn,
					'sp'=>$sp,
					'mode'=>$mode
			);
			$result['account'] = $this->_loginProcess($data);
		}
		
		$this->_response('', $result);
	}
	
	

	/*
	 * 检查登陆次数
	*/
	private function checkLoginNum($imei) {
		if(!$imei) return false;
		$unameNum = 0;
		$search = $unames = array();
		$recently = strtotime('-1 day');
	
		$search['create_time'] = array('>=', $recently);
		$search['imei'] = $imei;
	
		list(,$userLogs) = Account_Service_User::getUserLogList(1, 22, $search);
		foreach($userLogs as $k=>$v){
			$unames[]  = $v['uname'];
		}
	
		$unameNum = array_unique($unames);
		if(count($unameNum) >= 10) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * 账号签名自动登录
	 * @param string uuid 登录用户-uuid
	 * @param string ts 登录时间戳
	 * @param string nonce 登陆随即字符
	 * @param string mac 签名后的数据
	 * @param string token 获取的token 值
	 * @param string h 请求token的 h 值
	 * @param string sp 客户端登录 填写
	 * @param string mode 登录端 【1-客户端 2-web】
	 * @return json 接口返回值 | 错误代码
	 *
	 */	
	public function autologinAction(){
		$request = $this->getInput(array('uuid', 'pk', 'ts', 'nonce', 'mac', 'token', 'h', 'sp', 'mode'));
		$uuid = $request['uuid'];
		$pk = $request['pk'];
		$ts = $request['ts'];
		$nonce = $request['nonce'];
		$mac = $request['mac'];
		$mode = $request['mode'];
		$sp = $request['sp'];
		$h = $request['h'];
		$token = $request['token'];
		//缺少参数
		if(!$uuid || !$ts || !$nonce || !$mac || !$mode || !$h || !$token) $this->_response('-1');
		//token过期或异常
		if(!($this->_checkToken($h, $token))) $this->_response('-2');
	
		$spArr = Common::parseSp($sp);
		$clientVersion = $spArr['game_ver'];
		$imei = $spArr['imei'];
		
		$loginNum = $this->checkLoginNum($imei);
		if(!$loginNum)  $this->_response('-1');
		
		if(strnatcmp($clientVersion, '1.5.1') < 0) 
			//低于1.5.1使用
			$response = Api_Gionee_Account::auth2($uuid, $ts, $nonce, $mac);
		else 
			$response = Api_Gionee_Account::auth3($uuid, $ts, $nonce, $mac);

		$result = array('api' => $response);
		if($response['tn']){
			if(stristr($response['tn'], '+86') !== FALSE) $response['tn'] = substr($response['tn'], 3);
			if(!preg_match("/^1[34578]{1}[0-9]{9}$/",$response['tn'])) $this->_response('-1');
				
			$data = array(
					'u'=>$uuid,
					'tn'=>$response['tn'],
					'pk' => $pk ? $pk : '', //自动登陆增加pk参数, 从v1.5.3开始.
					'sp'=>$sp,
					'mode'=>$mode
			);
			$result['account'] = $this->_loginProcess($data);
		}
		
		
		
		$this->_response('', $result);
	}
	
	/**
	 * 登录状态检测
	 * @param uuid 账号登录|注册后返回的 u
	 * @param mode 检测的账号端口 【1-客户端 2-web】
	 * @param lctime 上次检测时最后登录时间
	 * @param sp 客户端检测传入
	 * @param string token token 值
	 * @param string h  请求接口之前 h 值
	 * @return  json |错误状态
	 * 
	 * 每次检测机制：根据上次检测时间，24小时内只处理一次
	 * 以最后一次登录时间为准，账号登录数据有效期为15天,过期失效。  
	 * lltime 最后一次登录时间 unix时间戳
	 * lctime 上次登录检测时间 unix时间戳
	 * mode 
	 * 错误状态
	 *  0-不在线需要登录
	 * -1-请求非法
	 * -3-重复检测
	 * 正常状态
	 * 无需登录返回值
	 * json格式数据:{"uuid":"账号UUID", "lltime":"最后登录时间戳", "lctime":"本次检测时间戳",};
	 */
	public function checkloginAction(){
		$request = $this->getInput(array('uuid', 'mode', 'lctime', 'sp', 'token', 'h'));
		$uuid = $request['uuid'];
		$mode = $request['mode'];
		$lctime = $request['lctime'];
		$sp = $request['sp'];
		$token = $request['token'];
		$h = $request['h'];
		  //参数不符合
		if(!$uuid || !$mode || !$h || !$token) $this->_response('-1');
		//token过期或异常
		if(!($this->_checkToken($h, $token))) $this->_response('-2');
		
		$user = Account_Service_User::getUser(array('uuid'=>$uuid));
		//用户不存在
		if (!$user) $this->_response('-1');
		
		//解析sp参数
		if($sp){
			$sp= $this->_parseSp($sp);
		}
		
		//客户端-设备登录来源处理
		if($mode == '1') {
			$imei = $sp['imei'];
			if ($imei != $user['imei']) $this->_response('0');
		}
		
		$time = Common::getTime();
		//已登录过 24小时之内重复检测登录状态
		if ($lctime && strnatcmp($sp['game_ver'], '1.5.4') < 0) {
			$today = strtotime(date('Y-m-d', $time)); //当天时间戳
			$reday = strtotime(date('Y-m-d', $lctime)); //上次登录的时间戳
			if($reday == $today) $this->_response('-3');
		}
		//账户不在线
		if (!$user['online']) $this->_response('0');
		//登录过身份信息 15天 过期
		$lltime = $user['last_login_time'];
		if ($time > ($lltime + 86400 * 15)) $this->_response('0');
		//刷新用户主表最后登录时间 在线状态
		$check = array('online' => 1, 'last_login_time' => $time);
		//客户端
		if($mode == '1') {
			$check['client'] = 1;
			$check['imei'] = $sp['imei'];
		}
		//web
		if($mode == '2') $check['web'] = 1;
		Account_Service_User::updateUser($check, array('uuid' => $uuid));
		$response = array('uuid'=> $user['uuid'], 'lltime' => $time, 'lctime' => $time);
		//添加检测日志
		$log = array('uuid'=>$user['uuid'], 'uname'=>$user['uname'], 'mode'=>$mode, 'act'=>3, 'create_time' => $time);
		if($sp){
			$log = array_merge($log, $sp);
		}
		Account_Service_User::addLog($log); 
		
		//1.5.4加入
		if(strnatcmp($sp['game_ver'], '1.5.4') >= 0 ){
			//登录赠送
			$this->loginInitial($uuid, $sp['game_ver'],  $time,  $sp['imei'],  $user['uname']);
		}

		$this->_response('', array('api'=> $response));
	}
	
	/**
	 * 连续登录赠送
	 * @param unknown_type $uuid
	 */
	private function continueLoginSend($uuid){
		$configArr = array('uuid'=> $uuid,
							'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
							'task_id'=>Util_Activity_Context::DAILY_TASK_CONTINUELOGIN_TASK_ID,
						);
		$activity = new Util_Activity_Context(new Util_Activity_ContinueLogin($configArr));
		$activity->sendTictket();	
	}
	
	/**
	 * 账号退出
	 * @param string uuid  账号登录|注册后返回的 u
	 * @param string mode 检测的账号端口 【1-客户端 2-web】
	 * @param string sp 客户端登录传递 sp参数
	 * @param string token token 值
	 * @param string h  请求接口之前 h 值
	 * @return 退出状态 1
	 */
	public function logoutAction(){
		$request = $this->getInput(array('uuid', 'mode', 'sp', 'token', 'h'));
		$uuid = $request['uuid'];
		$mode = $request['mode'];
		$sp = $request['sp'];
		$token = $request['token'];
		$h = $request['h'];
		//参数不符合
		if(!$uuid || !$mode || !$h || !$token) $this->_response('-1');
		//token过期或异常
		if(!($this->_checkToken($h, $token))) $this->_response('-2');
		
		$time = Common::getTime();
		$user = Account_Service_User::getUser(array('uuid'=>$uuid));
		if(!$user) $this->_response('-1');
		//客户端退出对设备来源检测。
		if($mode == '1') {
			$imei = $this->_parseSp($sp, 'imei');
			if($imei != $user['imei']) $this->_response('1');
		}
		//更新用户表中相关字段
		Account_Service_User::updateUser(array('client' => 0, 'web' => 0, 'online' => 0), array('uuid' => $uuid));
		//添加退出日志
		$log = array('uuid'=>$user['uuid'], 'uname'=>$user['uname'], 'mode' => $mode, 'act' => 4, 'create_time' => $time);
		if($sp){
			$sp= $this->_parseSp($sp);
			$log = array_merge($log, $sp);
		}
		Account_Service_User::addLog($log);
		$this->_response('1');
	}
	
	/**
	 * 忘记密码地址
	 * 
	 */
	public function forgetpasswordAction(){
		$forgetUrl = Game_Service_Config::getValue("game_account_forgetpwd");
		$data = array("data" => $forgetUrl ? $forgetUrl:'');
		$this->_response('', array('api'=> $data));
	}
	
	/**
	 * 用户注册协议地址
	 * 
	 */
	public function regagreementAction(){
		$agreement = Game_Service_Config::getValue("game_account_agreement_url");
		$data = array("data" => $agreement ? $agreement : '' );
		$this->_response('', array('api'=> $data));
	}
	
	/**
	 * 菜单账号下的提示语
	 * 
	 */
	public function tipsAction(){
		$agreement = Game_Service_Config::getValue("game_account_tips");
		$data = array("data" => $agreement ? $agreement : '' );
		$this->_response('', array('api'=> $data));
	}
	
	/**
	 * 修改用户资料
	 * @param uuid uuid
	 * @param uname 用户账号
	 * @param nick 用户昵称
	 * @param mode 请求端口 【1-客户端 2-web】
	 * @param sp 客户端传入
	 * @param string token token 值
	 * @param string h  请求接口之前 h 值
	 * @return 状态码【0-需要登陆 1-操作正常 2-包含敏感词 -3-操作失败】
	 */
	public function modifyuserAction(){
		$request = $this->getInput(array('uuid', 'uname', 'nickname', 'sex', 'favorType', 'mode', 'sp', 'token', 'h'));
		$uname = $request['uname'];
		$uuid = $request['uuid'];
		$nickname = $request['nickname'];
		$sex = $request['sex'];
		$mode = $request['mode'];
		$sp = $request['sp'];
		$imei = ($sp) ? $this->_parseSp($sp, 'imei') : '';
		$token = $request['token'];
		$h = $request['h'];
		//参数不符合
		if(!$uname || !$mode || !$h || !$token) $this->_response('-1');
		//token过期或异常
		if(!($this->_checkToken($h, $token))) $this->_response('-2');
		//账号是否在线-v1.5.4使用uuid兼容老版本
		if($uuid){
			$online = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
		}else{
			$online = Account_Service_User::checkOnline($uname, $imei);
		}
		
		if(!$online) $this->_response('0');
		$data = array();
		if($nickname) {
			//v1.5.4-敏感词检测
			$filter = Util_Sensitive::search($nickname);
			if($filter) $this->_response('2');
			$data['nickname'] = $nickname;
		}
		if($sex) $data['sex'] = $sex;
		//更新用户资料
		if(empty($data)) $this->_response('1');
		//v1.5.4账号增加uuid字段
		if($uuid) $data['uuid'] = $uuid;
		$ret = Account_Service_User::updateUserInfo($data, array('uname'=>$uname));
		//喜欢的分类-v1.5.4
		if($request['favorType']){
			$favorArr = explode('|',$request['favorType']);
			Account_Service_User::saveFavorCategory($uuid, $favorArr);
		}
		
		if(!$ret) $this->_response('-3');
		$this->_response('1');
	}

	/**
	 * 保存头像
	 * v1.5.4
	 */
	public function saveAvatarAction(){
		$request = $this->getInput(array('uuid', 'uname', 'mode', 'sp', 'token', 'h'));
		$uuid = $request['uuid'];
		$mode = $request['mode'];
		$sp = $request['sp'];
		$imei = ($sp) ? $this->_parseSp($sp, 'imei') : '';
		$token = $request['token'];
		$h = $request['h'];
		//参数不符合
		if(!$uuid || !$mode || !$h || !$token) $this->_response('-1');
		//账号是否在线-v1.5.4使用uuid
		$online = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
		if(!$online) $this->_response('0');
		//上传到本地临时目录
		$file = $_FILES['img'];
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		if ($file['error']) $this->_response('-3');
		$allowType = array('jpg','jpeg','png','gif');
		$savePath = sprintf('%s/%s', $attachPath, 'temp');
		$uploader = new Util_Upload($allowType);
		$ret = $uploader->upload('img', uniqid(), $savePath);
		if ($ret < 0) $this->_response('-3');
		//远程通过ftp上传附件到后台
		$localFile = sprintf('%s/%s/%s',$attachPath, 'temp', $ret['newName']);
		$remotePath = sprintf('%s/%s/%s', 'avatar', date('Ym'), $ret['newName']);
		//获取配置信息
		$config = Common::getConfig('ftpConfig');
		// 打开FTP连接 上传文件
		$ftp = new Util_Ftp($config['host'], $config['port'], $config['user'], $config['pass']);
		$ret = $ftp->up_file($localFile, $remotePath);
		if(!$ret) $this->_response('-3');
		$ftp->close();
		
		//保存远程头像信息
		$ret = Account_Service_User::updateUserInfo(array('avatar'=> '/' . $remotePath), array('uuid' => $uuid));
		if(!$ret) $this->_response('-3');
		$attachUrl = Common::getAttachPath();
		$data = array('avatar'=> $attachUrl . '/' . $remotePath);
		$this->_response('1', array('api'=> $data));
	}
	
	/**
	 * token 验证
	 * @param string $key
	 * @param unknown $token
	 * @return boolean
	 */
	private function _checkToken($key, $token){
		$time = Common::getTime();
		//token 解密
		$token = base64_decode($token);
		$token = Common::encrypt($token, 'DECODE');
		list($_hash, $_time) = explode(':',$token);
		//请求来源不一致
		if($_hash != $key) return false;
		//请求超时
		if($time > $_time) return false;
		return true;
	}
	
	/**
	 * 注册新用户
	 * @param array $data
	 * @param int $act
	 */
	private function _addUser($data, $act){
		$reg = array(
				'uuid' => $data['u'],
				'passkey' => $data['pk'],
				'uname' => $data['tn'],
				'reg_time' => $data['time'],
				'last_login_time' => $data['time'],
				'online' => 1,
				'status' => 1
		);
		//客户端
		if($data['mode'] == '1') {
			$reg['client'] = 1;
			$reg['imei'] = $this->_parseSp($data['sp'], 'imei');
		}
		//web
		if($data['mode'] == '2') $reg['web'] = 1;
		
		Account_Service_User::addUser($reg);
		//昵称增加
		$nickName = substr_replace($data['tn'], '****', 3, 4);
		//v1.5.4 userinfo 中 加入  uuid
		Account_Service_User::addUserInfo(array('uuid' =>$data['u'], 'uname'=> $data['tn'], 'nickname' => $nickName));
		
		//写入注册日志
		$log = array('uuid' => $data['u'], 'uname' => $data['tn'], 'act' => $act, 'create_time' => $data['time']);
		if($data['mode']) $log['mode'] = $data['mode'];
		if($data['sp']){
			$sp= $this->_parseSp($data['sp']);
			$log = array_merge($log, $sp);
		}
		Account_Service_User::addLog($log);
	}
	
	/**
	 * 登陆认证通过后数据处理
	 * @param array
	 * 	$data = array(
	 *			'u'=>"uuid",
	 *          'pk => "pk",
	 *			'tn'=>"tn",
	 *			'sp'=>'sp',
	 *			'mode'=>'mode'
	 *       );
	 */
	private function _loginProcess($data){
		$time = Common::getTime();
		//更新用户数据
		$user = Account_Service_User::getUser(array('uuid'=> $data['u']));
		if (!$user) {
			//写入注册用户信息 并自动登录
			$reg = array(
					'u'=> $data['u'],
					'pk'=> $data['pk'],
					'tn'=> $data['tn'],
					'time'=> $time,
					'sp' => $data['sp'],
					'mode'=> $data['mode'],
			);
			//金立账号会员注册
			$act = 5;
			$this->_addUser($reg, $act);
		}else {
			//换绑手机号特殊处理
			if($data['tn'] != $user['uname']){
				//更新用户主表
				Account_Service_User::updateUser(array('uname'=>$data['tn']), array('uuid' => $data['u']));
				//更新用户信息表
				Account_Service_User::updateUserInfo(array('uuid' => $data['u'], 'uname'=>$data['tn']), array('uname' => $user['uname']));
				//跟用户有关的数据特殊处理,使用异步队列处理
				Common::getQueue()->push("GUQ:uname-bind", array('uuid'=>$data['u'], 'new_uname'=>$data['tn'], 'old_uname' => $user['uname']));
			}
		}
		//v1.5.4用户信息表查询开始使用uuid条件做检索
		$userInfo = Account_Service_User::getUserInfo(array('uuid' => $data['u']));
		
		$login= array('online' => 1, 'last_login_time' => $time);
		//增加对user表中passkey字段更新操作 从v1.5.3开始增加
		if ($data['pk']) $login['passkey']= $data['pk'];
		
		//客户端
		if($data['mode'] == '1') {
			$login['client'] = 1;
			$login['imei'] = $this->_parseSp($data['sp'], 'imei');
		}
		//web
		if($data['mode'] == '2') $login['web'] = 1;
		Account_Service_User::updateUser($login, array('uuid' => $data['u']));
		//登录成功加入登录日志
		$log = array('uuid'=> $data['u'], 'uname'=> $data['tn'], 'mode'=>$data['mode'], 'act'=>2, 'create_time' => $time);
		if($data['sp']){
			$sp= $this->_parseSp($data['sp']);
			$log = array_merge($log, $sp);
		}
		Account_Service_User::addLog($log);
		
		//1.5.4加入
		if(strnatcmp($sp['game_ver'], '1.5.4') >= 0 ){
			//登录赠送
			$this->loginInitial($data['u'], $sp['game_ver'], $time, $sp['imei'],  $data['tn']);
		}
		
		//1.5.4 加入用户喜欢的分类
		$favorTypes = Account_Service_User::getFavorCategory(array('uuid'=>$data['u']));
		$favorInfo = "";
		if($favorTypes) {
			$items = array();
			foreach($favorTypes as $value){
				$items[] = $value['category_id'];
			}
			$favorInfo = implode('|', $items);
		}
		
		//加入最后登录时间 用户名 昵称
		$attachPath = Common::getAttachPath();
		$response = array(
				'uuid'=> $data['u'],
				'pk'=>$data['pk'],
				'uname' => $data['tn'],
				'nickname'=> html_entity_decode($userInfo['nickname']),
				'sex' => $userInfo['sex'],
				'favorType'=> $favorInfo,
				'avatar'=> $userInfo['avatar'] ? $attachPath . $userInfo['avatar'] : "",
				'lltime' => $time
		);
		return $response;
	}
	
	/**
	 * sp 参数分析
	 * 完整 E3_1.4.9.e_4.2.1_Android4.2.1_720*1280_I01000_wifi_FD34645D0CF3A18C9FC4E2C49F11C510
	 * 机型_客户端版本_金立rom版本_android版本_分辨率（320*480）_渠道号_网络类型_加密imei
	 * @param string $sp 
	 * @param string $key
	 */
	private function _parseSp($sp, $key = ''){
		$tmp = array();
		$data = explode('_',$sp);
		$tmp['sp'] = $sp ? $sp : '';
		$tmp['device'] = is_null($data[0]) ? '' : $data[0];
		$tmp['game_ver'] = is_null($data[1]) ? '' : $data[1];
		$tmp['rom_ver'] = is_null($data[2]) ? '' : $data[2];
		$tmp['android_ver'] = is_null($data[3]) ? '' : $data[3];
		$tmp['pixels'] = is_null($data[4]) ? '' : $data[4];
		$tmp['channel'] = is_null($data[5]) ? '' : $data[5];
		$tmp['network'] = is_null($data[6]) ? '' : $data[6];
		$tmp['imei'] = is_null($data[7]) ? '' : $data[7];
		return ($key) ? $tmp[$key] : $tmp;	
	}
	
	/**
	 * 统一输出格式
	 * @param string $code
	 * @param array $data
	 */
	private function _response($code, $data = array()) {
		header("Content-type:text/json");
		$tmp = array(
				'sign'=>'GioneeGameHall',
				'code' => $code,
				
		);
		$data = array_merge($tmp, $data);
		exit(json_encode($data));
	}
	
	/**
	 * 登录初始化
	 */
	private function loginInitial($uuid, $clientVersion, $time, $imei, $uname){
		//1.5.4加入用户登录次数
		$cache = Common::getCache();
		$cacheKey = $uuid.'_user_info' ; //获取用户的uuid
		//用户名
		$cache->hSet($cacheKey, 'uname', $uname); //用户名	
		$cache->hSet($cacheKey, 'imei', $imei);//用户的imei
		$cache->hSet($cacheKey, 'clientVersion', $clientVersion);//客户端的版本
		
		if(strnatcmp($clientVersion, '1.5.5') < 0 ){
			$cache->hdel($cacheKey, 'continueLoginDay');
			return false;
		}
	
		//连续登录天数
		$continueLoginDay = $cache->hGet($cacheKey, 'continueLoginDay');
		if($continueLoginDay === false){
			$cache->hSet($cacheKey, 'continueLoginDay', 1); //连续登录天数
			$cache->hSet($cacheKey, 'continueLoginTotal', 1);//累计连续登录天数
			$cache->hSet($cacheKey, 'everyLoginTotal', 1);   //每天登录的次数
			
		}else{
			 //最后的登录时间
			$lastLoginTime = $cache->hGet($cacheKey, 'lastLoginTime');
			$days = Common::diffDate($lastLoginTime,  date('Y-m-d H:i:s', $time));
			//当前登录
			if($days == 0){	
				$cache->hIncrBy($cacheKey, 'everyLoginTotal', 1);
				//连续登录
			}elseif($days == 1){
				//七天一个周期
				if($continueLoginDay >= 7){
					$cache->hSet($cacheKey, 'continueLoginDay', 1);
				}else{
					$cache->hIncrBy($cacheKey, 'continueLoginDay', 1);
				}
				$cache->hIncrBy($cacheKey, 'continueLoginTotal', 1);
				$cache->hSet($cacheKey, 'everyLoginTotal',1);
				//其他的
			}else{
				$cache->hSet($cacheKey, 'continueLoginDay', 1);
				$cache->hSet($cacheKey, 'continueLoginTotal', 1);
				$cache->hSet($cacheKey, 'everyLoginTotal', 1);
			} 
		}
		$cache->hSet($cacheKey, 'lastLoginTime', date('Y-m-d H:i:s',$time) ); //最后登录时间
		//连续登录的配置
		$taskParams['id'] = 1;
		$taskParams['status'] = 1;
		$taskConfig = Client_Service_ContinueLoginCofig::getBy($taskParams);
		if(!$taskConfig){
			$cache->hSet($cacheKey, 'continueLoginDay', 0);
			$cache->hSet($cacheKey, 'continueLoginTotal',0 );
			$cache->hSet($cacheKey, 'everyLoginTotal', 0);
		}

	}

	
	/**
	 * 登录赠送接口
	 * @return boolean
	 */
	public function loginSendAction(){
		
		$info = $this->getInput(array('puuid', 'uname', 'sp', 'clientId'));
	
		$data['success'] = 'true';
		$data['msg'] = '';
		$data['sign'] = 'GioneeGameHall';
		$data['returnState'] = 'Fail';
		if(!$info['puuid'] || !$info['uname'] || !$info['clientId'] || !$info['sp']){
			$this->clientOutput($data);
		}
		
		$clientVersion = Common::parseSp($info['sp'], 'game_ver');
        //验证客户上报的数据
        $rs = Common::verifyClientEncryptData($info['puuid'], $info['uname'], $info['clientId']);
		if(!$rs){
			$this->clientOutput($data);
		}
	/* 	if($info['puuid']){
			$cache = Common::getCache();
			$cacheHash = $info['puuid'].'_user_info' ;
			$loginClientVertion = $cache->hGet($cacheHash, 'clientVersion');
			if(!strnatcmp($loginClientVertion, '1.5.5') >= 0 ){
				$this->clientOutput($data);
			}
		} */
	
		//福利任务登录赠送
		$configArr = array('uuid'=>$info['puuid'],
				           'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
				           'task_id'=>Util_Activity_Context::WEAL_TASK_LOING_TASK_ID);
		$login = new Util_Activity_Context(new Util_Activity_Login($configArr));		
		$login ->sendTictket();
		//活动登录
		$activityConfig = array('uuid'=>$info['puuid'],
								'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK,
								'version'=>$clientVersion);
		$activity = new Util_Activity_Context(new Util_Activity_Login($activityConfig));
		$activity ->sendTictket();
		
		//连续登录赠送
		$this->continueLoginSend($info['puuid']);
		
		$data['returnState'] = 'Success';
		$this->clientOutput($data);

	}
	
}
