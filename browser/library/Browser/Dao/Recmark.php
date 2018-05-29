<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Browser_Dao_RecMark
 * @author tiansh
 *
 */
class Browser_Dao_Recmark extends Common_Dao_Base{
	protected $_name = 'browser_recmark';
	protected $_primary = 'id';


	/**
	 * 
	 */
	public function getCanUseRecmarks($model_id) {
		$sql = sprintf('SELECT * FROM %s WHERE status = 1 AND model_id = %s ORDER BY sort DESC, id ASC',$this->getTableName(), $model_id);
		return $this->fetcthAll($sql);
	}
	
	public function getAllRecmarks() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 ORDER BY sort DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}