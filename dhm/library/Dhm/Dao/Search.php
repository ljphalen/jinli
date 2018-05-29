<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Dhm_Dao_Search
 * @author terry
 *
 */
class Dhm_Dao_Search extends Common_Dao_Base{
    protected $_name = 'dhm_search_log';
    protected $_primary = 'id';
    public $hash;

    public function getTableName() {
//        return $this->_name."_".(($this->hash % 10) + 1);
        return $this->_name . "_" . (intval(fmod($this->hash, 10)) + 1);
    }
}
