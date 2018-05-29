<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Account_Dao_User
 * @author fanch
 *
*/
class Account_Dao_User extends Common_Dao_Base{
	protected $_name = 'game_user';
	protected $_primary = 'id';
	
	public function getUserInfoList($start = 0, $limit = 10, $params = array()) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sql = sprintf('SELECT game_user_info.*, game_user.* FROM `game_user` left join game_user_info on game_user.uuid=game_user_info.uuid WHERE %s LIMIT %d,%d', $where, intval($start), intval($limit));
	    return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	public function coutUserInfoList($params = array()) {
	    $where = Db_Adapter_Pdo::sqlWhere($params);
	    $sql = sprintf('SELECT count(game_user_info.uuid) FROM `game_user` left join game_user_info on game_user.uuid=game_user_info.uuid WHERE %s', $where);
        return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
}