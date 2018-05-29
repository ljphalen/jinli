<?php
/**
 * 验证数据存取类，客户端使用类
 *
 * @name SessionClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2012-04-23 14:27:30
 */

class SessionClient extends SessionModel {
	protected static $_dao = null;
	/**
	 * 得到数据存储对象
	 *
	 * @return QModels_Sso_Session_Dao
	 */
	private static function dao() {
		if (! isset ( self::$_dao )) {
			self::$_dao = D ( "Session" );
		}
		return self::$_dao;
	}
	/**
	 * 得到session全部信息
	 *
	 * @param string $sessionkey
	 * @return array|false sesskey,uid,expire,postid,logoutid,createtime,createip
	 */
	public static function getSessionC($sessionkey) {
		return self::dao ()->getSession ( $sessionkey );
	}
	/**
	 * 得到session全部信息
	 *
	 * @param integer $uid
	 * @param string $ip
	 * @param integer $expire
	 * @return string|false
	 */
	public static function createSessionC($uid, $ip, $expire = 0) {
		return self::dao ()->createSession ( $uid, $ip, $expire );
	}
	/**
	 * 删除session
	 *
	 * @param string $sessionkey
	 * @return boolean
	 */
	public static function deleteSessionC($sessionkey) {
		return self::dao ()->deleteSession ( $sessionkey );
	}
	/**
	 * 更新sessoin信息
	 *
	 * @param string $sessionkey
	 * @param array $params
	 * @return boolean
	 */
	public static function updateSessionC($sessionkey, $params) {
		return self::dao ()->updateSession ( $sessionkey, $params );
	}
	/**
	 * 得到用户的所有session
	 * 
	 * @param integer $uid
	 * @return array|false sesskey,uid,expire,postid,logoutid,createtime,createip
	 */
	public static function getUserSessionsC($uid) {
		$ret = self::dao ()->getUserSessions ( $uid );
		return empty ( $ret ) ? false : $ret;
	}
}