<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Resource_Dao_Type
 * @author lichanghau
 *
 */
class Resource_Dao_Type extends Common_Dao_Base{
	protected $_name = 'game_resource_model';
	protected $_primary = 'id';
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceTypes($start, $limit, $params) {
	$tmp = array();
	    if($params['operators'] == 'b'){
	    	$params['operators'] = 0;
	    }
		if($params['resolution']){
			foreach($params as $key=>$value){
				if($key != 'resolution')  $tmp[$key] = $value;
			}
		}
		if($params['resolution']){
			$where = Db_Adapter_Pdo::sqlWhere($tmp);
			$sql = sprintf('SELECT * FROM %s WHERE  %s AND resolution IN %s  ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $where,Db_Adapter_Pdo::quoteArray($params['resolution']), $start, $limit);
		} else {
			unset($params['resolution']);
			$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
			$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		}
		return $this->fetcthAll($sql);
	}
	
	/**
	 * 
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceTypeCount($params) {
	    $tmp = array();
	    if($params['operators'] == 'b'){
	    	$params['operators'] = 0;
	    }
		if($params['resolution']){
			foreach($params as $key=>$value){
				if($key != 'resolution')  $tmp[$key] = $value;
			}
		}
		if($params['resolution']){
			$where = Db_Adapter_Pdo::sqlWhere($tmp);
			$sql = sprintf('SELECT count(*) FROM %s WHERE  %s AND resolution IN %s',$this->getTableName(), $where,Db_Adapter_Pdo::quoteArray($params['resolution']));
		} else {
			unset($params['resolution']);
			$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
			$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		}
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	public function getSortAll() {
		$sql = sprintf('SELECT * FROM %s ORDER BY id DESC', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceGameTypes($start, $limit, $params) {
		$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
		$sql = sprintf('SELECT * FROM %s WHERE  %s ORDER BY sort DESC,id DESC LIMIT %d,%d',$this->getTableName(), $where, $start, $limit);
		return $this->fetcthAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $start
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public function getCanUseResourceGameTypeCount($params) {
			$where = count($params) ? Db_Adapter_Pdo::sqlWhere($params) : 1;
			$sql = sprintf('SELECT count(*) FROM %s WHERE  %s',$this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
}
