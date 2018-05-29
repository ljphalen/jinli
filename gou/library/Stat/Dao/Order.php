<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Stat_Dao_Order extends Common_Dao_Base{
	protected $_name = 'gou_order_report';
	protected $_primary = 'id';
    public $adapter = 'BI';
}
