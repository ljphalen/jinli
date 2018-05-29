<?php
/**
 * 用户密码算法生成类
 *
 * @name AuthCodeClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AuthCodeClient {
	private static $authSiteKey = "123ee806ac456c7c9bc1d9e826ce6f7f31397259";
	private static $stretches = 10;
	public static function makepass($password, $salt = '') {
		$digest = self::$authSiteKey;
		for($i = 0; $i < self::$stretches; $i ++) {
			$arr = array (
					$digest,
					$salt,
					$password,
					self::$authSiteKey 
			);
			$digest = self::secureDigest ( $arr );
		}
		return $digest;
	}
	public static function secureDigest($args = array()) {
		$args = implode ( "--", $args );
		return sha1 ( $args );
	}
}

?>