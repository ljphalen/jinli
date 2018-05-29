<?php
/**
 * 验证客户端类
 *
 * @name AuthClient.class.php
 * @version 1.0 (2013-12-15 08:27:30)
 * @author xiayang(c61811@163.com)
 * @since 1.0
 */
class AuthClient {
	const COOKIE_MERGED = 'dev_gionee';
	const PERSIST_LIFE = 3110400; // 一年
	const AUTH_EXPIRE = 36000; // 十个小时
	const AUTO_LOGIN_EXPIRE = 2592000; // 自动登录保存30天
	
	/**
	 * 设置浏览器端用户cookie
	 *
	 * @param integer $uid
	 *        	用户编码
	 * @param string $sessionkey
	 *        	sessionkey
	 * @param boolean $saveAccount        	
	 * @return boolean
	 */
	public static function setAuthedCookie($uid, $sessionkey, $saveAccount = true, $autoLogin = false, $login = null) {
		$expire = ! $saveAccount ? time () + self::AUTH_EXPIRE : (time () + self::PERSIST_LIFE);
		$authExpire = ! $autoLogin ? time () + self::AUTH_EXPIRE : (time () + self::AUTO_LOGIN_EXPIRE);
		$values = $uid . ',' . $sessionkey . ',' . ($saveAccount ? 'y' : 'n') . ',' . $authExpire . ',' . ($autoLogin ? "y" : "n");
		cookie ( "login", $login, $expire );
		return cookie ( self::COOKIE_MERGED, $values, array (
				'expire' => $authExpire 
		) );
	}
	/**
	 * 得到浏览器端用户cookie
	 *
	 * @return integer | false 用户ID
	 */
	public static function getAuthedCookie() {
		return ($a = self::getAuthedCookieArray ()) ? $a ['uid'] : false;
	}
	/**
	 * 得到浏览器端用户cookie's sessionkey
	 *
	 * @return integer | false 用户ID
	 */
	public static function getAuthedCookieSession() {
		return ($a = self::getAuthedCookieArray ()) ? $a ['magic'] : false;
	}
	/**
	 * 得到是否记住自己
	 *
	 * @return boolean
	 */
	public static function getCookiePersist() {
		return ($a = self::getAuthedCookieArray ()) ? $a ['persist'] : false;
	}
	/**
	 * 得到是否自动登录
	 *
	 * @return boolean
	 */
	public static function getCookieAutoLogin() {
		return ($a = self::getAuthedCookieArray ()) && ($a ['autoLogin'] !== 'n') ? $a ['autoLogin'] : false;
	}
	
	/**
	 * 得到浏览器端COOKIE 验证数组
	 *
	 * @return array | false
	 */
	public static function getAuthedCookieArray() {
		$return = false;
		$cookie = cookie(self::COOKIE_MERGED);
		if (! isset ( $cookie )) {
			APP_DEBUG && Log::write("Log Cookie is NULL", Log::EMERG);
			$return = false;
		} else {
			$values = explode ( ',', $cookie );
			$uid = isset ( $values [0] ) ? ( int ) $values [0] : false;
			$magic = isset ( $values [1] ) ? $values [1] : false;
			$persist = isset ( $values [2] ) ? $values [2] === 'y' : false;
			$authExpire = isset ( $values [3] ) ? intval ( $values [3], 10 ) : time () + self::AUTH_EXPIRE;
			$autoLogin = isset ( $values [4] ) ? $values [4] : false;
			$return = array (
					'uid' => $uid,
					'magic' => $magic,
					'persist' => $persist,
					'authexpire' => $authExpire,
					'autoLogin' => $autoLogin 
			);
		}
		return $return;
	}
	/**
	 * 摧毁客户端用户Cookie
	 *
	 * @return void
	 */
	public static function unsetAuthedCookie() {
		$persist = self::getCookiePersist ();
		$login = $persist ? cookie ( 'login' ) : null;
		$autoLogin = self::getCookieAutoLogin ();
		$cookie = cookie(self::COOKIE_MERGED);
		if (isset ( $cookie )) {
			cookie ( self::COOKIE_MERGED, null );
		}
		// 不保存用户名
		if (! $persist) {
			cookie ( "login", null );
		}
		// 为下次登录保存"记住用户名"和"自动登录"
		self::setAuthedCookie ( 0, 0, $persist, $autoLogin, $login );
	}
	/**
	 * 得到Session中的用户编码
	 *
	 * @return integer false
	 */
	public static function getAuthedUid() {
		$return = false;
		$aAuth = self::getAuthedCookieArray ();
		if ($aAuth) {
			import ( "@.Client.SessionClient" );
			$aSession = SessionClient::getSessionC ( $aAuth ['magic'] );
			if ($aSession) {
				// 验证本地cookie是否过期
				if ($aAuth ['authexpire'] < time ()) {
					return false;
				}
				// 验证远程session是否过期
				if ($aSession ['expire'] && $aSession ['expire'] < time ()) {
					return false;
				}
				if ($aAuth ['autoLogin'] == 'y') {
					$expire = array (
							'expire' => time () + self::AUTO_LOGIN_EXPIRE 
					);
				} else {
					$expire = array (
							'expire' => time () + self::AUTH_EXPIRE 
					);
				}
				SessionClient::updateSessionC ( $aSession ['sesskey'], $expire );
				$login = cookie ( "login" );
				$expire = ! $aAuth ['persist'] ? (time () + self::AUTH_EXPIRE) : (time () + self::PERSIST_LIFE);
				cookie ( "login", $login, $expire );
				$return = $aSession ['uid'];
			} else {
				self::unsetAuthedCookie ();
			}
		}
		return $return;
	}
	/**
	 * 加密存储到浏览器端的cookie内容信息
	 *
	 * @param string $string        	
	 * @return string
	 */
	private static function authCookieCryptEncode($string) {
		$str1 = substr ( $string, 0, 16 );
		$str2 = substr ( md5 ( 'dev.gionee.com' . mt_rand ( 1, 999999 ) ), 0, 8 );
		$str3 = substr ( $string, 16 );
		return $str1 . $str2 . $str3;
	}
	/**
	 * 解密存储到浏览器端的cookie内容
	 *
	 * @param string $string        	
	 * @return string
	 */
	private static function authCookieCryptDecode($string) {
		$str1 = substr ( $string, 0, 16 );
		$str2 = substr ( $string, - 16 );
		$return = $str1 . $str2;
		return strlen ( $return ) == 32 ? $return : '';
	}
}

?>