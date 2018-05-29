<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Front_BaseController {

	public $actions = array(
			'indexUrl' => '/subject/index',
			'downloadUrl' => '/detail/down',
		);
	
	public $perpage = 9;

	public function indexAction() {
		$sid = intval($this->getInput('sid'));
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
		
		$qii_ids = array();
		$gionee_ids = array();
		foreach ($list as $key=>$value) {
			if($value['channel_id'] == 1) { //精灵锁屏
				$qii_ids[] = $value['file_id'];
			} else {
				$gionee_ids[] = $value['file_id'];
				$gionee_ids = array_intersect($gionee_ids, $file_ids);
			}
		}
		
		//精灵锁屏
		$qii_data = array();
		$out_ids = $kernel_list = array();
		if($qii_ids) {
			$qii_files = Lock_Service_QiiFile::getByIds($qii_ids);
			foreach ($qii_files as $key=>$value) {
				$qii_data[$key]['id'] = $value['id'];
				$qii_data[$key]['out_id'] = $value['out_id'];
				$qii_data[$key]['title'] = $value['zh_title'];
				$qii_data[$key]['icon'] = $value['icon_micro'];
				$qii_data[$key]['update_time'] = $value['update_time'];
				$out_ids[] = $value['out_id'];
 			}
 			$qii_data = Common::resetKey($qii_data, 'out_id');
 			
 			//kernel
 			$kernel = Util_Cookie::get('kernel', true);
 			
 			list(, $kernel_list) = Lock_Service_QiiFileKernel::getsBy(array('scene_code'=>array('IN', $out_ids), 'kernel_code'=>$kernel));
 			$kernel_list = Common::resetKey($kernel_list, 'scene_code');
		}
		
		//金立锁屏
		$gionee_data = array();
		if($gionee_ids) {
			$gionee_files = Lock_Service_File::getByIds($gionee_ids);
			foreach ($gionee_files as $key=>$value) {
				$gionee_data[$key]['id'] = $value['id'];
				$gionee_data[$key]['title'] = $value['title'];
				$gionee_data[$key]['icon'] = $value['img_png'];
				$gionee_data[$key]['update_time'] = $value['update_time'];
			}
			$gionee_data = Common::resetKey($gionee_data, 'id');
		}
		$file_data = array('1'=>$qii_data, 2=>$gionee_data);
		$this->assign('list', $list);
		$this->assign('kernel_list', $kernel_list);
		$this->assign('file_data', $file_data);
		$this->assign('file_total', count($list));
		$this->assign('sid', $sid);
	}	
}
