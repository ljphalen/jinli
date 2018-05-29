<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Widget_Dao_column extends Common_Dao_Base {
	protected $_name = 'widget_column';
	protected $_primary = 'id';

	/**
	 * 指修改新闻显示状态
	 * @param array $ids
	 */
	public function updateStatusByIds($ids, $status) {
		$sql = sprintf('UPDATE %s SET status = %s WHERE id in %s', $this->getTableName(), $status, Db_Adapter_Pdo::quoteArray($ids));
		return Db_Adapter_Pdo::execute($sql);
	}
}
