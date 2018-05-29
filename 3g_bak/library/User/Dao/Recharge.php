<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//第三方充值返回结果的日志，方便对账用
class User_Dao_Recharge  extends Common_Dao_Base {
	protected $_name = 'user_recharge_log';
	protected $_primary = 'id';
}