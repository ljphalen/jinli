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
			if(stripos($e->getMessage(),"active transaction")){
				return false;
			}else{
				//throw new Exception("事务异常");
				return false;
			}
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
}