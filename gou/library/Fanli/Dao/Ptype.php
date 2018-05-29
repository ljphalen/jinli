<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Fanli_Dao_Ptype
 * @author tiansh
 *
 */
class Fanli_Dao_Ptype extends Common_Dao_Base{
	protected $_name = 'fanli_ptype';
	protected $_primary = 'id';
	
	/**
	 *
	 * 查询所有数据
	 */
	public function getAll() {
		$sql = sprintf('SELECT * FROM %s ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}