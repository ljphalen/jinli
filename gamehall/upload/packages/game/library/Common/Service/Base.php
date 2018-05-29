<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Common_Service_Base {
	private static $hasBeginTrans = false; 	// 是否在事务中
	private static $refreshList = array();	// 刷新逻辑列表
	 
	/**
	 * beginTransaction
	 * @return boolean
	 */
	public static function beginTransaction() {
		try{
			self::$hasBeginTrans = true;
			return Db_Adapter_Pdo::getPDO()->beginTransaction();
		}catch(Exception $e){
			self::$hasBeginTrans = false;
			if(stripos($e->getMessage(),"active transaction")){
				return false;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * rollback
	 * @return boolean
	 */
	public static function rollBack() {
		$result = Db_Adapter_Pdo::getPDO()->rollBack();
		self::_runRefreshFuncs();
		return $result;
	}
	
	/**
	 * commit
	 * @return boolean
	 */
	public static function commit() {
		$result = Db_Adapter_Pdo::getPDO()->commit();
		self::_runRefreshFuncs();
		return $result;
	}
	
	/**
	 * 现网mysql配置的事务类型是READ-COMMITTED.要保证redis的sql缓存正确,需要
	 * 在事务提交或回滚后,刷新(失效)涉及的sql缓存.
	 * 
	 * 此函数为登记需要执行的刷新逻辑,并确保不重复登记.
	 * 
	 * @param unknown $class
	 * @param unknown $method
	 * @param unknown $args
	 */
	public static function recordRefreshFunc($class, $method, $args=array()) {
		if (!self::$hasBeginTrans) return;
		
		$value = array(
				'class' => $class, 
				'method' => $method, 
				'args' => $args
		);
		$key = json_encode($value);
		self::$refreshList[$key] = $value;
	}
	
	/**
	 * 事务提交或回滚后,执行所有刷新逻辑
	 */
	private static function _runRefreshFuncs() {
		foreach (self::$refreshList as $key => $value) {
			call_user_func_array(array($value['class'], $value['method']), $value['args']);
		}
		self::$hasBeginTrans = false;
		self::$refreshList = array();
	}
	
}