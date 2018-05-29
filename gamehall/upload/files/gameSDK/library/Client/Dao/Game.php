<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Game_Dao_Client
 * @author rainkid
 *
 */
class Client_Dao_Game extends Common_Dao_Base{
	protected $_name = 'game_client_games';
	protected $_primary = 'id';
	protected $_resource_game_id = 'resource_game_id';
	
}