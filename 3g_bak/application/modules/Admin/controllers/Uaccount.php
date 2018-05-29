<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UAccountController
 * 用户账号管理
 */
class  UAccountController extends Admin_BaseController {

	public $actions  = array(
		'indexUrl'       => '/Admin/Uaccount/index',
		'monthUrl'       => '/Admin/Uaccount/month',
		'detailUrl'      => '/Admin/Uaccount/detail',
		'blacklistUrl'   => '/Admin/Uaccount/blacklist',
		'blackeditUrl'   => '/Admin/Uaccount/blackedit',
		'blackPostUrl'   => '/Admin/Uaccount/blackPost',
		'blackdeleteUrl' => '/Admin/Uaccount/blackdelete',

	);
	public $pageSize = 20;

	public $types = array(
		'1' => '按天',
		'2' => '按月'
	);

	public $accountTypes = array(
		'1' => '黑名单账号',
		'2' => '冻结账号',
		'3' => '积分清零账号'
	);

	public function indexAction() {
		$page     = $this->getInput('page');
		$postData = $this->getInput(array('sdate', 'edate', 'num', 'type', 'group_type', 'export', 'recharge_number'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		!$postData['num'] && $postData['num'] = 3;
		!$postData['group_type'] && $postData['group_type'] = 1;
		$page = max(1, $page);

		$where = array();
		if (!empty($postData['recharge_number'])) {
			$where['recharge_number'] = $postData['recharge_number'];
		}
		$where['add_time']   = array(
			array('>=', strtotime($postData['sdate'])),
			array('<=', strtotime($postData['edate']))
		);
		$where['order_type'] = $postData['group_type'];
		$pageSize            = $this->pageSize;
		if ($postData['export']) {
			$pageSize = 1000;
		}
		list($total, $dataList) = User_Service_Order::getRechargeTimesInfo($where, $postData['num'], $page, $pageSize);

		$rechargeData = array();
		foreach ($dataList as $key => $val) {
			$rechargeData[$val['recharge_number']][$val['add_date']] = $val['total'];
		}
		$dateList = array();
		for ($i = strtotime($postData['sdate']); $i <= strtotime($postData['edate']); $i += 86400) {
			$dateList[] = date('Y-m-d', $i);
		}
		$result = array();
		foreach ($rechargeData as $k => $v) {
			foreach ($dateList as $m => $n) {
				$result[$k][$n]['number'] = $v[$n] ? $v[$n] : 0;
			}
		}

		if (!empty($postData['export'])) {
			$postData = $this->getInput(array(
				'sdate',
				'edate',
				'num',
				'type',
				'group_type',
				'export',
				'recharge_number'
			));
			$this->_export('day', $result, '每天被充值用户记录', $postData['sdate'], $postData['edate']);
			exit();
		}

		$this->assign('list', $result);
		$this->assign('dateList', $dateList);
		$this->assign('accountNum', $total);
		$this->assign('groupTypes', Common::getConfig('userConfig', 'ofpay_api_log'));
		$this->assign('types', $this->types);
		$this->assign('params', $postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . http_build_query($postData) . "&"));
	}

	public function monthAction() {
		$page     = $this->getInput('page');
		$postData = $this->getInput(array('date', 'num', 'group_type', 'export', 'recharge_number'));
		!$postData['date'] && $postData['date'] = date('Y-m', strtotime('-1 month'));
		$sdate = strtotime($postData['date']);
		$edate = strtotime($postData['date'] . ' +1 month');
		!$postData['num'] && $postData['num'] = 3;
		!$postData['group_type'] && $postData['group_type'] = 1;
		$page = max(1, $page);

		$where = array();
		if (!empty($postData['recharge_number'])) {
			$where['recharge_number'] = $postData['recharge_number'];
		}
		$pageSize = $this->pageSize;
		if (!empty($postData['export'])) {
			$pageSize = 1000;
		}
		$where['add_time']   = array(array('>=', $sdate), array('<=', $edate));
		$where['order_type'] = $postData['group_type'];
		list($total, $dataList) = User_Service_Order::getRechargedMsg($where, array('recharge_number'), $postData['num'], $page, $pageSize);
		if (!empty($postData['export'])) {
			$this->_export('month', $dataList, '月兑换次数记录', date('Y-m-d', $sdate), date('Y-m-d', $edate));
			exit();
		}
		$this->assign('list', $dataList);
		$this->assign('groupTypes', Common::getConfig('userConfig', 'ofpay_api_log'));
		$this->assign('params', $postData);
		$this->assign('totalAcounts', $total);
		$this->assign('pager', Common::getPages($total, $page, $pageSize, $this->actions['monthUrl'] . "?" . http_build_query($postData) . "&"));
	}

	public function detailAction() {
		$page     = $this->getInput('page');
		$postData = $this->getInput(array('date', 'recharge_number', 'group_type', 'num'));
		if (empty($postData['date']) || !intval($postData['recharge_number']) || !intval($postData['group_type'])) {
			$this->output('-1', '参数有错');
		}
		$page                     = max($page, 1);
		$sdate                    = strtotime($postData['date']);
		$edate                    = strtotime($postData['date'] . ' +1 month');
		$where                    = array();
		$where['order_type']      = $postData['group_type'];
		$where['add_time']        = array(array('>=', $sdate), array('<=', $edate));
		$where['recharge_number'] = $postData['recharge_number'];
		list($total, $dataList) = User_Service_Order::getList($page, $this->pageSize, $where, array('add_time' => 'DESC'));
		foreach ($dataList as $k=>$v){
			$user = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username']  = $user['username'];
		}
		$this->assign('list', $dataList);
		$this->assign('total', $total);
		$this->assign('orderStatus', Common::getConfig('userConfig', 'statusFlag'));
		$this->assign('params', $postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['detailUrl'] . "?" . http_build_query($postData) . "&"));
	}


	public function blacklistAction() {
		$postData = $this->getInput(array('page', 'type', 'account'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (intval($postData['type'])) {
			$where['type'] = $postData['type'];
		}
		if (intval($postData['account'])) {
			$where['account'] = $postData['account'];
		}

		list($total, $dataList) = User_Service_Blacklist::getList($page, $this->pageSize, $where, array('add_time' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('params', $postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['blacklistUrl'] . "?type={$postData['type']}&mobile={$postData['mobile']}&"));
		$this->assign('accoutTypes', $this->accountTypes);
	}


	public function blackeditAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$data = User_Service_Blacklist::get($id);
			$this->assign('data', $data);
		}
		$this->assign('accountTypes', $this->accountTypes);
	}

	public function blackPostAction() {
		$postData = $this->getInput(array('account', 'account_type', 'id'));
		if ($postData['id']) {
			User_Service_Blacklist::update($postData['id'], $postData);
		} else {
			$postData['add_time'] = time();
			$user = Gionee_Service_User::getUserBy($postData['account']);
			$postData['uid'] = $user['id'];
			User_Service_Blacklist::add($postData);
		}
		$this->output('0', '操作成功！');
	}

	public function blackdeleteAction() {
		$id = $this->getInput('id');
		User_Service_Blacklist::delete($id);
		$this->output('0', '操作成功！');
	}

	public function ajaxAddUsersToBlackListAction(){
		$postData =$this->getInput(array('type','recharge','group_type','date'));
		$stime = strtotime($postData['date']);
		list($year,$month) = explode('-', $postData['date']);
		$lastDay = Common::getMonthLastDay($year,$month);
		$etime = strtotime("{$postData['date']}-$lastDay 23:59:59");
		$where = array();
		$where['add_time'] = array(array(">=",$stime),array("<=",$etime));
		$where['order_type'] = $postData['group_type'];
		$where['recharge_number'] = $postData['recharge'];
		$dataList = User_Service_Order::getsBy($where);
		$uids = array();
		foreach ($dataList as $k=>$v){
			$uids[]  = $v['uid'];
		}
		
		switch ($postData['type']) {
			case 1:{
				$ret = Gionee_Service_User::updatesBy('id', $uids, array('is_black_user'=>1));
				var_dump($ret);exit;
				break;
			}
			case 2:{
				Gionee_Service_User::updatesBy('id', $uids, array('is_frozed'=>1));
				break;
			}
			case 3:{
				$params = array();
				$params['uid'] = array("IN",$uids);
				User_Service_Gather::updateBy(array('remained_score'=>0),$params);
				break;
			}
		}
		$this->output('0','操作成功');
	}
	
	private function _export($type, $data, $title, $sdate, $edate) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $title . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));

		switch ($type) {
			case 'day': {
				$titles = $dateList = array();
				for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
					$dateList[] = date('Y-m-d', $i);
				}
				$titles = $dateList;
				array_unshift($titles, '被充值号码');
				array_push($titles, '总计');
				fputcsv($out, $titles);
				foreach ($data as $m => $n) {
					$sum    = 0;
					$temp   = array();
					$temp[] = $m;
					foreach ($dateList as $k => $v) {
						$temp[] = $n[$v]['number'];
						$sum += $n[$v]['number'];
					}
					$temp[] = $sum;
					fputcsv($out, $temp);
				}
				break;
			}
			case 'month': {
				fputcsv($out, array('被充值号码', '总兑换次数'));
				foreach ($data as $k => $v) {
					fputcsv($out, array($v['recharge_number'], $v['total_times']));
				}

				break;
			}
			default:
				break;
		}
	}


}