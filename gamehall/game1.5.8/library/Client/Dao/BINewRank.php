<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 最新榜
 * Client_Dao_BINewRank
 * @author lichanghua
 *
 */
class Client_Dao_BINewRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_new';
	protected $_primary = '';
	public $adapter = 'BI';
}
