<?php
/**
 * 开发者认证信息的逻辑类
 *
 * @name AccountauthClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountauthClient extends AccountauthModel {
	private static $daoObj;
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Accountauth" );
		}
		return self::$daoObj;
	}
	public static function createAccAuth($data) {
		$data ['created_at'] = currentDate ();
		return self::dao ()->addAccAuth ( $data );
	}
	public static function upAccAuth($accountId, $data) {
		$data ['updated_at'] = currentDate ();
		return self::dao ()->updateAccAuth ( $accountId, $data );
	}
	public static function fetchAccAuth($accountId) {
		return self::dao ()->getAccAuth ( $accountId );
	}

}
?>