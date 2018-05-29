<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Dao_H5HomeAutoSave extends Common_Dao_Base {
	protected $_name = '';
	protected $_primary = 'tmp_id';
	
	public function changeTableName($name) {
	    $this->_name = $name;
	    return $this;
	}
	
	/**
	 * 任意字段的多插入,存在即更新
	 *  @param array $data
	 */
	public function mutiReplaceInsert($data, $setName) {
	    if (!is_array($data)) return false;
	    $sql = sprintf('INSERT INTO %s %s VALUES %s ON DUPLICATE KEY UPDATE %s',$this->getTableName(),Db_Adapter_Pdo::sqlKey($data),Db_Adapter_Pdo::quoteMultiArray($data), $this->replaceSql($setName));
	    return Db_Adapter_Pdo::execute($sql);
	}
	
	public function mutiNoUpdateReplaceInsert($data) {
	    if (!is_array($data)) return false;
	    $sql = sprintf('REPLACE INTO %s %s VALUES %s',$this->getTableName(),Db_Adapter_Pdo::sqlKey($data),Db_Adapter_Pdo::quoteMultiArray($data));
	    return Db_Adapter_Pdo::execute($sql);
	}
	
	public function replaceInsert($data) {
	    if (!is_array($data)) return false;
	    $sql = sprintf('REPLACE INTO %s SET %s',$this->getTableName(), Db_Adapter_Pdo::sqlSingle($data));
	    return Db_Adapter_Pdo::execute($sql);
	}
	
	public function replaceSql($setName) {
	    foreach($setName as $key => $value) {
	        $sql[] = '`'.$value.'` = VALUES('.$value.')';
	    }
	    return implode(',', $sql);
	}
	
}