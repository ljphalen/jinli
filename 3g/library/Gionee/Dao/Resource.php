<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Dao_Resource extends Common_Dao_Base {
	protected $_name = '3g_resource';
	protected $_primary = 'id';


	/**
	 *
	 * @return multitype:
	 */
	public function getCanUseResources() {
		$sql = sprintf('SELECT * FROM %s WHERE 1 AND status = 1 ORDER BY sort DESC, id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}

	/**
	 *
	 * @param array $ids
	 * @return multitype:
	 */
	public function getListByIds($ids) {
		$sql = sprintf('SELECT * FROM %s WHERE id in %s  AND status = 1', $this->getTableName(), Db_Adapter_Pdo::quoteArray($ids));
		return $this->fetcthAll($sql);
	}
}
