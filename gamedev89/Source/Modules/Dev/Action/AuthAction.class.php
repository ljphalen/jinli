<?php
/**
 * 用户激活控制类
 *
 * @name AuthAction.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AuthAction extends Action {
	function _initialize() {
		import ( "@.Client.AccountsClient" );
		import ( "@.Client.ResetpwdClient" );
	}
	
	public function index() {
		$this->display ();
	}
	
	public function repwd() {
		$email = $this->_post("email","trim","");
		$authcode = $this->_post("authcode","trim","");
		//验证email，预留
		if (empty ( $email ) || strpos ( $email, "@" ) < 1) {
			$_SESSION ['verify'] = "";
			$this->assign ( "jumpUrl", __URL__ . "/index/" );
			$this->error ( "您的邮箱地址不匹配！" );
		}
		if (empty ( $authcode )) {
			$_SESSION ['verify'] = "";
			$this->error ( '验证码必须！' );
		}
		
		if (empty ( $_SESSION ['verify'] ) || $_SESSION ['verify'] != md5 ( strtoupper ( $authcode ) )) {
			$_SESSION ['verify'] = "";
			$this->error ( '验证码错误！' );
		}
		
		$getUser = AccountsClient::checkEmail ( $email );
		if ($getUser) {
			$nickname = $getUser ['nickname'];
			$addRes = ResetpwdClient::addResetpwd ( $getUser ['id'], $email );
			if ($addRes === false) {
				$this->assign ( "jumpUrl", __URL__ . "/index/" );
				$this->error ( "重置密码失败！" );
			} elseif (is_array ( $addRes ) && $addRes [0] === 403) {
				$this->error ( $addRes [1] );
			} else {
				$code = $addRes;
				AccountsClient::sendRepwdEmail ( $nickname, $email, $code );
				$this->assign("waitSecond",15);
				$this->assign("jumpUrl",__APP__."/Login/login");
				$msg = "若要重置密码，请单击电子邮件中的链接,系统将引导您进入某个页面创建新密码。";
				$this->success ( $msg );
			}
		} else {
			$this->assign ( "jumpUrl", __URL__ . "/index/" );
			$this->error ( "您的邮箱地址不存在！" );
		}
	}
	
	public function sendok() {
		$this->display ( 'sendok' );
	}
	
	public function uppwd() {
		$code = $_GET ['code'];
		if (empty ( $code ) || strlen ( $code ) !== 32) {
			$this->assign ( "jumpUrl", __URL__ . "/index/" );
			$this->error ( "非法参数！" );
		} else {
			//验证是否超过24小时
			$res = ResetpwdClient::getOneAtByCode ( $code );
			if ($res ['state'] == "0" && ! empty ( $res ['reseted_at'] )) {
				$this->assign ( "jumpUrl", __URL__ . "/index/" );
				$this->error ( "此链接已经失效，请重新发送重置密码请求." );
			}
			if ($res) {
				$difftime = (time () - strtotime ( $res ['created_at'] )) / 3600; //计算时差
				if ($difftime > 24) {
					$this->assign ( "jumpUrl", __URL__ . "/index/" );
					$this->error ( "此次修改已经超过24小时！" );
				}
			} else {
				$this->error ( "非法修改！" );
			}
		}
		$this->assign ( "code", $code );
		$this->display ();
	}
	
	public function uppwdSub() {
		$newpwd = $this->_post('newpwd');
		$repwd = $this->_post('repwd');
		$code = $this->_post('code');
		if (empty ( $_POST ['newpwd'] )) {
			$_SESSION ['verify'] = "";
			$this->error ( '新密码必须！' );
		}
		if (strlen ( $_POST ['newpwd'] ) < 6) {
			$_SESSION ['verify'] = "";
			$this->error ( '密码不少于6个字符' );
		}
		if (empty ( $_POST ['repwd'] )) {
			$_SESSION ['verify'] = "";
			$this->error ( '确认密码必须！' );
		}
		if (empty ( $_POST ['code'] )) {
			$_SESSION ['verify'] = "";
			$this->error ( '参数有误！' );
		}
		if (empty ( $_SESSION ['verify'] ) || $_SESSION ['verify'] != md5 ( strtoupper ( $_POST ['authcode'] ) )) {
			$_SESSION ['verify'] = "";
			$this->error ( '验证码错误！' );
		}
		if (! empty ( $newpwd ) && ! empty ( $repwd ) && $newpwd === $repwd) {
			$res = ResetpwdClient::getOneAtByCode ( $code );
			if ($res) {
				$account = AccountsClient::checkEmail ( $res ['email'] );
				$uid = $account ['id'];
				//获取加密后的密码
				import ( "@.Client.AuthCodeClient" );
				$sha1Pwd = AuthCodeClient::makepass ( $newpwd, $account ['salt'] );
				//更新accounts表的密码
				$uppwd = AccountsClient::upPwd ( $uid, $sha1Pwd );
				//更新reset_passwords表的修改密码记录
				$upRes = ResetpwdClient::upResetpwd ( $code );
				if ($uppwd && $upRes) {
					$this->assign ( "jumpUrl", __APP__ . "/Login/login/" );
					$this->success ( "重置密码成功！" );
				} else {
					$this->assign ( "jumpUrl", __URL__ . "/index/" );
					$this->error ( "重置密码失败！" );
				}
			}
		}
		$this->error ( "非法修改！" );
	}
	
	public function verify() {
		$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : 'gif';
		import ( "ORG.Util.Image" );
		Image::buildImageVerify ( 4, 1, $type, 76, 36 );
	}
	
	public function authcode(){
		if (empty ( $_SESSION ['verify'] ) || $_SESSION ['verify'] != md5 ( strtoupper ( $_REQUEST ['authcode'] ) )) {
			$this->ajaxReturn("error",'error',0);
		}
		$this->ajaxReturn("ok",'ok',1);
	}

}