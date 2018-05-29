<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_IdxFileLabel extends Common_Service_Base{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAll() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxFileLabel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteByFileId($file_id) {
		if (!$file_id) return false;
		return self::_getDao()->deleteByFileId(intval($file_id));
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
	public static function getByFileId($file_id) {
		if (!$file_id) return false;
		return self::_getDao()->getByFileId($file_id);
	}
	
	/**
	 *
	 * @param unknown_type $parent_id
	 * @return multitype:
	 */
	public static function getByLabelId($type_id) {
		if (!$type_id) return false;
		return self::_getDao()->getByLabelId($type_id);
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getByFileIds($file_ids) {
		if (!is_array($file_ids)) return false;
		return self::_getDao()->getByFileIds($file_ids);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params) {
		if (!is_array($params)) return false;
		$ret = self::_getDao()->getsBy($params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
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
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getFileIdByFiletypeIds($file_id, $type_ids) {
		if (!intval($file_id) || !is_array($type_ids)) return false;
		return self::_getDao()->getFileIdByFiletypeIds($file_id, $type_ids);
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
			
			$out_id = $data[0]['sceneCode'];
			list(, $list) = self::getsBy(array('file_id'=>$out_id));
			
			if($list) {
				//del
				$del_ret = self::deleteByFileId($out_id);
				if (!$del_ret) throw new Exception("del sencelabels fail.", -210);
			}
			
			//分类
			$laebl_data = array();
			foreach ($data as $key=>$value) {
				$laebl_data[$key]['id'] = '';
				$laebl_data[$key]['out_id'] = $value['id'];
				$laebl_data[$key]['file_id'] = $value['sceneCode'];
				$laebl_data[$key]['label_id'] = $value['labelCode'];
				$laebl_data[$key]['create_time'] = strtotime($value['createTime']);
				$laebl_data[$key]['update_time'] = strtotime($value['updateTime']);
			}			
				
			//add
			$type_ret = self::batchAdd($laebl_data);
			if (!$type_ret) throw new Exception("add sencelaebls fail.", -220);
	
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['out_id'])) $tmp['out_id'] = $data['out_id'];
		if(isset($data['file_id'])) $tmp['file_id'] = $data['file_id'];
		if(isset($data['label_id'])) $tmp['label_id'] = $data['label_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_IdxFileLabel
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_IdxFileLabel");
	}
}
