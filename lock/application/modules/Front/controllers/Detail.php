<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class DetailController extends Front_BaseController {

	public $actions = array(
			'detailUrl' => '/detail/index',
			'downUrl' => '/detail/downstat',
		);

	public function indexAction() {
		
		$id = intval($this->getInput('id'));
		$tid = intval($this->getInput('tid'));
		$channel = intval($this->getInput('channel'));
		$refer = $this->getInput('refer');
		$pam = $this->getInput('pam');
		$pre = $this->getInput('pre');
		$next = $this->getInput('next');
		$orderby = $this->getInput('orderby');
		$sid = intval($this->getInput('sid'));
		
		if(!$orderby) $orderby = 'id';
		$sort = $orderby == 'id' ?  array('sort'=>'DESC', 'id'=>'DESC') : array('down'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		
		$current_resolution = Util_Cookie::get('resolution', true);
		$kernel = Util_Cookie::get('kernel', true);
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		$lock = Lock_Service_Lock::getLock($id);
		if (!$lock || $lock['id'] == 0) {
			$size = Lock_Service_Size::getSize($current_resolution);
			$resolution = $resolution = str_replace('*', '_', $size['size']);
			$this->redirect($webroot.'?resolution='.$resolution);
		}
		
		if($lock['channel_id'] == 1){
			$info = Lock_Service_QiiFile::getBy(array('out_id'=>$lock['file_id']));
			$file_kernel = Lock_Service_QiiFileKernel::getBy(array('scene_code'=>$info['out_id'], 'kernel_code'=>$kernel));
			$file_data = array(
					'id'=>$info['id'],
					'title'=>$info['zh_title'],
					'author'=>$info['author_name'],
					'description'=>$info['summary'],
					'icon'=>$info['icon'],
					'size'=>$file_kernel['total_size'],
					'down'=>$info['down']
			);
			//更新点击量
			Lock_Service_QiiFile::updateBy(array('hits'=>$info['hit'] + 1), array('out_id'=>$lock['file_id']));
		} else {
			$info = Lock_Service_File::getFile($lock['file_id']);
			$file_data = array(
					'id'=>$info['id'],
					'title'=>$info['title'],
					'author'=>$info['designer'],
					'description'=>$info['descript'],
					'icon'=>$webroot.'/attachs'.$info['img_png'],
					'size'=>$info['file_size'],
					'down'=>$lock['down']
			);
			//更新点击量
			Lock_Service_File::updateFile(array('hits'=>$info['hit'] + 1), $lock['file_id']);
		}
		
		$list = array();
		if($refer == 'index') {
			//根据分辨率
			if($current_resolution) {
				$file_size = Lock_Service_FileSize::getBySizeId($current_resolution);
				$subject_ids = array();
				foreach ($file_size as $key=>$value) {
					$file_ids[] = $value['file_id'];
				}
			}
			
			list($total, $list) = Lock_Service_Lock::getCanuseFiles(1, 100, $file_ids, $sort);
		} else if($refer == 'subject') {
			//专题
			$subject = Lock_Service_Subject::getSubject($sid);
			
			//根据分辨率
			$current_resolution = Util_Cookie::get('resolution', true);
			$file_ids = array();
			if($current_resolution) {
				$file_size = Lock_Service_FileSize::getBySizeId($current_resolution);
				foreach ($file_size as $key=>$value) {
					$file_ids[] = $value['file_id'];
				}
			}
			
			$subject_files = Lock_Service_SubjectFile::getCanuseSubjectFiles($subject['id'], $file_ids);
			$subject_files = Common::resetKey($subject_files, 'lock_id');
			$s_ids = array_keys($subject_files);
			
			$list = Lock_Service_Lock::getByIds($s_ids);
		} else {
			if($channel == 1) {//精灵锁屏
				//标签
				$list = $data = array();
					
				if($tid) {
					$idx_file_label = Lock_Service_IdxFileLabel::getByLabelId($tid);
					$idx_file_label = Common::resetKey($idx_file_label, 'file_id');
					$label_file_ids = array_keys($idx_file_label);
					if($label_file_ids) {
						list($total, $list) = Lock_Service_Lock::getList(1, 100, array('channel_id'=>1, 'file_id'=>array('IN', $label_file_ids)), $sort);
					}
				} else {
					list($total, $list) = Lock_Service_Lock::getList(1, 100, array('channel_id'=>1), $sort);
				}
			} else {
				//根据分辨率
				$file_size = Lock_Service_FileSize::getBySizeId($current_resolution);
				$file_size = Common::resetKey($file_size, 'file_id');
				$res_ids = array_keys($file_size);
					
				$list = $data = array();
				if($tid) {
					$ids_file_type = Lock_Service_FileTypes::getByTypeId($tid);
					$ids_file_type = Common::resetKey($ids_file_type, 'file_id');
					$file_ids = array_keys($ids_file_type);
			
					$ids = array_intersect($res_ids, $file_ids);
					if($ids) {
						list($total, $list) = Lock_Service_Lock::getList(1, 100, array('channel_id'=>2, 'file_id'=>array('IN', $label_file_ids)), $sort);
					}
				} else {
					list($total, $list) = Lock_Service_Lock::getList(1, 100, array('channel_id'=>2), $sort);
				}
			}
		}		
				
		$list = Common::resetKey($list, 'id');
		$ids = array_keys($list);
		$ids_flip = array_flip($ids);
		
		$haspre = 0;
		$pre_file = array();
		$pre_id = $ids[$ids_flip[$id] - 1];
		if(in_array($pre_id, array_keys($ids_flip))) $pre_file = Lock_Service_Lock::getLock($pre_id);
		//下一个
		$hasnext = 0;
		$next_file = array();
		$next_id = $ids[$ids_flip[$id]  + 1];
		if(in_array($next_id, array_keys($ids_flip)))  $next_file = Lock_Service_Lock::getLock($next_id);
		
		if($pam && in_array($pam, array('next', 'pre'))) {
			if($pam == 'next' && $next) {
				$haspre = 1;
		
				if(!in_array($next_id, array_keys($ids_flip))) {
					$this->redirect($webroot);
				}
				$fileid = $next_id;
				if(in_array($ids[$ids_flip[$id]  + 2], array_keys($ids_flip))) $hasnext = 1;
			}else if ($pam == 'pre' && $pre) {
				$hasnext = 1;
		
				if(!in_array($pre_id, array_keys($ids_flip))) {
					$this->redirect($webroot);
				}
				$fileid = $pre_id;
				if(in_array($ids[$ids_flip[$id]  - 2], array_keys($ids_flip))) $haspre = 1;
			}else {
				$this->redirect($webroot);
			}
				
			$file = Lock_Service_Lock::getLock($fileid);
			
			if($file['channel_id'] == 1) {
				$lock_data = Lock_Service_QiiFile::getBy(array('out_id'=>$file['file_id']));
				$qii_kernel = Lock_Service_QiiFileKernel::getBy(array('scene_code'=>$lock_data['out_id'], 'kernel_code'=>$kernel));
			} else {
				$lock_data = Lock_Service_File::getFile($file['file_id']);
			}
			
			$downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
			$url = sprintf('%s/detail?id=%d&pre=%d&next=%d&update_time=%d&orderby=%s&channel=%d&title=%s&refer=%s',
					$webroot, $fileid, $haspre, $hasnext, $lock_data['update_time'], $orderby, $channel, Util_String::utf82unicode($file['title']), $refer);
			
			if($file['channel_id'] == 1) $url .= '&out_id='.$lock_data['out_id'].'&version='.$qii_kernel['res_version'];
			if($file['channel_id'] == 2) $url .= '&dlurl='.urlencode($webroot.$this->actions['downUrl'].'/?id='.$file['id']);
			if($tid) $url .= '&tid='.$tid;
			if($sid) $url .= '&sid='.$sid;
			
			$this->redirect($url);
		}
		//更新点击量
		Lock_Service_Lock::updateLock(array('hits'=>$lock['hit'] + 1), $lock['id']);
		$this->assign('file_data', $file_data);
	}

	public function downAction() {	
		$id = intval($this->getInput('id'));
		if ($id) $info = Lock_Service_Lock::getLock($id);
		if ($info) {
			//更新点击量
			Lock_Service_Lock::updateLock(array('down'=>$info['down'] + 1), $info['id']);
			$downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
			$file = Lock_Service_File::getFile($info['file_id']);
			$this->redirect($downloadroot.$file['file']);
			exit;
		}
		exit;
	}
}
