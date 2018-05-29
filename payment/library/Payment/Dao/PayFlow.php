<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 流水帐
 * Payment_Dao_PayFlow
 * @author ljphalen@163.com
 *
 */
class Payment_Dao_PayFlowLog extends Common_Dao_Base{
	protected $_name = 'payflow';
	protected $_primary = 'flowlogid';
}