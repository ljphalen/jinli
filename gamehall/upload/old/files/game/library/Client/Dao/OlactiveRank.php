<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 网游活跃榜
 * Client_Dao_OlactiveRank
 *
 */
class Client_Dao_OlactiveRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_online_game_active';
	protected $_primary = 'day_id';
}
