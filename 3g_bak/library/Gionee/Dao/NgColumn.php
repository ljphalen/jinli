<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author tiger
 *        
 */
class Gionee_Dao_NgColumn extends Common_Dao_Base {
	protected $_name = '3g_ng_column';
	protected $_primary = 'id';
	
	/**
	 * 二级分类
	 */
	public function getParentList() {
		$sql = sprintf('SELECT * FROM %s WHERE model_id != 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 二级分类
	 */
	public function getParentByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE root_id = parent_id AND parent_id in %s AND status=1 ORDER BY sort DESC, id DESC', $this->_name, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param
	 *        	getAllChild
	 * @return multitype:
	 */
	public function getAllChild() {
		$sql = sprintf('SELECT * FROM %s WHERE model_id != 0 ORDER BY sort DESC, id DESC', $this->_name);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param
	 *        	getAllChild
	 * @return multitype:
	 */
	public function getAllType($model_id) {
		$sql = sprintf('SELECT * FROM %s WHERE model_id = %s ORDER BY sort DESC, id DESC', $this->_name, $model_id);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
}