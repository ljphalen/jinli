<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_QiiFile extends Common_Service_Base{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllFile() {
		return self::_getDao()->getAll();
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getOnlineFiles() {
		return self::_getDao()->getOnlineFiles();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order_by, $sort) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		
		$ret = self::_getDao()->getList($start, $limit, $params, $order_by, $sort);
		$total = self::_getDao()->getCount($params);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getFile($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getPre($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getPre(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNext($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getNext(intval($id));
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateFile($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteFile($id) {
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
	public static function addFile($data) {
		if (!is_array($data)) return false;
		$data['status'] = 1;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * 批量插入
	 * @param array $data
	 */
	public static function batchAdd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		self::_getDao()->mutiInsert($data);
		return true;
	}
	
	/**
	 *
	 * @param array $file_ids
	 * @return multitype:
	 */
	public static function getByIds($ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getByFileIds($ids);
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
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['out_id'])) $tmp['out_id'] = intval($data['out_id']);
		if(isset($data['zh_title'])) $tmp['zh_title'] = $data['zh_title'];
		if(isset($data['en_title'])) $tmp['en_title'] = $data['en_title'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['icon_hd'])) $tmp['icon_hd'] = $data['icon_hd'];
		if(isset($data['icon_micro'])) $tmp['icon_micro'] = $data['icon_micro'];
		if(isset($data['summary'])) $tmp['summary'] = $data['summary'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['down'])) $tmp['down'] = intval($data['down']);
		if(isset($data['author_id'])) $tmp['author_id'] = intval($data['author_id']);
		if(isset($data['author_name'])) $tmp['author_name'] = $data['author_name'];
		
		if(isset($data['keyword'])) $tmp['keyword'] = $data['keyword'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_QiiFile
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_QiiFile");
	}
	
	/**
	 * add
	 *
	 * @param array $user
	 * @param array $file_data
	 * @param array $type
	 */
	public static function add($file_data = array()){
		if (!is_array($file_data)) return false;		
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
		
			$file = self::getBy(array('out_id'=>$file_data['sceneCode']));
			if($file) return false;
			
			$data = array(
				'out_id'=>$file_data['sceneCode'],
				'zh_title'=>$file_data['zhName'],
				'en_title'=>$file_data['enName'],
				'icon'=>$file_data['icon'],
				'icon_hd'=>$file_data['iconHd'],
				'icon_micro'=>$file_data['iconMicro'],
				'summary'=>$file_data['intro'],
				'author_id'=>$file_data['authorId'],
				'author_name'=>$file_data['authorName'],
				'create_time'=>strtotime($file_data['createTime']),
				'update_time'=>strtotime($file_data['updateTime']),
			);
			
			$sence_label = array();
			$labels = explode(',', $file_data['belongToLabels']);
			
			foreach ($labels as $key=>$value) {
				$sence_label[$key]['id'] = '';
				$sence_label[$key]['out_id'] = 0;
				$sence_label[$key]['file_id'] = $file_data['sceneCode'];
				$sence_label[$key]['label_id'] = $value;
				$sence_label[$key]['create_time'] = strtotime($file_data['createTime']);
				$sence_label[$key]['update_time'] = strtotime($file_data['updateTime']);
			}
			
			//add
			$ret = self::addFile($data);
			if (!$ret) throw new Exception("add sence fail.", -220);
			
			//sence label 
			$result = Lock_Service_IdxFileLabel::batchAdd($sence_label);
			if (!$result) throw new Exception("add sencelabel fail.", -221);
			
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}			
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/sence_error.log");
			return false;
		}
	}
	
	/**
	 * add
	 *
	 * @param array $user
	 * @param array $file_data
	 * @param array $type
	 */
	public static function banchAdd($file_data = array()){
		if (!is_array($file_data)) return false;
		$list = self::getAllFile();
		if($list) self::deleteAll();
		//file
		$data = array();
		$scene_label = array();
		foreach ($file_data as $key=>$value) {
			$data[] = array(
					'id' => '',
					'out_id' => $value['sceneCode'],
					'zh_title' => $value['zhName'],
					'en_title' => $value['enName'],
					'icon' => $value['icon'],
					'icon_hd' => $value['iconHd'],
					'icon_micro' => $value['iconMicro'],
					'summary' =>$value['intro'],
					'author_id'=>$value['authorId'],
					'author_name'=>$value['authorName'],
					'down'=>0,
					'hits'=>0,
					'create_time' => strtotime($value['createTime']),
					'update_time' => strtotime($value['updateTime']),
					'status' => 1
				);
			$belongs = explode(",", $value['belongToLabels']);
			foreach ($belongs as $v) {
				if ($v) {
				$scene_label[] = array(
							"id"=>"",
							"out_id"=>'',
							"file_id"=>$value['sceneCode'],
							"label_id"=>$v,
							'create_time' => strtotime($value['createTime']),
							'update_time' => strtotime($value['updateTime'])
						);
				}
			}
		}
		
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
			
			list(,$sence_labes) = Lock_Service_IdxFileLabel::getAll();
			if($sence_labes) {
				$del_ret = Lock_Service_IdxFileLabel::deleteAll();
				if (!$del_ret) throw new Exception("del scene label fail.", -211);
			}
			
			$result = self::_getDao()->mutiInsert($data);
			if (!$result) throw new Exception("add scene fail.", -212);
			
			$ret = Lock_Service_IdxFileLabel::batchAdd($scene_label);
			if (!$ret) throw new Exception("add scene_label fail.", -213);
			
			
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}				
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/sence_error.log");
			return false;
		}
	}
	
	/**
	 * 修改文件
	 *
	 * @param int $file_id
	 * @param array $file_data
	 * @param array $type_data
	 * @param array $log_data
	 * @param array $message_data
	 */
	public static function update($file_data = array()){
		if (!is_array($file_data)) return false;
			
		$file = self::getBy(array('out_id'=>$file_data['sceneCode']));
		if(!$file) return false;
		
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
			
			$out_id = $file_data['sceneCode'];
			list(, $list) = Lock_Service_IdxFileLabel::getsBy(array('file_id'=>$out_id));
			
			if($list) {
				//del
				$del_ret = Lock_Service_IdxFileLabel::deleteByFileId($out_id);
				if (!$del_ret) throw new Exception("del sencelabels fail.", -210);
			}
		
			$data = array(
				'out_id'=>$file_data['sceneCode'],
				'zh_title'=>$file_data['zhName'],
				'en_title'=>$file_data['enName'],
				'icon'=>$file_data['icon'],
				'icon_hd'=>$file_data['iconHd'],
				'icon_micro'=>$file_data['iconMicro'],
				'summary'=>$file_data['intro'],
				'author_id'=>$file_data['authorId'],
				'author_name'=>$file_data['authorName'],
				'create_time'=>strtotime($file_data['createTime']),
				'update_time'=>strtotime($file_data['updateTime']),
			);
			
			$ret = self::updateFile($data, $file['id']);
			if (!$ret) throw new Exception("update sence fail.", -211);
			
			$sence_label = array();
			$labels = explode(',', $file_data['belongToLabels']);
				
			foreach ($labels as $key=>$value) {
				$sence_label[$key]['id'] = '';
				$sence_label[$key]['out_id'] = 0;
				$sence_label[$key]['file_id'] = $file_data['sceneCode'];
				$sence_label[$key]['label_id'] = $value;
				$sence_label[$key]['create_time'] = strtotime($file_data['createTime']);
				$sence_label[$key]['update_time'] = strtotime($file_data['updateTime']);
			}
			
			//sence label
			$result = Lock_Service_IdxFileLabel::batchAdd($sence_label);
			if (!$result) throw new Exception("add sencelabel fail.", -221);
			
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
			
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/sence_error.log");
			return false;
		}
	}
	
	/**
	 *del
	 *
	 * @param int $id
	 * @param array $type_data
	 * @param array $log_data
	 */
	public static function delete($file_data = array()){
		if (!is_array($file_data)) return false;
			
		$file = self::getBy(array('out_id'=>$file_data['sceneCode']));
		if(!$file) return false;
		
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
			
			//删除关系表
			$out_id = $file_data['sceneCode'];
			list(, $list) = Lock_Service_IdxFileLabel::getsBy(array('file_id'=>$out_id));
				
			if($list) {
				//del
				$del_ret = Lock_Service_IdxFileLabel::deleteByFileId($out_id);
				if (!$del_ret) throw new Exception("del sencelabels fail.", -210);
			}
			
			//删除锁屏管理
			$lock = Lock_Service_Lock::getBy(array('channel_id'=>1, 'file_id'=>$file['id']));
			if($lock) {
				//del
				$del_lock_ret = Lock_Service_Lock::deleteFile($lock['id']);
				if (!$del_lock_ret) throw new Exception("del lock fail.", -218);
			}
			
			//删除专题
			$subject_file = Lock_Service_SubjectFile::getsBy(array('channel_id'=>1, 'file_id'=>$file['id']));
			if($subject_file) {
				$del_subject_ret = Lock_Service_SubjectFile::deleteBy(array('channel_id'=>2, 'file_id'=>$file['id']));
				if (!$del_lock_ret) throw new Exception("del subject fail.", -219);
			}
			
			//删除内核列表 
			list(, $kernels) = Lock_Service_QiiFIleKernel::getsBy(array('scene_code'=>$file_data['sceneCode']));
			if($kernels) {
				$del_kernels = Lock_Service_QiiFIleKernel::deleteBy(array('scene_code'=>$file_data['sceneCode']));
				if (!$del_kernels) throw new Exception("del file_kernel fail.", -229);
			}
			
			//删除场景
			$result = self::deleteFile($file['id']);
			if (!$result) throw new Exception("del sence fail.", -221);
				
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
				
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/sence_error.log");
			return false;
		}
	}
	
}
