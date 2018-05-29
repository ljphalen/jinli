<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户玩游戏记录
 * User_Dao_GameLog
 * @author wupeng
 */
class User_Dao_GameLog extends Common_Dao_Base {
	protected $_name = 'game_user_game_log';
	protected $_primary = 'uuid,game_id';
}