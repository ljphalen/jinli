<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_Product extends Common_Dao_Base {
	protected $_name = '3g_product';
	protected $_primary = 'id';

	/**
	 *
	 */
	public function getNewProduct() {
		$sql = sprintf('SELECT * FROM %s WHERE is_new = 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return $this->fetcthAll($sql);
	}

	/**
	 *
	 * @param unknown_type $series
	 * @return multitype:
	 */
	public function getListBySeries($series) {
		$sql = sprintf('SELECT * FROM %s WHERE series_id = %s ORDER BY sort DESC, id DESC', $this->getTableName(), Db_Adapter_Pdo::quote($series));
		return $this->fetcthAll($sql);
	}

	public function getProductByModelId($model_id) {
		$sql = sprintf('SELECT * FROM %s WHERE model_id = %s ', $this->getTableName(), Db_Adapter_Pdo::quote($model_id));
		return Db_Adapter_Pdo::fetch($sql);
	}
}
