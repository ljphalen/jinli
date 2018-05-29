<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ListController extends Front_BaseController{
	
	public $actions = array(
		'indexUrl' => '/list/index',
		'morelUrl' => '/list/more',
		'downloadUrl' => '/detail/down',
	);
	
	public $perpage = 9;

	/**
	 * 列表
	 */
	public function indexAction() {
		$page = 1;
		$tid = intval($this->getInput('tid'));
		$channel = intval($this->getInput('channel'));
		$order_by = $this->getInput('orderby');
		if(!$order_by || !in_array($order_by, array('id', 'down'))) {
			$order_by = 'id';
		}
		$orderby = $order_by == 'id' ? array('sort'=>'DESC', 'id'=>'DESC') : array('down'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		if($channel == 1) {//精灵锁屏
			//标签
			$list = $data = array();
			$out_ids = $kernel_list = array();
			
			if($tid) {
				$idx_file_label = Lock_Service_IdxFileLabel::getByLabelId($tid);
				$idx_file_label = Common::resetKey($idx_file_label, 'file_id');
				$label_file_ids = array_keys($idx_file_label);
				if($label_file_ids) {
					list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>1, 'file_id'=>array('IN', $label_file_ids)), $orderby);
				}
			} else {
				list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>1), $orderby);
			}
			
			if($list) {
				$list = Common::resetKey($list, 'file_id');
				$qii_ids = array_keys($list);
				
				$qii_files = Lock_Service_QiiFile::getByIds($qii_ids);
				$qii_data = array();
				foreach ($qii_files as $key=>$value) {
					$qii_data[$key]['id'] = $value['id'];
					$qii_data[$key]['out_id'] = $value['out_id'];
					$qii_data[$key]['title'] = $value['zh_title'];
					$qii_data[$key]['icon'] = $value['icon_micro'];
					$qii_data[$key]['update_time'] = $value['update_time'];
					$out_ids[] = $value['out_id'];
				}
				$data = Common::resetKey($qii_data, 'out_id');
				//kernel
				$kernel = Util_Cookie::get('kernel', true);
				
				list(, $kernel_list) = Lock_Service_QiiFileKernel::getsBy(array('scene_code'=>array('IN', $out_ids), 'kernel_code'=>$kernel));
				$kernel_list = Common::resetKey($kernel_list, 'scene_code');
			}
		} else {
			//根据分辨率
			$current_resolution = Util_Cookie::get('resolution', true);
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
					list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>2, 'file_id'=>array('IN', $label_file_ids)), $orderby);
				}
			} else {
				list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>2), $orderby);
			}
			
			if($list) {
				$list = Common::resetKey($list, 'file_id');
				$gionee_ids = array_keys($list);
			
				$gionee_files = Lock_Service_File::getByIds($gionee_ids);
				$gionee_data = array();
				foreach ($gionee_files as $key=>$value) {
					$gionee_data[$key]['id'] = $value['id'];
					$gionee_data[$key]['title'] = $value['title'];
					$gionee_data[$key]['icon'] = $value['img_png'];
					$gionee_data[$key]['update_time'] = $value['update_time'];
				}
				$data = Common::resetKey($gionee_data, 'id');
			}
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? 'true' : 'false';
		$this->assign('data', $data);
		$this->assign('kernel_list', $kernel_list);
		$this->assign('list', $list);
		$this->assign('tid', $tid);
		$this->assign('page', $page);
		$this->assign('perpage', $this->perpage);
		$this->assign('hasnext', $hasnext);
		$this->assign('channel', $channel);
		$this->assign('curpage', $page);
		$this->assign('orderby', $order_by);
		$this->assign('file_total', $total);
		$this->assign('more_url', $tid ? $webroot.$this->actions['morelUrl'].'/?tid='.$tid.'&orderby='.$orderby.'&channel='.$channel : $webroot.$this->actions['morelUrl'].'/?orderby='.$orderby.'&channel='.$channel);
	}
	
	/**
	 * 加载更多
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 2) $page = 2;
		$perpage = $this->perpage;
		
		$tid = intval($this->getInput('tid'));
		$channel = intval($this->getInput('channel'));
		$order_by = $this->getInput('orderby');
		if(!$order_by || !in_array($order_by, array('id', 'down'))) {
			$order_by = 'id';
		}
		$orderby = $order_by == 'id' ? array('sort'=>'DESC', 'id'=>'DESC') : array('down'=>'DESC', 'sort'=>'DESC', 'id'=>'DESC');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		if($channel == 1) {//精灵锁屏
			//标签
			$list = $data = array();
			$out_ids = $kernel_list = array();
			
			if($tid) {
				$idx_file_label = Lock_Service_IdxFileLabel::getByLabelId($tid);
				$idx_file_label = Common::resetKey($idx_file_label, 'file_id');
				$label_file_ids = array_keys($idx_file_label);
				if($label_file_ids) {
					list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>1, 'id'=>array('IN', $label_file_ids)), $orderby);
				}
			} else {
				list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>1), $orderby);
			}
			
			if($list) {
				$list = Common::resetKey($list, 'file_id');
				$qii_ids = array_keys($list);
				
				$qii_files = Lock_Service_QiiFile::getByIds($qii_ids);
				$qii_data = array();
				foreach ($qii_files as $key=>$value) {
					$qii_data[$key]['id'] = $value['id'];
					$qii_data[$key]['out_id'] = $value['out_id'];
					$qii_data[$key]['title'] = $value['zh_title'];
					$qii_data[$key]['icon'] = $value['icon_micro'];
					$qii_data[$key]['update_time'] = $value['update_time'];
					$out_ids[] = $value['out_id'];
				}
				$data = Common::resetKey($qii_data, 'out_id');
				//kernel
				$kernel = Util_Cookie::get('kernel', true);
				
				list(, $kernel_list) = Lock_Service_QiiFileKernel::getsBy(array('scene_code'=>array('IN', $out_ids), 'kernel_code'=>$kernel));
				$kernel_list = Common::resetKey($kernel_list, 'scene_code');
			}
		} else {
			//根据分辨率
			$current_resolution = Util_Cookie::get('resolution', true);
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
					list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>2, 'id'=>array('IN', $label_file_ids)), $orderby);
				}
			} else {
				list($total, $list) = Lock_Service_Lock::getList($page, $this->perpage, array('channel_id'=>2), $orderby);
			}
			
			if($list) {
				$list = Common::resetKey($list, 'file_id');
				$gionee_ids = array_keys($list);
			
				$gionee_files = Lock_Service_File::getByIds($gionee_ids);
				$gionee_data = array();
				foreach ($gionee_files as $key=>$value) {
					$gionee_data[$key]['id'] = $value['id'];
					$gionee_data[$key]['title'] = $value['title'];
					$gionee_data[$key]['icon'] = $value['img_png'];
					$gionee_data[$key]['update_time'] = $value['update_time'];
				}
				$data = Common::resetKey($gionee_data, 'id');
			}
		}
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$list_data = array();
		$i = ($page - 1) * $perpage + 1;
		foreach ($list as $key=>$value) {
			$pre = $next = 1;
			if ($total == $i) $next = 0;
			if ($i % $perpage == 0 && ($total - ($page * $perpage) == 0) ) $next = 0;
				
			$list_data[$key]['title'] = $value['title'];
			$list_data[$key]['down'] = $webroot.$this->actions['downloadUrl'].'?id='.$value['id'];
			$list_data[$key]['img'] = $data[$value['file_id']]['icon'];
			if($value['channel_id'] == 1) {
				$out_id = $data[$value['file_id']]['out_id'];
				$list_data[$key]['link'] = sprintf("%s/detail?id=%d&out_id=%d&channel=%d&pre=%d&next=%d&update_time=%d&refer=%s&version=%d",$webroot, $value['id'], $out_id, $value['channel_id'], $pre, $next, $data[$value['file_id']]['update_time'], 'index', $kernel_list[$out_id]['res_version']);
			} else {
				$list_data[$key]['link'] = sprintf("%s/detail?id=%d&channel=%d&pre=%d&next=%d&update_time=%d&refer=%s",$webroot,$value['id'], $value['channel_id'], $pre, $next, $data[$value['file_id']]['update_time'], 'index');
			}
			$list_data[$key]['link'] = $tid ? $list_data[$key]['link'].'&tid='.$tid : $list_data[$key]['link'];
			$i++;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$list_data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}	
}
