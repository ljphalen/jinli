<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信管理
 */
class WxhelpController extends Admin_BaseController {

	public $actions = array(
		'listUrl'        => '/Admin/Wxhelp/list',
		'editUrl'        => '/Admin/Wxhelp/edit',
		'delUrl'         => '/Admin/Wxhelp/del',
		'userlistUrl'    => '/Admin/Wxhelp/userlist',
		'usereditUrl'    => '/Admin/Wxhelp/useredit',
		'addresslistUrl' => '/Admin/Wxhelp/addresslist',
		'addressEditUrl' => '/Admin/Wxhelp/addressedit',
	);

	public $perpage = 20;


	public function listAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
		if (!empty($get['togrid'])) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if ($k == 'start_time') {
					$where['created_at'][] = array('>=', strtotime($v . ' 00:00:00'));
				} else if ($k == 'end_time') {
					$where['created_at'][] = array('<=', strtotime($v . ' 23:59:59'));
				} else if (strlen($v) > 0) {
					$where[$k] = $v;
				}
			}

			$total = Gionee_Service_WxHelp::getDaoList()->count($where);
			$start = (max($page, 1) - 1) * $offset;
			$list  = Gionee_Service_WxHelp::getDaoList()->getList($start, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$where                    = array('event_id' => $v['id']);
				$list[$k]['total_amount'] = Gionee_Service_WxHelp::getDaoRel()->sum('amount', $where);
				$list[$k]['amount_num']   = Gionee_Service_WxHelp::getDaoRel()->count($where);
				$list[$k]['user_num']     = Gionee_Service_WxHelp::getDaoUser()->count($where);
				$list[$k]['created_at']   = date('y/m/d H:i', $v['created_at']);
				$list[$k]['start_time']   = date('y/m/d H:i', $v['start_time']);
				$list[$k]['end_time']     = date('y/m/d H:i', $v['end_time']);
				$list[$k]['num']          = date('y/m/d H:i', $v['end_time']);
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

	}

	public function editAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array(
			'id',
			'title',
			'start_time',
			'end_time',
			'wx_appid',
			'wx_appkey',
			'total_num',
			'rule_num',
			'result_num',
		));
		$now      = time();
		if (!empty($postData['title'])) {
			$postData['rule_num']   = $_POST['rule_num'];
			$postData['start_time'] = strtotime($postData['start_time']);
			$postData['end_time']   = strtotime($postData['end_time']);
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_WxHelp::getDaoList()->insert($postData);
			} else {
				$ret = Gionee_Service_WxHelp::getDaoList()->update($postData, $postData['id']);
				Gionee_Service_WxHelp::getInfo($postData['id'], true);
				Gionee_Service_WxHelp::getResultList($postData['id'], true);
			}

			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info   = Gionee_Service_WxHelp::getDaoList()->get($id);
		$qrcode = json_decode($info['qrcode'], true);
		if ($info['wx_appid'] != $qrcode['appid'] || empty($qrcode['ticket'])) {
			$wx     = new Vendor_Weixin($info['wx_appid'], $info['wx_appkey']);
			$qrcode = $wx->makeQrcode('51');
			if ($qrcode['ticket']) {
				$qrcode['appid'] = $info['wx_appid'];
				Gionee_Service_WxHelp::getDaoList()->update(array('qrcode' => Common::jsonEncode($qrcode)), $id);
			}
		}
		$info['qrcode'] = $qrcode;

		$this->assign('info', $info);
	}

	public function getRank($top, $uid) {
		foreach ($top as $rank => $list) {
			foreach ($list as $user) {
				if ($user['id'] == $uid) {
					return $rank;
				}
			}
		}
		return 0;
	}

	public function addresslistAction() {
		$get      = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
		$userList = Gionee_Service_WxHelp::getResultTop(2);

		$top[1] = array_slice($userList, 0, 1);
		$top[2] = array_slice($userList, 1, 2);
		$top[3] = array_slice($userList, 3, 5);
		$top[4] = array_slice($userList, 8);

		if (!empty($get['togrid'])) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort] = $order;
			$where          = array();
			$total          = Gionee_Service_WxHelp::getDaoAddress()->count($where);
			$start          = (max($page, 1) - 1) * $offset;
			$list           = Gionee_Service_WxHelp::getDaoAddress()->getList($start, $offset, $where, $orderBy);

			$tmpList = Gionee_Service_Area::getAllCity();
			$citys   = array();
			foreach ($tmpList as $val) {
				$citys[$val['id']] = $val['name'];
			}

			$tmpList   = Gionee_Service_Area::getProvinceList();
			$provinces = array();
			foreach ($tmpList as $val) {
				$provinces[$val['id']] = $val['name'];
			}

			foreach ($list as $k => $v) {
				$user                    = Gionee_Service_WxHelp::getDaoUser()->get($v['uid']);
				$list[$k]['openid']      = $user['openid'];
				$list[$k]['nickname']    = $user['nickname'];
				$list[$k]['created_at']  = date('y/m/d H:i', $v['created_at']);
				$list[$k]['city_id']     = $citys[$v['city_id']];
				$list[$k]['province_id'] = $provinces[$v['province_id']];
				$list[$k]['rank']        = $this->getRank($top, $v['uid']);
			}

			if ($_POST['export']) {
				$rows = array(
					array(
						'id',
						'openid',
						'名称',
						'省份',
						'城市',
						'收货地址',
						'时间'
					)
				);

				foreach ($list as $v) {
					$rows[] = array(
						$v['id'],
						$v['openid'],
						$v['username'],
						$v['province_id'],
						$v['city_id'],
						$v['address'],
						$v['created_at'],
					);
				}
				Common::export($rows, '', '', '获奖用户地址');
				exit;
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}
	}

	public function userlistAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
		if (!empty($get['togrid'])) {
			$page                    = max(intval($get['page']), 1);
			$offset                  = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort                    = !empty($get['sort']) ? $get['sort'] : 'id';
			$order                   = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy['total_amount'] = 'desc';
			$orderBy[$sort]          = $order;
			$where                   = array('event_id' => 2);
			foreach ($_POST['filter'] as $k => $v) {
				if ($k == 'start_time') {
					$where['created_at'][] = array('>=', strtotime($v . ' 00:00:00'));
				} else if ($k == 'end_time') {
					$where['created_at'][] = array('<=', strtotime($v . ' 23:59:59'));
				} else if (strlen($v) > 0) {
					$where[$k] = $v;
				}
			}

			$id    = $where['event_id'];
			$total = Gionee_Service_WxHelp::getDaoUser()->count($where);
			$start = (max($page, 1) - 1) * $offset;
			$list  = Gionee_Service_WxHelp::getDaoUser()->getList($start, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
			}

			if ($_POST['export']) {
				$rows = array(
					array(
						'id',
						'openid',
						'名称',
						'省份',
						'城市',
						'总数',
						'被帮助次数',
						'帮助他人次数',
						'首访次数',
						'参加时间'
					)
				);

				foreach ($list as $v) {
					$rows[] = array(
						$v['id'],
						$v['openid'],
						$v['nickname'],
						$v['province'],
						$v['city'],
						$v['total_amount'],
						$v['total_times'],
						$v['total_times_f'],
						$v['created_at'],
					);
				}
				Common::export($rows, '', '', '活动用户_' . $id);
				exit;
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

		$list = Gionee_Service_WxHelp::getDaoList()->getsBy();
		$this->assign('list', $list);
	}

	public function usereditAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'award_code'));
		if (!empty($postData['id'])) {
			$ret = Gionee_Service_WxHelp::getDaoUser()->update($postData, $postData['id']);
			if (!empty($postData['done_time'])) {
				$postData['done_time'] = strtotime($postData['done_time']);
			}

			$info = Gionee_Service_WxHelp::getUserInfo($postData['id'], true);
			Gionee_Service_WxHelp::getResultList($info['event_id'], true);
			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info  = Gionee_Service_WxHelp::getDaoUser()->get($id);
		$where = array('event_id' => $info['event_id'], 'uid' => $info['id']);
		$list  = Gionee_Service_WxHelp::getDaoRel()->getsBy($where, array('created_at' => 'desc'));
		$this->assign('info', $info);
		$this->assign('list', $list);
	}


	public function statAction() {
		$sDate   = $this->getInput('sdate');
		$eDate   = $this->getInput('edate');
		$eventid = $this->getInput('event_id');
		$export  = $this->getInput('export');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		$searchParam          = $this->getInput(array('event_id', 'sdate', 'edate', 'export'));
		$searchParam['sdate'] = empty($searchParam['sdate']) ? date('Y-m-d', strtotime("-8 day")) : $searchParam['sdate'];
		$searchParam['edate'] = empty($searchParam['edate']) ? date('Y-m-d', strtotime("+1 day")) : $searchParam['edate'];

		$dates = $cateNames = $dataList = $names = $cates = $bodyRow = array();
		for ($i = strtotime($searchParam['sdate']); $i <= strtotime($searchParam['edate']); $i += 86400) {
			$dates[] = date('Y-m-d', $i);
		}

		$eventid = !empty($eventid) ? $eventid : 1;

		$where = array(
			'type' => array('IN', array(Gionee_Service_Log::TYPE_WXHELP_PV, Gionee_Service_Log::TYPE_WXHELP_UV)),
			'key'  => $eventid,
			'date' => array(array('>=', date('Ymd', strtotime($sDate))), array('<=', date('Ymd', strtotime($eDate)))),
		);

		$list = Gionee_Service_Log::getsBy($where, array('date' => 'desc'));
		$tmp  = array();
		foreach ($list as $val) {
			$tmp[$val['date']][$val['type']][$val['ver']] = $val['val'];
		}

		if ($export) {


			$rows = array(
				array(
					'日期',
					'首页',
					'结果页pv',
					'结果页uv',
					'自己分享pv',
					'自己分享uv',
					'朋友分享pv',
					'朋友分享uv',
					'帮自己pv',
					'帮自己uv',
					'帮朋友pv',
					'帮朋友uv',
				)
			);

			foreach ($tmp as $k => $v) {
				$rows[] = array(
					date('Y-m-d', strtotime($k)),
					$v['wxhelp_pv']['index'] ? $v['wxhelp_pv']['index'] : 0,
					$v['wxhelp_pv']['result'] ? $v['wxhelp_pv']['result'] : 0,
					$v['wxhelp_uv']['result'] ? $v['wxhelp_uv']['result'] : 0,
					$v['wxhelp_pv']['share_self'] ? $v['wxhelp_pv']['share_self'] : 0,
					$v['wxhelp_uv']['share_self'] ? $v['wxhelp_uv']['share_self'] : 0,
					$v['wxhelp_pv']['share_friend'] ? $v['wxhelp_pv']['share_friend'] : 0,
					$v['wxhelp_uv']['share_friend'] ? $v['wxhelp_uv']['share_friend'] : 0,
					$v['wxhelp_pv']['apply_self'] ? $v['wxhelp_pv']['apply_self'] : 0,
					$v['wxhelp_uv']['apply_self'] ? $v['wxhelp_uv']['apply_self'] : 0,
					$v['wxhelp_pv']['apply_friend'] ? $v['wxhelp_pv']['apply_friend'] : 0,
					$v['wxhelp_uv']['apply_friend'] ? $v['wxhelp_uv']['apply_friend'] : 0,
				);
			}
			Common::export($rows, '', '', '微信帮忙活动_' . $eventid);
			exit;
		}

		$list = Gionee_Service_WxHelp::getDaoList()->getsBy();
		$this->assign('dataList', $tmp);
		$this->assign('list', $list);
		$this->assign('searchParam', $searchParam);

	}


	public function statuserincrAction() {
		$searchParam          = $this->getInput(array('event_id', 'sdate', 'edate', 'export'));
		$searchParam['sdate'] = empty($searchParam['sdate']) ? date('Y-m-d', strtotime("today")) . ' 00:00:00' : $searchParam['sdate'];
		$searchParam['edate'] = empty($searchParam['edate']) ? date('Y-m-d', strtotime("today")) . ' 23:59:59' : $searchParam['edate'];
		$eventid              = !empty($eventid) ? $eventid : 1;
		$list                 = Gionee_Service_WxHelp::getDaoUser()->getIncrByHI($eventid, $searchParam['sdate'], $searchParam['edate']);
		$tmp                  = array();
		foreach ($list as $val) {
			$tmp[$val['date_hi']] = intval($val['num']);
		}
		$list = Gionee_Service_WxHelp::getDaoList()->getsBy();
		$this->assign('dataList', $tmp);
		$this->assign('list', $list);
		$this->assign('searchParam', $searchParam);
	}

}