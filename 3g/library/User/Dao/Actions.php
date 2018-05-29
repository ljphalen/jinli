<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//订单操作记录
class User_Dao_Actions extends Common_Dao_Base {
		protected $_name = 'user_order_action';
		protected $_primary = 'id';
}