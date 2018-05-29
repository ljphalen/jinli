<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Lock_Service_File extends Common_Service_Base{
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllFile($ids) {
		return self::_getDao()->getAllFile($ids);
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
	 */
	public static function getIndexFile($ids) {
		return self::_getDao()->getIndexFile($ids);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $order_by, $sort) {
		if(isset($params['file_type'])) $tmp['file_type'] = $params['file_type'];
		if(isset($params['file_ids'])) $tmp['file_ids'] = $params['file_ids'];
		$params = self::_cookData($params);
		if(isset($tmp['file_type'])) $params['file_type'] = $tmp['file_type'];
		if(isset($tmp['file_ids'])) $params['file_ids'] = $tmp['file_ids'];
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
		$data['update_time'] = Common::getTime();
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
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
	 * @param unknown_type $data
	 */
	public static function addFile($data) {
		if (!is_array($data)) return false;
		$data['create_time'] = Common::getTime();
		$data['status'] = 1;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
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
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['file'])) $tmp['file'] = $data['file'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['img_gif'])) $tmp['img_gif'] = $data['img_gif'];
		if(isset($data['img_png'])) $tmp['img_png'] = $data['img_png'];
		if(isset($data['file_size'])) $tmp['file_size'] = $data['file_size'];
		if(isset($data['summary'])) $tmp['summary'] = $data['summary'];
		if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
		if(isset($data['designer'])) $tmp['designer'] = $data['designer'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['size_id'])) $tmp['size_id'] = intval($data['size_id']);
		if(isset($data['hit'])) $tmp['hit'] = intval($data['hit']);
		if(isset($data['down'])) $tmp['down'] = intval($data['down']);
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		
		if(isset($data['keyword'])) $tmp['keyword'] = $data['keyword'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Lock_Dao_File
	 */
	private static function _getDao() {
		return Common::getDao("Lock_Dao_File");
	}
	
	/**
	 * 上传文件
	 *
	 * @param array $user
	 * @param array $file_data
	 * @param array $type
	 */
	public static function add($user = array(), $file_data = array(), $type = array(), $size = array()){
		if (!is_array($user) || !is_array($file_data) || !is_array($type) || !is_array($size)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
				
			//添加文件
			$file_id = self::addFile($file_data);
			if(!$file_id) throw new Exception("add file fail.", -209);
			
			//分类
			$type_data = array();
			foreach ($type as $key=>$value) {
				$type_data[$key]['id'] = '';
				$type_data[$key]['file_id'] = $file_id;
				$type_data[$key]['type_id'] = $value;
			}
			$type_ret = Lock_Service_FileTypes::batchAdd($type_data);
			if (!$type_ret) throw new Exception("add file_types fail.", -220);
			
			//分辨率
			$size_data = array();
			foreach ($size as $key=>$value) {
				$size_data[$key]['id'] = '';
				$size_data[$key]['file_id'] = $file_id;
				$size_data[$key]['size_id'] = $value;
			}
			$size_ret = Lock_Service_FileSize::batchAdd($size_data);
			if (!$size_ret) throw new Exception("add file_size fail.", -230);
				
			//记录日志
			$log_data = array(
					'uid'=>$user['uid'],
					'username'=>$user['username'],
					'message'=>$user['username'].'上传了文件：<a href=/Admin/File/detail/?id='.$file_id.'>'.$file_data['title'].'</a>',
					'file_id'=>$file_id
			);
			$log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
			if(!$log_ret) throw new Exception("add log fail.", -210);
				
			//发消息
			$message_data = array();
			$group_id = 2;
			//消息内容
			list(,$users) = Admin_Service_User::getList(1, 20, array('groupid'=>$group_id));
			if($users) {
				foreach ($users as $key=>$value) {
					$message_data[$key]['id'] = '';
					$message_data[$key]['uid'] = $value['uid'];
					$message_data[$key]['content'] = $user['username'].'上传了文件：<a href=/Admin/File/detail/?id='.$file_id.'>'.$file_data['title'].'</a>';
					$message_data[$key]['status'] = 0;
					$message_data[$key]['create_time'] = Common::getTime();
				}
			}
			if($message_data) {
				$message_ret = Lock_Service_Message::batchAdd($message_data);
				if(!$message_ret) throw new Exception("add message fail.", -211);
			}
				
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
				
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
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
	public static function update($file_id, $file_data = array(), $type_data = array(), $size_data = array(), $log_data = array(), $message_data = array()){
		if (!intval($file_id)) return false;
		if (!is_array($file_data) || !is_array($type_data) || !is_array($size_data) || !is_array($log_data) || !is_array($message_data)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
	
			//修改文件
			$ret = self::updateFile($file_data, $file_id);
			if(!$ret) throw new Exception("update file fail.", -212);
				
			//分类
			$del_ret = Lock_Service_FileTypes::deleteByFileId($file_id);
			if(!$ret) throw new Exception("del file_types fail.", -213);
			$type_ret = Lock_Service_FileTypes::batchAdd($type_data);
			if (!$type_ret) throw new Exception("add file_types fail.", -220);
			
			//分辨率
			$delsize_ret = Lock_Service_FileSize::deleteByFileId($file_id);
			if(!$delsize_ret) throw new Exception("del file_size fail.", -223);
			$size_ret = Lock_Service_FileSize::batchAdd($size_data);
			if (!$size_ret) throw new Exception("add file_size fail.", -220);
			
			//记日志
			$log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
			if(!$log_ret) throw new Exception("add log fail.", -210);
	
			//发消息			
			if($message_data) {
				$message_ret = Lock_Service_Message::batchAdd($message_data);
				if(!$message_ret) throw new Exception("add message fail.", -211);
			}
	
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
	
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
			return false;
		}
	}
	
	/**
	 * 修改文件
	 *
	 * @param int $id
	 * @param array $type_data
	 * @param array $log_data
	 */
	public static function delete($id, $log_data = array()){
		if (!intval($id)) return false;
		if (!is_array($log_data)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
	
			//删除文件
			$ret = self::deleteFile($id);
			if(!$ret) throw new Exception("delete file fail.", -212);
	
			//删除分类
			$del_ret = Lock_Service_FileTypes::deleteByFileId($id);
			if(!$del_ret) throw new Exception("del file_types fail.", -213);
				
			//记日志
			$log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
			if(!$log_ret) throw new Exception("add log fail.", -210);
			
			//删除锁屏管理
			$lock = Lock_Service_Lock::getBy(array('channel_id'=>2, 'file_id'=>$file['id']));
			if($lock) {
				//del
				$del_lock_ret = Lock_Service_Lock::deleteFile($lock['id']);
				if (!$del_lock_ret) throw new Exception("del lock fail.", -218);
			}
			
			//删除专题
			list($subject_total, ) = Lock_Service_SubjectFile::getsBy(array('channel_id'=>2, 'file_id'=>$file['id']));
			if($subject_total) {
				$del_subject_ret = Lock_Service_SubjectFile::deleteBy(array('channel_id'=>2, 'file_id'=>$file['id']));
				if (!$del_subject_ret) throw new Exception("del subject fail.", -219);
			}
		
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
	
		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($id) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
			return false;
		}
	}
	
	/**
	 * 修改文件状态
	 * 
	 * @param int $id
	 * @param array $file_data
	 * @param array $log_data
	 * @param array $message_data
	 */
	public static function editStatus($id, $status, $file_data = array(), $log_data = array(), $message_data = array()){
		if (!intval($id)) return false;
		if (!is_array($file_data) || !is_array($log_data)) return false;
		try {
			//开始事务
			$transactionOn = parent::beginTransaction();
			if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);
			
			//更新文件状态
			$ret = self::updateFile($file_data, $id);
			if(!$ret) throw new Exception("update file status fail.", -206);
			
			//记录日志
			$log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
			if(!$log_ret) throw new Exception("add log fail.", -207);
			
			//发消息
			if($message_data) {
				$message_ret = Lock_Service_Message::batchAdd($message_data);
				if(!$message_ret) throw new Exception("add message fail.", -208);
			}
			
			//如果文件状态改为下架,要删除最新和最热中此文件
			if($status == 5){
				//删除锁屏管理
				$lock = Lock_Service_Lock::getBy(array('channel_id'=>2, 'file_id'=>$file['id']));
				if($lock) {
					//del
					$del_lock_ret = Lock_Service_Lock::deleteFile($lock['id']);
					if (!$del_lock_ret) throw new Exception("del lock fail.", -218);
				}
				
				//删除专题
				list($subject_total, ) = Lock_Service_SubjectFile::getsBy(array('channel_id'=>2, 'file_id'=>$file['id']));
				if($subject_total) {
					$del_subject_ret = Lock_Service_SubjectFile::deleteBy(array('channel_id'=>2, 'file_id'=>$file['id']));
					if (!$del_subject_ret) throw new Exception("del subject fail.", -219);
				}
			}
			
			//事务提交
			if ($transactionOn) {
				$return = parent::commit();
				return $return;
			}
			
 		}catch (Exception $e) {
			parent::rollBack();
			//出错监控
			error_log(json_encode($file_data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
			return false;
		}
	}
	/**
	 *
	 * @param unknown_type $name
	 * @param unknown_type $dir
	 * @return multitype:unknown_type
	 */
	public static function uploadFile($name, $dir) {
		//定义文件路径
		$attachPath = Common::getConfig('siteConfig', 'attachPath'); //网站附件目录
		$tmpPath = Common::getConfig('siteConfig', 'tmpPath');  //zip包临时存放目录
		$uxPath = Common::getConfig('siteConfig', 'uxPath');  //ux文件存放目录
		
		$zip = new Util_System();
		if($zip->smkdir($tmpPath) !== true) return Common::formatMsg(-1, '文件处理失败!');
		
		$file = $_FILES[$name];
		if ($file['error']) {
			return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
		}
		if (!$file['tmp_name'] || $file['tmp_name'] == '') return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
		//取文件名
		$file_name = self::escapeStr($file['name']);		
		$prifix = Util_String::substr($file_name, 0, strrpos($file_name,'.'));
		//重命名
		$newname = strtolower(Common::randStr(4)).date('His');
		
		//充许上传的文件类型
		$allowType = array('zip');
		
		//zip包临时保存目录
		$savePath = sprintf('%s/%s/%s', $tmpPath, date('Ym'),$newname);
		
		//上传
		$uploader = new Util_Upload(array('allowFileType'=>$allowType, 'maxSize'=>28672));
		$ret = $uploader->upload($name, $newname, $savePath);
		if ($ret < 0) {
			return Common::formatMsg(-1, '上传失败:'.$ret);
		}
		$url = sprintf('/%s/%s/%s/', $dir, date('Ym'), $newname);
		
		//解压的目录
		$unzipPath = $savePath;
		//包名
		$zipName = sprintf('%s/%s',$savePath, $ret['newName']);
		
		$res = $zip->unzipFile($zipName, $unzipPath);	
		
		if($res == true) {
			//检测是大文件还是小文件
			if(file_exists($savePath.'/icon_72.png')) {
				$ux_file = rename($savePath.'/w.ux', $savePath.'/'.$newname.'.ux');
				$ux_file = $newname.'.ux';
				$icon = 'icon_72.png';
				$img_gif = 'pre_web_180300.gif';
				$img_png = 'pre_preview_180300.jpg';
			}elseif (file_exists($savePath.'/icon_48.png')){
				$ux_file = rename($savePath.'/h.ux', $savePath.'/'.$newname.'.ux');
				$ux_file = $newname.'.ux';
				$icon = 'icon_48.png';
				$img_gif = 'pre_web_180300.gif';
				$img_png = 'pre_preview_120180.jpg';
			}else {
				return Common::formatMsg(-1, '未找到对应的缩略图!');
			}
			
			if(!self::checkExists($savePath.'/'.$icon)) return Common::formatMsg(-1, '未找到对应的缩略图!');
			if(!self::checkExists($savePath.'/'.$img_gif)) return Common::formatMsg(-1, '未找到对应的动态gif预览图!');
			if(!self::checkExists($savePath.'/'.$img_png)) return Common::formatMsg(-1, '未找到对应的静态png预览图!');
			if(!self::checkExists($savePath.'/'.$ux_file)) return Common::formatMsg(-1, '未找到对应的ux文件!');
			
			//将ux文件拷贝到指定目录
			$uxfilePath = sprintf('%s/%s/%s',$uxPath, date('Ym'), $newname);
			self::copyFile($ux_file, $savePath, $uxfilePath);
			
			//获取文件大小
			$file_size = filesize($uxfilePath.'/'.$ux_file) + filesize($savePath.'/'.$img_gif) + filesize($savePath.'/'.$img_png);
									
			//拷贝图片到附件目录
			$filePath = $attachPath.$dir.'/'.date('Ym').'/'.$newname;			
			self::copyFile($icon, $savePath, $filePath);
			self::copyFile($img_gif, $savePath, $filePath);
			self::copyFile($img_png, $savePath, $filePath);
			
		}else{
			return Common::formatMsg(-1, '文件处理失败:'.$ret);
		}
		$data = array(
						'file'=> '/'.date('Ym').'/'. $newname.'/'.$ux_file,
						'icon'=> $url.$icon,
						'img_gif'=> $url.$img_gif,
						'img_png'=> $url.$img_png,
						'file_size' => $file_size
				);
		
		return Common::formatMsg(0, '', $data);
	}
	
	/**
	 * 字符转换
	 *
	 * @param  string  $string  转换的字符串
	 * @return string  返回转换后的字符串
	 */
	private function escapeStr($string) {
		$string = str_replace(array("\0","%00","\r"), '', $string);
		$string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
		$string = str_replace(array("%3C",'<'), '&lt;', $string);
		$string = str_replace(array("%3E",'>'), '&gt;', $string);
		$string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
		return $string;
	}
	
	/**
	 * 检测文件是否存在
	 */
	
	private function checkExists($file) {
		if(!$file) return false;
		return file_exists($file);
	}

	/**
	 * 拷贝文件
	 * $file 文件名
	 * $src 源目录
	 * $dst 目标目录
	 */
	
	private function copyFile($file, $src, $dst) {
		if(!$file || !$src || !$dst) return false;
		$system = new Util_System();
		if($system->smkdir($dst) !== true) return Common::formatMsg(-1, '文件处理失败!');
		$ret = copy($src.'/'.$file, $dst.'/'.$file);
		if($ret !== true) return Common::formatMsg(-1, '文件处理失败!');
		@chmod($dst.'/'.$file, 0777);
		return true;
	}
}
