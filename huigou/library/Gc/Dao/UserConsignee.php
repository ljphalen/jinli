<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author lichanghua
 *
 */
class Gc_Dao_UserConsignee extends Common_Dao_Base{
	protected $_name = 'gc_user_consignee';
	protected $_primary = 'id';		
		
	
	/**
	 *根据用户id查询收货地址
	 * @param int $user_id
	 * @return array:
	 */
	public function getListByUserId($user_id) {
		$sql = sprintf('SELECT * FROM %s WHERE user_id = %s ORDER BY id DESC', $this->_name, Db_Adapter_Pdo::quote($user_id));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}