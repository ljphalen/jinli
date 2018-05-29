<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Glog_Dao_FreedlProgress
 * @author fanch
 *
 */
class Glog_Dao_FreedlProgress extends Common_Dao_Base{
	protected $_name = 'freedl_log_progress';
	protected $_primary = 'id';
	public $adapter = 'GLOG'; //$adapter
}
