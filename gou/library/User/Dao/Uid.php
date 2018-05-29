<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Fanli_Dao_User
 * @author tiansh
 *
 */
class User_Dao_Uid extends Common_Dao_Base{
	protected $_name = 'gou_user_uid';
	protected $_primary = 'id';
    public $hash;

    public function getTableName() {
//        return $this->_name."_".(($this->hash % 10) + 1);
        return $this->_name . "_" . (intval(fmod($this->hash, 10)) + 1);
    }
}
