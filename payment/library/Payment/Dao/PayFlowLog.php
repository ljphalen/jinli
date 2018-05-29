<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * H5 广告DAO 文件
 * Payment_Dao_PayFlowLog
 * @author ljphalen@163.com
 *
 */
class Payment_Dao_PayFlowLog extends Common_Dao_Base{
	protected $_name = 'log_payflow';
	protected $_primary = 'flowlogid';
}