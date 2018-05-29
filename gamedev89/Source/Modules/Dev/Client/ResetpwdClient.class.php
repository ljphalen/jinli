<?php

class ResetpwdClient extends ResetpwdModel {
	private static $daoObj;
	
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Resetpwd" );
		}
		return self::$daoObj;
	}
	
	public static function addResetpwd($accountId, $email) {
		return self::dao ()->createResetpwd ( $accountId, $email );
	}
	
	public static function upResetpwd($code) {
		return self::dao ()->updateReasetpwd ( $code );
	}
	
	public static function getOneAtByCode($code) {
		return self::dao ()->getResetAtByCode ( $code );
	}

}

?>