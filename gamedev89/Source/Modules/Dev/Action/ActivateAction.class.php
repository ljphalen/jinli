<?php
/**
 * 用户激活控制类
 *
 * @name ActivateAction.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class ActivateAction extends Action {
	private $regPage;
	private $loginPage;
	function _initialize() {
		import ( "@.Client.AccountsClient" );
		$this->regPage = U("Login/register");
		$this->loginPage = U("Login/login");
	}
	
	public function index() {
		$activeCode = $this->_get("ac", "trim", "");

		if (strlen ( $activeCode ) !== 40) {
			$this->assign ( "jumpUrl", $this->regPage );
			$this->error ( "非法参数！" );
		}
		
		$userInfo = AccountsClient::checkByActivateCode ( $activeCode );
		if (! $userInfo) {
			$this->assign ( "jumpUrl", $this->loginPage );
			$this->error ( "此激活码已失效！" );
		}
		
		$activeRes = AccountsClient::activate ( $activeCode );
		if (! $activeRes) {
			$this->assign ( "jumpUrl", $this->regPage );
			$this->error ( "激活失败！" );
		} else {
			$this->assign("uid", $userInfo['id']);
			$this->assign ( "jumpUrl", $this->loginPage );
			$this->display("success");
		}
	}
}