<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_QiiLabel extends Common_Service_Base{

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllLabel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getLabel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateLabel($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteLabel($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAll() {
		return self::_getDao()->deleteAll();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLabel($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAdd($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByIds($ids) {
		if (!count($ids)) return false;
		return self::_getDao()->getByIds($ids);
	}
	
	/**
	 * add 
	 * @param array $data
	 */
	public static function add($data){
		if (!is_array($data)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
	
			//分类
			$laebl_data = array();
			foreach ($data as $key=>$value) {
				$laebl_data[$key]['id'] = '';
				$laebl_data[$key]['out_id'] = $value['labelCode'];
				$laebl_data[$key]['name'] = $value['labelName'];
				$laebl_data[$key]['sort'] = $value['labelSort'];
				$laebl_data[$key]['img'] = $value['labelImage'];
				$laebl_data[$key]['create_time'] = strtotime($value['createTime']);
				$laebl_data[$key]['update_time'] = strtotime($value['updateTime']);
			}
			//del
			list(, $list) = self::getAllLabel();
			if($list) {
				$del_ret = self::deleteAll();
				if (!$del_ret) throw new Exception("del labels fail.", -210);
			}
			
			//add
			$type_ret = self::batchAdd($laebl_data);
			if (!$type_ret) throw new Exception("add laebls fail.", -220);
				
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
	
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($laebl_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/laebl_error.log");
			return false;
		}
	}
	
	/**
	 * add
	 * @param array $data
	 */
	public static function update($data){
		if (!is_array($data)) return false;
		
		$label = self::getBy(array('out_id'=>$data['labelCode']));
		if(!$label) return false;
		
		//分类
		$laebl_data = array(
			'name' => $data['labelName'],
			'sort' => $data['labelImage'],
			'img' => $data['labelSort'],
			'create_time' => strtotime($data['createTime']),
			'update_time' => strtotime($data['updateTime']),
		);
		return self::updateLabel($laebl_data, $label['id']);
	}
	
	
	/**
	 * add
	 * @param array $data
	 */
	public static function del($data){
		if (!is_array($data)) return false;
	
		$label = self::getBy(array('out_id'=>$data['labelCode']));
		if(!$label) return false;
		return self::deleteLabel($label['id']);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['out_id'])) $tmp['out_id'] = $data['out_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_FileType
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_QiiLabel");
	}
}
