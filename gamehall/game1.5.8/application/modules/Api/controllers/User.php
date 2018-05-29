<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 网页版账号接口
 * @author fanch
 *
 */

class UserController extends Api_BaseController {
	
	
	private $mNotifyConsume =1;
	private $mNotifyPayment =2;

	/**
	 * 获取|刷新图形验证码
	 * @return json 接口返回值
	 * 
	 */
	public function refreshgvcAction(){
		$data  = Api_Gionee_Account::refreshGVC();
		//返回结果
		$this->output(0,'',$data);
	}
	
	/**
	 * 用户注册第一步使用手机号跟验证码做注册前准备工作
	 */
	
	public function registerbygvcAction(){
		$request = $this->getInput(array('tn', 'vid', 'vtx', 'vty'));
		$tn = $request['tn'];
		$vid = $request['vid'];
		$vtx = $request['vtx'];
		//缺少参数
		if(!$tn || !$vid || !$vtx ) $this->output(1,'非法请求');
		$vty = ($request['vty']) ? $request['vty'] : 'vtext';
		$data = Api_Gionee_Account::registerByGvc($tn, $vid, $vtx, $vty);
		//返回结果
		$this->output(0,'',$data);
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
		if(!$tn) $this->output(1,'非法请求');
		$data = Api_Gionee_Account::getSmsForRegister($tn, $s);
		//返回结果
		$this->output(0,'',$data);
	}
	
	/**
	 * 短信验证码(注册-验证)
	 * @param string tn 手机号
	 * @param string sc 填写短信验证码的值
	 * @param string s 图形验证 返回session 【要求输入图形验证时填写】
	 * @return json 接口返回值
	 */
	public function regsmsAction(){
		$request = $this->getInput(array('tn','sc','s'));
		$tn = $request['tn'];
		$sc = $request['sc'];
		$s = ($request['s']) ? $request['s'] : '';
		if(!$tn || !$sc) $this->output(1,'非法请求');
		$data = Api_Gionee_Account::registerBySmsCode($tn, $sc, $s);
		//返回结果
		$this->output(0,'',$data);
	}
	
	/**
	 * 设置密码(注册-完成)
	 * @param string s 短信验证码通过后返回的session
	 * @param string p 用户设置的密码
	 * @return string json 格式的数据
	 * {
	 *	tn: "15818712234",
	 *	u: "7442F8C7A59C4E9E99EDA8C3BE75235B"
	 *	}
	 */
	public function regpassAction(){
		$request = $this->getInput(array('s', 'p'));
		$s = $request['s'];
		$p = $request['p'];
		//缺少参数
		if(!$s || !$p) $this->output(1,'非法请求');
		$p = Api_Gionee_Account::encode_password($p);
		$response = Api_Gionee_Account::registerByPass($s, $p);
		$result = $response;
		//处理返回结果
		if($response['u'] && $response['tn']){
			//写入注册用户信息 并自动登录
			$reg = array(
				'u'=> $response['u'],
				'tn'=> $response['tn'],
				'mode'=> 2,
				'time'=> Common::getTime()
			);
			//游戏大厅会员注册
			$act = 1;
			$this->_addUser($reg, $act);
			//写入登录日志
			$log = array('uuid' => $reg['u'], 'uname' => $reg['tn'], 'mode'=> $reg['mode'], 'act' => 2, 'create_time' => $reg['time']);
			Account_Service_User::addLog($log);
			//加入注册时间 最后登录时间 用户资料
			$nickName = substr_replace($response['tn'], '****', 3, 4);
			$result = array('uuid' => $response['u'], 'uname'=> $response['tn'], 'nickname' => html_entity_decode($nickName), 'optime' => $reg['time']);
			//网页版tgc处理登陆
			$this->_onlineProcess('login', $result);
		} 
		$this->output(0,'', $result);
	}
	
	/**
	 * 账号普通登录
	 * @param string tn 登录的用户名 -手机号
	 * @param string p 登录密码
	 * @param string vid 获取图形验证码返回值
	 * @param string vtx 填写的验证码值
	 * @param string vty 验证码类别 【登录需要验证码时该值会返回，当使用是将返回值传递过来】
	 * @return json 接口返回值 | 错误代码
	 * 
	 */
	public function loginAction(){
		$request = $this->getInput(array('tn', 'p', 'vid', 'vtx', 'vty'));
		$tn = $request['tn'];
		$p = $request['p'];
		//缺少参数
		if(!$tn || !$p) $this->output(1,'非法请求');
		$vid = ($request['vid']) ? $request['vid'] : '';
		$vtx = ($request['vtx']) ? $request['vtx'] : '';
		$vty = ($request['vty']) ? $request['vty'] : '';
		$p = Api_Gionee_Account::encode_password($p);
		$response = Api_Gionee_Account::auth($tn, $p, $vid, $vtx, $vty);
		$result = $response;
		//处理返回结果
		if($response['u']){
			$data = array(
					'u' => $response['u'],
					'tn' => $tn,
					'pk' => Api_Gionee_Account::createPasskey($response['u'], $p), //v1.5.3 增加pk
					'mode' => 2 //网页登陆
			);
			$result = $this->_loginProcess($data);
			//网页版tgc处理登陆
			$this->_onlineProcess('login', $result);
		}
		$this->output(0,'', $result);
	}

	
	/**
	 * 登陆的账号退出
	 * @param redirect_uri
	 * @return  json
	 */
	public function logoutAction(){
		$online = Account_Service_User::checkOnline2();
		$uuid = $online['uuid'] ? $online['uuid'] : '';
		//参数不符合
		if(!$uuid) $this->output(1,'非法请求');
		$user = Account_Service_User::getUser(array('uuid'=>$uuid));
		if(!$user) $this->output(1,'非法请求');
		//更新用户表中相关字段
		Account_Service_User::updateUser(array('client' => 0, 'web' => 0, 'online' => 0), array('uuid' => $uuid));
		//添加退出日志
		$log = array('uuid'=>$user['uuid'], 'uname'=>$user['uname'], 'mode' => 2, 'act' => 4, 'create_time' => Common::getTime());
		Account_Service_User::addLog($log);
		//网页版tgc处理登陆
		$this->_onlineProcess('logout');
		
		$this->output(0, '', array(1));
	}

	
	/**
	 * 忘记密码地址
	 * 
	 */
	public function forgetpasswordAction(){
		$forgetUrl = Game_Service_Config::getValue("game_account_forgetpwd");
		$result = array($forgetUrl ? $forgetUrl:'');
		$this->output(0, '', $result);
	}
	
	/**
	 * 用户注册协议地址
	 * 
	 */
	public function regagreementAction(){
		$agreement = Game_Service_Config::getValue("game_account_agreement_url");
		$result = array($agreement ? $agreement : '' );
		$this->output(0, '', $result);
	}

	/**
	 * 用户数据处理cookie处理 
	 * GAME-TC :md5('PASSPORT:'.$uuid.':'.$uname).':'.$uuid
	 * 
	 * @param string $action login
	 * @param array $data
	 * @return void
	 */
	private function _onlineProcess($action, $data = array()){
		switch ($action){
			case 'login':
				$tgc = md5('PASSPORT:' . $data['uuid'] . ':'. $data['uname']) .':' . $data['uuid'];
				Util_Cookie::set('GAME-TGC', $tgc, true); //会话级别的cookie，数据使用加密, 目前禁止客户端脚本访问。
				break;
			case 'logout':
				Util_Cookie::delete('GAME-TGC');
				break;
		}
	}
	
	/**
	 * 注册新用户
	 * @param array $data 
	 * @param int $act
	 * @return void
	 */
	private function _addUser($data, $act){
		$reg = array(
				'uuid' => $data['u'],
				'uname' => $data['tn'],
				'reg_time' => $data['time'],
				'last_login_time' => $data['time'],
				'online' => 1,
				'status' => 1
		);
		//web
		if($data['mode'] == '2') $reg['web'] = 1;
		
		Account_Service_User::addUser($reg);
		//昵称增加
		$nickName = substr_replace($data['tn'], '****', 3, 4);
		Account_Service_User::addUserInfo(array('uname'=> $data['tn'], 'nickname' => $nickName));
		//写入注册日志
		$log = array('uuid' => $data['u'], 'mode' => $data['mode'], 'uname' => $data['tn'], 'act' => $act, 'create_time' => $data['time']);
		Account_Service_User::addLog($log);
	}
	
	
	/**
	 * 登陆认证通过后数据处理
	 * @param array
	 * 	$data = array(
	 *			'u'=>"uuid",
	 *			'tn'=>"tn",
	 *			'mode'=>'mode'
	 *       );
	 * @return void
	 */
	
	private function _loginProcess($data){
		$time = Common::getTime();
		//更新用户数据
		$user = Account_Service_User::getUser(array('uuid'=> $data['u']));
		if (!$user) {
			//写入注册用户信息 并自动登录
			$reg = array(
					'u'=> $data['u'],
					'tn'=> $data['tn'],
			        'pk'=> $data['pk'],
					'time'=> $time,
					'mode'=> $data['mode'],
			);
			//金立账号会员注册
			$act = 5;
			$this->_addUser($reg, $act);
		} else {
			//换绑手机号特殊处理
			if($data['tn'] != $user['uname']){
				//更新用户主表
				Account_Service_User::updateUser(array('uname'=>$data['tn']), array('uuid' => $data['u']));
				//更新用户信息表
				Account_Service_User::updateUserInfo(array('uname'=>$data['tn']), array('uname' => $user['uname']));
				//跟用户有关的数据特殊处理,使用异步队列处理
				Common::getQueue()->push("GUQ:uname-bind", array('uuid'=>$data['u'], 'new_uname'=>$data['tn'], 'old_uname' => $user['uname']));
			}
		}
		$userInfo = Account_Service_User::getUserInfo(array('uname' => $data['tn']));
		$login= array('online' => 1, 'last_login_time' => $time);
		//web登陆
		if($data['mode'] == '2') $login['web'] = 1;
		
		if ($data['pk']) $login['passkey']= $data['pk'];
		
		Account_Service_User::updateUser($login, array('uuid' => $data['u']));
		//登录成功加入登录日志
		$log = array('uuid'=> $data['u'], 'uname'=> $data['tn'], 'mode'=>$data['mode'], 'act'=>2, 'create_time' => $time);
		Account_Service_User::addLog($log);
			
		//加入最后登录时间 用户名 昵称
		return array('uuid'=> $data['u'], 'uname' => $data['tn'], 'nickname'=> html_entity_decode($userInfo['nickname']), 'optime' => $time);
	}
	
	
	public function testAction(){
		header("Content-type: text/html; charset=utf-8");
				
		//获取服务器时间 http://dev.game.gionee.com/api/account/gettime
		//注册第一步：短信验证码 http://dev.game.gionee.com/api/account/getsms/?tn=15818712234
		//验证注册码 http://dev.game.gionee.com/api/account/regsms/?tn=15818712234&sc=423049
		//注册第二步：设置密码 http://dev.game.gionee.com/api/account/regpass/?p=1234567890&s=
		//账号登录 http://dev.game.gionee.com/api/account/login/?tn=15818712234&p=1234567890
	}
	
	
	/**
	 * 充值成功/消费成功
	 */
	public function tradeAction(){
		$token      = $this->getInput('token');
		$uuid        = $this->getInput('uuid');
		$type        = $this->getInput('type');
		$event       = $this->getInput('event');
		$money       = $this->getInput('money');
		$tradeTime   = $this->getInput('tradeTime');
		$tradeNo     = $this->getInput('tradeNo');
		$api_key     = $this->getInput('api_key');
		
	
		//记录日志
		$path = Common::getConfig('siteConfig', 'logPath');
		$file_name = date('Y-m-d').'_trade.log';
		$log_data= '进入交易请求接口uuid='.$uuid.',type='.$type.',event='.$event.',money='.$money.',tradeTime='.$tradeTime.',tradeNo='.$tradeNo.',api_key='.$api_key;
		Common::WriteLogFile($path, $file_name, $log_data);
	
		//组装数据
		$data['needPresent'] = "false";
		$data['msg'] = '';
		$data['data'] = array();
	
		if(!$uuid || !$token){
			$data['msg']='非法请求参数';
			$this->clientOutput($data);
		}
	
		//连接字符串
		$str = $api_key.$event.$money.$tradeNo.$tradeTime.$type.$uuid;
	
		//取得密钥
		$payment_arr = Common::getConfig('paymentConfig','payment_send');
		$ciphertext  = $payment_arr['ciphertext'];
	
		//写日志
		$log_data= '验证token='.$token.'加密串='.md5($ciphertext.$str);
		Common::WriteLogFile($path, $file_name, $log_data);
		//验证签名正确
		if(strtolower($token) == strtolower(md5($ciphertext.$str))){
			//保存记录
			$tmp['uuid'] = $uuid;
			$tmp['type'] = $type;
			$tmp['event'] = $event;
			$tmp['money'] = $money;
			$tmp['trade_time'] = strtotime($tradeTime);
			$tmp['trade_no'] = $tradeNo;
			$tmp['api_key'] = $api_key;
			$tmp['create_time'] = Common::getTime();
			
			if($money < 0 ){
				$data['msg'] = '金额小于零';
				$this->clientOutput($data);
			}		
			
			try {
			    $result = Client_Service_MoneyTrade::insert($tmp);
			} catch (Exception $e) {
			    $log_data='保存记录异常,应为重复通知. exception:' . json_encode($e);
			    Common::WriteLogFile($path, $file_name, $log_data);
			    $data['msg'] = '重复通知';
			    $this->clientOutput($data);
			}
			
			if(!$result){
			    $log_data='支付服务器提交的充值/消费记录保存失败. info:' . json_encode($tmp);
			    Common::WriteLogFile($path, $file_name, $log_data);
			}
		
			// enent = 1表示消费通知 2充值通知
			if($event == $this->mNotifyConsume){
				$this->cosumeSend ( $uuid, $money, $api_key, $path, $file_name );
			}elseif($event == $this->mNotifyPayment){
				$this->paymentSend ( $uuid, $money, $api_key );
			}
			
			$data['needPresent'] = "true";
			$this->clientOutput($data);
		}else{
			$data['msg'] = '签名失败';
			$this->clientOutput($data);
	    }
	
	}

	 private function paymentSend($uuid, $money, $api_key) {
	     // 活动充值赠送
	     $request = array(
	             'uuid'=> $uuid,
	             'money' => $money,
	             'apiKey'=>$api_key
	     );
        $eventObj = new Task_Event('user_payment', $request);
        Task_EventHandle::postEvent($eventObj);
	}

	private function cosumeSend($uuid, $money, $api_key, $path, $file_name) {
		//获得缓存
		$cache = Cache_Factory::getCache();
		//获取用户的uuid
		$cacheKey = $uuid.'_user_info' ;
		//客户端版本
		$client_version = $cache->hGet($cacheKey,'clientVersion');
		//写日志
		$log_data= '客户端版本client_version='.$client_version;
		Common::WriteLogFile($path, $file_name, $log_data);
		
		if(strnatcmp($client_version, '1.5.4') >= 0 ){
			//福利任务消费赠送
			$configArr = array('uuid'=>$uuid,
					           'type'=>Util_Activity_Context::TASK_TYPE_WEAK_TASK,
					           'task_id'=>Util_Activity_Context::WEAL_TASK_CONSUME_TASK_ID,
					           'api_key'=>$api_key );
			$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
			$activity->sendTictket();
		}
		
		//A券活动消费返利
        if ($uuid && $money && $api_key) {
            $eventName = 'user_consume';
            $request = array(
                    'uuid' => $uuid,
                    'money' => $money,
                    'apiKey' => $api_key 
            );
            Client_Service_TaskHd::runTask($eventName, $request);
        }
		
// 		$configArr = array('uuid'=>$uuid, 
// 				           'type'=>Util_Activity_Context::TASK_TYPE_ACTIVITY_TASK, 
// 				           'money'=>$money, 
// 				           'api_key'=>$api_key);
// 		$activity = new Util_Activity_Context(new Util_Activity_Consume($configArr));
// 		$activity->sendTictket();

	}

			
	/**
	 * 获取用户信息
	 * @author yinjiayan
	 */
	public function getInfoAction() {
	    $uuid = $this->getInput('puuid');
	    $this->checkOnline($uuid);
        
        $params = array('uuid' => $uuid);
	    $userInfo = Account_Service_User::getUserInfo($params);
	    $data = array();
	    $data['puuid'] = $userInfo['uuid'];
	    $data['avatar'] = Common::getAttachPath().$userInfo['avatar'];
	    $data['nickName'] = html_entity_decode($userInfo['nickname']);
	    $data['totalPoints'] = intval($userInfo['points']);
	    $this->localOutput(0, '', $data);
	}
}
