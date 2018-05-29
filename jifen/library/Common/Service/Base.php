<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Common_Service_Base {
	/**
	 * beginTransaction
	 * @return boolean
	 */
	public static function beginTransaction() {
		if (Db_Adapter_Pdo::getAdapter()->inTransaction()) return true;
		try{
			return Db_Adapter_Pdo::getAdapter()->beginTransaction();
		}catch(Exception $e){
			return false;
		}
	}
	
	/**
	 * rollback
	 * @return boolean
	 */
	public static function rollBack() {
		return Db_Adapter_Pdo::getAdapter()->rollBack();
	}
	
	/**
	 * commit
	 * @return boolean
	 */
	public static function commit() {
		return Db_Adapter_Pdo::getAdapter()->commit();
	}
	
	/**
	 * @desc生成签名
	 *
	 * @param $paramArr：api参数数组
	 * @return $sign
	 */
	public function createSign($params,  $auth_key) {
		Common::log($params, 'sign.log');
		ksort($params);
		reset($params);
		$sign = http_build_query($params) . $auth_key;
		Common::log($sign, 'sign.log');
		$sign = md5($sign);
		Common::log($sign, 'sign.log');
		return $sign;
	}
	
}