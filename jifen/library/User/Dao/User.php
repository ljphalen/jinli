<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * User_Dao_User
 * @author tiansh
 *
 */
class User_Dao_User extends Common_Dao_Base{
	protected $_name = 'u_user';
	protected $_primary = 'id';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getUser($id) {
		return self::get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getAllUser() {
		return array(self::count(), self::getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $username
	 */
	public function getByUserName($username) {
		if (!$username) return false;
		return self::where(array('username'=>$username));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $username
	 */
	public function getByOutUid($out_uid) {
		if (!$out_uid) return false;
		return self::where(array('out_uid'=>$out_uid));
	}
}
