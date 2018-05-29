<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * class StatController
 * 统计报表类
 *
 */
class StatController extends Admin_BaseController {

	public $actions = array(
		'listUrl'        => '/Admin/Stat/index',
		'shortUrl'       => '/Admin/Stat/shorturl',
		'content'        => '/Admin/Stat/content',
		'column'         => '/Admin/Stat/column',
		'leading'        => '/Admin/Stat/leading',
		'cooperate'      => '/Admin/Stat/cooperate',
		'hotwords'       => '/Admin/Stat/hotwords',
		'amount'         => '/Admin/Stat/amount',
		'topicList'      => '/Admin/Stat/topicList',
		'topic'          => '/Admin/Stat/topic',
		'uvUrl'          => '/Admin/Stat/uv',
		'fedmsg'         => '/Admin/Stat/feedbackDetail',
		'search'         => '/Admin/Stat/baiduSearch',
		'monkeytime'     => '/Admin/Stat/monkeytime',
		'tourlexportUrl' => '/Admin/Stat/tourlexport',
	);

	public $perpage      = 20;
	public $pgaeCategory = array('nav' => '导航页', 'news' => '新闻页', 'games' => '轻应用');
	public $positions    = array(
		'0' => '全部',
		'1' => '顶部站点',
		'2' => '轮播图片',
		'4' => '文字链1',
		'5' => '推荐广告',
		'6' => '文字链2',
		'7' => '文字链3',
		'8' => '文字链4',
		'9' => '底部站点',
	);

	public function pv2Action() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$group = $this->getInput('group');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));


		$keys = isset(Gionee_Service_Log::$statKeys[$group]) ? Gionee_Service_Log::$statKeys[$group] : Gionee_Service_Log::$statKeys['all'];

		//pv
		$lineData = Gionee_Service_Log::getStatData(Gionee_Service_Log::TYPE_PV, $keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));
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
		$this->assign('type', Gionee_Service_Log::TYPE_PV);
		$this->assign('key', implode(',', $keys));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('date', $date);
	}


	public function accesstimesAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		$keys   = array();
		$_klist = Gionee_Service_Log::getsBy(array(
			'date' => date('Ymd', strtotime("-1 day")),
			'type' => 'access_times_pv'
		));
		foreach ($_klist as $val) {
			$keys[$val['key']] = $val['key'];
		}
		//pv
		$lineData = Gionee_Service_Log::getStatData('access_times_pv', $keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));
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
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('date', $date);
	}

	public function uv2Action() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$group = $this->getInput('group');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
		$keys = isset(Gionee_Service_Log::$statKeys[$group]) ? Gionee_Service_Log::$statKeys[$group] : Gionee_Service_Log::$statKeys['all'];
		//uv
		$lineData = Gionee_Service_Log::getStatData(Gionee_Service_Log::TYPE_UV, $keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));
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
		$this->assign('type', Gionee_Service_Log::TYPE_UV);
		$this->assign('key', implode(',', $keys));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('date', $date);
	}

	public function uv3Action() {
		$ym = $this->getInput('ym');
		if (empty($ym)) {
			$ym = date('Ym');
		}
		$type = $this->getInput('type');

		$types = array(
			Gionee_Service_Log::TYPE_URL_UV,
			Gionee_Service_Log::TYPE_NEWS_UV,
			Gionee_Service_Log::TYPE_CONTENT_UV,
		);
		if (empty($type)) {
			$type = $types[0];
		}
		$list = Gionee_Service_Log::getTotalByMonth($type, $ym);

		$this->assign('lineData', $list);
		$this->assign('type', $type);
		$this->assign('types', $types);
		$this->assign('ym', $ym);
	}

	public function shorturlAction() {
		$type = $this->getInput('type');
		if (empty($type)) {
			$type = Gionee_Service_Log::TYPE_URL;
		}

		$page   = $this->getInput('page');
		$sDate  = $this->getInput('sdate');
		$eDate  = $this->getInput('edate');
		$export = $this->getInput('export');
		$urlVal = $this->getInput('url_val');
		$urlKey = $_REQUEST['url_key'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		$types = array(Gionee_Service_Log::TYPE_URL, Gionee_Service_Log::TYPE_URL_UV);
		$where = array();
		if (!empty($urlVal)) {
			$where['url'] = array('LIKE', $urlVal);
		}

		if (!empty($urlKey)) {
			$where['key'] = array('IN', explode(',', $urlKey));
		}
		//总URL数
		$total = Gionee_Service_ShortUrl::getTotal($where);

		$pageSize = $this->perpage;
		if ($export) {
			$pageSize = $total;
		}
		$keys = $hashVal = $list = array();
		$urls = Gionee_Service_ShortUrl::getList($page, $pageSize, $where);
		if ($urls) {

			foreach ($urls as $val) {
				$keys[]               = $val['key'];
				$info                 = json_decode($val['mark'], true);
				$hashVal[$val['key']] = html_entity_decode($val['url']) . "({$info['id']},{$info['type']})";
			}
			$lineData = Gionee_Service_Log::getUrlList($keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)), $type);
			$date     = array();
			for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
				$date[] = date('Y-m-d', $i);
			}

			foreach ($lineData as $k => $v) {
				foreach ($date as $kdv) {
					$list[$k][$kdv] = isset($v[$kdv]) ? intval($v[$kdv]) : 0;
				}
			}

			if ($export) {
				$this->_exportCloumn($list, $sDate, $eDate, 'shorturl', array('date' => $date, 'url' => $hashVal));
				exit();
			}
		}

		$this->assign('lineData', $list);
		$this->assign('hashVal', $hashVal);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['shortUrl'] . "/?sdate={$sDate}&edate={$eDate}&url_val={$urlVal}&type={$type}&"));
		$this->assign('date', $date);
		$this->assign('url_val', $urlVal);
		$this->assign('url_key', $urlKey);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('types', $types);
		$this->assign('type', $type);
	}

	public function stat_nav($searchParam = array()) {
		$sDate = $searchParam['sdate'];
		$eDate = $searchParam['edate'];

		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}
		if (!$searchParam['column_id']) {
			$conditionArr = array();
			$searchParam['showStatus'] != 0 && $conditionArr['status'] = $searchParam['showStatus'] - 1;
			!empty($searchParam['keywords']) && $conditionArr['name'] = array('LIKE', $searchParam['keywords']);
			!empty($searchParam['type_id']) && $conditionArr['type_id'] = $searchParam['type_id'];
			if (in_array($searchParam['channel'], array('com', 'all'))) { //渠道统计条件
				list($total, $columnData) = $this->_getColumnData($conditionArr);
				$other = array();
				$searchParam['channel'] == 'com' && $other['ver'] = '';
				list($sum, $result) = $this->_getNgColumnCountInfo($columnData, $sDate, $eDate, $other);
			} else {
				list($sum, $result) = $this->_getNgColumnCountByChannel($searchParam['channel'], $sDate, $eDate, $conditionArr);
			}
		} else {
			$other = array();
			if ($searchParam['channel'] != 'all') {
				$other['ver'] = $searchParam['channel'] == 'com' ? ' ' : $searchParam['channel'];
			}
			$searchParam['showStatus'] != 0 && $other['condition']['status'] = $searchParam['showStatus'] - 1;
			!empty($searchParam['keywords']) && $other['condition']['title'] = array('LIKE', $searchParam['keywords']);
			!empty($searchParam['url']) && $other['condition']['link'] = array('LIKE', $searchParam['url']);
			$colType  = $searchParam['dataType'] == 1 ? Gionee_Service_Log::TYPE_CONTENT_UV : Gionee_Service_Log::TYPE_NAV;
			$lineData = Gionee_Service_Log::getNgColumnData($searchParam['column_id'], date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)), $colType, $other);

			$list = $sumTrData = array();
			$sum  = 0;
			foreach ($lineData as $k => $v) {
				$info  = Gionee_Service_Ng::get($k);
				$title = $info['title'];
				foreach ($date as $kdv) {
					$list[$k]['title'] = $title;
					$list[$k][$kdv]    = isset($v[$kdv]) ? intval($v[$kdv]) : 0;
					$list[$k]['ngid']  = $k;
					$sumTrData[$kdv] += intval($v[$kdv]);
				}
			}
			$sumTrData[] = array_sum($sumTrData);
			list($sum, $result, $sumTrData) = array($sum, $list, $sumTrData);
		}
		return array($sum, $result, count($result), $sumTrData, 0, 0);
	}

	public function stat_localnav($searchParam = array()) {
		$this->perpage = 50;
		$sDate         = $searchParam['sdate'];
		$eDate         = $searchParam['edate'];
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Ymd', $i);
		}
		if ($searchParam['local_type_id']) {
			$words   = $ids = array();
			$columns = array();

			$conditionArr = array('type_id' => $searchParam['local_type_id']);

			if ($searchParam['local_column_id']) {
				$conditionArr = array('column_id' => $searchParam['local_column_id']);
			}
			$searchParam['showStatus'] != 0 && $conditionArr['status'] = $searchParam['showStatus'] - 1;
			!empty($searchParam['keywords']) && $conditionArr['name'] = array('LIKE', $searchParam['keywords']);
			!empty($searchParam['url']) && $conditionArr['link'] = array('LIKE', $searchParam['url']);
			$total       = Gionee_Service_LocalNavList::count($conditionArr);
			$contentList = Gionee_Service_LocalNavList::getList(1, $total, $conditionArr, array('id' => 'desc'));

			foreach ($contentList as $v) {
				$column            = Gionee_Service_LocalNavColumn::get($v['column_id']);
				$ids[]             = $v['id'];
				$words[$v['id']]   = $v['name'];
				$columns[$v['id']] = $column['name'];
			}

			if (!empty($ids)) {
				$type           = $searchParam['dataType'] == 1 ? 'localnav_uv' : 'localnav_pv';
				$params         = array();
				$params['type'] = $type;
				$params['key']  = array('IN', $ids);
				$params['date'] = array(
					array('>=', date('Ymd', strtotime($searchParam['sdate']))),
					array('<=', date('Ymd', strtotime($searchParam['edate'])))
				);
				$orderby        = array('`key`' => 'desc');
				$dataList       = Gionee_Service_Log::getsBy($params, $orderby);
				$data           = $date = array();
				$sum            = 0;


				if (empty($searchParam['local_column_id'])) {
					foreach ($dataList as $k => $v) {
						$t                   = date('Y-m-d', strtotime($v['date']));
						$name                = isset($columns[$v['key']]) ? $columns[$v['key']] : '';
						$data[$name]['name'] = $name;
						$data[$name][$t] += $v['val'];
						$sum += $v['val'];
					}

				} else {
					foreach ($dataList as $k => $v) {
						$t                         = date('Y-m-d', strtotime($v['date']));
						$data[$v['key']][$t]       = $v['val'];
						$data[$v['key']]['name']   = $words[$v['key']];
						$data[$v['key']]['column'] = isset($columns[$v['key']]) ? $columns[$v['key']] : '';
						$data[$v['key']]['id']     = $v['key'];
						$sum += $v['val'];
					}
				}


			} else {
				$data = array();
			}

		}
		return array($sum, $data, 0, 0, 0, 0);
	}


	public function stat_news($searchParam = array()) {
		$sDate = $searchParam['sdate'];
		$eDate = $searchParam['edate'];
		$other = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Ymd', $i);
		}
		$searchParam['dataType'] == 1 && $other['statype'] = Gionee_Service_Log::TYPE_NEWS_UV;
		$searchParam['showStatus'] != 0 && $other['condition']['status'] = $searchParam['showStatus'] - 1;
		!empty($searchParam['keywords']) && $other['condition']['title'] = array('LIKE', $searchParam['keywords']);
		!empty($searchParam['url']) && $other['condition']['link'] = array('LIKE', $searchParam['url']);
		list($sum, $result, $total) = $this->_getShouHuCountInfo($searchParam['pos_id'], $sDate, $eDate, $other);

		return array($sum, $result, $total, 0, 0, 0);
	}

	public function stat_sites($searchParam = array()) {
		$sDate = $searchParam['sdate'];
		$eDate = $searchParam['edate'];
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Ymd', $i);
		}
		if ($searchParam['siteCate1']) {
			$words = $ids = array();
			if ($searchParam['siteCate2']) {
				$conditionArr = array('cat_id' => $searchParam['siteCate2']);
				$searchParam['showStatus'] != 0 && $conditionArr['status'] = $searchParam['showStatus'] - 1;
				$contentList = Gionee_Service_SiteContent::getsBy($conditionArr);
				foreach ($contentList as $v) {
					$ids[]           = $v['id'];
					$words[$v['id']] = $v['name'];
				}
			} else {
				$conditionArr = array('parent_id' => $searchParam['siteCate1']);
				$searchParam['showStatus'] != 0 && $conditionArr['status'] = $searchParam['showStatus'] - 1;
				$subCatList = Gionee_Service_SiteCategory::getsBy($conditionArr);
				if ($subCatList) {
					foreach ($subCatList as $m) {
						$contents = Gionee_Service_SiteContent::getsBy(array('cat_id' => $m['id']));
						foreach ($contents as $k) {
							$ids[]           = $k['id'];
							$words[$k['id']] = $k['name'];
						}
					}
				}
			}
			if (!empty($ids)) {
				$params         = array();
				$params['key']  = array('IN', $ids);
				$params['type'] = 'sitemap';
				$params['date'] = array(
					array('>=', date('Ymd', strtotime($searchParam['sdate']))),
					array('<=', date('Ymd', strtotime($searchParam['edate'])))
				);
				$dataList       = Gionee_Service_Log::getsBy($params);
				$data           = $date = array();
				$sum            = 0;
				foreach ($dataList as $k => $v) {
					$t                       = date('Y-m-d', strtotime($v['date']));
					$data[$v['key']][$t]     = $v['val'];
					$data[$v['key']]['name'] = $words[$v['key']];
					$data[$v['key']]['id']   = $v['id'];
					$sum += $v['val'];
				}
			} else {
				$data = array();
			}
		}
		return array($sum, $data, count($data), 0, 0, 0);
	}

	public function stat_bookmark($searchParam = array()) {
		$sDate    = $searchParam['sdate'];
		$eDate    = $searchParam['edate'];
		$tmp      = Gionee_Service_Config::getValue('recwebsite_tourl');
		$tmpdesc  = Gionee_Service_Config::getValue('recwebsite_tourl_desc');
		$urls     = json_decode($tmp, true);
		$urlsdesc = json_decode($tmpdesc, true);

		$keys = $keys1 = $hashVal = array();
		foreach ($urls as $key => $url) {
			parse_str($key, $p);
			if (!in_array($p['m'], array('A809', 'V338', 'wap'))) {
				$t           = crc32($url . 1 . 'TOURL' . Common::$urlPwd);
				$keys[]      = $t;
				$hashKey[$t] = $key;
			}

			$t1              = Gionee_Service_ShortUrl::genTVal(crc32($key) . $url . 'TOURL' . Common::$urlPwd);
			$keys[]          = $t1;
			$hashKey[$t1]    = $key;
			$hashTitle[$key] = $urlsdesc[$key];
			$hashVal[$key]   = $url;
		}
		$lineData = Gionee_Service_Log::getUrlList($keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));

		$date = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}

		$list = array();
		foreach ($lineData as $k => $v) {
			$kName = $hashKey[$k];
			foreach ($date as $d) {
				$list[$kName][$d] += isset($v[$d]) ? intval($v[$d]) : 0;
			}
		}

		ksort($list);

		return array(0, $list, count($list), 0, $hashVal, $hashTitle);
	}

	public function export_nav_t($result, $sDate, $eDate, $type, $date, $other) {
		$other = array('date' => $date, 'filename' => '导航统计');
		$this->_exportCloumn($result, $sDate, $eDate, 'navType', $other);
	}

	public function export_nav_c($result, $sDate, $eDate, $type, $date, $other) {
		$other = array('date' => $date, 'filename' => '导航统计');
		$this->_exportCloumn($result, $sDate, $eDate, 'navCol', $other);
	}


	public function export_news($result, $sDate, $eDate, $type, $date, $other) {
		$other = array('date' => $date, 'filename' => '新闻统计');
		$this->_exportCloumn($result, $sDate, $eDate, $type, $other);
	}

	public function export_sites($result, $sDate, $eDate, $type, $date, $other) {
		$other = array('date' => $date, 'filename' => '网址大全统计');
		$this->_exportCloumn($result, $sDate, $eDate, $type, $other);
	}

	public function export_localnav($result, $sDate, $eDate, $type, $date, $other) {
		$other = array('date' => $date, 'filename' => '本地化导航');
		$this->_exportCloumn($result, $sDate, $eDate, $type, $other);
	}

	public function export_bookmark($result, $sDate, $eDate, $type, $date, $other) {
		$filename = '书签页' . date('YmdHis') . '.csv';
		$headers  = array_merge(array('name', 'key'), $date);
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		$hashTitle = $other;

		fputcsv($fp, $headers);
		foreach ($result as $k => $v) {
			$row = array_merge(array($hashTitle[$k], $k), $v);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}


	/**
	 * 分页面 pv/uv 统计
	 */
	public function pvstatAction() {
		$searchParam = $this->getInput(array(
			'type_id',
			'column_id',
			'local_type_id',
			'local_column_id',
			'page_type',
			'pos_id',
			'page_id',
			'channel',
			'export',
			'position_id',
			'siteCate1',
			'siteCate2',
			'keywords',
			'url',
			'showStatus',
			'dataType',
		));
		list($page, $sDate, $eDate) = $this->_getParams();
		$navChannelList = Gionee_Service_Vendor::getAll();  //获得所有渠道号
		$arr1           = array('id' => '-1', 'name' => '全部', 'ch' => 'all');
		$arr2           = array('id' => '0', 'name' => '普通', 'ch' => 'com');
		array_unshift($navChannelList, $arr1, $arr2);

		$siteCate1    = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => 'DESC'));
		$siteCate2    = Gionee_Service_SiteCategory::getsBy(array('>' => array('parent_id', 0)), array('id' => 'DESC'));
		$localnavtype = Gionee_Service_LocalNavType::options();
		//查询过滤条件
		$searchData = array(
			'pageCate'       => array(
				'nav'      => '导航页',
				'news'     => '新闻页',
				'sites'    => '网址大全页',
				'bookmark' => '书签页',
				'localnav' => '本地化导航'
			),
			'navCate'        => Gionee_Service_NgType::getAll(),
			'navColumn'      => Gionee_Service_NgColumn::getAll(),
			'navChannel'     => $navChannelList,
			'siteCate1'      => $siteCate1,
			'siteCate2'      => $siteCate2,
			'position'       => $this->positions,
			'showStatus'     => array(0 => '所有', 1 => '关闭', 2 => '开启'),
			'dataType'       => array(0 => 'pv', 1 => 'uv'),
			'localnavtype'   => $localnavtype,
			'localnavcolumn' => Gionee_Service_LocalNavColumn::getsBy(),
		);

		$searchParam['sdate']      = $sDate;
		$searchParam['edate']      = $eDate;
		$searchParam['page_type']  = $searchParam['page_type'] ? $searchParam['page_type'] : 'nav';
		$searchParam['channel']    = $searchParam['channel'] ? $searchParam['channel'] : 'all';
		$searchParam['page_id']    = $searchParam['page_id'] ? $searchParam['page_id'] : '0'; //实际查询时不起作用
		$searchParam['pos_id']     = $searchParam['pos_id'] ? $searchParam['pos_id'] : '0';
		$searchParam['showStatus'] = $searchParam['showStatus'] ? $searchParam['showStatus'] : 0;
		$searchParam['dataType']   = $searchParam['dataType'] ? $searchParam['dataType'] : 0;
		$searchParam['page']       = $page;

		$export = $searchParam['export'];

		$date = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}

		$statFunc = 'stat_' . $searchParam['page_type'];
		if (!method_exists($this, $statFunc)) {
			echo $statFunc;
			exit;
		}
		$callres = $this->$statFunc($searchParam);

		list($sum, $result, $total, $sumTrData, $hashVal, $hashTitle) = $callres;

		if ($export) { //导出
			$exportFunc = 'export_' . $searchParam['page_type'];

			if ($searchParam['page_type'] == 'nav') {
				$exportFunc .= !$searchParam['column_id'] ? '_t' : '_c';
			}
			if (!method_exists($this, $exportFunc)) {
				exit;
			}
			$this->$exportFunc($result, $sDate, $eDate, $searchParam['page_type'], $date, $hashTitle);
			exit();
		} else {
			if ($searchParam['page_type'] == 'bookmark' || $searchParam['page_type'] == 'news' || ($searchParam['page_type'] == 'nav' && !$searchParam['type_id']) || ($searchParam['page_type'] == 'sites' && !$searchParam['siteCate2'])) {
				$result = array_slice($result, ($page - 1) * $this->perpage, $this->perpage);
			}
		}
		$this->assign('date', $date);
		$this->assign('sum', $sum);
		$this->assign('sumTrData', $sumTrData);
		$this->assign('list', $result);
		$this->assign('hashVal', $hashVal);
		$this->assign('hashTitle', $hashTitle);
		$this->assign('searchParam', $searchParam);
		$this->assign('searchData', $searchData);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, 'pvstat?' . http_build_query($searchParam) . '&'));

	}


	/**
	 * CP 日报
	 */
	public function cpdayAction() {
		$searchParam = $this->getInput(array('cp', 'keywords', 'export', 'page'));
		list($page, $sDate, $eDate) = $this->_getParams();
		$searchParam['sdate'] = $sDate;
		$searchParam['edate'] = $eDate;

		$export   = $searchParam['export'] ? $searchParam['export'] : 0;
		$page     = $searchParam['page'] ? $searchParam['page'] : 1;
		$pagesize = $searchParam['pagesize'] ? $searchParam['pagesize'] : 20;

		//查询过滤条件
		$cpCondition = array();
		!empty($searchParam['cp']) && $cpCondition['name'] = array('LIKE', $searchParam['cp']);
		$searchData   = array(
			'cpList' => Gionee_Service_Parter::getsBy($cpCondition),
		);
		$conditionArr = array();
		!empty($searchParam['keywords']) && $conditionArr['title'] = array('LIKE', $searchParam['keywords']);
		$tpvList = $dataList = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$t           = date('Y-m-d', $i);
			$date[]      = $t;
			$tpvList[$t] = 0;
		}

		foreach ($searchData['cpList'] as $cp) {
			$conditionArr['partner'] = $cp['id'];
			$ngList                  = Gionee_Service_Ng::getsBy($conditionArr);
			$logCondition            = array();
			$logCondition['type']    = Gionee_Service_Log::TYPE_NAV;
			$logCondition['date']    = array(
				array('>=', date('Ymd', strtotime($searchParam['sdate']))),
				array('<=', date('Ymd', strtotime($searchParam['edate'])))
			);

			foreach ($ngList as $k => $v) {
				$logCondition['key'] = $v['id'];
				$pvList              = Gionee_Service_Log::getsBy($logCondition);
				$key                 = 'x' . $v['id'] . crc32($v['link']);
				if (isset($dataList[$key]) && !empty($pvList)) {
					foreach ($pvList as $pv) {
						$t = date('Y-m-d', strtotime($pv['date']));
						$dataList[$key]['pv'][$t] += intval($pv['val']);
						continue;
					}
				} else {
					empty($pvList) && $pvList = $tpvList;
					foreach ($pvList as $pv) {
						$dataList[$key] = array(
							'id'    => $v['id'],
							'title' => $v['title'],
							'cp'    => $cp['name']
						);
						if (!empty($pv['date'])) {
							$t                        = date('Y-m-d', strtotime($pv['date']));
							$dataList[$key]['pv'][$t] = empty($pv['val']) ? 0 : intval($pv['val']);
						} else {
							$dataList[$key]['pv'][$pv] = 0;
						}
					}
				}
			}
		}

		$total = count($dataList);

		if ($export) {
			$other = array('date' => $date, 'filename' => 'CP日报');
			$this->_exportCloumn($dataList, $sDate, $eDate, 'cpday', $other);
			exit;
		} else {
			$result = array_slice($dataList, ($page - 1) * $pagesize, $pagesize);
		}
		$this->assign('searchData', $searchData);
		$this->assign('searchParam', $searchParam);
		$this->assign('date', $date);
		$this->assign('dataList', $result);
		$this->assign('pager', Common::getPages($total, $page, $pagesize, 'cpday?' . http_build_query($searchParam) . '&'));

	}

	/**
	 * PC&导流统计
	 */
	public function leadingAction() {
		$attributes = array('普通', '导流', '广告');
		$postData   = $this->getInput(array('attrType', 'channel', 'partner_id', 'export'));
		list($page, $sDate, $eDate) = $this->_getParams();
		$attrType = $postData['attrType'] ? $postData['attrType'] : '普通'; //内容属性
		$params   = $p = array();
		if (in_array($attrType, array($attributes[1], $attributes[2]))) {
			$params['style'] = $attrType;
		} else {
			$params['style'] = array('NOT IN', array($attributes[1], $attributes[2]));
		}
		$partnerName = '';
		//添加合作商家查询条件
		if ($postData['partner_id'] > 0) {
			$partnerInfo = Gionee_Service_Parter::get($postData['partner_id']);
			$partnerName = $params['partner'] = $partnerInfo['name'];
		}
		$sum   = 0; // 满足条件的总点击数
		$total = Gionee_Service_Ng::count($params);
		if (in_array($postData['channel'], array('com', 'all'))) { //如果渠道不为空，则先查LOG表信息，否则先查NG表
			$dataList = Gionee_Service_Ng::getsBy($params, array('id' => 'DESC'));
			$elements = array();
			if ($postData['channel'] == 'com') { //普通渠道时
				$elements['ver'] = '';
			}
			foreach ($dataList as $k => $v) {
				$perMount                = Gionee_Service_Log::getNgTotalNumber(array_merge($elements, array(
					'key'   => $v['id'],
					'sdate' => strtotime($sDate),
					'edate' => strtotime($eDate),
					'type'  => Gionee_Service_Log::TYPE_NAV
				)));
				$dataList[$k]['clicked'] = $perMount;
				$sum += $perMount;
			}
			if (!$postData['export']) {
				$dataList = array_slice($dataList, $this->perpage * ($page - 1), $this->perpage);
			}
		} else {
			$p['ver']   = $postData['channel'];
			$p['sdate'] = strtotime($sDate);
			$p['edate'] = strtotime($eDate);
			$types      = array(Gionee_Service_Log::TYPE_NAV, Gionee_Service_Log::TYPE_TOPIC_CONTENT);
			$logData    = Gionee_Service_Log::getSumByChannel($p, $types, array('key'), array('key')); //所有内容统计信息
			$ids        = $click = array();
			foreach ($logData as $k => $v) {
				$tempArr            = explode('&', $v['key']);
				$ids[]              = $tempArr[0];
				$click[$tempArr[0]] = $v['total']; //点击数
			}
			$params['id'] = array('IN', $ids ? array_unique($ids) : array(0));
			$total        = Gionee_Service_Ng::count($params);
			$dataList     = Gionee_Service_Ng::getsBy($params, array('id' => 'DESC'));
			foreach ($dataList as $k => $v) {
				$dataList[$k]['clicked'] = $click[$v['id']];
				$sum += $click[$v['id']];
			}
			if (!$postData['export']) {
				$dataList = array_slice($dataList, $this->perpage * ($page - 1), $this->perpage);
			}
		}
		if ($postData['export']) {
			$this->_exportLeading($dataList, $partnerName, $attrType);
			exit;
		}
		$allChannel = Gionee_Service_Vendor::getAll();
		array_unshift($allChannel, array('name' => '全部', 'ch' => 'all'), array('name' => '普通', 'ch' => 'com'));
		$channelInfo = Gionee_Service_Vendor::getBy($postData['channel']);//渠道商家
		$partners    = Gionee_Service_Parter::getAll(); //获得所有合作商信息
		array_unshift($partners, array('id' => 0, 'name' => '全部'));
		$this->assign('list', $dataList);
		$this->assign('params', array(
			'partners'    => $partners,
			'partner_id'  => $postData['partner_id'],
			'cval'        => $postData['channel'],
			'channelInfo' => $channelInfo,
			'channels'    => $allChannel,
			'sdate'       => $sDate,
			'edate'       => $eDate,
			'attrubutes'  => $attributes,
			'attrType'    => $attrType,
			'sum'         => $sum
		));
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['leading'] . "?sdate={$sDate}&edate={$eDate}&attrType={$postData['attrType']}&channel={$postData['channel']}&partner_id={$postData['partner_id']}&"));
	}


	/**
	 * 导航热词统计
	 */
	public function hotwordsAction() {
		$type   = $this->getInput('type'); //查询类型 0，全部，1，后台添加 2，百度热词
		$type   = $type ? $type : 0;
		$export = $this->getInput('export');
		list($page, $sDate, $eDate) = $this->_getParams();
		$result = array();
		$sum    = $count = 0;

		//后台人工添加的统计
		if (in_array($type, array(0, 1))) {
			$ids          = array();
			$bd_column_id = Gionee_Service_Baidu::getColumnID();
			list($count, $dataList) = Gionee_Service_Ng::getList(1, $this->perpage, array('column_id' => $bd_column_id), array('id' => 'DESC'));
			foreach ($dataList as $v) {
				$ids[] = $v['id'];
			}
			if ($ids) {
				$calcuated = Gionee_Service_Log::getTotalByNidVer(array(
					'sdate' => strtotime($sDate . ' 00:00:00'),
					'edate' => strtotime($eDate . ' 23:59:59'),
					'ids'   => $ids
				));
				foreach ($calcuated as $v) {
					$temp[$v['key']] = $v['total'];
					$sum += $v['total'];
				}

				foreach ($dataList as $val) {
					$result[] = array('title' => $val['title'], 'clicked' => $temp[$val['id']]);
				}
			}
		}
		//百度热词的统计
		if (in_array($type, array(0, 2))) {
			$baiduAmount = Gionee_Service_Log::getSumByChannel(array(
				'sdate' => strtotime($sDate),
				'edate' => strtotime($eDate)
			), //where条件
				Gionee_Service_Log::TYPE_BAIDU_HOT, //类型
				array('key', 'ver'), array('key'),// group by
				array('total' => 'DESC'));
			foreach ($baiduAmount as $v) {
				$result[] = array('title' => $v['ver'], 'clicked' => $v['total']);
				$sum += $v['total'];
			}
		}
		//排序
		foreach ($result as $k => $v) {
			$num[$k] = $v['clicked'];
		}
		array_multisort($num, SORT_DESC, $result);
		$p['date'] = array(array('>=', date('Ymd', strtotime($sDate))), array('<=', date('Ymd', strtotime($eDate))));
		$p['type'] = Gionee_Service_Log::TYPE_BAIDU_HOT;
		$total     = Gionee_Service_Log::count($p);
		if ($export) {
			$this->_exportCloumn($result, $sDate, $eDate, 'hotwords');
			exit();
		}
		$result = array_slice($result, ($page - 1) * $this->perpage, $this->perpage);
		$this->assign('list', $result);
		$this->assign('sum', $sum);
		$this->assign('params', array('sdate' => $sDate, 'edate' => $eDate, 'page' => $page, 'type' => $type));
		$this->assign('types', array('0' => '全部', '1' => '手动', '2' => '百度热词'));
		$this->assign('pager', Common::getPages($total + $count, $page, $this->perpage, $this->actions['hotwords'] . "?sdate={$sDate}&edate={$eDate}&type={$type}&"));
	}


	/**
	 * 百度搜索统计
	 */
	public function baiduSearchAction() {
		$paramsData = $this->getInput(array('sdate', 'edate', 'page'));
		$page       = max(1, $paramsData['page']);
		$sDate      = $paramsData['sdate'];
		$eDate      = $paramsData['edate'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-1 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("now"));
		$export = $this->getInput('export');
		if ($export) {
			list($total, $dataList) = Gionee_Service_Searchwords::getSearchHot(1, 100, $sDate, $eDate, array('content'), array('total' => 'DESC'));
			$this->_exportCloumn($dataList, $sDate, $eDate, 'search');
			exit();
		}
		list($total, $dataList) = Gionee_Service_Searchwords::getSearchHot($page, $this->perpage, $sDate, $eDate, array('content'), array('total' => 'DESC'));
		$this->assign('data', $dataList);
		$this->assign('params', array('sdate' => $sDate, 'edate' => $eDate));
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['search'] . "/?sdate={$sDate}&edate={$eDate}&"));
	}


	/**
	 * 专题列表
	 */

	public function topicListAction() {

		list($page, $sDate, $eDate) = $this->_getParams();
		//推广内容点击
		$data = Gionee_Service_Log::getSumByChannel(array(
			'sdate' => strtotime($sDate . ' 00:00:00'),
			'edate' => strtotime($eDate . ' 23:59:59')
		), Gionee_Service_Log::TYPE_TOPIC_CONTENT, array('key'), array('key'));
		$sum  = 0; //总点击量
		$list = array();
		foreach ($data as $val) {
			$temp = explode('&', $val['key']);
			$list[$temp[1]] += $val['total'];
			$sum += $val['total'];
		}
		list($total, $dataList) = Gionee_Service_Topic::getElements(array(
			'id',
			'title',
			'like_num',
			'status',
			'start_time',
			'end_time'
		), array(
			'start_time' => array('<=', strtotime($eDate)),
			'end_time'   => array('>=', strtotime($sDate))
		), array('id' => "DESC"), array(($page - 1) * $this->perpage, $this->perpage));
		foreach ($dataList as $k => $v) {
			$tmp   = 0;
			$ver   = '';
			$array = array();
			//URL点击统计
			$result = Gionee_Service_Log::getSumByChannel(array(
				'ver'   => $v['id'],
				'sdate' => strtotime($sDate . ' 00:00:00'),
				'edate' => strtotime($eDate . ' 23:59:59')
			), array(
				Gionee_Service_Log::TYPE_TOPIC,
				Gionee_Service_Log::TYPE_TOPIC_LIST,
				Gionee_Service_Log::TYPE_TOPIC_MAIN
			), array('type', 'key', 'ver'), array('type'));
			foreach ($result as $val) {
				$array[$val['type']] = $val['total'];
				$sum += $val['total'];
				if (in_array($val['type'], array('topic_main', 'topic_list'))) {
					$tmp += $val['total'];
				}
				$ver = $val['ver'];
			}
			$dataList[$k]['clickInfo'] = $array;
			$dataList[$k]['csum']      = $list[$v['id']] + $tmp;
		}
		//内容点击统计
		$export = $this->getInput('export');
		if ($export) {
			$this->_exportCloumn($dataList, $sDate, $eDate, 'topic');
			exit();
		}
		$this->assign('date', array('sdate' => $sDate, 'edate' => $eDate));
		$this->assign('dataList', $dataList);
		$this->assign('sum', $sum);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['topicList'] . "?sdate={$sDate}&edate={$eDate}&"));
	}

	/**
	 * 专题详细统计信息
	 */

	public function topicAction() {
		$postData = $this->getInput(array('id', 'sdate', 'edate'));
		$id       = $postData['id'];
		$sDate    = $postData['sdate'];
		$eDate    = $postData['edate'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-10 day"));
		!$eDate && $eDate = date('Y-m-d', time());
		if (!$id) return false;
		$topicInfo = Gionee_Service_Topic::get($id);
		$topicCate = Gionee_Service_Log::getSumByChannel(array('ver' => $id), array(
			Gionee_Service_Log::TYPE_TOPIC,
			Gionee_Service_Log::TYPE_TOPIC_LIST,
			Gionee_Service_Log::TYPE_TOPIC_MAIN
		), array('type'), array('type'));
		$typeTotal = array();
		$sum       = 0;
		foreach ($topicCate as $v) {
			$typeTotal[$v['type']] = $v['total'];
			$sum += $v['total'];
		}

		$contentMsg = Gionee_Service_Log::getSumByChannel(array(), Gionee_Service_Log::TYPE_TOPIC_CONTENT, array(
			'key',
			'ver'
		), array('key'));
		$ng         = $topic = $titleList = array();
		//获得每个专题中内容的详细点击信息
		$channel = array();
		if ($contentMsg) {
			foreach ($contentMsg as $v) {
				$temp = explode('&', $v['key']);
				$topic[$temp[1]] += $v['total']; //针对专题
				$ng[$temp[1]][$temp[0]] += $v['total'];
				$channel[$temp[1]][$temp[0]] = $v['ver'];
				$ng[$temp[1]]['key'][]       = $v['key'];
			}
		}
		$params = $ngDatas = array();
		$ngKeys = array_keys($ng[$id]);
		if ($ngKeys) {
			$params['id'] = array('IN', $ngKeys);
			$ngDatas      = Gionee_Service_Ng::getsBy($params, array('id' => 'DESC'));
			foreach ($ngDatas as $key => $value) {
				$titleList[$value['id']] = $value['title'];
				$ngDatas[$key]['click']  = $ng[$id][$value['id']];
				$sum += $ng[$id][$value['id']];
				if ($channel) {
					$cinfo                  = Gionee_Service_Vendor::getBy($channel[$id][$value['id']]);
					$ngDatas[$key]['cname'] = $cinfo['name'] ? $cinfo['name'] : '--';
				} else {
					$ngDatas[$key]['cname'] = '普通';
				}
			}
		}
		//按时间查询
		$dateList = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$dateList[] = date('Ymd', $i);
		}
		$s = array();
		if (!empty($ng[$id]['key'])) {
			$options['date'] = array(
				array(">=", date('Ymd', strtotime($sDate))),
				array("<=", date('Ymd', strtotime($eDate)))
			);
			$options['type'] = Gionee_Service_Log::TYPE_TOPIC_CONTENT;
			$options['key']  = array('IN', $ng[$id]['key']);
			$data            = Gionee_Service_Log::getsBy($options);

			if ($data) {
				foreach ($data as $k => $v) {
					$key                 = explode('&', $v['key'])[0];
					$s[$key][$v['date']] = $v['val'];
					$s[$key]['title']    = $titleList[$key] ? $titleList[$key] : '';
				}
			}
		}

		//反馈信息
		$this->assign('sum', $sum);
		$this->assign('info', $topicInfo); //专题详细信息
		$this->assign('log', $typeTotal); //类型点击数
		$this->assign('content', $topic[$id]); //内容总点击
		$this->assign('ngData', $ngDatas); //内容详情
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('dlist', $dateList);
		$this->assign('s', $s);
	}

	/**
	 * 意见统计
	 */

	public function suggestionAction() {
		list($page, $sDate, $eDate, $title) = $this->_getParams();
		$params                = array();
		$params['create_time'] = array(array('>=', strtotime($sDate)), array("<=", strtotime($eDate)));
		if (!empty($title)) {
			$topicIds = array();
			$tmpList  = Gionee_Service_Topic::getsBy(array('title' => array('LIKE', $title)), array('id' => 'desc'));
			foreach ($tmpList as $val) {
				$topicIds[] = $val['id'];
			}
			$params['topic_id'] = array('IN', $topicIds);

			if (!empty($topicIds)) {
				$this->perpage = 100;
			}
		}

		$flieds  = array('sum(num) as total', 'topic_id');
		$order   = array('topic_id' => 'DESC', 'create_time' => 'DESC');
		$groupBy = array('topic_id');
		$limit   = array($this->perpage * ($page - 1), $this->perpage);
		list($total, $data) = Gionee_Service_Feedback::getFeedBackDatas($flieds, $params, $groupBy, $order, $limit);

		$temp = $result = $ids = array();
		foreach ($data as $key => $val) {
			$ids[]                                = $val['topic_id'];
			$temp[$val['topic_id']]['detail_url'] = $this->actions['fedmsg'] . '?topic_id=' . $val['topic_id'];
			$temp[$val['topic_id']]['num']        = $val['total'];
		}
		$searchFlieds = array('id', 'issue_name', 'option', 'title');
		$where        = array('id' => array("IN", array_unique($ids)));
		list($sum, $topicList) = Gionee_Service_Topic::getElements($searchFlieds, $where, array('id' => 'DESC'));
		foreach ($topicList as $k => $v) {
			$result[$k]['title']      = $v['title'];
			$result[$k]['id']         = $v['id'];
			$result[$k]['number']     = $temp[$v['id']]['num'];
			$result[$k]['issue_name'] = $v['issue_name'];
			$result[$k]['detail_url'] = $temp[$v['id']]['detail_url'];
		}

		$this->assign('data', $result);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['suggestion'] . "?sdate={$sDate}&edate={$eDate}&"));
		$this->assign('date', array('sdate' => $sDate, 'edate' => $eDate));
		$this->assign('title', $title);
	}

	/**
	 * 意见反馈详细信息
	 */
	public function feedbackDetailAction() {
		$topicId = $this->getInput('topic_id');
		list($total, $info) = Gionee_Service_Feedback::getsBy(array('topic_id' => $topicId), array('create_time' => 'DESC'));
		$topic = Gionee_Service_Topic::get($topicId);
		if (stristr($topic['option'], ',')) {
			$issues = explode(",", trim($topic['option']));
		} else {
			$issues = explode("\n", trim($topic['option']));
		}
		$ret = array();
		foreach ($info as $k => $v) {
			$ret[$k]['name']       = $issues[$v['option_num'] - 1];
			$ret[$k]['num']        = $v['num'];
			$ret[$k]['option_num'] = $v['option_num'];
		}
		$this->assign('data', $ret);
		$this->assign('topic', $topic);
	}

	/**
	 * 导航统计导出
	 */
	public function _exportNg($params = array(), $date = array()) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $params['extType'] . 'statistics-data ' . date('Ymd') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$out      = fopen('php://output', 'w');
		$typeName = $columnName = '';
		if (isset($params['type_id'])) {
			$types    = Gionee_Service_NgType::get($params['type_id']);
			$typeName = $types['name'];
		}
		if ($params['column_id']) {
			$columns    = Gionee_Service_NgColumn::get($params['column_id']);
			$columnName = $columns['name'];
		}
		$sum  = Gionee_Service_Ng::count($params);
		$list = Gionee_Service_Ng::getList(1, $sum, $params);
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		fputcsv($out, array('ID', '排序', '状态', '内容', '链接', '开始时间', '结束时间', '页面', '分类', '栏目', '属性', '点击量'));
		foreach ($list[1] as $val) {
			$sta     = $val['status'] == '1' ? '正常' : '已下线';
			$clicked = Gionee_Service_Log::getNgTotalNumber(array(
				'key'   => $val['id'],
				'sdate' => strtotime($date['sdate']),
				'edate' => strtotime($date['edate']),
				'ver'   => '',
				'type'  => Gionee_Service_Log::TYPE_NAV
			)); //点击数
			fputcsv($out, array(
				$val['title'],
				$val['id'],
				$sta,
				$val['link'],
				date('Y-m-d', $date['start_time']),
				date('Y-m-d', $val['end_time']),
				1,
				$typeName,
				$columnName,
				$val['id'],
				$val['style'],
				$clicked
			));
		}
		fclose($out);
	}

	/**
	 * 内容页数据导出
	 */
	public function _export($params = array(), $date = array()) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = 'content-statistics-data ' . date('Ymd') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$out      = fopen('php://output', 'w');
		$typeName = $columnName = '';
		if (isset($params['type_id'])) {
			$types    = Gionee_Service_NgType::get($params['type_id']);
			$typeName = $types['name'];
		}
		if ($params['column_id']) {
			$columns    = Gionee_Service_NgColumn::get($params['column_id']);
			$columnName = $columns['name'];
		}
		$sum  = Gionee_Service_Ng::count($params);
		$list = Gionee_Service_Ng::getList(1, $sum, $params);
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		fputcsv($out, array('ID', '排序', '状态', '内容', '链接', '开始时间', '结束时间', '页面', '分类', '栏目', '属性', '点击量'));
		foreach ($list[1] as $val) {
			$sta     = $val['status'] == '1' ? '正常' : '已下线';
			$clicked = Gionee_Service_Log::getNgTotalNumber(array(
				'key'   => $val['id'],
				'sdate' => strtotime($date['sdate']),
				'edate' => strtotime($date['edate']),
				'ver'   => '',
				'type'  => Gionee_Service_Log::TYPE_NAV
			)); //点击数
			fputcsv($out, array(
				$val['title'],
				$val['id'],
				$sta,
				$val['link'],
				date('Y-m-d', $date['start_time']),
				date('Y-m-d', $val['end_time']),
				1,
				$typeName,
				$columnName,
				$val['id'],
				$val['style'],
				$clicked
			));
		}
		fclose($out);
	}

	/**
	 * 栏目统计数据导出CSV
	 */
	private function _exportCloumn($msg, $sdate, $edate, $flag, $other = array()) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = empty($other['filename']) ? '栏目统计' : $other['filename'];
		$filename .= $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($flag) {
			case 'nav': {
				fputcsv($out, array('日期', '栏目', '所属页面', '点击量'));
				foreach ($msg as $v) {
					fputcsv($out, array(
						$sdate . ' 至 ' . $edate,
						$v['name'],
						$this->pgaeCategory[$flag],
						$v['clicked']
					));
				}
				break;
			}
			case 'navType': {
				$title = array('ID', '栏目');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				$total = array();
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $k);
					array_push($temp, $v['name']);
					foreach ($other['date'] as $date) {
						$num            = empty($v['details'][$date]) ? 0 : $v['details'][$date];
						$temp[]         = $num;
						$total[$date][] = $num;
					}
					array_push($temp, $v['clicked']);
					fputcsv($out, $temp);
				}

				$t   = 0;
				$tmp = array('', '总和');
				foreach ($other['date'] as $date) {
					$tmp[] = array_sum($total[$date]);
					$t += array_sum($total[$date]);
				}
				$tmp[] = $t;
				fputcsv($out, $tmp);
				break;
			}
			case 'news': {
				$title = array('ID', '标题');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $v['id']);
					array_push($temp, $v['title']);
					foreach ($other['date'] as $date) {
						$temp[] = empty($v['details'][$date]) ? 0 : $v['details'][$date];
					}
					array_push($temp, $v['clicked']);
					fputcsv($out, $temp);
				}
				break;
			}
			case 'cooperate':
				fputcsv($out, array('日期', '合作商', '点击量'));
				foreach ($msg as $v) {
					fputcsv($out, array($v['date'], $other['vendor'], $v['val']));
				}

				break;
			case 'hotwords':
				fputcsv($out, array('日期', '关键词', '点击量'));
				foreach ($msg as $v) {
					fputcsv($out, array($sdate . ' 至 ' . $edate, $v['title'], $v['clicked']));
				}
				break;


			case 'amount':
				fputcsv($out, array('日期', '点击量'));
				foreach ($msg as $v) {
					fputcsv($out, array($v['date'], $v['click']));
				}
				break;


			case 'topic':
				fputcsv($out, array('专题名', '点赞', 'ＰＶ', '内容点击', 'ｐｖ/内容'));
				foreach ($msg as $v) {
					fputcsv($out, array(
						$v['title'],
						$v['like_num'],
						$v['clickInfo']['topic'],
						$v['csum'],
						(bcdiv($v['clickInfo']['topic'], $v['csum'], 4) * 100) . "%"
					));
				}
				break;

			case 'shorturl':
				$title = array('短链接', 'Hash值');
				$title = array_merge($title, $other['date']);
				array_push($title, '合计');
				fputcsv($out, $title);
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $other['url'][$k]);
					array_push($temp, $k);
					$sum = 0;
					foreach ($other['date'] as $date) {
						$temp[] = $v[$date];
						$sum += $v[$date];
					}
					array_push($temp, $sum);
					fputcsv($out, $temp);
				}

				break;
			case 'search':
				fputcsv($out, array('时间区间', '内容', '次数'));
				foreach ($msg as $v) {
					fputcsv($out, array($sdate . ' - ' . $edate, $v['content'], $v['total']));
				}
				break;
			case 'navCol':
				$title = array('ID', '标题');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $k);
					array_push($temp, $v['title']);
					$sum = 0;
					foreach ($other['date'] as $date) {
						$temp[] = $v[$date] ? $v[$date] : 0;
						$sum += $v[$date];
					}
					array_push($temp, $sum);
					fputcsv($out, $temp);
				}
				break;
			case 'sites':
				$title = array('名称');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $v['name']);
					$sum = 0;
					foreach ($other['date'] as $date) {
						$temp[] = $v[$date] ? $v[$date] : 0;
						$sum += $v[$date];
					}
					array_push($temp, $sum);
					fputcsv($out, $temp);
				}
				break;
			case 'localnav':
				$title = array('名称');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				$total = array();
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $v['name']);
					$sum = 0;
					foreach ($other['date'] as $date) {
						$temp[] = $v[$date] ? $v[$date] : 0;
						$sum += $v[$date];
						$total[$date][] = $v[$date];
					}
					array_push($temp, $sum);
					fputcsv($out, $temp);
				}
				$t   = 0;
				$tmp = array('总和');
				foreach ($other['date'] as $date) {
					$tmp[] = array_sum($total[$date]);
					$t += array_sum($total[$date]);
				}
				$tmp[] = $t;
				fputcsv($out, $tmp);
				break;
			case 'cpday':
				$title = array('ID', '业务名称');
				$title = array_merge($title, $other['date']);
				array_push($title, '总和');
				fputcsv($out, $title);
				foreach ($msg as $k => $v) {
					$temp = array();
					array_push($temp, $v['id']);
					array_push($temp, $v['title']);
					$sum = 0;
					foreach ($other['date'] as $date) {
						$t      = empty($v['pv'][$date]) ? 0 : $v['pv'][$date];
						$temp[] = $t;
						$sum += $t;
					}
					array_push($temp, $sum);
					fputcsv($out, $temp);
				}
				break;
			default:
				break;
		}
		fclose($out);
	}

	/**
	 * 渠道商数据导出
	 */
	private function _exportLeading($data, $vendor, $attType) {

		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel');
		$filename = 'leading-in-statistics-data ' . date('Ymd') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$out = fopen('php://output', 'w');

		fputcsv($out, array('合作商', '内容', 'URL', '点击量', '内容属性', '上线时间', '下线时间'));
		foreach ($data as $k => $v) {
			fputcsv($out, array(
				$vendor,
				$v['title'],
				$v['link'],
				$v['clicked'],
				$attType,
				date('Y-m-d H:i:s', $v['start_time']),
				date('Y-m-d H:i:s', $v['end_time'])
			));
		}
		fclose($out);
	}

	/**
	 * 获取栏目数据
	 */
	private function _getColumnData($params) {
		$columnData = Gionee_Service_NgColumn::getsBy($params);
		return array(count($columnData), $columnData);
	}

	/**
	 * 获得搜狐新闻的统计信息
	 *
	 * @param int  $position_id
	 * @param int  $page
	 * @param date $sDate
	 * @param date $eDate
	 * @param int  $export
	 *
	 * @return array
	 */
	private function _getShouHuCountInfo($position_id, $sDate, $eDate, $other = array()) {
		$sum     = 0;
		$parms   = array();
		$statype = empty($other['statype']) ? Gionee_Service_Log::TYPE_SOHU : $other['statype'];
		if (intval($position_id) > 0) {
			$parms['position'] = $position_id;
		}
		!empty($other['condition']) && $parms = array_merge($parms, $other['condition']);
		list($total, $news) = Gionee_Service_Sohu::getsBy($parms, array('sort' => 'DESC', 'id' => 'DESC'));
		$conditionArr = array(
			'type' => $statype,
			'date' => array(array('>=', date('Ymd', strtotime($sDate))), array('<=', date('Ymd', strtotime($eDate))))
		);
		foreach ($news as $k => $v) {
			$conditionArr['key'] = $v['id'];
			$newsPv              = Gionee_Service_Log::getsBy($conditionArr);
			foreach ($newsPv as $pv) {
				$t                       = date('Y-m-d', strtotime($pv['date']));
				$news[$k]['details'][$t] = $pv['val'];
			}
			$news[$k]['clicked'] = intval(array_sum($news[$k]['details']));
			//$perAmount      = Gionee_Service_Log::getNgTotalNumber(array('key' => $v['id'], 'sdate' => strtotime($sDate), 'edate' => strtotime($eDate), 'type' => $statype));
			//$temp[$v['id']] = $perAmount;
			//$sum += $perAmount;
			$sum += intval($news[$k]['clicked']);
		}
		$result = array();
		foreach ($news as $k => $v) {
			$result[] = array(
				'id'      => $v['id'],
				'title'   => $v['title'],
				'details' => $v['details'],
				'clicked' => $v['clicked']
			);
		}
		return array($sum, $result, $total);
	}

	/**
	 * 获得栏目的统计信息
	 *
	 * @param array $columnData 需要统计的栏目
	 * @param date  $sDate      开始时间
	 * @param date  $eDate      结果时间
	 * @param array $other      其它参数
	 *
	 * @return array
	 */
	private function  _getNgColumnCountInfo($columnData, $sDate, $eDate, $other) {
		$sum       = 0;
		$detailSum = array();
		foreach ($columnData as $key => $val) { //先得到所有数据的统计信息
			$calculated       = Gionee_Service_Log::getNgColumnData($val['id'], date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)), Gionee_Service_Log::TYPE_NAV, $other);
			$temp[$val['id']] = 0;

			foreach ($calculated as $cal) {
				foreach ($cal as $k => $v) {
					$sum += $v;
					$temp[$val['id']] += $v;
					$detailSum[$val['id']][$k] += $v;
				}
			}
		}
		//var_dump($detailSum);exit;
		/*
	for($i = strtotime($sDate);$i<= strtotime($eDate);$i +=86400){
		$data[] = date('Y-m-d',$i);
	}
	*/
		$result = array();
		foreach ($columnData as $k => $v) {
			$result[$k]['id']       = $v['id'];
			$result[$k]['name']     = $v['name']; //获得栏目名
			$typeData               = Gionee_Service_NgType::get($v['type_id']);
			$result[$k]['typeName'] = $typeData['name'];
			$result[$k]['type_id']  = $v['type_id'];
			$result[$k]['clicked']  = $temp[$v['id']];
			$result[$k]['details']  = $detailSum[$v['id']] ? $detailSum[$v['id']] : 0;
		}
		//var_dump($result);exit;
		return array($sum, $result);
	}


	/**
	 * 按渠道号统计信息
	 *
	 * @param var  $channel
	 * @param date $sDate
	 * @param date $eDate
	 * @param int  $page
	 * @param      return array
	 */
	private function _getNgColumnCountByChannel($channel, $sDate, $eDate, $other = array()) {
		$p['ver']   = $channel;
		$p['sdate'] = strtotime($sDate);
		$p['edate'] = strtotime($eDate);
		$sum        = 0;
		$result     = array();
		$logData    = Gionee_Service_Log::getSumByChannel($p, Gionee_Service_Log::TYPE_NAV, array('key'), array('key')); //所有内容统计信息

		if ($logData) {
			$ids = $click = array();
			$sum = 0;
			foreach ($logData as $k => $v) {
				if (strpos($v['key'], '&')) {
					$tmpArr = explode('&', $v['key']);
					$ids[]  = $tmpArr[0];
					$click[$tmpArr[0]] += $v['total']; //点击数
				} else {
					$ids[] = $v['key'];
					$click[$v['key']] += $v['total'];
				}
			}
			$params['id'] = array('IN', $ids ? array_unique($ids) : array(0));
			$NgData       = Gionee_Service_Ng::getColumnidsByNgId($params);
			foreach ($NgData as $v) {
				$temp[$v['column_id']] = $v['id'];
			}
			$criteria['id'] = array('IN', array_keys($temp));
			if (isset($other['type_id'])) {
				$criteria['type_id'] = $other['type_id'];
			}
			$columnData = Gionee_Service_NgColumn::getsBy($criteria);
			foreach ($columnData as $v) {
				$typeInfo = Gionee_Service_NgType::get($v['type_id']);
				$result[] = array(
					'id'       => $v['id'],
					'name'     => $v['name'],
					'clicked'  => $click[$temp[$v['id']]],
					'typeName' => $typeInfo['name'],
					'type_id'  => $v['type_id']
				);
				$sum += $click[$temp[$v['id']]];
			}
		}

		return array($sum, $result);
	}

	/**
	 * 参数获取
	 */
	public function _getParams() {
		$paramsData = $this->getInput(array('sdate', 'edate', 'page', 'title'));
		$page       = max(1, $paramsData['page']);
		$sDate      = $paramsData['sdate'];
		$eDate      = $paramsData['edate'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime('+1 day'));
		return array($page, $sDate, $eDate, $paramsData['title']);
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

	public function inbuiltAction() {
		$searchParam = $this->getInput(array('cate'));
		if (empty($searchParam['cate'])) {
			unset($searchParam['cate']);
		}
		list($page, $sDate, $eDate) = $this->_getParams();

		$result = array(); //查询后的最终结果
		$sum    = 0; //统计总点击数


		$orderParam = array('sort' => 'ASC');
		list($total, $l) = Gionee_Service_Inbuilt::getList($page, $this->perpage, $searchParam, $orderParam);
		$keys = array();
		foreach ($l as $v) {
			$keys[$v['id']] = $v['key'];
		}

		$lineData = Gionee_Service_Log::getStatData(Gionee_Service_Log::TYPE_INBUILT, $keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)));

		$date = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}

		$list = array();
		foreach ($keys as $id => $name) {
			foreach ($date as $d) {
				$list[$name][$d] = isset($lineData[$name][$d]) ? intval($lineData[$name][$d]) : 0;
			}
		}

		$searchParam['sdate'] = $sDate;
		$searchParam['edate'] = $eDate;
		$cates                = Gionee_Service_Inbuilt::getCate();
		$this->assign('cates', $cates);
		$this->assign('date', $date);
		$this->assign('lineData', $list);
		$this->assign('searchParam', $searchParam);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, 'inbuilt?' . http_build_query($searchParam) . '&'));
	}

	public function duanwuAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$sdate    = date('Ymd', strtotime($postData['sdate']));
		$edate    = date('Ymd', strtotime($postData['edate']));
		$dateList = $ret = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$dateList[] = date('Ymd', $i);
		}

		$searchType = array(
			'pv' => 'PV',
			'uv' => 'UV',
		);

		$vers           = array(
			'index_'          => '首页',
			'index_wechat'    => '首页微信',
			'index_topic'     => '首页专题',
			'index_banner'    => '首页banner',
			'index_community' => '首页社区',
			'index_push'      => '首页push',
			'start'           => '开始',
			'stop'            => '停止',
			'give_up'         => '放弃',
			'userindex'       => '用户中心',
			'goods_detail'    => '商品详情',
		);
		$where          = array();
		$where['ver']   = 'duanwu';
		$where['key']   = array_keys($vers);
		$where['stime'] = strtotime($sdate);
		$where['etime'] = strtotime($edate);
		$where['type']  = $postData['search_type'];

		$datas = Gionee_Service_Log::getListByWhere($where);
		$ret   = array();
		foreach ($datas as $val) {
			$ret[$val['key']][$val['date']] = $val['val'];
		}

		if ($postData['export']) {
			$_data = array(
				'dateList' => $dateList,
				'list'     => $ret,
				'vers'     => $vers,
			);
			$this->_exportTotal($_data, "端午统计数据", $sdate, $edate);
			exit();
		}

		$this->assign('vers', $vers);
		$this->assign('dateList', $dateList);
		$this->assign('list', $ret);
		$this->assign('params', $postData);
		$this->assign('searchType', $searchType);
	}

	
	public function qixiAction(){
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$sdate    = date('Ymd', strtotime($postData['sdate']));
		$edate    = date('Ymd', strtotime($postData['edate']));
		$dateList = $ret = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$dateList[] = date('Ymd', $i);
		}
		
		$searchType = array(
				'pv' => 'PV',
				'uv' => 'UV',
		);
		
		$vers           = array(
				'index_'          							=> '首页',
				'index_weixin'    					=> '首页微信',
				'index_topic'     						=> '首页专题',
				'index_banner'    					=> '首页banner',
				'index_acti'								=>'启动页',
				'index_push'      					=> '首页push',
				'giveup'         							=> '放弃奖品次数',
				'game_link_btn_refresh'		=>'洗牌',
				'game_link_btn_start'			=>'开始游戏',
				'game_record_label_text'	=>'我的奖品',
				'game_rule_label_text'		=>'活动规则',
				'game_record_btn_win'		=>'我的奖品-我要赢奖品',
				'game_prize_btn_giveup'		=>'确定放弃',
				'game_btn_again'					=>'再玩一次',
				'ucenter_tip_btn_prize'			=>'个人中心领取奖品',
				'ucenter_tip_btn_look'			=>'我想逛逛',
				'ucenter_tip_btn_goback'		=>'回活动页',
				'ucenter_tip_btn_fanqi'		=>'点放弃奖品',
				'score'           							=> '点击金币次数',
				'prize'            							=> '点击中奖奖品次数',
		);
		$where          = array();
		$where['ver']   = 'qixi';
		$where['key']   = array_keys($vers);
		$where['stime'] = strtotime($sdate);
		$where['etime'] = strtotime($edate);
		$where['type']  = $postData['search_type'];
		
		$datas = Gionee_Service_Log::getListByWhere($where);
		$ret   = array();
		foreach ($datas as $val) {
			$ret[$val['key']][$val['date']] = $val['val'];
		}
			if	($postData['search_type'] =='pv' || empty($postData['search_type']) ){
				$vers['total_prize_users'] 				= '总中奖用户数';
				$vers['real_prize_users'] 	 			= '总获得实物奖品总用户';
				$vers['score_prize_users'] 				='总获得20金币用户数';
				$vers['luck_prize_users'] 				= '幸运奖领取用户数';
				
				$vers['total_success_users'] 			='总获取成功用户数';
				$vers['real_success_users'] 				='实物奖品获取成功用户数';
				$vers['score_success_users'] 			='20金币获取成功用户数';
				
				$vers['total_giveup_users']  			= '放弃奖品总用户数';
				$vers['real_giveup_users']			='放弃实物奖品用户数';
				$vers['score_giveup_users']  		='放弃20金币用户数';
				
				$vers['total_prize_times']	 				='总获奖次数';
				$vers['real_prize_times']					='获得实物奖品次数';
				$vers['score_prize_times'] 				='获得20金币次数';
				$ver['luck_prize_times'] 					='获得幸运奖次数';
				
				$vers['total_giveup_times']	 		='放弃奖品总次数';
				$vers['real_giveup_times']			='放弃实物奖品次数';
				$vers['score_giveup_times'] 		='放弃20金币次数';
		
				$arr = array();
				list($total,$subTotal,$realTotal,$levelTotal,$detail)  = Event_Service_Link::getScoreUsers(array(),array('date','prize_level','prize_status'));
				$ret['total_prize_users'] = $total['total_prize_users'];
				$ret['total_prize_times'] = $total['total_prize_times'];
				
				$ret['score_prize_users']	 = $levelTotal['total_users']['6'];
				$ret['total_success_users'] = $subTotal['total_score_users']['1'];
				$ret['total_giveup_users'] =$subTotal['total_score_users']['-2'];
				$ret['total_success_times'] = $subTotal['total_score_times']['1'];
				$ret['total_giveup_times'] = $subTotal['total_score_times']['-2'];
				
				
				$ret['score_success_users'] = $detail['total_users']['6']['1'];
				$ret['score_giveup_users']	= $detail['total_users']['6']['-2'];
				$ret['score_prize_times']		= $detail['total_times']['6']['1'];
				$ret['score_giveup_times']  = $detail['total_times']['6']['-2'];
				
				$ret['luck_prize_users']  		= $detail['total_users']['0']['1'];
				$ret['luck_prize_times']		= $detail['total_times']['0']['1'];
				
				$ret['real_prize_users']		= $realTotal['total_users'];
				$ret['real_prize_times']		= $realTotal['total_times'];
				$ret['real_success_users']	 = $realTotal['status_users']['1'];
				$ret['real_success_times']	 = $realTotal['status_times']['1'];
				$ret['real_giveup_users']		 = $realTotal['status_users']['-2'];
				$ret['real_giveup_times']	 = $realTotal['status_times']['-2'];
			}
			
		if ($postData['export']) {
			$_data = array(
					'dateList' => $dateList,
					'list'     => $ret,
					'vers'     => $vers,
			);
			$this->_exportTotal($_data, "七夕统计数据", $sdate, $edate);
			exit();
		}
		
		$this->assign('vers', $vers);
		$this->assign('dateList', $dateList);
		$this->assign('list', $ret);
		$this->assign('params', $postData);
		$this->assign('searchType', $searchType);
	}
	
	//国庆活动统计
	public function nationalAction(){
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$sdate    = date('Ymd', strtotime($postData['sdate']));
		$edate    = date('Ymd', strtotime($postData['edate']));
		$dateList = $ret = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$dateList[] = date('Ymd', $i);
		}
		
		$searchType = array(
				'pv' => 'PV',
				'uv' => 'UV',
		);
		$vers = array(
				'index'							=>'首页',
				'banner'    					=> '首页banner',
				'push'      					=> '首页push',
				'drawing'					=>'抢红包按钮',
				'get'								=>'立即领奖',
		);
		
		$where          = array();
		$where['ver']   = 'year';
		$where['key']   = array_keys($vers);
		$where['stime'] = strtotime($sdate);
		$where['etime'] = strtotime($edate);
		$where['type']  = $postData['search_type'];
		$datas = Gionee_Service_Log::getListByWhere($where);
		$ret   = array();
		foreach ($datas as $val) {
			$ret[$val['key']][$val['date']] = $val['val'];
		}
		$vers['scores']		='发放金币数';
		$scoresList = Event_Service_Activity::totalSendScoresPerDay(array('prize_status'=>1),array('add_date','prize_id'));
		foreach ($scoresList as $k=>$v){
			$ret['scores'][$k] = array_sum($v);
			} 
		if ($postData['export']) {
			$_data = array(
					'dateList' => $dateList,
					'list'     => $ret,
					'vers'     => $vers,
			);
			$this->_exportTotal($_data, "国庆活动数据", $sdate, $edate);
			exit();
		}
		
		$clickUsers = Event_Service_Activity::getClickUsers(array(),array('event_type'));
		$this->assign('drawingUsers', $clickUsers['year_drawing']);
		$this->assign('vers', $vers);
		$this->assign('dateList', $dateList);
		$this->assign('list', $ret);
		$this->assign('params', $postData);
		$this->assign('searchType', $searchType);
	}


    //双十一活动统计
    public function seckillAction(){
        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $searchType = array(
            'pv' => 'PV',
            'uv' => 'UV',
        );
        $vers = array(
            'preheat'						=>'预约首页',
            'remind'    					=> '预约按钮',
            'index'      					=> '活动首页',
            'drawing'					    =>'抢按钮',
        );

        $where          = array();
        $where['ver']   = 'seckill';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($sdate);
        $where['etime'] = strtotime($edate);
        $where['type']  = $postData['search_type'];
        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
        }
        $vers['scores']		='发放金币数';
       // $scoresList = Event_Service_Activity::totalSendScoresPerDay(array('prize_status'=>1),array('add_date','prize_id'));
        $where  = array(
            'date'=>array(array(">=",$sdate),array("<=",$edate)),
            'score_type'=>216,
          //  'group_id'=>2,
        );
        $scoresList  = User_Service_ScoreLog::totalRemindScores($where,array('date'),array('date'=>'DESC'));
        foreach ($scoresList as $k=>$v){
            $ret['scores'][$k] = $v;
        }
        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportTotal($_data, "双十一活动数据", $sdate, $edate);
            exit();
        }

        $clickUsers = Event_Service_Activity::getClickUsers(array('event_type'=>'seckill_drawing'),array('event_type'));
        $this->assign('drawingUsers', $clickUsers['seckill_drawing']);
        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
        $this->assign('searchType', $searchType);
    }


	
	
	private function _exportTotal($data, $title, $sdate, $edate) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $title . '-' . $sdate . '-' . $edate . '.csv';
		header('Content-Type: text/csv');
		$filename = iconv('utf8', 'gb2312', $filename);
		header('Content-Disposition: attachment;filename=' . $filename);
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		$dateList = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$dateList[] = date('Ymd', $i);
		}

		$_data = $data;

		$vers        = $_data['vers'];
		$tempHeaders = array('名称');
		foreach ($_data['dateList'] as $v) {
			$tempHeaders[] = date('Y-m-d', strtotime($v));
		}
		$tempHeaders[] = '总计';
		fputcsv($out, $tempHeaders);
		$total = array();

		foreach ($vers as $k => $txt) {
			$sumTotal = 0;
			$_t       = array($txt);
			foreach ($dateList as $d) {
				$num = intval($_data['list'][$k][$d]);
				$sumTotal += $num;
				$total[$d][] = $num;
				$_t[]        = $num;
			}
			$_t[] = $sumTotal;
			fputcsv($out, $_t);
		}


		$footer = array('总计');
		$n      = 0;
		foreach ($dateList as $d) {
			$sd = array_sum($total[$d]);
			$n += $sd;
			$footer[] = $sd;
		}
		$footer[] = $n;

		fputcsv($out, $footer);
	}

}
