<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据)游戏大厅 -- 游戏推荐
 * Client_Dao_BIRecommend
 * @author rainkid
 *
 */
class Client_Dao_BIRecommend extends Common_Dao_Base{
	protected $_name = 'DLV_GAME_RECOMEND';
	protected $_primary = '';
	public $adapter = 'BI';
}