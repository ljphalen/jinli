<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class StatController extends Admin_BaseController {

	public $actions = array(
		'listUrl'    => '/Admin/Stat/pv',
		'monkeytime' => '/Admin/Stat/monkeytime',
	);

	public $perpage = 20;

	public function pvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		//pv
		list($pvlist, $pvLineData) = Widget_Service_Stat::getPvLineData(strtotime($sDate), strtotime($eDate));

		$lineData = array();
		if ($pvLineData[0]) {
			array_push($lineData, $pvLineData[0]);
		}

		$this->assign('pvlist', $pvlist);

		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-2 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}


	public function uvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		//ip
		list($list, $lineData) = Gionee_Service_Stat::getUvLineData($sDate, $eDate);

		$this->assign('list', $list);
		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-2 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}


	public function visitAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		//pv
		$lineData = Widget_Service_Stat::getVisitData(date('YmdH', strtotime($sDate)), date('YmdH', strtotime($eDate)));

		$this->assign('lineData', $lineData);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-2 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}

	public function lognewsAction() {
		if (!empty($_REQUEST['key'])) {
			$keys = explode(',', $_REQUEST['key']);
		} else {
			$keys = Widget_Service_Log::getLastIdByType(Widget_Service_Log::TYPE_NEWS, 10);
		}

		$this->_logData(Widget_Service_Log::TYPE_NEWS, $keys);
	}

	public function logcallAction() {
		$keys = array_keys(Widget_Service_Log::$callKeys);
		$this->_logData(Widget_Service_Log::TYPE_CALL, $keys);
	}

	public function logtocpAction() {
		$keys = array_keys(Widget_Service_Cp::$CpCate);
		$this->_logData(Widget_Service_Log::TYPE_TOCP, $keys);
	}

	public function logresAction() {
		$keys = array_keys(Widget_Service_Cp::$CpCate);
		$this->_logData(Widget_Service_Log::TYPE_RES, $keys);
	}

	public function logdownAction() {
		$keys = array_keys(Widget_Service_Cp::$CpCate);
		$this->_logData(Widget_Service_Log::TYPE_DOWN, $keys);
	}

	private function _logData($type, $keys) {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		//pv
		$lineData = Widget_Service_Log::getStatData($type, $keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));
		$date     = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}

		$list = array();
		foreach ($lineData as $k => $v) {
			foreach ($date as $kdv) {
				$list[$k][$kdv] = isset($v[$kdv]) ? intval($v[$kdv]) : 0;
			}
		}

		$this->assign('lineData', $list);
		$this->assign('type', $type);
		$this->assign('key', implode(',', $keys));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('date', $date);
	}

	public function monkeynumAction() {
		$date = $this->getInput('date');
		if (empty($date)) {
			$date = date('Ymd');
		}
		$list = Common::getCache()->hGetAll('MON_KEY_NUM:' . $date);
		arsort($list);
		$tmp = array();
		foreach ($list as $name => $num) {
			$t1 = Common::getCache()->hGetAll('MON_KEY_TIME:' . $date . ':' . $name);
			krsort($t1);
			$a1 = array_slice($t1, 0, 5);
			$s1 = array();
			foreach ($a1 as $ak => $av) {
				$s1[] = "[{$ak}=>{$av}]";
			}
			$tmp[$name] = array(
				'num'  => $num,
				'time' => implode(' ', $s1),
			);
		}
		$this->assign('date', $date);
		$this->assign('list', $tmp);
	}

	public function monkeytimeAction() {
		$date = $this->getInput('date');
		$name = $this->getInput('name');
		$list = Common::getCache()->hGetAll('MON_KEY_TIME:' . $date . ':' . $name);
		krsort($list);
		$this->assign('list', $list);
		$this->assign('date', $date);
		$this->assign('name', $name);
	}

	public function logexportAction() {
		$args     = array(
			'sdate' => FILTER_SANITIZE_STRING,
			'edate' => FILTER_SANITIZE_STRING,
			'type'  => FILTER_SANITIZE_STRING,
		);
		$formVals = filter_var_array($_REQUEST, $args);
		$sdate    = !empty($formVals['sdate']) ? $formVals['sdate'] : date('Ymd', strtotime("-8 day"));
		$edate    = !empty($formVals['edate']) ? $formVals['edate'] : date('Ymd', strtotime("today"));

		if (!empty($formVals['type'])) {
			$filename = date('YmdHis') . "_{$formVals['type']}.csv";
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=' . $filename);
			$fp   = fopen('php://output', 'w');
			$keys = array('date', 'type', 'key', 'keyname', 'val', 'ver');

			fputcsv($fp, $keys);
			$where   = array(
				'date' => array(array('>=', $sdate), array('<=', $edate)),
				'type' => $formVals['type'],
			);
			$orderBy = array('date'=>'asc', 'val' => 'desc');
			$list    = Widget_Service_Log::getsBy($where, $orderBy);

			$cpList = Widget_Service_Cp::getAll();

			foreach ($list as $fields) {
				$key   = $fields['key'];
				$title = Widget_Service_Log::keyName($fields['type'], $key, $urlId);

				$row   = array($fields['date'], $fields['type'], $key, iconv('UTF8', 'GBK', $title), $fields['val'], $fields['ver']);
				if ($formVals['type'] == Widget_Service_Log::TYPE_NEWS) {
					$row[] = iconv('UTF8', 'GBK', $cpList[$urlId]['title']);
				}
				fputcsv($fp, $row);
			}
			fclose($fp);
			exit;
		}

		$types = array(
			Widget_Service_Log::TYPE_CALL  => '接口',
			Widget_Service_Log::TYPE_NEWS  => '新闻',
			Widget_Service_Log::TYPE_CPURL => '栏目',
			Widget_Service_Log::TYPE_RES   => '资源查看',
			Widget_Service_Log::TYPE_DOWN  => '资源下载',
			Widget_Service_Log::TYPE_TOPIC  => '专题',
		);
		$this->assign('types', $types);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);


	}
}
