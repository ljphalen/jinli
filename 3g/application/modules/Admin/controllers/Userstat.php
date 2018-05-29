<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UserstatController extends Admin_BaseController {

	public $pageSize = 20;

	/**
	 * 用户入口统计
	 */
	public $actions = array(
		'rankUrl'       => '/Admin/Userstat/rank',
		'dayUrl'        => '/Admin/Userstat/day',
		'monthUrl'      => '/Admin/Userstat/month',
		'chatDay'       => '/Admin/Userstat/chatday',
		'chatMonth'     => '/Admin/Userstat/chatmonth',
		'veDetailUrl'   => '/Admin/Userstat/vedetail',
		'vcenterUrl'    => '/Admin/Userstat/vcenter',
		'expuserUrl'    => '/Admin/Userstat/expuser',
		'expchannelUrl' => '/Admin/Userstat/expchannel',
		'exprankUrl'    => '/Admin/Userstat/exprank',
		'expusedUrl'    => '/Admin/Userstat/expused',
		'rankdetailUrl' => '/Admin/Userstat/rankdetail',
		'quizUrl'				=>'/Admin/Userstat/quiz'
	);


	public $snatch_cost_type   = 311;
	public $snatch_prize_type  = 207;
	public $snatch_cost_scores = array(
		'10',
		'20',
		'50',
		'100'
	);

	public static function sortData(&$dataList, $sort_key, $sort = SORT_ASC) {
		foreach ($dataList as $row_array) {
			$key_array[] = $row_array[$sort_key];
		}
		array_multisort($key_array, $sort, $dataList);
	}

	public function entranceAction() {
		$sDate  = $this->getInput('sdate');
		$eDate  = $this->getInput('edate');
		$export = $this->getInput('export');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		$shortValTmp = Gionee_Service_Config::getValue('user_index_index_short_val');
		$shortVal    = json_decode($shortValTmp, true);
		if ($_POST['short_val']) {
			$shortVal = array();
			$tmp      = explode("\n", $_POST['short_val']);
			foreach ($tmp as $v) {
				list($tmp_k, $tmp_v) = explode(',', trim($v));
				$shortVal[$tmp_k] = $tmp_v;
			}
			Gionee_Service_Config::setValue('user_index_index_short_val', Common::jsonEncode($shortVal));
		}

		$keys = array_keys($shortVal);

		//总URL数
		$lineDataPv = Gionee_Service_Log::getUrlList($keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)), Gionee_Service_Log::TYPE_URL);
		$lineDataUv = Gionee_Service_Log::getUrlList($keys, date('Ymd', strtotime($sDate)), date('Ymd', strtotime($eDate)), Gionee_Service_Log::TYPE_URL_UV);
		$date       = array();
		for ($i = strtotime($sDate); $i <= strtotime($eDate); $i += 86400) {
			$date[] = date('Y-m-d', $i);
		}

		$list = array();
		foreach ($lineDataPv as $k => $v) {
			foreach ($date as $kdv) {
				$list[] = array(
					'date' => $kdv,
					'key'  => $k,
					'name' => $shortVal[$k],
					'pv'   => isset($v[$kdv]) ? intval($v[$kdv]) : 0,
					'uv'   => isset($lineDataUv[$k][$kdv]) ? intval($lineDataUv[$k][$kdv]) : 0,
				);

			}
		}

		if ($export) {
			$this->export('entrance', '用户签到页-', $sDate, $eDate, $list);
			exit();
		}


		$this->assign('lineData', $list);
		$this->assign('date', $date);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$t = array();
		foreach ($shortVal as $k => $v) {
			$t[] = implode(',', array($k, $v));
		}
		$this->assign('shortval', implode("\n", $t));

	}

	/**
	 * 签到统计
	 */
	public function signinAction() {
		$searchParam          = $this->getInput(array('sdate', 'edate', 'days', 'page', 'pagesie', 'export'));
		$searchParam['sdate'] = empty($searchParam['sdate']) ? date('Y-m-1') : $searchParam['sdate'];
		$searchParam['edate'] = empty($searchParam['edate']) ? date('Y-m-1', strtotime('+1 month')) : $searchParam['edate'];

		$sdate = strtotime($searchParam['sdate']);
		$edate = strtotime($searchParam['edate']);

		$page     = empty($searchParam['page']) ? 1 : intval($searchParam['page']);
		$pagesize = empty($searchParam['pagesize']) ? 20 : intval($searchParam['pagesize']);
		$export   = empty($searchParam['export']) ? 0 : $searchParam['export'];

		$conditionArr = array(
			'sdate'    => $sdate,
			'edate'    => $edate,
			'days'     => intval($searchParam['days']),
			'group_id' => 1
		);

		list($total, $dataList) = User_Service_Earn::statMonthSignin($conditionArr, array(), array(
			'page'     => $page,
			'pagesize' => $pagesize
		));

		foreach ($dataList as $k => $v) {
			$uInfo                    = Gionee_Service_User::getUser(intval($v['uid']));
			$dataList[$k]['name']     = $uInfo['username'];
			$dataList[$k]['realname'] = $uInfo['realname'];
		}

		if ($export) {
			$this->export('signin', '用户签到页' . $page . '-', date('Y-m', $sdate), date('Y-m', $edate), $dataList);
		}
		$this->assign('searchParam', $searchParam);
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $pagesize, 'signin?' . http_build_query($searchParam) . '&'));

	}

	/**
	 * 用户中心日报
	 */
	public function dayAction() {
		$searchParams = $this->getInput(array('page', 'sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);

		//PV,UV值
		$userMsg = User_Service_Gather::getSumScoresInfo();
		$formatSdate = date('Ymd', strtotime($sdate));
		$formatEdate = date('Ymd', strtotime($edate));
		$pvList  = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_index', $formatSdate, $formatEdate);
		$uvList  = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_index', $formatSdate,$formatEdate);
		
		//抽奖的按钮的PV,UV
		$pvDrawList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_lottery_drawing', $formatSdate, $formatEdate);
		$uvDrawList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_lottery_drawing', $formatSdate, $formatEdate); 

		$params         = array();
		$params['date'] = array(
			array('>=', date('Ymd', strtotime($sdate))),
			array('<=', date('Ymd', strtotime($edate)))
		);
		//夺宝兵
		$snatchData     = User_Service_ScoreLog::snatchCalucateData(array_merge($params, array('score_type' => '311')), array('date'));
		
		//答题
		$quizParams  = $quizScoreSearch  = array();
		$quizParams['add_time'] = array(array(">=",strtotime($sdate)),array("<=",strtotime($edate." 23:59:59")));
		$quizParams['selected'] = array('>',0);
		$quizData 	 = User_Service_QuizResult::getAnswerUserData($quizParams);
		
		$quizScoreSearch['add_time'] = $quizParams['add_time'] ;
		$quizScoreSearch['group_id']  = 2;
		$quizScoreSearch['score_type'] = array("IN",array('211','212'));
		$quizScores = User_Service_ScoreLog::getEverydayScoreInfo($quizScoreSearch,array('date'));
		
		$params['type'] = Gionee_Service_Log::TYPE_USER;
		$nums           = count(Gionee_Service_Log::$userKeys);
		$pageSize       = $nums * $this->pageSize;
		$dataList       = Gionee_Service_Log::getsBy($params, array('date' => 'DESC'));
		$temp           = $date = $data = array();
		foreach ($dataList as $key => $val) {
			$temp[$val['date']][$val['key']] = $val['val'];
		}
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Ymd', $i);
		}
		foreach ($date as $v) {
			$data[$v]['goods']        = $temp[$v];
			$data[$v]['pv']           = $pvList[date('Y-m-d', strtotime($v))];
			$data[$v]['uv']           = $uvList[date('Y-m-d', strtotime($v))];
			$data[$v]['pv_draw']      = $pvDrawList[date('Y-m-d', strtotime($v))];
			$data[$v]['uv_draw']      = $uvDrawList[date('Y-m-d', strtotime($v))];
			$data[$v]['snatch_users'] = $snatchData[date('Y-m-d', strtotime($v))][$this->snatch_cost_type]['snatch_users'];
			$data[$v]['snatch_times'] = $snatchData[date('Y-m-d', strtotime($v))][$this->snatch_cost_type]['snatch_times'];
			$data[$v]['quiz_users']		 = $quizData[$v]['quiz_users']?$quizData[$v]['quiz_users']:0;
			$data[$v]['quiz_scores']		 = $quizScores[$v]['quiz_scores']?$quizScores[$v]['quiz_scores']:0;
		}
		if ($searchParams['export']) {
			$this->export('day', '日报报表', $sdate, $edate, $list = array('data' => $data, 'user' => $userMsg));
			exit();
		}
		$this->assign('data', $data);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
		$this->assign('userMsg', $userMsg);
	}

	/**
	 * 用户中心月报
	 */
	public function monthAction() {
		$postData = $this->getInput(array('page', 'sdate', 'edate', 'export'));
		$sdate    = $postData['sdate'];
		$edate    = $postData['edate'];
		!$sdate && $sdate = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
		!$edate && $edate = date('Y-m-d', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
		$params = $p = $visit = array();;
		$p['type']      = array('IN', array('pv', 'uv'));
		$p['key']       = 'user_index';
		$params['date'] = $p['date'] = array(
			array('>=', date('Ymd', strtotime($sdate))),
			array('<=', date('Ymd', strtotime($edate)))
		);
		$pvuvData       = Gionee_Service_Log::getsBy($p, array('date' => "DESC"));
		foreach ($pvuvData as $k => $v) {
			$date                                 = substr(date('Y-m-d', strtotime($v['date'])), 0, 7);
			$visit[$date][$v['type']][$v['date']] = $v['val'];
		}

		//抽奖的按钮的PV,UV
		$pvDrawList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_lottery_drawing', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));
		$uvDrawList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_lottery_drawing', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));

		//夺宝奇兵
		$snatchMonthData = array();
		$snatchData      = User_Service_ScoreLog::snatchCalucateData(array_merge($params, array('score_type' => '311')), array('date'));
		foreach ($snatchData as $k => $v) {
			$date = substr($k, 0, 7);
			foreach ($v as $m => $n) {
				$snatchMonthData[$date]['snatch_users'] += intval($n['snatch_users']);
				$snatchMonthData[$date]['snatch_times'] += intval($n['snatch_times']);
			}
		}
		$params['type'] = Gionee_Service_Log::TYPE_USER;
		$dataList       = Gionee_Service_Log::getsBy($params, array('date' => 'DESC'));
		$temp           = $data = array();
		foreach ($dataList as $k => $v) {
			$dateTime                               = substr(date('Y-m-d', strtotime($v['date'])), 0, 7);
			$temp[$dateTime][$v['key']][$v['date']] = $v['val'];
		}
		foreach ($temp as $key => $val) {
			foreach ($val as $m => $n) {
				$data[$key]['goods'][$m]    = array_sum($n);
				$data[$key]['pv']           = array_sum($visit[$key]['pv']);
				$data[$key]['uv']           = array_sum($visit[$key]['uv']);
				$data[$key]['snatch_users'] = $snatchMonthData[$key]['snatch_users'];
				$data[$key]['snatch_times'] = $snatchMonthData[$key]['snatch_times'];
				$data[$key]['pv_draw']      = array_sum($pvDrawList[date('Y-m-d', strtotime($v['date']))]);
				$data[$key]['uv_draw']      = array_sum($uvDrawList[date('Y-m-d', strtotime($v['date']))]);
				if ($m == 'user_currency_scores') {
					$data[$key]['user_currency_scores'] = max($n);
				}
				if ($m == 'user_total_number') {
					$data[$key]['user_total_number'] = max($n);
				}
			}
		}
		$userMsg = User_Service_Gather::getSumScoresInfo();
		if ($postData['export']) {
			$this->export('month', '月报报表', $sdate, $edate, array('data' => $data, 'user' => $userMsg));
			exit();
		}
		$this->assign('userMsg', $userMsg);
		$this->assign('data', $data);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
	}

	/**
	 * 金币排名
	 */
	public function rankAction() {
		$postData = $this->getInput(array('page', 'date', 'min_scores', 'max_scores', 'export'));
		$page     = max($postData['page'], 1);
		$date     = $postData['date'] ? $postData['date'] : date('Y-m-d', time());
		if (intval($postData['min_scores'])) {
			$params['score'] = array('>=', $postData['min_scores']);
		}
		if (intval($postData['max_scores'])) {
			$params['score'] = array('<=', $postData['max_scores']);
		}
		$params['add_time'] = array(array('>=', strtotime($date)), array('<=', strtotime($date . " 23:59:59")));
		list($total, $dayData) = User_Service_Earn::getDayScoreRank($params, array('uid'), $page, $this->pageSize);
		foreach ($dayData as $k => $v) {
			$p['add_time'] = array('<=', strtotime($date . "23:59:59"));
			$p['uid']      = $k;
			list($dayData[$k]['deadline_scores'], $dayData[$k]['deadline_tasks']) = User_Service_Earn::getSumScoresByUserRank($p);
			$userInfo                = Gionee_Service_User::getUser($k);
			$dayData[$k]['username'] = $userInfo['username'];
		}
		if ($postData['export']) {
			$this->export('rank', '金币排行报表', $date, $date, $dayData);
			exit();
		}
		$this->assign('data', $dayData);
		$this->assign('date', $date);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['rankUrl'] . "?date={$date}&"));
	}


	/**
	 * 刮刮乐统计信息
	 */

	public function scratchAction() {
		$postData = $this->getInput(array('page', 'sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($postData);

		//PV,UV
		$indexpvList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_activity_lot', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));
		$indexUvList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_activity_lot', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));

		$scratchPvList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_activity_scratch', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));
		$scratchUvList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_activity_scratch', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));

		$output = $temp = array();
		//已刮开验证码的用户统计信息
		$scratchedList = User_Service_Gather::getIncreUserAmount(array(
			'is_scratch'   => array("IN", array('1', '2')),
			'created_time' => array(
				array('>=', $sdate),
				array('<=', $edate)
			)
		), array('created_time'), array('created_time' => 'DESC'), $page, $this->pageSize);
		foreach ($scratchedList as $m => $n) {
			$temp[$n['created_time']] = $n['amount_users'];
		}
		//实际领取金币的用户统计信息
		$rewardsList = Gionee_Service_WxFeedback::getRewardsData(array('key' => 'activity_1'), array('created_at' => 'DESC'), $page, $this->pageSize);

		$date = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$date[] = date("Y-m-d", $i);
		}
		$rewardScores = Gionee_Service_Config::getValue('user_wx_content_rewards');
		foreach ($date as $k => $v) {
			$output[$v]['index_pv']      = $indexpvList[$v];
			$output[$v]['index_uv']      = $indexUvList[$v];
			$output[$v]['scratch_pv']    = $scratchPvList[$v];
			$output[$v]['scratch_uv']    = $scratchUvList[$v];
			$output[$v]['scratch_num']   = $temp[$v];
			$output[$v]['geted_number']  = $rewardsList[$v];//当天领取的人数
			$output[$v]['scores_amount'] = $rewardsList[$v] * $rewardScores;
		}
		if ($postData['export']) {
			$this->export('scratch', '刮刮乐统计', $sdate, $edate, $output);
			exit;
		}
		$this->assign('dataList', $output);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
	}

	/**
	 * 兑换通话时长日报
	 */
	public function chatdayAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('- 10 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$page           = max(1, $postData['page']);
		$params         = array();
		$params['date'] = array(
			array('>=', date('Ymd', strtotime($postData['sdate']))),
			array('<=', date('Ymd', strtotime($postData['edate'])))
		);
		$params['type'] = 'user';
		$params['key']  = array('IN', Gionee_Service_Log::$voipTypes);
		list($total, $data) = Gionee_Service_Log::getList($page, $this->pageSize, $params, array('date' => 'DESC'));
		$temp = array();
		foreach ($data as $key => $val) {
			$temp[$val['date']][$val['key']] = $val['val'];
		}
		if ($postData['export']) {
			$this->export('chatday', '金币兑换通话时长日报表', $postData['sdate'], $postData['edate'], $temp);
			exit();
		}
		$this->assign('data', $temp);
		$this->assign('params', $postData);
	}

	public function chatMonthAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-01', strtotime('- 3 month'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('+1 month -1 day'));
		$params         = array();
		$params['date'] = array(
			array('>=', date('Ymd', strtotime($postData['sdate']))),
			array('<=', date('Ymd', strtotime($postData['edate'])))
		);
		$params['type'] = 'user';
		$temp           = Gionee_Service_Log::getVoipTimeExChangeSumData($params);
		$data           = array();
		foreach ($temp as $k => $v) {
			$data[$v['month']][$v['key']] = $v['total'];
		}
		if ($postData['export']) {
			$this->export('chatday', '金币兑换通话时长月报表', $postData['sdate'], $postData['edate'], $data);
			exit();
		}
		$this->assign('data', $data);
		$this->assign('params', $postData);
	}

	/**
	 *
	 */

	public function vcenterAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$sdate     = date('Ymd', strtotime($postData['sdate']));
		$edate     = date('Ymd', strtotime($postData['edate']));
		$page      = max(1, $postData['page']);
		$voipMePv  = Gionee_Service_Log::getPvUvStatByKey('pv', 'voip_me_pv', $sdate, $edate);
		$voipMeUv  = Gionee_Service_Log::getPvUvStatByKey('uv', 'voip_me_uv', $sdate, $edate);
		$data      = $dateList = array();
		$goodsList = $this->_getVoipExchangeGoods($sdate, $edate);
		$ids       = $this->_getGoodsIds($goodsList);
		if (!empty($ids)) {
			$clickData = $this->_getVoipExchangeGoodsPvData($sdate, $edate, $ids);
			for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
				$dateList[] = date('Ymd', $i);
			}
			if (!empty($clickData)) {
				$successed = $this->_getVoipExchangeSuccessData($sdate, $edate);
				foreach ($dateList as $n) {
					$sum                                     = array_sum($clickData[$n]);
					$data[$n]['voip_center_pv']              = $voipMePv[date('Y-m-d', strtotime($n))];
					$data[$n]['voip_center_uv']              = $voipMeUv[date('Y-m-d', strtotime($n))];
					$data[$n]['voip_exchange_pv']            = $sum;
					$data[$n]['voip_exchange_ratio']         = $data[$n]['voip_center_pv'] ? bcdiv($sum, $voipMePv[date('Y-m-d', strtotime($n))], 4) : '0';
					$data[$n]['voip_exchange_success_times'] = $successed[$n];
					$data[$n]['voip_exchange_success_ratio'] = $data[$n]['voip_center_pv'] ? bcdiv($successed[$n], $sum, 4) : '0';
				}
			}
			if ($postData['export']) {
				$this->export('vcenter', '畅聊用户中心', $sdate, $edate, $data);
				exit();
			}
		}
		$this->assign('data', $data);
		$this->assign('params', $postData);
	}

	/**
	 * 兑换详情
	 */
	public function vedetailAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'export'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$sdate     = date('Ymd', strtotime($postData['sdate']));
		$edate     = date('Ymd', strtotime($postData['edate']));
		$page      = max(1, $postData['page']);
		$goodsList = $this->_getVoipExchangeGoods($sdate, $edate);
		$temp      = array();
		foreach ($goodsList as $s => $t) {
			$temp[$t['id']] = $t['name'];
		}
		$ids = $this->_getGoodsIds($goodsList);
		if (!empty($ids)) {
			$clickData  = $this->_getVoipExchangeGoodsPvData($sdate, $edate, $ids); //兑换点击次数
			$successMsg = $this->_getExchangeSuccessDetailData($sdate, $edate, $ids);
			$result     = array();
			foreach ($clickData as $k => $v) {
				foreach ($v as $m => $n) {
					$pv                                    = Gionee_Service_Log::getPvUvStatByKey('u_g_detail', $m, $k, $k);
					$result[$k][$m]['visit_pv']            = $pv[date('Y-m-d', strtotime($k))];
					$result[$k][$m]['click_times']         = $n;
					$result[$k][$m]['total_user']          = $successMsg[$k][$m]['total_user'];
					$result[$k][$m]['total_success_times'] = $successMsg[$k][$m]['total_times'];
					$result[$k][$m]['name']                = $temp[$m];
					$result[$k][$m]['exchange_ratio']      = bcdiv($n, $pv[date('Y-m-d', strtotime($k))], 4);
					$result[$k][$m]['sussess_ratio']       = bcdiv($successMsg[$k][$m]['total_times'], $n, 4);
				}
			}
			if ($postData['export']) {
				$this->export('vedetail', '兑换时长明细表', $sdate, $edate, $result);
				exit();
			}
		}
		$this->assign('dataList', $result);
		$this->assign('params', $postData);
	}


	/**
	 * 夺宝奇兵日报
	 *
	 * @param unknown $sdate
	 * @param unknown $edate
	 *
	 * @return boolean
	 */

	public function snatchAction() {
		$searchParams = $this->getInput(array('page', 'sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		$where               = $params = array();
		$formstSdate         = date('Ymd', strtotime($sdate));
		$formatEdate         = date('Ymd', strtotime($edate));
		$snatchPv            = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_snatch_index', $formstSdate, $formatEdate);
		$snatchUv            = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_snatch_index', $formstSdate, $formatEdate);
		$where['date']       = $params['date'] = array(array('>=', $formstSdate), array('<=', $formatEdate));
		$where['score_type'] = array('IN', array($this->snatch_cost_type, $this->snatch_prize_type));
		$snatchData          = User_Service_ScoreLog::snatchCalucateData($where, array('date', 'score_type'));
		$params['type']      = array("IN", array('snatch_goods_pv', 'snatch_goods_uv'));
		$snatchGoodsData     = Gionee_Service_Log::getsBy($params, array('date' => 'DESC'));
		$temp                = array();
		foreach ($snatchGoodsData as $k => $v) {
			$temp[date('Y-m-d', strtotime($v['date']))][$v['ver']][$v['type']] = $v['val'];
		}

		$dateList = $result = array();
		for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
			$dateList[] = date('Y-m-d', $i);
		}
		foreach ($dateList as $k => $v) {
			$result[$v]['goods']               = $temp[$v];
			$result[$v]['index_pv']            = $snatchPv[$v] ? $snatchPv[$v] : 0;
			$result[$v]['index_uv']            = $snatchUv[$v] ? $snatchUv[$v] : 0;
			$result[$v]['snatch_users']        = $snatchData[$v][$this->snatch_cost_type]['snatch_users'];
			$result[$v]['snatch_times']        = $snatchData[$v][$this->snatch_cost_type]['snatch_times'];
			$result[$v]['snatch_cost_scores']  = abs($snatchData[$v][$this->snatch_cost_type]['snatch_cost_scores']);
			$result[$v]['snatch_prize_scores'] = $snatchData[$v][$this->snatch_prize_type]['snatch_cost_scores'];
		}

		if ($searchParams['export']) {
			$this->export('snatch', '夺宝奇兵日报', $sdate, $edate, $result);
			exit();
		}

		$this->assign('data', $result);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
	}


	/**
	 * 用户等级数据
	 *
	 * @param unknown $sdate
	 * @param unknown $edate
	 *
	 * @return boolean
	 */

	public function expUserAction() {
		$searchParams = $this->getInput(array('sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		$where       = $params = array();
		$formatSdate = date('Ymd', strtotime($sdate));
		$formatEdate = date('Ymd', strtotime($edate));
		$indexPv     = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_experience_index', $formatSdate, $formatEdate);
		$indeUv      = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_experience_index', $formatSdate, $formatEdate);

		$where       = array(
			'date'      => array(array('>=', $sdate), array("<=", $edate)),
			'new_level' => array(">", 1),
		);
		$upgradeInfo = User_Service_ExperienceLevelLog::getPerDayUpgradeUser($where, array('date', 'new_level'));
		$levelUsers  = Gionee_Service_Log::getLevelUserData($formatSdate, $formatEdate);
		$ret         = $date = array();
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Ymd', $i);
		}
		$config = Common::getConfig('userConfig', 'experience_level_data');
		$ranks  = array_keys($config);
		foreach ($date as $v) {
			foreach ($ranks as $m) {
				$ret[$v][$m]['users'] = $levelUsers[$v][$m] ? $levelUsers[$v][$m] : 0;
				$ret[$v][$m]['incre'] = $upgradeInfo[$v][$m] ? $upgradeInfo[$v][$m] : 0;
			}

			$ret[$v]['pv'] = $indexPv['pv'];
			$ret[$v]['uv'] = $indeUv['uv'];
		}
		if ($searchParams['export']) {
			$this->export('experience_users', '用户等级数据', $sdate, $edate, $ret);
			exit();
		}
		$this->assign('data', $ret);
		$this->assign('date', $date);
		$this->assign('ranks', $ranks);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
	}

	public function expChannelAction() {
		$searchParams = $this->getInput(array('sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		$where             = $params = array();
		$where['add_time'] = array(array('>=', strtotime($sdate)), array('<=', strtotime($edate)));
		$channelData       = User_Service_ExperienceLog::getEachTypeExperienceData($where);
		$ret               = $date = array();
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Ymd', $i);
		}
		$config = Common::getConfig('userConfig', 'experience_activity_type');
		$types  = array_keys($config);
		foreach ($date as $v) {
			foreach ($types as $m) {
				$ret[$v][$m]['users']  = $channelData[$v][$m]['total_users'] ? $channelData[$v][$m]['total_users'] : 0;
				$ret[$v][$m]['points'] = $channelData[$v][$m]['total_points'] ? $channelData[$v][$m]['total_points'] : 0;
			}
		}
		if ($searchParams['export']) {
			$this->export('experience_channel', '获取经验方式', $sdate, $edate, $ret);
			exit();
		}
		$this->assign('data', $ret);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
		$this->assign('types', $types);
	}

	public function expRankAction() {
		$searchParams = $this->getInput(array('sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		$where             = $params = array();
		$where['add_time'] = array(array('>=', strtotime($sdate)), array('<=', strtotime($edate)));
		$data              = User_Service_ExperienceLog::getExperienceSumData($where);
		$temp              = array();
		foreach ($data as $m => $n) {
			$temp[$n['add_date']]['total_users']  = $n['total_users'];
			$temp[$n['add_date']]['total_points'] = $n['total_points'];
		}
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Ymd', $i);
		}
		$ret = array();
		foreach ($date as $v) {
			$ret[$v]['total_users']  = $temp[$v]['total_users'] ? $temp[$v]['total_users'] : 0;
			$ret[$v]['total_points'] = $temp[$v]['total_points'] ? $temp[$v]['total_points'] : 0;
		}

		if ($searchParams['export']) {
			$this->export('experience_rank', '每天经验统计', $sdate, $edate, $ret);
			exit();
		}
		$this->assign('data', $ret);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);
	}

	public function rankdetailAction() {
		$postData = $this->getInput(array('date', 'page'));
		$page     = max($postData['page'], 1);
		if (empty($postData['date'])) $this->output('-1', '日期有错!');
		$stime             = strtotime($postData['date']);
		$etime             = strtotime($postData['date'] . "235959");
		$where['add_time'] = array(array('>=', $stime), array('<=', $etime));
		list($total, $dataList) = User_Service_ExperienceLog::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$typeList = Common::getConfig('userConfig', 'experience_activity_type');
		$this->assign('dataList', $dataList);
		$this->assign('types', $typeList);
		unset($postData['page']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['rankdetailUrl'] . "?" . http_build_query($postData) . "&"));

	}

	public function expUsedAction() {
		$searchParams = $this->getInput(array('sdate', 'edate', 'export', 'type'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		$where             = $params = array();
		$where['add_time'] = array(array('>=', strtotime($sdate)), array('<=', strtotime($edate)));
	}


	public function quizAction(){
		$searchParams = $this->getInput(array('sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($searchParams);
		
		$hreader = array('日期', '首页PV', '首页UV','答题完成页PV','答题完成页UV','额外奖励按钮PV','额外奖励按钮UV','求助按钮PV',
				'求助按钮UV','人均求助次数','找答案PV','找答案UV','人均找答案次数','总答题用户数','总答题数','人均答题数', '总答对题数'
				,'总答错题数','答题奖励金币数','额外奖励用户数','答题额外奖励金币数','求助用户占比','找答案用户占比','参与转化率'
		);
		$where             = $params = array();
		$formatSdate = date('Ymd',strtotime($sdate));
		$formatEdate = date('Ymd',strtotime($edate));
		$where['date'] = array(array('>=', $formatSdate), array('<=', $formatEdate));
		$where['score_type'] = array('IN',array( '211','212'));
		$where['group_id'] = 2;
		$dataList = User_Service_ScoreLog::getQuizScoreData($where,array('date','score_type'),array('date'=>'DESC'));
		$params['date'] = $where['date'] ;
		$params['ver'] = 'user_quiz';
		$quizData = Gionee_Service_Log::getsBy($params,array('date'=>'DESC'));
		$date = $ret = $temp = array();
		foreach ($quizData as $k=>$v){
			$temp[$v['date']][$v['type']][$v['key']] = $v['val']?$v['val']:0;
		}
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Ymd', $i);
		}
		foreach ($date as $v){
			$ret[$v]['quiz_pv_index'] 				= $temp[$v]['pv']['index']?$temp[$v]['pv']['index']:0;
			$ret[$v]['quiz_uv_index']	 				= $temp[$v]['uv']['index']?$temp[$v]['uv']['index']:0;
			$ret[$v]['quiz_pv_done']					 = $temp[$v]['pv']['done']?$temp[$v]['pv']['done']:0;
			$ret[$v]['quiz_uv_done']					=  $temp[$v]['uv']['done']?$temp[$v]['uv']['done']:0;
			$ret[$v]['quiz_pv_reward']				= $temp[$v]['pv']['reward']?$temp[$v]['pv']['reward']:0;
			$ret[$v]['quiz_uv_reward']				= $temp[$v]['uv']['reward']?$temp[$v]['uv']['reward']:0;
			$ret[$v]['quiz_pv_find']					 = $temp[$v]['pv']['find']?$temp[$v]['pv']['find']:0;
			$ret[$v]['quiz_uv_find']					 = $temp[$v]['uv']['find']?$temp[$v]['uv']['find']:0;
			$ret[$v]['quiz_pv_help']				 	= $temp[$v]['pv']['help']?$temp[$v]['pv']['help']:0;
			$ret[$v]['quiz_uv_help']					 = $temp[$v]['uv']['help']?$temp[$v]['uv']['help']:0;
			$ret[$v]['quiz_answer_total']  		= $temp[$v]['pv']['answer_total']?$temp[$v]['pv']['answer_total']:0;
			$ret[$v]['quiz_answer_right'] 			= $temp[$v]['pv']['answer_succ']?$temp[$v]['pv']['answer_succ']:0;
			$ret[$v]['quiz_answer_false'] 			= $temp[$v]['pv']['answer_fail']?$temp[$v]['pv']['answer_fail']:0;
			$ret[$v]['quiz_answer_user']			 = $dataList[$v]['211']['total_users']?$dataList[$v]['211']['total_users']:0;
			$ret[$v]['quiz_answer_scores']			 = $dataList[$v]['211']['total_scores']?$dataList[$v]['211']['total_scores']:0;
			$ret[$v]['quiz_reward_user']			 = $dataList[$v]['212']['total_users']?$dataList[$v]['212']['total_users']:0;
			$ret[$v]['quiz_reward_scores']			 = $dataList[$v]['212']['total_scores']?$dataList[$v]['212']['total_scores']:0;
		}
		if($searchParams['export']){
			$this->export('quiz', '答题日报', $sdate, $edate, array('data'=>$ret,'header'=>$hreader));
			exit();
		}
	$this->assign('ret', $ret);
	$this->assign('sdate', $sdate);
	$this->assign('edate', $edate);
	$this->assign('date', $date);
	$this->assign('header', $hreader);
	}
	
	public function broswerAction(){
		$postData = $this->getInput(array('page','sdate','edate'));
		list($page, $sdate, $edate) = $this->_initParams($postData);
		$header = array('');
		
	}
	
	private function _getVoipExchangeGoods($sdate, $edate) {
		$params                    = $where = array();
		$params['virtual_type_id'] = '999';
		$params['goods_type']      = '1';
		$params['start_time']      = array('<=', strtotime($edate));
		$params['end_time']        = array('>=', strtotime($sdate));
		$goodsList                 = User_Service_Commodities::getsBy($params, array('id' => 'DESC'));
		return $goodsList;
	}

	private function _getGoodsIds($goodsList) {
		$ids = array();
		foreach ($goodsList as $k => $v) {
			$ids[] = $v['id'];
		}
		return $ids;
	}

	private function _getVoipExchangeGoodsPvData($sdate, $edate, $ids) {
		if (empty($ids)) return false;
		$where['type'] = Gionee_Service_Log::TYPE_U_G_EXCHANGE;
		$where['key']  = array("IN", $ids);
		$where['date'] = array(array(">=", $sdate), array('>=', $edate));
		$logData       = Gionee_Service_Log::getsBy($where);
		$data          = array();
		foreach ($logData as $key => $val) {
			$data[$val['date']][$val['key']] = $val['val'];
		}
		return $data;
	}

	private function _getVoipExchangeSuccessData($sdate, $edate, $ids) {
		$where               = array();
		$where['score_type'] = '309';
		$where['fk_earn_id'] = array("IN", $ids);
		$where['date']       = array(array('>=', $sdate), array('<=', $edate));
		$dataList            = User_Service_ScoreLog::getVoipExchangeAmount($where, array('date'));
		$data                = array();
		foreach ($dataList as $k => $v) {
			$data[$v['date']] = $v['total'];
		}
		return $data;
	}


	private function _getExchangeSuccessDetailData($sdate, $edate, $ids) {
		$where               = array();
		$where['score_type'] = '309';
		$where['fk_earn_id'] = array("IN", $ids);
		$where['date']       = array(array('>=', $sdate), array('<=', $edate));
		$data                = User_Service_ScoreLog::getVoipExchangeDetailData($where, array('date', 'fk_earn_id'));
		if (empty($data)) return;
		$result = array();
		foreach ($data as $k => $v) {
			$result[$v['date']][$v['fk_earn_id']] = array(
				'total_user'  => $v['total_users'],
				'total_times' => $v['total_times']
			);
		}
		return $result;
	}

	/**
	 *
	 * @param string $type     类型
	 * @param string $filename 文件名
	 * @param string $sdate    开始时间
	 * @param string $edate    结束时间
	 * @param array  $list     数据列表
	 */
	public function export($type, $filename, $sdate, $edate, $list) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = empty($filename) ? '统计报表' : $filename;
		$filename .= $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($type) {
			case 'entrance':
				fputcsv($out, array('日期', 'key', '名称', 'PV', 'UV'));
				foreach ($list as $v) {
					fputcsv($out, array($v['date'], $v['key'], $v['name'], $v['pv'], $v['uv']));
				}
				break;
			case 'signin': {
				fputcsv($out, array('日期', '用户', '用户名', '累计签到数'));
				foreach ($list as $v) {
					fputcsv($out, array($sdate . ' 至 ' . $edate, $v['name'], $v['realname'], $v['cnt']));
				}
				break;
			}

			case 'day':
			case 'month': {
				$data    = $list['data'];
				$userMsg = $list['user'];

				fputcsv($out, array(
					'日期',
					'新增用户数',
					'累计用户数',
					',访问PV',
					'访问UV',
					'签到用户数',
					'签到用户占比',
					'领取金币用户数',
					'领取金币用户占比',
					'领取金币总额',
					'完成任务用户数',
					'完成任务用户占比',
					'完成任务次数',
					'积分兑换成功用户数',
					'积分兑换成功次数',
					'兑换次数',
					'积分兑换成功占比',
					'金币兑换金额数',
					'截止当日流通金币数',
					'抽奖用户数',
					'抽奖用户数占比',
					'抽奖次数',
					'夺宝用户数',
					'夺宝用户占比',
					'夺宝次数',
					'参与答题用户数',
					'答题用户占比',
					'答题发放金币数',
				));
				foreach ($data as $k => $v) {
					fputcsv($out, array(
						date('Y-m-d', strtotime($k)),
						$v['goods']['user_day_incre'] ? $v['goods']['user_day_incre'] : 0,
						$v['goods']['user_total_number'] ? $v['goods']['user_total_number'] : 0,
						$v['pv'] ? $v['pv'] : 0,
						$v['uv'] ? $v['uv'] : 0,
						$v['goods']['user_signin_num'] ? $v['goods']['user_signin_num'] : 0,
						$v['goods']['user_signin_num'] ? (bcdiv($v['goods']['user_signin_num'], $v['uv'], 4) * 100) . "%" : 0,
						$v['goods']['user_earn_num'] ? $v['goods']['user_earn_num'] : 0,
						$v['uv'] ? (bcdiv($v['goods']['user_earn_num'], $v['uv'], 4) * 100) . "%" : 0,
						$v['goods']['user_earn_amount'] ? $v['goods']['user_earn_amount'] : 0,
						$v['goods']['user_tasks_num'] ? $v['goods']['user_tasks_num'] : 0,
						$v['uv'] ? (bcdiv($v['goods']['user_tasks_num'], $v['uv'], 4) * 100) . "%" : 0,
						$v['goods']['user_tasks_amount'] ? $v['goods']['user_tasks_amount'] : 0,
						$v['goods']['user_exchange_num'] ? $v['goods']['user_exchange_num'] : 0,
						$v['goods']['user_exchange_scuessed_times'] ? $v['goods']['user_exchange_scuessed_times'] : 0,
						$v['goods']['user_exchange_total'] ? $v['goods']['user_exchange_total'] : 0,
						(bcdiv($v['goods']['user_exchange_scuessed_times'], $v['goods']['user_exchange_total'], 4) * 100) . "%",
						$v['goods']['user_exchange_costs'] ? $v['goods']['user_exchange_costs'] : 0,
						$v['goods']['user_currency_scores'] ? $v['goods']['user_currency_scores'] : 0,
						$v['uv_draw'] ? $v['uv_draw'] : 0,
						$v['uv_draw'] ? (bcdiv($v['uv_draw'], $v['uv'], 4) * 100) . '%' : 0,
						$v['pv_draw'] ? $v['pv_draw'] : 0,
						$v['snatch_users'] ? $v['snatch_users'] : 0,
						(bcdiv($v['user_amount'], $v['goods']['user_total_number'], 4) * 100) . '%',
						$v['snatch_times'] ? $v['snatch_times'] : 0,
						$v['quiz_users'],
						(bcdiv($v['quiz_users'], $userMsg['total_users'], 4) * 100) . '%',
						$v['quiz_scores']
					));
				}
				break;
			}
			case 'rank': {
				fputcsv($out, array('序号', '用户名', '当日领取金额', '截止当日领取金币总数', '当日完成任务次数', '截止当日完成任务总数'));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
						$k + 1,
						$v['username'],
						$v['total_scores'],
						$v['deadline_scores'],
						$v['tasks_number'] ? $v['tasks_number'] : 0,
						$v['deadline_tasks']
					));
				}
				break;
			}

			case 'lottery': {
				fputcsv($out, array(
					'日期',
					'抽奖页面PV',
					'抽奖页面UV',
					'抽奖按钮PV',
					'抽奖按钮UV',
					'成功抽奖次数',
					'人均抽奖次数',
					'人均成功抽奖次数',
					'中奖金币总额',
					'中奖金币用户数',
					'人均中奖金币额',
					'访问-抽奖转化率'
				));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
						$k,
						$v['pv_index'] ? $v['pv_index'] : 0,
						$v['uv_index'] ? $v['uv_index'] : 0,
						$v['pv_draw'] ? $v['pv_draw'] : 0,
						$v['uv_draw'] ? $v['uv_draw'] : 0,
						$v['drawing_times'] ? $v['drawing_times'] : 0,
						$v['uv_draw'] ? bcdiv($v['pv_draw'], $v['uv_draw'], 2) : 0,
						$v['total_users'] ? bcdiv($v['drawing_times'], $v['total_users'], 2) : 0,
						$v['total_scores'] ? $v['total_scores'] : 0,
						$v['total_users'] ? $v['total_users'] : 0,
						bcdiv($v['total_scores'], $v['total_users'], 2),
						(bcdiv($v['uv_draw'], $v['uv_index'], 4) * 100) . "%"
					));
				}
				break;
			}

			case 'scratch': {
				fputcsv($out, array('日期', '页面PV', '页面UV', '刮奖PV', '刮奖UV', '刮开人数', '获得金币用户数', '获得金币总数', '领取－刮开转化率'));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
							$k,
							$v['index_pv'] ? $v['index_pv'] : 0,
							$v['index_uv'] ? $v['index_uv'] : 0,
							$v['scratch_pv'] ? $v['scratch_pv'] : 0,
							$v['scratch_uv'] ? $v['scratch_uv'] : 0,
							$v['scratch_num'],
							$v['geted_number'],
							$v['scores_amount'],
							(bcdiv($v['geted_number'], $v['scratch_num'], 4) * 100) . "%"
						));
				}
				break;
			}

			case 'chatday':
			case 'chatmonth': {
				fputcsv($out, array(
					'日期',
					'持有通话时长用户数',
					'总兑换用户数',
					'累计总兑换时长',
					'人均兑换时长',
					'人均兑换消耗金币',
					'全部用户总持有通话时长',
					'人均持有通话时长'
				));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
							$k,
							$v['v__total_user'],
							$v['v_exchange_total_user'],
							$v['v_exchange_total_seconds'] ? $v['v_exchange_total_seconds'] : 0,
							($v['v_exchange_total_user'] ? bcdiv($v['v_exchange_total_seconds'], $v['v_exchange_total_user'], 2) : 0),
							$v['v_exchange_total_user'],
							$v['v_total_seconds'],
							($v['v__total_user'] ? bcdiv($v['v_total_seconds'], $v['v__total_user'], 2) : 0)
						));
				}
				break;
			}

			case 'vcenter': {
				fputcsv($out, array('日期', '畅聊账户PV', '畅聊账户UV', '兑换分钟按钮PV', '兑换率', '兑换成功次数', '兑换成功率'));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
						$k,
						$v['voip_center_pv'] ? $v['voip_center_pv'] : 0,
						$v['voip_center_uv'] ? $v['voip_center_uv'] : 0,
						$v['voip_exchange_pv'] ? $v['voip_exchange_pv'] : 0,
						$v['voip_exchange_ratio'],
						$v['voip_exchange_success_times'] ? $v['voip_exchange_success_times'] : 0,
						$v['voip_exchange_success_ratio']

					));
				}
				break;
			};
			case 'vedetail': {
				fputcsv($out, array('日期', '商品名称', '兑换分钟按钮PV', '兑换率', '兑换成功次数', '兑换成功率', '兑换用户'));
				foreach ($list as $k => $v) {
					foreach ($v as $m => $t) {
						fputcsv($out, array(
							$k,
							$t['name'],
							$t['click_times'] ? $t['click_times'] : 0,
							$t['exchange_ratio'] ? $t['exchange_ratio'] : 0,
							$t['total_success_times'] ? $t['total_success_times'] : 0,
							$t['exchange_ratio'] ? $t['exchange_ratio'] : 0,
							$t['total_user'] ? $t['total_user'] : 0,
						));
					}
				}
				break;

			}
			case 'snatch': {
				fputcsv($out, array(
					'日期',
					'页面PV',
					'页面UV',
					'10金币按钮PV',
					'10金币按钮UV',
					'20金币按钮PV',
					'20金币按钮UV',
					'50金币按钮PV',
					'50金币按钮UV',
					'100金币按钮PV',
					'100金币按钮UV',
					'完成夺宝总用户数',
					'完成夺宝总次数',
					'人均夺宝次数',
					'金币发放总数',
					'金币回收总数',
					'访问-夺宝转化率'
				));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
						$k,
						$v['index_pv'],
						$v['index_uv'],
						$v['goods']['10']['snatch_goods_pv'],
						$v['goods']['10']['snatch_goods_uv'],
						$v['goods']['20']['snatch_goods_pv'],
						$v['goods']['20']['snatch_goods_uv'],
						$v['goods']['50']['snatch_goods_pv'],
						$v['goods']['50']['snatch_goods_uv'],
						$v['goods']['100']['snatch_goods_pv'],
						$v['goods']['100']['snatch_goods_uv'],
						$v['snatch_users'],
						$v['snatch_times'],
						bcdiv($v['snatch_times'], $v['snatch_users'], 4),
						$v['snatch_prize_scores'],
						$v['snatch_cost_scores'],
						$v['snatch_users'] ? (bcdiv($v['snatch_users'], $v['index_uv'], 4) * 100) . "%" : 0
					));
				}
				break;
			}
			case 'experience_users': {
				fputcsv($out, array(
					'日期',
					'等级页PV',
					'等级页UV',
					'等级1用户数',
					'等级2用户数',
					'晋级用户数',
					'等级3用户数',
					'晋级用户数',
					'等级4用户数',
					'晋级用户数',
					'等级5用户数',
					'晋级用户数',
					'等级6用户数',
					'晋级用户数'
				));
				foreach ($list as $k => $v) {
					fputcsv($out, array(
						$k,
						$v['pv'] ? $v['pv'] : 0,
						$v['uv'] ? $v['uv'] : 0,
						$v['1']['users'],
						$v['2']['users'],
						$v['2']['incre'],
						$v['3']['users'],
						$v['3']['incre'],
						$v['4']['users'],
						$v['4']['incre'],
						$v['5']['users'],
						$v['5']['incre'],
						$v['6']['users'],
						$v['6']['incre']
					));
				}
				break;
			}
			case 'experience_rank': {
				fputcsv($out, array('日期', '获得经验值数', '获得经验用户数'));
				foreach ($list as $k => $v) {
					fputcsv($out, array($k, $v['total_points'], $v['total_users']));
				}
				break;
			}
			case 'quiz':{
				fputcsv($out,$list['header']);
				foreach ($list['data'] as $k=>$v){
					fputcsv($out, 
					array($k, 
					$v['quiz_pv_index'],
					 $v['quiz_uv_index'],
					 $v['quiz_pv_done'],
					 $v['quiz_uv_done'],
					 $v['quiz_pv_reward'],
					 $v['quiz_uv_reward'],
					 $v['quiz_pv_help'],
					 $v['quiz_uv_help'],
					 bcdiv($v['quiz_pv_help'],$v['quiz_uv_help'],2),
					 $v['quiz_pv_find'],
					 $v['quiz_uv_find'],
					 bcdiv($v['quiz_pv_find'],$v['quiz_uv_find'],2),
					 $v['quiz_answer_user'],
					 $v['quiz_answer_total'],
					 bcdiv($v['quiz_answer_total'],$v['quiz_answer_user'],2),
					 $v['quiz_answer_right'],
					 $v['quiz_answer_false'],
					 $v['quiz_answer_scores'],
					 $v['quiz_reward_user'],
					 $v['quiz_reward_scores'],
					 (bcdiv($v['quiz_uv_help'],$v['quiz_uv_index'],4)*100)."%",
					 (bcdiv($v['quiz_uv_find'],$v['quiz_uv_index'],4)*100)."%",
					 (bcdiv($v['quiz_answer_user'],$v['quiz_uv_index'],4)*100)."%"
					));
				}
				break;
			}
			
		}
	}

	/**
	 * 参数初始化
	 */
	private function _initParams($data = array()) {
		if (!is_array($data)) return false;
		$page  = max($data['page'], 1);
		$sDate = $data['sdate'];
		$eDate = $data['edate'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', time());
		return array($page, $sDate, $eDate);
	}


	public function exchangeAction() {
		$searchParam          = $this->getInput(array('goods_id', 'sdate', 'edate', 'page', 'pagesie', 'export'));
		$searchParam['sdate'] = empty($searchParam['sdate']) ? date('Y-m-d', strtotime("-8 day")) : $searchParam['sdate'];
		$searchParam['edate'] = empty($searchParam['edate']) ? date('Y-m-d', strtotime("+1 day")) : $searchParam['edate'];

		$dates = $ids = $dataList = $bodyRow = $names = array();
		for ($i = strtotime($searchParam['sdate']); $i <= strtotime($searchParam['edate']); $i += 86400) {
			$dates[] = date('Y-m-d', $i);
		}

		$param = array();
		if (!empty($searchParam['goods_id'])) {
			$param['id'] = $searchParam['goods_id'];
		}

		$tmp = User_Service_Commodities::getsBy();
		foreach ($tmp as $v) {
			$names[$v['id']] = $v['name'];
		}

		list($totalNum, $goodsList) = User_Service_Commodities::getList($searchParam['page'], $this->pageSize, $param);

		foreach ($goodsList as $val) {
			$ids[$val['id']] = $val['id'];
			$where           = array(
				'id'    => $val['id'],
				'sdate' => strtotime($searchParam['sdate']),
				'edate' => strtotime($searchParam['edate']),
			);
			$times           = User_Service_OrderGoods::getTimesByGoodsId($where);
			$peoples         = User_Service_OrderGoods::getPeoplesByGoodsId($where);
			$total           = User_Service_OrderGoods::getTotalByGoodsId($where);


			foreach ($times as $v) {
				$dataList[$val['id']]['times'][$v['day']] = $v['num'];
			}
			foreach ($peoples as $v) {
				$dataList[$val['id']]['peoples'][$v['day']] = $v['num'];
			}
			foreach ($total as $v) {
				$dataList[$val['id']]['cost_money'][$v['day']] = $v['cost_money'];
				$dataList[$val['id']]['cost_score'][$v['day']] = $v['cost_score'];
			}
		}


		$pv_uv = array(
			Gionee_Service_Log::TYPE_U_G_EXCHANGE    => 'exchange_pv',
			Gionee_Service_Log::TYPE_U_G_EXCHANGE_UV => 'exchange_uv',
			Gionee_Service_Log::TYPE_U_G_DETAIL      => 'detail_pv',
			Gionee_Service_Log::TYPE_U_G_DETAIL_UV   => 'detail_uv',
			Gionee_Service_Log::TYPE_U_G_SCORE       => 'score_pv',
			Gionee_Service_Log::TYPE_U_G_SCORE_UV    => 'score_uv',
			Gionee_Service_Log::TYPE_U_G_PHONE       => 'phone_pv',
			Gionee_Service_Log::TYPE_U_G_PHONE_UV    => 'phone_uv',
		);

		foreach ($pv_uv as $t => $k) {
			$listPV = Gionee_Service_Log::getStatData($t, $ids, date('Ymd', $where['sdate']), date('Ymd', $where['edate']));
			foreach ($listPV as $id => $v) {
				$dataList[$id][$k] = $v;
			}
		}

		foreach ($dataList as $id => $v) {
			foreach ($dates as $d) {
				$exchange_pv = intval($v['exchange_pv'][$d]);
				$exchange_uv = intval($v['exchange_uv'][$d]);
				$detail_pv   = intval($v['detail_pv'][$d]);
				$detail_uv   = intval($v['detail_uv'][$d]);
				$score_pv    = intval($v['score_pv'][$d]);
				$score_uv    = intval($v['score_uv'][$d]);
				$phone_pv    = intval($v['phone_pv'][$d]);
				$phone_uv    = intval($v['phone_uv'][$d]);
				$times       = intval($v['times'][$d]);
				$peoples     = intval($v['peoples'][$d]);
				$cost_score  = intval($v['cost_score'][$d]);
				$cost_money  = $v['cost_money'][$d];
				$name        = isset($names[$id]) ? $names[$id] : $id;
				$bodyRow[]   = array(
					$d,
					$id,
					$name,
					$score_pv,
					$score_uv,
					$detail_pv,
					$detail_uv,
					$phone_pv,
					$phone_uv,
					$exchange_pv,
					$exchange_uv,
					floor($exchange_pv / $exchange_uv),
					$times,
					$peoples,
					floor($times / $peoples),
					(number_format($times / $exchange_pv, 2) * 100) . '%',
					$cost_score,
					$cost_money,
				);
			}
		}

		$headers = array(
			'日期',
			'ID',
			'兑换名称',
			'立即兑换PV',
			'立即兑换UV',
			'兑换详情PV',
			'兑换详情UV',
			'号码确认PV',
			'号码确认UV',
			'立即领取PV',
			'立即领取UV',
			'人均领取次数',
			'成功次数',
			'成功人数',
			'人均成功次数',
			'成功率',
			'消耗金币',
			'消耗成本',
		);

		if (!empty($searchParam['export'])) {
			array_unshift($bodyRow, $headers);
			$this->_export($bodyRow, $searchParam['sdate'], $searchParam['edate'], '兑换报表');
			exit;
		}

		$this->assign('headers', $headers);
		$this->assign('dates', $dates);
		$this->assign('names', $names);
		$this->assign('searchParam', $searchParam);
		$this->assign('dataList', $dataList);
		$this->assign('bodyRow', $bodyRow);
		$this->assign('pager', Common::getPages($totalNum, $searchParam['page'], $this->pageSize, 'exchange?' . http_build_query($searchParam) . '&'));
	}

	public function lotteryAction() {
		$postData = $this->getInput(array('page', 'sdate', 'edate', 'export'));
		list($page, $sdate, $edate) = $this->_initParams($postData);
		$params               = array();
		$params['score_type'] = 203;
		$params['date']       = array(
			array('>=', date('Ymd', strtotime($sdate))),
			array('<=', date('Ymd', strtotime($edate)))
		);
		$dataList             = User_Service_ScoreLog::getLotteryDayData($params, array('date'), array('date' => 'DESC')); //获得积分

		$params['score_type'] = array('IN', array('303', '307'));
		$drawTimes            = User_Service_ScoreLog::getDrawingTimesList($params, array('date'), array('date' => 'DESC'));
		//pv,uv 查询
		$indexpvList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_lottery_index', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));
		$indexUvList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_lottery_index', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));

		$drawPvList = Gionee_Service_Log::getPvUvStatByKey('pv', 'user_lottery_drawing', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));
		$drawUvList = Gionee_Service_Log::getPvUvStatByKey('uv', 'user_lottery_drawing', date('Ymd', strtotime($sdate)), date('Ymd', strtotime($edate)));


		$date = $result = array();
		for ($i = strtotime($edate); $i >= strtotime($sdate); $i -= 86400) {
			$date[] = date('Y-m-d', $i);
		}

		foreach ($date as $k => $v) {
			$result[$v]['pv_index']      = $indexpvList[$v];
			$result[$v]['uv_index']      = $indexUvList[$v];
			$result[$v]['pv_draw']       = $drawPvList[$v];
			$result[$v]['uv_draw']       = $drawUvList[$v];
			$result[$v]['total_users']   = $dataList[$v]['total_users'];
			$result[$v]['total_scores']  = $dataList[$v]['total_scores'];
			$result[$v]['drawing_times'] = $drawTimes[$v]['drawing_times'];
		}
		if ($postData['export']) {
			$this->export('lottery', '抽奖日报', $sdate, $edate, $result);
			exit();
		}
		$this->assign('data', $result);
		$this->assign('sdate', $sdate);
		$this->assign('edate', $edate);

	}

	public function taskAction() {

		$searchParam          = $this->getInput(array('goods_id', 'sdate', 'edate', 'export'));
		$searchParam['sdate'] = empty($searchParam['sdate']) ? date('Y-m-d', strtotime("-8 day")) : $searchParam['sdate'] ;
		$searchParam['edate'] = empty($searchParam['edate']) ? date('Y-m-d', strtotime("+1 day")) : $searchParam['edate'] ;

		$dates = $cateNames = $dataList = $names = $cates = $bodyRow = array();
		for ($i = strtotime($searchParam['sdate']); $i <= strtotime($searchParam['edate']); $i += 86400) {
			$dates[] = date('Y-m-d', $i);
		}

		$cateAll = User_Service_Category::getsBy();
		foreach ($cateAll as $v) {
			$cateNames[$v['id']] = $v['name'];
		}

		$ids = $name =  array();
		$tmp = User_Service_Produce::getsBy();
		foreach ($tmp as $v) {
			$names[$v['id']] = $v['name'];
			$cates[$v['id']] = $cateNames[$v['cat_id']];
			$ids[] = $v['id'];
		}
		//$ids = array_keys($names);
		if (!empty($searchParam['goods_id'])) {
			$ids = array($searchParam['goods_id']);
		}

		$where = array(
			'sdate'    => strtotime($searchParam['sdate']." 00:00:00"),
			'edate'    => strtotime($searchParam['edate']." 23:59:59"),
			'goods_id' => $ids,
		);

		$list = User_Service_Earn::getTaskLogList($where);
		foreach ($list as $v) {
			$dataList[$v['goods_id']]['times'][$v['day']]   = $v['times'];
			$dataList[$v['goods_id']]['peoples'][$v['day']] = $v['peoples'];
			$dataList[$v['goods_id']]['scores'][$v['day']]  = $v['scores'];
		}

		$pv_uv = array(
			Gionee_Service_Log::TYPE_U_G_IMG    => 'img_pv',
			Gionee_Service_Log::TYPE_U_G_IMG_UV => 'img_uv',
		);

		foreach ($pv_uv as $t => $k) {
			$listPV = Gionee_Service_Log::getStatData($t, $ids, date('Ymd', $where['sdate']), date('Ymd', $where['edate']));
			foreach ($listPV as $id => $v) {
				$dataList[$id][$k] = $v;
			}
		}

		foreach ($ids as $id) {
			$v = $dataList[$id];
			foreach ($dates as $d) {
				$times     = intval($v['times'][$d]);
				$peoples   = intval($v['peoples'][$d]);
				$scores    = intval($v['scores'][$d]);
				$imgPv     = intval($v['img_pv'][$d]);
				$imgUv     = intval($v['img_uv'][$d]);
				$name      = isset($names[$id]) ? $names[$id] : $id;
				$cateName  = isset($cates[$id]) ? $cates[$id] : $id;
				$bodyRow[] = array($d, $id, $cateName, $name, $imgPv, $imgUv, $times, $peoples, $scores);
			}
		}

		$headers = array('日期', '任务ID', '任务类型', '任务名称', 'PV', 'UV', '次数', '用户数', '发放金币');

		if (!empty($searchParam['export'])) {
			array_unshift($bodyRow, $headers);
			$this->_export($bodyRow, $searchParam['sdate'], $searchParam['edate'], '任务报表');
			exit;
		}

		$this->assign('headers', $headers);
		$this->assign('names', $names);
		$this->assign('cates', $cates);
		$this->assign('dates', $dates);
		$this->assign('searchParam', $searchParam);
		$this->assign('dataList', $dataList);
		$this->assign('bodyRow', $bodyRow);

	}

	public function ajaxGetGoodsListAction() {
		$task_id = $this->getInput('task_id');
		if (empty($task_id)) return;


	}

	private function _export($list, $sdate, $edate, $filename = '报表') {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename .= $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		foreach ($list as $v) {
			fputcsv($out, $v);
		}
		exit;
	}


    public function browseronlinestatAction() {
        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $vers = array(
            'browser_online_pv' => '头像PV',
            'browser_online_uv' => '头像UV',
            'p_1' => '任务1用户',
            'p_2' => '任务2用户',
            'p_3' => '任务3用户',
            'c_2' => '任务1金币',
            'c_5' => '任务2金币',
            'c_10' => '任务3金币',
        );

        $where          = array();
        $where['ver']   = 'user_index';
        $where['key']   = 'browser_online';
        $where['stime'] = strtotime($sdate);
        $where['etime'] = strtotime($edate);
        $where['type']  = 'pv';
        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key'].'_pv'][$val['date']] = $val['val'];
        }
        $where['type']  = 'uv';
        $datas = Gionee_Service_Log::getListByWhere($where);
        foreach ($datas as $val) {
            $ret[$val['key'].'_uv'][$val['date']] = $val['val'];
        }

        $datas = User_Service_TaskBrowserOnline::getDao()->getTaskBrowserOnlineTotalPeople($sdate,$edate);
        foreach ($datas as $val) {
            $ret['p_'.$val['stage']][$val['date']] = $val['val'];
        }

        $datas = User_Service_ScoreLog::getTaskBrowserOnlineTotalCoin($sdate,$edate);

        foreach ($datas as $val) {
            $ret['c_'.$val['stage']][$val['date']] = $val['val'];
        }

        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportStatTotal($_data, "统计数据", $sdate, $edate);
            exit();
        }

        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
    }


}
