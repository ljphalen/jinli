<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据) 游戏大厅用户数据
 * Client_Dao_SplitBase
 * @author liyf
 *
 */
class Client_Dao_SplitBase {
	
	/**
	 * 默认db
	 * @var string
	 */
	public $adapter = 'default';
	
	/**
	 *
	 * 构造函数
	 */
	public function __construct() {
	    $this->initAdapter();
	}
	
	/**
	 * @throws Exception
	 */
	public function initAdapter() {
	    $adapter = $this->adapter . 'Adapter';
	    if ($adapter != Db_Adapter_Pdo::getAdaterName()) {
	        Db_Adapter_Pdo::setAdapter($adapter);
	    }
	}
}
