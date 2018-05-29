<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_QiiFileKernel extends Common_Service_Base{
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
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
	 */
	public static function addKernel($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
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
	 * @param unknown_type $id
	 */
	public static function updateKernel($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
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
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -401);
			
			list(, $list) = self::getsBy(array('scene_code'=>$data['sceneCode'], 'kernel_code'=>$data['kernelCode']));
				
			if($list) {
				$del_ret = self::deleteBy(array('scene_code'=>$data['sceneCode'], 'kernel_code'=>$data['kernelCode']));
			}
				
			//分类
			$kernel_data = array();
			$kernel_data['scene_code'] = $data['sceneCode'];
			$kernel_data['kernel_code'] = $data['kernelCode'];
			$kernel_data['total_size'] = $data['totalSize'];
			$kernel_data['res_version'] = $data['resVersion'];
			$kernel_data['accept_kernel'] = $data['acceptKernel'];
			$kernel_data['create_time'] = strtotime($data['createTime']);
			$kernel_data['update_time'] = strtotime($data['updateTime']);
	
			//add
			$type_ret = self::addKernel($kernel_data);
			if (!$type_ret) throw new Exception("add sencekernel fail.", -420);
	
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
	
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($kernel_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_kernel_error.log");
			return false;
		}
	}
	
	/**
	 * add
	 * @param array $data
	 */
	public static function addBatch($data){
		if (!is_array($data)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -301);
			
			//分类
			$kernel_data = array();
			foreach ($data as $key=>$value) {
				list(, $list) = self::getsBy(array('scene_code'=>$value['sceneCode'], 'kernel_code'=>$value['kernelCode']));
					
				if($list) {
					$del_ret = self::deleteBy(array('scene_code'=>$value['sceneCode'], 'kernel_code'=>$value['kernelCode']));
				}
					
				$kernel_data[$key]['id'] = '';
				$kernel_data[$key]['scene_code'] = $value['sceneCode'];
				$kernel_data[$key]['kernel_code'] = $value['kernelCode'];
				$kernel_data[$key]['total_size'] = $value['totalSize'];
				$kernel_data[$key]['res_version'] = $value['resVersion'];
				$kernel_data[$key]['accept_kernel'] = $value['acceptKernel'];
				$kernel_data[$key]['create_time'] = strtotime($value['createTime']);
				$kernel_data[$key]['update_time'] = strtotime($value['updateTime']);
			}			
				
			//add
			$type_ret = self::batchAdd($kernel_data);
			if (!$type_ret) throw new Exception("add sencekernel fail.", -320);
	
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
	
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($kernel_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_kernel_error.log");
			return false;
		}
	}
	
	/**
	 * add
	 * @param array $data
	 */
	public static function update($data){
		if (!is_array($data)) return false;
	
		$label = self::getBy(array('scene_code'=>$data['sceneCode'], 'kernel_code'=>$data['kernelCode']));
		if(!$label) return false;
	
		//分类
		$laebl_data = array(
				'total_size' => $data['totalSize'],
				'res_version' => $data['resVersion'],
				'accept_kernel' => $data['acceptKernel'],
				'create_time' => strtotime($data['createTime']),
				'update_time' => strtotime($data['updateTime']),
		);
		return self::updateKernel($laebl_data, $label['id']);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['kernel_code'])) $tmp['kernel_code'] = intval($data['kernel_code']);
		if(isset($data['total_size'])) $tmp['total_size'] = intval($data['total_size']);
		if(isset($data['scene_code'])) $tmp['scene_code'] = intval($data['scene_code']);
		if(isset($data['res_version'])) $tmp['res_version'] = intval($data['res_version']);
		if(isset($data['accept_kernel'])) $tmp['accept_kernel'] = intval($data['accept_kernel']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_QiiFileKernel
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_QiiFileKernel");
	}
}
