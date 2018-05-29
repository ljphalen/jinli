<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

	public $actions = array(
			'indexUrl' => '/index/index',
			'downloadUrl' => '/detail/down',
		);
	
	public $perpage = 9;

	public function indexAction() {
		$page = 1;
		$prepage = $this->perpage;
		
		//统计首页pv		
		Common::getCache()->increment('Lock_index_pv');
		
		//专题
		list(, $subjects) = Lock_Service_Subject::getList(1, 1);
		
		//本周新品
		$newSubjectIds = array();
		if(!empty($subjects)){
			$subjectId =$subjects[0]['id'];
			$subjectFiles = Lock_Service_SubjectFile::getBySubjectId($subjectId);
			$restFile = Common::resetKey($subjectFiles, 'lock_id');
			$newSubjectIds = array_keys($restFile);
		}
		
		//根据分辨率
		$current_resolution = Util_Cookie::get('resolution', true);
		if($current_resolution) {
			$file_size = Lock_Service_FileSize::getBySizeId($current_resolution);
			$file_ids = array();
			foreach ($file_size as $key=>$value) {
				$file_ids[] = $value['file_id'];
			}
		}
		
		list($total, $list) = Lock_Service_Lock::getCanuseFiles($page, $prepage, $file_ids, array('sort'=>'DESC', 'id'=>'DESC'));
		$qii_ids = array();
		$gionee_ids = array();
		foreach ($list as $key=>$value) {
			if($value['channel_id'] == 1) { //精灵锁屏
				$qii_ids[] = $value['file_id'];
			} else {
				$gionee_ids[] = $value['file_id'];
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
		
		$hasnext = (ceil((int) $total / $prepage) - ($page)) > 0 ? 'true' : 'false';
		$file_data = array('1'=>$qii_data, 2=>$gionee_data);
		$this->assign('list', $list);
		$this->assign('kernel_list', $kernel_list);
		$this->assign('file_data', $file_data);
		$this->assign('subjects', $subjects);
		$this->assign('newsubjects', $newSubjectIds);
		$this->assign('file_total', $total);
		$this->assign('perpage', $prepage);
		$this->assign('page', $page);
		$this->assign("cache",  Lock_Service_Config::getValue('lock_index_cache'));
		$this->assign('hasnext', $hasnext);
	}	
	
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 2) $page = 2;
		$perpage = $this->perpage;
		
		//根据分辨率
		$current_resolution = Util_Cookie::get('resolution', true);
		if($current_resolution) {
			$file_size = Lock_Service_FileSize::getBySizeId($current_resolution);
			$file_ids = array();
			foreach ($file_size as $key=>$value) {
				$file_ids[] = $value['file_id'];
			}
		}
		
		list($total, $list) = Lock_Service_Lock::getCanuseFiles($page, $perpage, $file_ids, array('sort'=>'DESC', 'id'=>'DESC'), true);
		$qii_ids = array();
		$gionee_ids = array();
		foreach ($list as $key=>$value) {
			if($value['channel_id'] == 1) { //精灵锁屏
				$qii_ids[] = $value['file_id'];
			} else {
				$gionee_ids[] = $value['file_id'];
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
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$data = array();
		//$i = ($page - 1) * $perpage;
		$i = ($page - 1) * $perpage + 1;
		foreach ($list as $key=>$value) {
			$pre = $next = 1;
			if ($total == $i) $next = 0;
			if (($i+1) % $perpage == 0 && ($total - ($page * $perpage - 1) == 0) ) $next = 0;
			//if ($i % $perpage == 0 && ($total - ($page * $perpage) == 0) ) $next = 0;
			
			$data[$key]['title'] = $value['title'];
			$data[$key]['down'] = $webroot.$this->actions['downloadUrl'].'?id='.$value['id'];
			$data[$key]['img'] = $file_data[$value['channel_id']][$value['file_id']]['icon'];
			if($value['channel_id'] == 1) {
				$out_id = $file_data[$value['channel_id']][$value['file_id']]['out_id'];
				$data[$key]['link'] = sprintf("%s/detail?id=%d&out_id=%d&channel=%d&pre=%d&next=%d&update_time=%d&refer=%s&version=%d",$webroot, $value['id'], $out_id, $value['channel_id'], $pre, $next, $file_data[$value['channel_id']][$value['file_id']]['update_time'], 'index', $kernel_list[$out_id]['res_version']);
			} else {
				$data[$key]['link'] = sprintf("%s/detail?id=%d&channel=%d&pre=%d&next=%d&update_time=%d&refer=%s",$webroot,$value['id'], $value['channel_id'], $pre, $next, $file_data[$value['channel_id']][$value['file_id']]['update_time'], 'index');
			}
			$i++;
		}
		$hasnext = (ceil((int) ($total+1) / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
