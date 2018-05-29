<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Ad
 * @author rainkid
 *
 */
class Game_Dao_BIRank extends Common_Dao_Base{
	protected $_name = 'dlv_game_dl_times';
	protected $_primary = 'id';
	public $adapter = 'BI';
}