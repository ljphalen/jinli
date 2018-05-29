<?php
/**
 * 用户帐户详细信息修改审核前临时信息的逻辑类
 *
 * @name InfotempClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class InfotempClient extends InfotempModel {
	private static $daoObj;
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Infotemp" );
		}
		return self::$daoObj;
	}
	public static function createInfoTemp($data) {
		$data ['created_at'] = currentDate ();
		return self::dao ()->addInfoTemp ( $data );
	}
	public static function updateInfoTemp($where, $data) {
		$data ['updated_at'] = currentDate ();
		return self::dao ()->editInfoTemp ( $where, $data );
	}
	public static function fetchInfoTemp($where, $field) {
		return self::dao ()->getInfoTemp ( $where, $field );
	}
}
?>