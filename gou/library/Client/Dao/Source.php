<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author ryan
 *
 */
class Client_Dao_Source extends Common_Dao_Base{
	protected $_name = 'client_channel_goods_source';
	protected $_primary = 'id';
	public function  dropAll(){
		$query = sprintf('TRUNCATE TABLE %s;',$this->_name);
		return Db_Adapter_Pdo::execute($query);
	}
}