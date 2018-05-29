<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author ljp
 *
 */
class Festival_AutumnController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Festival_Autumn/index',
		'batchUpdateUrl'=>'/Admin/Festival_Autumn/batchUpdate',
		'exportUrl'=>'/admin/Festival_Autumn/export',
	);
	
	public $perpage = 20;
	public $awards = array(
			1 => '一等奖',
			2 => '二等奖',
			3 => '三等奖',
	);
	public $activity_id = array(1=>'中秋活动');
  
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$page_params = $this->getInput(array('status', 'name','start_time','end_time','activity_id','prize'));
		if ($page < 1) $page = 1;
		

		$params = array();
		if ($page_params['activity_id']) $params['activity_id'] = $page_params['activity_id'];
		if ($page_params['status']) $params['status'] = $page_params['status'] - 1;
		if ($page_params['name'])   $params['name'] = array('LIKE',$page_params['name']);
		if ($page_params['start_time'] && $page_params['end_time']){
			$params['create_time'] = array(array('>=',$page_params['start_time']),array('<=',$page_params['end_time']));
		} 
		if ($page_params['prize']) $params['prize'] = $page_params['prize'] - 1;
		
	    list($total, $activityies) = Festival_Service_Log::getList($page, $this->perpage, $params);
		$this->assign('activityies', $activityies);
		$this->assign('total', $total);
		$this->assign('page_params', $page_params);
		$this->assign('awards', $this->awards);
		$this->assign('activity_id', $this->activity_id);
		$url = $this->actions['listUrl'].'/?' . http_build_query($page_params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='open'){
			$ret = Festival_Service_Log::updateStatus($info['ids'], 1);
		} else if($info['action'] =='close'){
			$ret = Festival_Service_Log::updateStatus($info['ids'], 0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	
	/**
	 *
	 * Get  list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('start_time','end_time','status','prize'));
		if ($page < 1) $page = 1;
	
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time'] && $s['end_time']){
			$params['create_time'] = array(array('>=',$s['start_time']),array('<=',$s['end_time']));
		}
		if ($s['prize']) $params['prize'] = $s['prize'] - 1;
		
        list($total, $logs) = Festival_Service_Log::getList($page, 10000, $params);
        
		$file_content = "";
		//抽奖记录
		if($total)  {
			$file_content .= "\"用户id\",";
			$file_content .= "\"用户名称\",";
			$file_content .= "\"用户联系方式\",";
			$file_content .= "\"抽奖时间\",";
			$file_content .= "\"奖项\",";
			$file_content .= "\"状态\",";
			$file_content .= "\r\n";
			foreach ($logs as $key=>$value) {
				$create_time = $value['create_time'] ? $value['create_time'] : '';
				$file_content .= "\"	" . $value['user_id'] . "\",";
				$file_content .= "\"" . $value['name'] . "\",";
				$file_content .= "\"" . $value['tel'] . "\",";
				$file_content .= "\"" . $create_time . "\",";
				$file_content .= "\"" . ($value['prize'] ? $this->awards[$value['prize']] : '无') . "\",";
				$file_content .= "\"" . ($value['status'] == 1 ? '未领取' : '已领取') . "\",";
				$file_content .= "\r\n";
					
			}
		} 
		
		if( Common::browserPlatform()) {
			$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		}
	    Util_DownFile::downloadFile(date('Y-m-d H:i:s') . '.csv', $file_content);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

}
