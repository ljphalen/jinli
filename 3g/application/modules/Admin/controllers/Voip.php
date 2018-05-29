<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 免费电话管理
 */
class VoipController extends Admin_BaseController {

	public $actions = array(
		'configUrl'    => '/Admin/VoIP/config',
		'indexUrl'     => '/Admin/VoIP/index',
		'addUrl'       => '/Admin/VoIP/add',
		'addPostUrl'   => '/Admin/VoIP/addPost',
		'eddUrl'       => '/Admin/VoIP/edit',
		'editPostUrl'  => '/Admin/VoIP/editPost',
		'userUrl'      => '/Admin/VoIP/user',
		'deleteUrl'    => '/Admin/VoIP/delete',
		'logUrl'       => '/Admin/VoIP/log',
		'deleteLogUrl' => '/Admin/VoIP/deleteLog',
		'service'      => '/Admin/VoIP/service',
		'serviceList'  => '/Admin/VoIP/serviceList',
		'servicePost'  => '/Admin/VoIP/servicePost',
		'serviceEdit'  => '/Admin/VoIP/serviceEdit',
		'tipsListUrl'  => '/Admin/VoIP/tips',
		'phoneVestUrl' => '/Admin/VoIP/phoneVest',
	);

	public $pageSize = 20;

	public function  indexAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = Gionee_Service_VoIP::getListByPage($page, $this->pageSize, array(), array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$used                    = Gionee_Service_VoIPUser::getCount(array('pid' => $v['id']));
			$dataList[$k]['remined'] = $v['number'] - $used;
		}
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "/?"));
	}

	public function configAction() {
		$data                   = $this->getPost(array(
			'accountSid',
			'accountToken',
			'appId',
			'month_call_time',
			'show_number',
			'login_per_time'
		));
		$data['login_per_time'] = $_POST['login_per_time'];
		if (!empty($data['accountSid'])) {
			Gionee_Service_Config::setValue('voip_config', json_encode($data));
			$this->output(0, '添加成功！');
		}
		$data = Gionee_Service_Config::getValue('voip_config');

		$tel  = new Vendor_Tel();
		$info = $tel->getAccountInfo();
		$this->assign('data', json_decode($data, true));
		$this->assign('info', $info);

	}

	public function addAction() {
	}

	public function addPostAction() {
		$dataList             = $this->getInput(array(
			'start_time',
			'end_time',
			'valid_time',
			'number',
			'status',
			'sort'
		));
		$dataList['add_time'] = time();
		$insert               = Gionee_Service_VoIP::insert($dataList);
		if ($insert) {
			$this->output(0, '添加成功！');
		} else {
			$this->output(-1, '添加失败！');
		}
	}

	public function editAction() {
		$id      = $this->getInput('id');
		$message = Gionee_Service_VoIP::get($id);
		$this->assign('msg', $message);
		$this->assign('id', $id);
	}

	public function editPostAction() {
		$data = $this->getInput(array('id', 'start_time', 'end_time', 'valid_time', 'number', 'status', 'sort'));
		$edit = Gionee_Service_VoIP::update($data['id'], $data);
		if ($edit) {
			$this->output(0, '编辑成功！');
		} else {
			$this->output('-1', '编辑失败！');
		}
	}

	public function deleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_VoIP::delete($id);
		if ($res) $this->output(0, '操作成功'); else $this->output('-1', '操作失败');
	}

	public function userAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = Gionee_Service_VoIPUser::getDataList($page, 20, array(), array('id' => 'DESC'));
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, 20, $this->actions['userUrl'] . "/?"));
	}

	public function export($name, $list) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $name . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));

		foreach ($list as $v) {
			fputcsv($out, $v);
		}
		fclose($out);
	}

	public function logAction() {
		$data  = $this->getPost(array('sdate', 'edate', 'export'));
		$page  = $this->getInput('page');
		$sDate = $data['sdate'];
		$eDate = $data['edate'];

		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));

		$where['called_time'] = array(
			array('>=', strtotime($sDate . ' 00:00:00')),
			array('<=', strtotime($eDate . ' 23:59:59'))
		);
		$export               = $data['export'];

		//exit;
		if ($export == 1) {
			$tmpList = array(array('呼出手机号', '被呼叫号码', '呼叫时间', '是否接通', '通话时长', '归属地'));
			$list    = Gionee_Service_VoIPLog::getsBy($where, array('id' => 'DESC'));
			foreach ($list as $row) {
				$tmpList[] = array(
					$row['caller_phone'],
					$row['called_phone'],
					date('Y-m-d H:i:s', $row['called_time']),
					$row['connected_time'] ? '是' : '否',
					$row['duration'],
					$row['area']
				);
			}
			$this->export("用户拨打记录{$sDate}_{$eDate}_" . date('s'), $tmpList);
			exit;
		}

		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		list($total, $dataList) = Gionee_Service_VoIPLog::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['logUrl'] . "/?"));
	}

	public function deleteLogAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_VoIPLog::delete($id);
		if ($res) {
			$this->output('0', 'Success');
		} else {
			$this->output('-1', 'Failed');
		}

	}

	public function serviceAction() {

	}

	public function servicePostAction() {
		$postData = $this->getInput(array('name', 'sort', 'contact', 'status'));
		if (!$postData['name'] || !$postData['contact']) {
			$this->output('-1', '数据不能为空');
		}
		$dataList = array();
		$data     = json_encode(array(
			'name'    => $postData['name'],
			'contact' => $postData['contact'],
			'sort'    => $postData['sort'],
			'status'  => $postData['status']
		));
		$services = Gionee_Service_Config::getValue('yx_customer_service');
		if ($services) {
			$services = json_decode($services, true);
			array_unshift($services, $data);
			$dataList = $services;
		} else {
			$dataList[] = $data;
		}
		$res = Gionee_Service_Config::setValue('yx_customer_service', json_encode($dataList));
		if ($res) {
			$this->output(0, '添加成功！');
		} else {
			$this->output('-1', '添加失败');
		}
	}

	public function serviceListAction() {
		$dataList = Gionee_Service_Config::getValue('yx_customer_service');
		$dataList = json_decode($dataList, true);
		$arr      = array();
		foreach ($dataList as $k => $v) {
			$arr[] = json_decode($v, true);
		}
		$this->assign('list', $arr);
	}

	public function serviceEditAction() {
		$postData = $this->getInput(array('service'));
		$temp     = array();
		foreach ($postData['service'] as $k => $v) {
			array_push($temp, json_encode($v));
		}
		$res = Gionee_Service_Config::setValue('yx_customer_service', json_encode($temp));
		if ($res) {
			$this->output('0', "编辑成功");
		} else {
			$this->output('-1', '编辑失败');
		}
	}


	//文字广告内容
	public function tipsAction() {
		$content     = Gionee_Service_Config::getValue('3g_voip_tips');
		$voip_ads    = Gionee_Service_Config::getValue('3g_voip_ads');
		$user_notice_title =  Gionee_Service_Config::getValue('user_notice_title');
		$show_notice = Gionee_Service_Config::getValue('user_show_notice');
		$call_notice = Gionee_Service_Config::getValue('3g_call_notice');
		$this->assign('content', $content);
		$this->assign('voip_ads', $voip_ads);
		$this->assign('call_notice', $call_notice);
		$this->assign('show_notice', $show_notice);
		$this->assign('user_notice_title', $user_notice_title);
	}

	public function editTipsAction() {
		$tips        = trim($_POST['3g_voip_tips']);
		$voip_ads    = $this->getInput('3g_voip_ads');
		$call_notice = trim($_POST['3g_call_notice']);
		$show_notice = $this->getInput('user_show_notice');
		$user_notice_title = $this->getInput('user_notice_title');
		$res         = Gionee_Service_Config::setValue('3g_voip_tips', $tips);
		$res2        = Gionee_Service_Config::setValue('3g_voip_ads', $voip_ads);
		$ret = Gionee_Service_Config::setValue('3g_call_notice', $call_notice);
		Gionee_Service_Config::setValue('user_show_notice', $show_notice);
		Gionee_Service_Config::setValue('user_notice_title', $user_notice_title);
		Gionee_Service_Config::setValue('3g_voip_tips_time', Common::getTime());

		if ($res && $res2) $this->output('0', '操作成功!'); else $this->output('-1', '操作失败');
	}


	public function billListAction() {
		$tel  = new Vendor_Tel();
		$date = $this->getInput('date');
		$list = $tel->getBillList($date);
		echo json_encode($list);
		exit;
	}

	public function phoneVestAction() {
		$page    = $this->getInput('page');
		$conf    = Gionee_Service_Config::getValue('voip_config');
		$tmpList = Gionee_Service_User::phonevest($page, $this->pageSize);
		foreach ($tmpList as $val) {
			$ul = Gionee_Service_User::getsBy(array('imei_id' => $val['imei_id']));
			foreach ($ul as $v) {
				$callTime     = Gionee_Service_VoIPLog::getTotalTimeByMonth($v['mobile']);
				$userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($v['mobile']);
				$diff         = $userVoipInfo['m_sys_sec'] + $userVoipInfo['exchange_sec'];
				$totalTime    = Gionee_Service_VoIPLog::getTotalTimeByMonth($v['mobile'], -1);

				$dataList[$v['id']] = array(
					'id'            => $v['id'],
					'username'      => $v['username'],
					'mobile'        => $v['mobile'],
					'register_time' => date('d/m/y H:i:s', $v['register_time']),
					'imei_id'       => $v['imei_id'],
					'left_time'     => $diff,
					'call_time'     => $callTime,
					'total_time'    => $totalTime,
				);
			}
		}

		$total = count($dataList) + $this->pageSize;

		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['phoneVestUrl'] . "/?"));

	}
}