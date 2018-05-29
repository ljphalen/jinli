<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Dao_Order extends Common_Dao_Base{
	protected $_name = 'pay_order';
	protected $_primary = 'order_id';

}