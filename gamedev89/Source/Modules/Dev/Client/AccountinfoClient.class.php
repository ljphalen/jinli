<?php
/**
 * 用户帐户详细信息的逻辑类
 *
 * @name AccountinfoClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountinfoClient extends AccountinfoModel {
	private static $daoObj;
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Accountinfo" );
		}
		return self::$daoObj;
	}
	public $_validate = array (
			array (
					'name',
					'require',
					'名称必须' 
			),
			array (
					'tel',
					'require',
					'手机必须' 
			) 
	);
	
	// public $_auto = array (array ('created_at', 'time', self::MODEL_INSERT, 'function' ), array ('updated_at', 'time', self::MODEL_UPDATE, 'function' ) );
	public static function createAccInfo($account) {
		$account ['created_at'] = currentDate ();
		return self::dao ()->addAccountInfo ( $account );
	}
	public static function upAccInfo($accountId, $account) {
		$account ['updated_at'] = currentDate ();
		return self::dao ()->updateAccInfo ( $accountId, $account );
	}
	public static function fetchAccInfo($accountId) {
		return self::dao ()->getAccInfo ( $accountId );
	}
	
	/**
	 * 判断开发者的当前审核是否通过
	 *
	 * @param int $accountId        	
	 * @return number 通过
	 *         301: 首次基本信息审核未通过
	 *         303: 修改基本信息审核未通过
	 *         304: 无认证信息
	 *         305: 首次认证信息审核未通过
	 *         306: 没有将要审核的认证信息
	 *         307: 修改认证信息审核未通过)
	 */
	public static function isAudited($accountId) {
		import ( "@.Client.InfotempClient" );
		import ( "@.Client.AccountauthClient" );
		import ( "@.Client.AuthHistoryClient" );
		$info = self::fetchAccInfo ( $accountId );
		if ($info ['audited'] == '5') { // 马甲用户
			return 200;
		}
		if ($info ['status'] != "2")
			return 301; // 首次基本信息审核未通过
		$infotemp = InfotempClient::fetchInfoTemp ( "account_id=$accountId and do_finish=0", "status" );
		if ($infotemp && $infotemp ['status'] != "2")
			return 303; // 修改基本信息审核未通过
		$auth = AccountauthClient::fetchAccAuth ( $accountId );
		if (! $auth)
			return 304; // 无认证信息
		if ($auth ['status'] != "2")
			return 305; // 首次认证信息审核未通过
		$authHistory = AuthHistoryClient::fetchAuthHistory ( "account_id=$accountId and do_finish=0", "status" );
		if (! $authHistory)
			return 306; // 没有将要审核的认证信息
		if ($authHistory ['status'] != "2")
			return 307; // 修改认证信息审核未通过
		return 200;
	}
}
?>