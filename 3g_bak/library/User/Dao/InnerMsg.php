<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
//站内信
class  User_Dao_InnerMsg  extends Common_Dao_Base
{
	protected $_name = 'user_inner_msg';
	protected $_primary = 'id';
}