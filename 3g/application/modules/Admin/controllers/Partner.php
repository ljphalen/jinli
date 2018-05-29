<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 内部合作
 */
class PartnerController extends Admin_BaseController {
	public $actions = array(
		'listhistorytodayUrl' => '/Admin/partner/listhistorytoday',
		'edithistorytodayUrl' => '/Admin/partner/edithistorytoday',
		'delhistorytodayUrl'  => '/Admin/partner/delhistorytoday',
		'listsingernewsUrl'   => '/Admin/partner/listsingernews',
		'editsingernewsUrl'   => '/Admin/partner/editsingernews',
		'delsingernewsUrl'    => '/Admin/partner/delsingernews',

	);

	public $perpage    = 20;
	public $searchType = array(
		'partner_pv' => 'PV',
		'partner_uv' => 'UV',
	);

	public function listhistorytodayAction() {

		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));

		if (!empty($get['togrid'])) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) > 0) {
					$where[$k] = $v;
				}
			}

			$orderBy['date'] = 'DESC';
			$total           = Partner_Service_HistoryToday::getDao()->count($where);
			$start           = (max($page, 1) - 1) * $offset;
			$list            = Partner_Service_HistoryToday::getDao()->getList($start, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['status']     = Common::$status[$v['status']];
				$list[$k]['type']       = $v['type'] == 2 ? '事件' : '人物';
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}
	}

	public function edithistorytodayAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'title', 'status', 'link', 'desc'));

		$now = time();
		if (!empty($postData['title'])) {
			//$postData['source_ids'] = implode(',',$postData['source_ids']);
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Partner_Service_HistoryToday::getDao()->insert($postData);
			} else {
				$ret = Partner_Service_HistoryToday::getDao()->update($postData, $postData['id']);
			}

			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Partner_Service_HistoryToday::getDao()->get($id);
		$this->assign('info', $info);
	}

	public function delhistorytodayAction() {
		$idArr = (array)$this->getInput('id');
		$i     = 0;
		$succ  = array();
		foreach ($idArr as $id) {
			$ret = Partner_Service_HistoryToday::getDao()->delete($id);
			if ($ret) {
				$i++;
				$succ[] = $id;
			}
		}

		Admin_Service_Log::op($succ);
		if ($i == count($succ)) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败', $succ);
		}
	}


	public function listsingernewsAction() {

		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));

		if (!empty($get['togrid'])) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) > 0) {
					$where[$k] = $v;
				}
			}

			$orderBy['created_at'] = 'DESC';
			$total                 = Partner_Service_SingerNews::getDao()->count($where);
			$start                 = (max($page, 1) - 1) * $offset;
			$list                  = Partner_Service_SingerNews::getDao()->getList($start, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);

			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}
	}

	public function editsingernewsAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'title', 'status', 'link', 'desc'));

		$now = time();
		if (!empty($postData['title'])) {
			//$postData['source_ids'] = implode(',',$postData['source_ids']);
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Partner_Service_SingerNews::getDao()->insert($postData);
			} else {
				$ret = Partner_Service_SingerNews::getDao()->update($postData, $postData['id']);
			}

			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Partner_Service_SingerNews::getDao()->get($id);
		$this->assign('info', $info);
	}

	public function delsingernewsAction() {
		$idArr = (array)$this->getInput('id');
		$i     = 0;
		$succ  = array();
		foreach ($idArr as $id) {
			$ret = Partner_Service_SingerNews::getDao()->delete($id);
			if ($ret) {
				$i++;
				$succ[] = $id;
			}
		}

		Admin_Service_Log::op($succ);
		if ($i == count($succ)) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败', $succ);
		}
	}

	public function statweatherAction() {
		$params = $this->getInput(array('sdate', 'edate', 'search_type'));
		!$params['sdate'] && $params['sdate'] = date('Y-m-d', strtotime("-8 day"));
		!$params['edate'] && $params['edate'] = date('Y-m-d', strtotime("today"));
		!$params['search_type'] && $params['search_type'] = 'partner_pv';
		//uv
		$where   = array(
			'ver'   => 'weather_list',
			'stime' => strtotime($params['sdate']),
			'etime' => strtotime($params['edate']),
			'type'  => $params['search_type'],
		);
		$tmpList = Gionee_Service_Log::getListByWhere($where);
		$tmp     = array();
		foreach ($tmpList as $v) {
			$tmp[$v['key']][$v['date']] = $v['val'];
		}

		$where   = array(
			'ver'   => 'ad_pos',
			'stime' => strtotime($params['sdate']),
			'etime' => strtotime($params['edate']),
			'key'   => array(
				'partner_weather_list',
				'partner_weather_content',
				'partner_weather_reclink',
				'partner_weather_list_txt'
			),
			'type'  => $params['search_type'] == 'partner_pv' ? 'pv' : 'uv',
		);
		$tmpList = Gionee_Service_Log::getListByWhere($where);
		$tmp1    = array();
		foreach ($tmpList as $v) {
			$tmp1[$v['key']][$v['date']] = $v['val'];
		}

		$sumWhere = array(
			'ver'  => 'weather_detail',
			'type' => $params['search_type'],
		);
		$list     = $dateList = $contents = array();
		for ($i = $where['stime']; $i <= $where['etime']; $i += 86400) {
			$dataName            = date('Y-m-d', $i);
			$date                = date('Ymd', $i);
			$sumWhere['date']    = $date;
			$list[$dataName]     = !empty($tmp[1][$date]) ? intval($tmp[1][$date]) : 0;
			$list2[$dataName]    = !empty($tmp[2][$date]) ? intval($tmp[2][$date]) : 0;
			$list3[$dataName]    = $list[$dataName] + $list2[$dataName];
			$contents[$dataName] = Gionee_Service_Log::getPartnerSumByWhere($sumWhere);

			$adListTxt[$dataName]  = !empty($tmp1['partner_weather_list_txt'][$date]) ? intval($tmp1['partner_weather_list_txt'][$date]) : 0;
			$adList[$dataName]     = !empty($tmp1['partner_weather_list'][$date]) ? intval($tmp1['partner_weather_list'][$date]) : 0;
			$adContents[$dataName] = !empty($tmp1['partner_weather_content'][$date]) ? intval($tmp1['partner_weather_content'][$date]) : 0;
			$adreclist[$dataName]  = !empty($tmp1['partner_weather_reclink'][$date]) ? intval($tmp1['partner_weather_reclink'][$date]) : 0;

		}

		$this->assign('params', $params);
		$this->assign('searchType', $this->searchType);
		$this->assign('list', $list);
		$this->assign('list2', $list2);
		$this->assign('list3', $list3);
		$this->assign('contents', $contents);

		$this->assign('adContents', $adContents);
		$this->assign('adList', $adList);
		$this->assign('adreclist', $adreclist);
		$this->assign('adListTxt', $adListTxt);
	}

	public function statmusicAction() {
		$params = $this->getInput(array('sdate', 'edate', 'search_type'));
		!$params['sdate'] && $params['sdate'] = date('Y-m-d', strtotime("-8 day"));
		!$params['edate'] && $params['edate'] = date('Y-m-d', strtotime("today"));
		!$params['search_type'] && $params['search_type'] = 'partner_pv';
		//uv
		$where   = array(
			'ver'   => 'singer_list',
			'stime' => strtotime($params['sdate']),
			'etime' => strtotime($params['edate']),
			'key'   => 1,
			'type'  => $params['search_type'],
		);
		$tmpList = Gionee_Service_Log::getListByWhere($where);
		$tmp     = array();
		foreach ($tmpList as $v) {
			$tmp[$v['date']] = $v['val'];
		}

		$sumWhere = array(
			'ver'  => 'singer_news',
			'type' => $params['search_type'],
		);
		$list     = $dateList = $contents = array();
		for ($i = $where['stime']; $i <= $where['etime']; $i += 86400) {
			$dataName            = date('Y-m-d', $i);
			$date                = date('Ymd', $i);
			$sumWhere['date']    = $date;
			$list[$dataName]     = !empty($tmp[$date]) ? intval($tmp[$date]) : 0;
			$contents[$dataName] = Gionee_Service_Log::getPartnerSumByWhere($sumWhere);
		}

		$this->assign('params', $params);
		$this->assign('searchType', $this->searchType);
		$this->assign('list', $list);
		$this->assign('contents', $contents);
	}

	public function statcalendarAction() {
		$params = $this->getInput(array('sdate', 'edate', 'search_type'));
		!$params['sdate'] && $params['sdate'] = date('Y-m-d', strtotime("-8 day"));
		!$params['edate'] && $params['edate'] = date('Y-m-d', strtotime("today"));
		!$params['search_type'] && $params['search_type'] = 'partner_pv';
		//uv
		$where   = array(
			'ver'   => 'calendar_list',
			'stime' => strtotime($params['sdate']),
			'etime' => strtotime($params['edate']),
			'key'   => 1,
			'type'  => $params['search_type'],
		);
		$tmpList = Gionee_Service_Log::getListByWhere($where);
		$tmp     = array();
		foreach ($tmpList as $v) {
			$tmp[$v['date']] = $v['val'];
		}

		$where   = array(
			'ver'   => 'ad_pos',
			'stime' => strtotime($params['sdate']),
			'etime' => strtotime($params['edate']),
			'key'   => array('partner_calender_content', 'partner_calender_list', 'partner_calender_list_txt'),
			'type'  => $params['search_type'] == 'partner_pv' ? 'pv' : 'uv',
		);
		$tmpList = Gionee_Service_Log::getListByWhere($where);
		$tmp1    = array();
		foreach ($tmpList as $v) {
			$tmp1[$v['key']][$v['date']] = $v['val'];
		}


		$sumWhere = array(
			'ver'  => 'calendar_detail',
			'type' => $params['search_type'],
		);
		$list     = $dateList = $contents = array();
		for ($i = $where['stime']; $i <= $where['etime']; $i += 86400) {
			$dataName              = date('Y-m-d', $i);
			$date                  = date('Ymd', $i);
			$sumWhere['date']      = $date;
			$list[$dataName]       = !empty($tmp[$date]) ? intval($tmp[$date]) : 0;
			$contents[$dataName]   = Gionee_Service_Log::getPartnerSumByWhere($sumWhere);
			$adContents[$dataName] = !empty($tmp1['partner_calender_content'][$date]) ? intval($tmp1['partner_calender_content'][$date]) : 0;
			$adList[$dataName]     = !empty($tmp1['partner_calender_list'][$date]) ? intval($tmp1['partner_calender_list'][$date]) : 0;
			$adListTxt[$dataName]  = !empty($tmp1['partner_calender_list_txt'][$date]) ? intval($tmp1['partner_calender_list_txt'][$date]) : 0;
		}

		$this->assign('params', $params);
		$this->assign('searchType', $this->searchType);
		$this->assign('list', $list);
		$this->assign('contents', $contents);
		$this->assign('adContents', $adContents);
		$this->assign('adList', $adList);
		$this->assign('adListTxt', $adListTxt);
	}
}