<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Festival_GuoqingController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Festival_Guoqing/index',
		'batchUpdateUrl'=>'/Admin/Festival_Guoqing/batchUpdate',
		'logUrl'=>'/Admin/Festival_Guoqing/log',
		'exportUrl'=>'/admin/Festival_Guoqing/export',
	);
	
	public $perpage = 20;
	public $quota = array(
			"01" => '第一天',
			"02" => '第二天',
			"03" => '第三天',
			"04" => '第四天',
			"05" => '第五天',
			"06" => '第六天',
			"07" => '第七天',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('day_id', 'uname','start_time','end_time'));
		if ($page < 1) $page = 1;
		

		$params = array();
		if ($s['day_id']) $params['day_id'] = array('IN',$s['day_id']);
		if ($s['uname'])   $params['uname'] = array('LIKE',$s['uname']);
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		
		if($params){
			$logs = Festival_Service_GuoQingLog::getsByLog($params, array('day_id' => 'DESC','create_time' => 'DESC'));
			foreach($logs as $key=>$value){
				$tmp[] = $value['uname'];
			}
			$temp['uname'] = $tmp ? array('IN',$tmp) : '';
		} else {
			$temp = $params;
		}
		
		list($total, $activityies) = Festival_Service_GuoQing::getList($page, $this->perpage, $temp);
	    
		foreach($activityies as $key=>$value){
			$smtp = array();
			if ($s['day_id']) $smtp['day_id'] = array('IN',$s['day_id']);
			$smtp['uname'] = $value['uname'];
			$log = Festival_Service_GuoQingLog::getByLog($smtp, array('create_time' => 'DESC'));
			$day_tmp[$value['uname']] = $log['day_id'];
			$time_tmp[$value['uname']] = $log['create_time'];
		}
	    
	    
	    $this->assign('activityies', $activityies);
		$this->assign('total', $total);
		$this->assign('time_tmp', $time_tmp);
		$this->assign('day_tmp', $day_tmp);
		$this->assign('s', $s);
		$this->assign('quota', $this->quota);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *
	 * Get  list
	 */
	public function exportAction() {
	   $page = intval($this->getInput('page'));
		$s = $this->getInput(array('day_id', 'uname','start_time','end_time'));
		if ($page < 1) $page = 1;
		

		$params = array();
		if ($s['day_id']) $params['day_id'] = array('IN',$s['day_id']);
		if ($s['uname'])   $params['uname'] = array('LIKE',$s['uname']);
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		
		//excel-head
		$filename = '国庆活动_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('用户账号','最后答题日期','最后答题时间', '积分'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			if($params){
				$logs = Festival_Service_GuoQingLog::getsByLog($params, array('create_time' => 'DESC'));
				foreach($logs as $key=>$value){
					$tmp[] = $value['uname'];
				}
				$temp['uname'] = $tmp ? array('IN',$tmp) : '';
			} else {
				$temp = $params;
			}
			list(, $rs) = Festival_Service_GuoQing::getList($page, 100, $temp);
			if (!$rs) break;
			 
			foreach($rs as $key=>$value){
				$smtp = array();
				if ($s['day_id']) $smtp['day_id'] = array('IN',$s['day_id']);
				$smtp['uname'] = $value['uname'];
				$log = Festival_Service_GuoQingLog::getByLog($smtp, array('create_time' => 'DESC'));
				$time_tmp[$value['uname']] = $log['create_time'];
				$day_tmp[$value['uname']] = $log['day_id'];
			}
			$tmp = array();
			foreach ($rs as $key=>$value) {
				$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $time_tmp[$value['uname']]) : '';
				$tmp[] = array(
						$value['uname'],
						$this->quota[$day_tmp[$value['uname']]],
						$create_time,
						$value['score']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function logAction() {
		$uname = $this->getInput('uname');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('day_id','start_time','end_time','status'));
		
		$params = array();
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['day_id']) $params['day_id'] = $s['day_id'];
		$params['uname'] = $uname;
		$s['uname'] = $uname;
		
		list($total, $logs) = Festival_Service_GuoQingLog::getList($page, $this->perpage, $params);
		
		$this->assign('logs', $logs);
		$this->assign('total', $total);
		$this->assign('quota', $this->quota);
		$this->assign('s', $s);
		$this->assign('uname', $uname);
		$url = $this->actions['logUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

}
