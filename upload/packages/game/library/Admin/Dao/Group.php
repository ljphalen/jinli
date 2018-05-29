<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_Group
 * @author rainkid
 *
 */
class Admin_Dao_Group extends Common_Dao_Base {
	protected $_name = 'admin_group';
	protected $_primary = 'groupid';
	
	/**
	 * 
	 * 获取所有记录
	 */
	public function getGroups() {
		return array(self::count(), self::getAll());
	}
	
	/**
	 * 
	 * 增加一个用户组
	 * @param array $data
	 */
	public function addGroup($data) {
		if (!is_array($data)) return false;
		return self::insert($data);
	}
	
	/**
	 * 
	 * 删除一个用户组
	 * @param int $groupid
	 */
	public function deleteGroup($groupid) {
		return self::delete(intval($groupid));
	}
}
