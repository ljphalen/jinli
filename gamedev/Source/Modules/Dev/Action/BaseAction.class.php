<?php
class BaseAction extends CommonAction {
	const APP_INIT = 0; // 已提交(包括apk的未提交/审核中/审核通过)
	const APP_ONLINE = 1; // 已上线
	const APP_AUDIT_NO_PASS = -1; // 审核不通过
	const APP_OFFLINE = - 2; // 已下线
	
	const APK_EDIT = 0; // 未提交
	const APK_AUDITING = 1; // 审核中
	const APK_AUDIT_NOT_PASS = - 1; // 审核不通过
	const APK_TEST_PASS = 2; // 审核通过
	const APK_ONLINE = 3; // 已上线
	const APK_OFFLINE = - 2; // 已下线
	const APK_AUTO_ONLINE = 4; // 自动上线
	
	public $appStatus = array ();
	public $apkStatus = array ();
	protected $user = array ();
	private $feeMode = array();
	
	function _initialize()
	{
		parent::_initialize ();
		// 验证是否登录
		loadClient ( array ("Accounts") );
		if (! $this->uid = AccountsClient::checkAuth ())
		{
			if(APP_DEBUG){
				Log::write("AccountsClient::checkAuth FILE", log::EMERG);
				Log::write(var_export($_COOKIE, true), Log::EMERG);
				header('LOG: NOCOOKIE');
			}

			if($this->ispost() && strtolower(ACTION_NAME) == 'apkupload')
			{
				$return [0] ['error'] = '您已超时退出，请登陆后重新上传';
				$return [0] ['errorCode'] = 2;
				echo json_encode ( array (
						"files" => $return
				) );
				die ();
			}
			
			header('Location: ' . U("Login/login"));
			exit;
		}
		
		$this->user = AccountsClient::checkUser ( $this->uid );
		if (empty ( $this->user ))
		{
			header('Location: ' . U("Login/login"));
			exit;
		}
		$this->appStatus = array (
				self::APP_INIT => '已提交',
				self::APP_ONLINE => '已上线',
				self::APP_AUDIT_NO_PASS => '审核不通过',
				self::APP_OFFLINE => '已下线' 
		);
		
		$this->apkStatus = array (
				self::APK_ONLINE => '已上线',
				self::APK_TEST_PASS => '审核通过',
				self::APK_EDIT => '未提交',
				self::APK_AUDITING => '审核中',
				self::APK_AUDIT_NOT_PASS => '未通过',
				self::APK_OFFLINE => '已下线',
				self::APK_AUTO_ONLINE => '自动上线',
		);
		
		//添加新的支付方式，需要在admin/SystemAction中再添加一次
		$this->feeMode = array("101" => "移动MM",
								"102" => "移动基地",
								"103" => "联通短信",
								"104" => "电信短信",
								"105" => "第三方支付",
								"107" => "金立支付",
								"106" => "无"
						);
		
		//追加未读消息数
		$account_message = D('Dev://AccountMessage');
		$map = array('account_id' => $this->uid,'read_state' => AccountMessageModel::READ_STATE_INIT);
		$message_count = $account_message->where($map)->count();
		
		$this->isLogin = true;
		$this->assign ( "uid", $this->uid );
		$this->assign ( 'user', $this->user );
		$this->assign ( "email", $_SESSION ['email'] );
		$this->assign ( "app_status", $this->appStatus );
		$this->assign ( "apk_status", $this->apkStatus );
		$this->assign ( "fee_mode", $this->feeMode );
		$this->assign ( "message_count",$message_count);
	}
}