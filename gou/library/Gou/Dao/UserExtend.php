<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Gou_Dao_UserExtend
 * @author lichanghua
 *
 */
class Gou_Dao_UserExtend extends Common_Dao_Base{
	protected $_name = 'gou_user_extend';
	protected $_primary = 'id';
	
	/**
	 *根据用户id查询个人兴趣爱好
	 * @param int $user_id
	 * @return array:
	 */
	public function getUserExtendByUserId($user_id) {
		$sql = sprintf('SELECT * FROM %s WHERE user_id = %s', $this->_name, Db_Adapter_Pdo::quote($user_id));
		return Db_Adapter_Pdo::fetch($sql);
	}
}
