<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Admin_Dao_User
 * @author rainkid
 *
 */
class Admin_Dao_User extends Common_Dao_Base{
	protected $_name = 'admin_user';
	protected $_primary = 'uid';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getUser($uid) {
		return self::get(intval($uid));
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
                $sql = "select uid, ucenter_uid, username, "
                        . "password, hash, groupid, status, "
                        . " admin_type, nick_name, sex, icon "
                        . "from admin_user "
                        . "where username = '$username'";
                $user =  Db_Adapter_Pdo::fetch($sql);
		return $user;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $email
	 */
	public function getByEmail($email) {
		if (!$email) return false;
		return self::getBy(array('email'=>$email));
	}

	/**
	 * get user by uid
	 * @param $ [description]
	 */
	public function getByUcid($uid){
		if(!$uid) return false;
		return self::getBy(array('ucenter_uid'=>$uid));
	}
}
