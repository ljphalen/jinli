<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 免费电话统计
 */
class VoipstatController extends Admin_BaseController {

	public $actions = array(
		'visitUrl'       => '/Admin/Voipstat/visit',
		'userLogUrl'     => '/Admin/Voipstat/userLog',
		'calledLogUrl'   => '/Admin/Voipstat/calledLog',
		'callDetailUrl'  => '/Admin/Voipstat/callDetail',
		'connectedUrl'   => '/Admin/Voipstat/connected',
		'periodUrl'      => '/Admin/Voipstat/period',
		'newRegisterUrl' => '/Admin/Voipstat/newUser',
	);

	public $pageSize = 20;

	//PV&UV统计
	public function visitAction() {
		$ym = $this->getPost('ym');
		if (empty($ym)) {
			$ym = date('Ym');
		}
		$where = array(
			'date' => array(array('>=', $ym . '01'), array('<=', $ym . '31')),
			'key'  => array(
				'IN',
				array(
					'voip_pv',
					'voip_uv',
					'voip_pv_list',
					'voip_uv_list',
					'voip_pv_call',
					'voip_uv_call',
					'voip_pv_call1',
					'voip_uv_call1'
				)
			)
		);

		$dataList = Gionee_Service_Log::getsBy($where, array('date' => 'DESC'));

		$result = array();
		foreach ($dataList as $k => $v) {
			$result[$v['date']][$v['key']] = $v['val'];
		}
		$this->assign('ym', $ym);
		$this->assign('dataList', $result);
	}


	//用户统计
	public function userLogAction() {
		$page = $this->getInput('page');
		$page = max(1, $page);
		list($dayCount, $userCount) = Gionee_Service_VoIPUser::getCountByDate(($page - 1) * $this->pageSize, $this->pageSize, array('sta' => '1'), array('date'), array('date' => 'DESC'));
		$total = Gionee_Service_VoIPUser::getCount(array('sta' => 1));

		$this->assign('dataList', $userCount);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($dayCount, $page, $this->pageSize, $this->actions['userLogUrl'] . "?"));
	}

	public function userMsgAction() {
		$page = $this->getInput('page');
		$page = max($page, 1);
		$date = $this->getInput('date');
		if (!$date) $this->output('-1', '参数错误!');
		list($total, $dataList) = Gionee_Service_VoIPUser::getDataList($page, $this->pageSize, array('date' => $date), array('id' => 'DESC'));
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['callDetailUrl'] . "?date={$date}&"));
		$this->assign('datList', $dataList);

	}


	//拔打统计
	public function calledLogAction() {
		$page = $this->getInput('page');
		$page = max($page, 1);
		//总点击拔打按钮的次数
		list($total, $dataList) = Gionee_Service_VoIPLog::getCensusDataByDate($page, $this->pageSize, array(), array('date'), array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['calledLogUrl'] . "?"));
	}


	//接通记录
	public function connectedAction() {

		$m     = $this->getInput('ym');
		$list  = Gionee_Service_VoIPLog::getCalledStat($m, true);
		$list2 = Gionee_Service_VoIPLog::getCalledStat($m);
		$tmp   = array();
		foreach ($list2 as $val) {
			$tmp[$val['date']] = $val;
		}
		$this->assign('dataList', $list);
		$this->assign('dataList1', $tmp);
	}


	//通话时段统计
	public function periodAction() {
		$date = $this->getInput('date');
		!$date && $date = date('Ymd', strtotime('-1 day'));
		if (strtotime($date) > time()) {
			$this->output('-2', '时间不能大于当前时间');
		}

		for ($i = 0; $i < 24; $i++) {
			$st    = strtotime($date) + $i * 3600;
			$et    = strtotime($date) + ($i + 1) * 3600;
			$row   = Gionee_Service_VoIPLog::getDurationTimeByHour($st, $et);
			$d[$i] = $row['num'];
		}

		$ret = array('data' => $d, 'amount' => array_sum(array_values($d)));

		$this->assign('data', $ret);
		$this->assign('date', $date);
	}

	/**
	 * 通过畅聊接口新增的用户数
	 */
	public function newUserAction() {
		$page                = $this->getInput('page');
		$page                = max($page, 1);
		$params              = array();
		$params['come_from'] = 2;
		list($numbers, $dataList) = Gionee_Service_User::countByDays($page, $this->pageSize, $params);
		$sum = 0;
		foreach ($numbers as $v) {
			$sum += $v['total'];
		}
		$this->assign('dataList', $dataList);
		$this->assign('sum', $sum);
		$this->assign('pager', Common::getPages(count($numbers), $page, $this->pageSize, $this->actions['newRegisterUrl'] . "/?"));
	}

	//针对单个用户拔号统计
	public function perCallTimesAction() {
		$page  = $this->getInput('page');
		$date  = $this->getInput('date');
		$total = $this->getInput('total');
		$where = array('date' => $date);
		$data  = Gionee_Service_VoIPLog::getPerUserCallTimesInfo(max($page, 1), $this->pageSize, $where, array('caller_phone'), array('id' => 'DESC'));
		$this->assign('data', $data);
		$this->assign('date', $date);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['perCallTimes'] . "?date={$date}&total={$total}&"));
	}

	public function perCallTimesExportAction() {
		$date  = $this->getInput('date');
		$where = array('date' => $date);
		$order = array('id' => 'desc');
		$list  = Gionee_Service_VoIPLog::getsBy($where, $order);

		$headers  = array(
			'id',
			'caller_phone',
			'called_phone',
			'duration',
			'called_time',
			'connected_time',
			'hangup_time'
		);
		$filename = 'calltimes_' . date('YmdHis') . '.csv';
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$fields['area'] = iconv('UTF8', 'GBK', $fields['area']);

			$row = array(
				$fields['id'],
				$fields['caller_phone'],
				$fields['called_phone'],
				$fields['duration'],
				date('Y-m-d H:i:s', $fields['called_time']),
				!empty($fields['connected_time']) ? date('Y-m-d H:i:s', $fields['connected_time']) : 0,
				!empty($fields['hangup_time']) ? date('Y-m-d H:i:s', $fields['hangup_time']) : 0,
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	public function cDetailAction() {
		$page = $this->getInput('page');
		$date = $this->getInput('date');
		list($total, $list) = Gionee_Service_VoIPLog::getList($page, $this->pageSize, array('date' => $date), array('id' => 'desc'));
		$this->assign('dataList', $list);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['cDetail1Url'] . "/?date={$date}&"));
	}

}