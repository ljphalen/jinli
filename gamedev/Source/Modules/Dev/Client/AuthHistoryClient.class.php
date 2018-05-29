<?php
/**
 * 用户帐户认证信息修改历史的逻辑类
 *
 * @name AuthHistoryClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */

class AuthHistoryClient extends AuthHistoryModel {
	
	private static $daoObj;
	
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "AuthHistory" );
		}
		return self::$daoObj;
	}
	
	public static function createAuthHistory($data){
		$data['created_at'] = currentDate();
		return self::dao()->addAuthHistory($data);
	}
	
	public static function updateAuthHistory($where,$data){
		$data['updated_at'] = currentDate();
		return self::dao()->editAuthHistory($where,$data);
	}
	
	public static function fetchAuthHistory($where,$field){
		return self::dao()->getAuthHistory($where,$field);
	}
}
?>