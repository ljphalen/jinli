<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅排行榜 -- 下载最多(旧)
 * Client_Dao_BIRank
 * @author lichanghua
 *
 */
class Client_Dao_BIRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_times';
	protected $_primary = '';
	public $adapter = 'BI';
}