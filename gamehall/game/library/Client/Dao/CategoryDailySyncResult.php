<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据同步)每天同步，一天一条日志
 * Client_Dao_CategoryDailySyncResult
 * @author liyf
 *
 */
class Client_Dao_CategoryDailySyncResult extends Common_Dao_Base {
	protected $_name = 'category_sync_result_daily';
	protected $_primary = 'id';
	public $adapter = 'STATISTICS';
}