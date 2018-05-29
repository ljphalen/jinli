<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Browser_Dao_Redirect extends Common_Dao_Base{
	protected $_name = 'browser_redirect';
	protected $_primary = 'id';
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 */
	public function getRedictByUrl($url) {
		$sql = sprintf('SELECT * FROM %s WHERE md5_url = "%s"', $this->getTableName(), $url);
		return Db_Adapter_Pdo::fetch($sql);
	}
}

