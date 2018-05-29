<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Gionee_Dao_RecsiteType
 * @author tiansh
 *
 */
class Gionee_Dao_ElifeServerImages extends Common_Dao_Base {
	protected $_name = '3g_elife_images';
	protected $_primary = 'id';

	/**
	 *
	 * @return multitype:
	 */
	public function deleteImgs($elife_id) {
		$sql = sprintf('DELETE  FROM %s WHERE elife_id = %d', $this->getTableName(), $elife_id);
		return Db_Adapter_Pdo::execute($sql, array(), true);
	}
}