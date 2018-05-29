<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 网游榜
 * Client_Dao_BIOlgRank
 * @author lichanghua
 *
 */
class Client_Dao_BIOlgRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_rank_olg';
	protected $_primary = '';
	public $adapter = 'BI';
}
