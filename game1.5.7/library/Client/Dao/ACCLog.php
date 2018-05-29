<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 登录日志 -- 联运游戏登陆日志
 * Client_Dao_ACCLog
 * @author lichanghua
 *
 */
class Client_Dao_ACCLog extends Common_Dao_Base{
	protected $_name = 'log_acc_auth';
	protected $_primary = 'LogTime';
	public $adapter = 'ACCLOG';
}