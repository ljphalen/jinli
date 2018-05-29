<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Service_MsgMap {	
	static $mapStatus  = array( 0 => '未读'  , 1 => '已读');

	
	private static function _getDao() {
		return Common::getDao('Game_Dao_MsgMap');
	}
}
