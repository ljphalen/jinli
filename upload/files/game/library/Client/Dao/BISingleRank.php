<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 单机榜
 * Client_Dao_BISingleRank
 * @author lichanghua
 *
 */
class Client_Dao_BISingleRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_single';
	protected $_primary = '';
	public $adapter = 'BI';
}
