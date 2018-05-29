<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏大厅排行榜 -- 游戏飙升榜
 * Client_Dao_SoaringRank
 *
 */
class Client_Dao_SoaringRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_soaring';
	protected $_primary = 'day_id';
}
