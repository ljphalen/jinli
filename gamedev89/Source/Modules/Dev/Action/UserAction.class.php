<?php
/**
 * 用户信息控制类
 *
 * @name LoginAction.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class UserAction extends BaseAction {
	function _initialize() {
		parent::_initialize ();
		
		loadClient ( Array (
				"Accounts",
				"Accountinfo",
				"AuthCode"
		) );
	}
	function info() {
		$account = AccountsClient::getInfoById ( $this->uid );
		$info = AccountinfoClient::fetchAccInfo ( $this->uid );
		
		$this->assign ( "account", $account );
		$this->assign ( "info", $info );
		$this->display ();
	}
	function edit() {
		$account = AccountsClient::getInfoById ( $this->uid );
		$info = AccountinfoClient::fetchAccInfo ( $this->uid );
		
		$this->assign ( "account", $account );
		$this->assign ( "info", $info );
		$this->display ();
	}
	function editsave() {
		$account = $this->_post ( "account", "", "" );
		$info = AccountinfoClient::fetchAccInfo ( $this->uid );
		// 数据校验验证
		if (! is_array ( $account )) {
			$this->error ( "非法参数！" );
		}
		
		if ($account['phone'] !=$info['phone'] && !SmslogModel::checkCode($account['phone'], $account['authcode'],SmslogModel::SMS_MODULE_USEREDIT))
		{
			$this->error('短信验证码错误!');
		}
		
		AccountsClient::upAccounts ( $this->uid, array (
				'nickname' => $account ['nickname'] 
		) );
		unset ( $account ['nickname'] );
		unset ( $account ['authcode'] );
		
		// 用户审核通过后，公司，税务信息不可修改
		if ($this->user['info_status'] == AccountinfoModel::STATUS_SUC)
		{
			unset ( $account ['company'] );
			unset ( $account ['passport_num'] );
			unset ( $account ['company_passport'] );
			unset ( $account ['tax_number'] );
			unset ( $account ['tax_passport'] );
		}
		$uploadSetting = array(
				'maxSize'	=>1024*1024,           					//文件大小限制
				'allowExts'	=>array('jpg','gif','png','jpeg'),	//文件类型设置
				'isWater'	=>false        						//是否加水印
		);
		$uploadList = helper("Upload")->_upload ("user",$uploadSetting);
		
		if (is_array ( $uploadList[0] ))
		{
			$account ['company_passport'] = $uploadList[0]['filepath'];
		}elseif ($_FILES['company_passport']['name'])
		{
			$this->error($uploadList);
		}
		
		//当用户状态为驳回状态时，修改资料状态改为待审核
		if ($info['status'] == AccountinfoModel::STATUS_NOT)
		{
			$account['status'] = AccountinfoModel::STATUS_INIT;
		}
		
		$res = AccountinfoClient::upAccInfo($this->uid, $account);
		if ($res) {
			$msg = "修改成功<br>您修改的帐户信息已经生效";
			$this->success ( $msg, U("User/info") );
		}
		$this->error ( "修改失败" );
	}
	
	/**
	 * 完善税务信息
	 */
	function taxfix()
	{
		$status = D("AccountTax")->status_to_int($this->uid);
		
		$account = AccountsClient::getInfoById ( $this->uid );
		$info = AccountinfoClient::fetchAccInfo ( $this->uid );
		
		$this->assign ( "account", $account );
		$this->assign ( "info", $info );

		if($status > 0)
			$this->display('User:tax_info');
		else 
			$this->display('User:taxfix');
	}
	
	//单独修改保存税务信息
	function save_tax()
	{
		//税务通过审核则不能修改
		$status = D("AccountTax")->status_to_int($this->uid);
		if($status > 0)
			$this->error("<b>您的税务信息已经通过审核，不能修改</b><div>如果需要更改，请联系我们</div>");
		
		$account['tax_number'] = $this->_post("tax_number", "trim", "");
		$tax_passport_old = $this->_post("tax_passport_old", "trim", "");
		
		$uploadSetting = array(
				'maxSize'	=> 2*1024*1024,           					//文件大小限制
				'allowExts'	=>array('jpg','gif','png','jpeg'),	//文件类型设置
				'isWater'	=>false        						//是否加水印
		);
		$uploadList = Helper("Upload")->_upload ("user", $uploadSetting);

		if (is_array ( $uploadList[0] )){
			$account ['tax_passport'] = $uploadList[0]['filepath'];
		}elseif ($_FILES['tax_passport']['name'])
		{
			$this->error($uploadList);
		}
		
		if(empty($account["tax_number"]) && empty($account["tax_passport"]))
			$this->error("税务信息必须完整填写");

		$res = AccountinfoClient::upAccInfo($this->uid, $account);
		
		//保存税务信息到状态表
		$account["account_id"] = $this->uid;
		D("Dev://AccountTax")->save_tax($account);
		
		if ($res) {
			$msg = "您的帐户信息修改成功";
			$this->success ( $msg );
		}else{
			$this->error ( "您的帐户信息修改失败" );
		}
	}
	
	/*
	 * 修改密码显示页面
	 */
	public function pwdedit() {
		$this->display ();
	}
	
	/*
	 * 检查原始密码是否正确
	 */
	public function checkOldpwd() {
		$accInfo = AccountsClient::getInfoById ( $this->uid );
		$oldpwd = $this->_get ( "oldpassword", "trim", "" );
		if ($accInfo && ! empty ( $oldpwd )) {
			$salt = $accInfo ['salt'];
			$oldpassword = AuthCodeClient::makepass ( $oldpwd, $salt );
			if ($accInfo ['crypted_password'] === $oldpassword) {
				$this->success ( '200' );
			}
		}
		$this->error ( '404' );
	}
	
	/*
	 * 提交密码修改
	 */
	public function changePwd() {
		if (empty ( $_POST ['oldpassword'] )) {
			$this->error ( '旧密码不能为空！' );
		}
		if (empty ( $_POST ['password'] ) || empty ( $_POST ['repassword'] ) ) {
			$this->error ( '新密码不能为空' );
		}
		if ($_POST ['password'] != $_POST ['repassword']) {
			$this->error ( '新密码和确认密码必须相同！' );
		}
		
		if ($_POST ['password'] == $_POST ['oldpassword']) {
			$this->error ( '新密码和旧密码一样，无法修改' );
		}
		$map = array ();
		$accInfo = AccountsClient::getInfoById ( $this->uid );
		if ($accInfo) {
			$salt = $accInfo ['salt'];
			$oldpassword = AuthCodeClient::makepass ( $this->_post('oldpassword'), $salt );
			if ($accInfo ['crypted_password'] === $oldpassword) {
				$newpwd = AuthCodeClient::makepass ( $this->_post('password'), $salt );
				$res = AccountsClient::upPwd ( $this->uid, $newpwd );
				if ($res) {
					$this->assign ( "jumpUrl", "/Login/login" );
					$this->success ( '修改密码成功，请您重新登陆' );
				} else {
					$this->error ( '密码修改失败，请稍后！' );
				}
			} else {
				$this->error ( '旧密码不符，请重新输入！' );
			}
		}
		$this->error ( '此用户不存在！' );
	}
}