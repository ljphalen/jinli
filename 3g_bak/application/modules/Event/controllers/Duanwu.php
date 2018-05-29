<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 端午活动
 * @author huwei
 *
 */
class DuanwuController extends Front_BaseController {

	public function indexAction() {

		$uName = Common::getUName();
		if (empty($uName)) {
			exit('非法用户');
		}
		$data          = array();
		$now           = time();
		$timeArr       = Event_Service_Duanwu::getTime();
		$data['pz_ep'] = 1;
		if ($now > $timeArr[0] && $now < $timeArr[1]) {
			$data['pz_ep'] = 0;
		}

		$from = $this->getInput('from');
		$from = 'index_' . $from;

		$info = Event_Service_Duanwu::getInfoByName($uName);
		if (empty($info['id'])) {
			exit('非法用户');
		}

		Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $from . ':duanwu');
		Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $from . ':duanwu');

		$upData  = array();
		$nowDate = date('Ymd');
		if ($info['cur_date'] != $nowDate) {
			$info['cur_num']    = 0;
			$info['cur_date']   = $nowDate;
			$info['log_id']     = 0;
			$upData['cur_num']  = $info['cur_num'];
			$upData['cur_date'] = $info['cur_date'];
			$upData['log_id']   = $info['log_id'];
		}

		if (!empty($upData)) {
			Event_Service_Duanwu::upUserInfo($upData, $uName);
		}

		$logInfo = Event_Service_Duanwu::getLogInfo($info['log_id']);
		if (!empty($logInfo['rank']) && $logInfo['status'] == 0) {
			$rankKinds = Event_Service_Duanwu::getRankKinds();
			if (isset($rankKinds[$logInfo['rank']])) {
				$goodsId   = $logInfo['val'];
				$goodsInfo = User_Service_Commodities::get($goodsId);
				$msg       = $goodsInfo['name'];
				$url       = sprintf('%s/user/goods/detail?from=duanwu&goods_id=%d', Common::getCurHost(), $goodsId);
			} else {
				$coin = $logInfo['val'];
				$msg  = $coin . '金币';
				$url  = sprintf('%s/user/index/index?from=7', Common::getCurHost());
			}

			$data['pz_tt'] = $msg;
			$data['pz_ur'] = $url;
		}

		$data['pz_rk'] = $logInfo['status'] == 1 ? 0 : intval($logInfo['rank']);
		$data['pz_rm'] = max(Event_Service_Duanwu::getTimes() - $info['cur_num'], 0);
		$this->assign('data', $data);

	}

	public function doAction() {
		$act   = $this->getInput('act');
		$pzId  = $this->getInput('pz_id');
		$act   = !empty($act) ? $act : 'start';
		$uName = Common::getUName();
		if (empty($uName)) {
			exit('非法用户');
		}
		$info = Event_Service_Duanwu::getInfoByName($uName);
		if (empty($info['id'])) {
			exit('非法用户');
		}

		$out = array();
		if ($act == 'start') {
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'start:duanwu');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'start:duanwu');
			if ($info['cur_num'] >= Event_Service_Duanwu::getTimes()) {
				exit('次数已满');
			}

			if (!stristr($_SERVER['HTTP_REFERER'], 'event/duanwu/index')) {
				exit('非法操作');
			}

			$curNum       = $info['cur_num'] + 1;
			$leftNum      = max(Event_Service_Duanwu::getTimes() - $curNum, 0);
			$out['pz_rm'] = $leftNum; // 剩余次数

			$upData = array(
				'cur_num' => $curNum,
				'status'  => 1,
			);
			Event_Service_Duanwu::upUserInfo($upData, $uName);
		} else if ($act == 'stop') {
			if ($info['status'] != 1) {
				exit('非法操作');
			}

			if ($info['cur_num'] > Event_Service_Duanwu::getTimes()) {
				exit('次数已满');
			}

			if (!stristr($_SERVER['HTTP_REFERER'], 'event/duanwu/index')) {
				exit('非法操作');
			}

			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'stop:duanwu');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'stop:duanwu');
			$rcKey = 'duanwu_ip:' . date('Ymd') . ':' . Util_Http::ua('ip');
			$num   = Common::getCache()->get($rcKey);
			if (1 == $pzId) {
				if ($num >= Event_Service_Duanwu::getTimes()) {
					exit('非法操作');
				}
				Common::getCache()->set($rcKey, $num + 1, Common::T_ONE_DAY);


				$kindRates = Event_Service_Duanwu::getKindRate();
				$no        = Event_Service_Duanwu::getLogDao()->count();

				$_no = $this->getInput('_no');
				if (!empty($_no)) {
					$no = $_no;
				}

				$isOk = false;
				$tmp  = Event_Service_Duanwu::getLogDao()->getBy(array('no' => $no));
				if (empty($tmp['id'])) {
					$isOk = true;
				}


				if ($isOk && isset($kindRates[$no])) {
					$rankKinds = Event_Service_Duanwu::getRankKinds();
					$rank      = $kindRates[$no];
					$goodsId   = $rankKinds[$rank];
					$goodsInfo = User_Service_Commodities::get($goodsId);
					$msg       = $goodsInfo['name'];
					$url       = sprintf('%s/user/goods/detail?from=duanwu&goods_id=%s', Common::getCurHost(), $goodsId);
					$val       = $goodsId;
				} else {
					$rank      = 5;
					$coinRates = Event_Service_Duanwu::getCoinRate();
					$coin      = Common_Service_User::getRangeData($coinRates);
					$url       = sprintf('%s/user/index/index', Common::getCurHost());
					$msg       = $coin . '金币';
					$val       = $coin;
				}

				$addData = array(
					'uname'      => $uName,
					'date'       => date('Ymd'),
					'uid'        => 0,
					'rank'       => $rank,
					'val'        => $val,
					'created_at' => time(),
					'status'     => 0,
					'no'         => $no,
					'ip_addr'    => Util_Http::ua('ip'),
				);
				$ret     = Event_Service_Duanwu::getLogDao()->insert($addData);
				$logId   = 0;
				if ($ret) {
					$logId = Event_Service_Duanwu::getLogDao()->getLastInsertId();
				}
				$out['pz_rk'] = $rank;
				$out['pz_tt'] = $msg;
				$out['pz_ur'] = $url;

				$upData = array('status' => 2, 'log_id' => $logId);
			} else {
				$upData = array('status' => 0);
			}
			Event_Service_Duanwu::upUserInfo($upData, $uName);

		} else if ($act == 'give_up') {
			Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'give_up:duanwu');
			Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'give_up:duanwu');
			$upData       = array('log_id' => 0, 'status' => 0);
			$out['pz_rk'] = 0;
			Event_Service_Duanwu::upUserInfo($upData, $uName);
		}

		$this->output(0, '', $out);
	}

	public function clearAction() {
		$uName = Common::getUName();
		if (empty($uName)) {
			exit('非法用户');
		}

		$info = Event_Service_Duanwu::getInfoByName($uName);
		if (empty($info['id'])) {
			exit('非法用户');
		}
		$upData = array(
			'cur_num' => 0,
			'status'  => 0,
			'log_id'  => 0
		);
		Event_Service_Duanwu::upUserInfo($upData, $uName);
		echo Common::jsonEncode($info);
		Common::redirect('/event/duanwu/index');
		exit;
	}



}