<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 合作商类
 */
class ParterController extends Admin_BaseController {
	public $pageSize = 20;

	public $actions = array(
		'accountUrl'           => '/Admin/Parter/account',
		'accountEditUrl'       => '/Admin/Parter/accountEdit',
		'accountEditPostUrl'   => '/Admin/Parter/accountEditPost',
		'deAccountUrl'         => '/Admin/Parter/accountDel',
		'qualificationUrl'     => '/Admin/Parter/qualification',
		'editQualificationUrl' => '/Admin/Parter/editQualification',
		'postQualificationUrl' => '/Admin/Parter/postQualification',
		'uploadUrl'            => '/Admin/Parter/upload',
		'uploadPostUrl'        => '/Admin/Parter/upload_post',
		'delQualifitionUrl'    => '/Admin/Parter/delQualifition',
		'bussinessUrl'         => '/Admin/Parter/business',
		'editBussinessUrl'     => '/Admin/Parter/editBussiness',
		'postBusinessUrl'      => '/Admin/Parter/postBussiness',
		'delBusinessUrl'       => '/Admin/Parter/delBusiness',
		'linkListUrl'          => '/Admin/Parter/linkList',
		'editLinkUrl'          => '/Admin/Parter/editLink',
		'postLinkUrl'          => '/Admin/Parter/postLink',
		'delLinkUrl'           => '/Admin/Parter/delLink',
		'clicksUrl'            => '/Admin/Parter/clicks',
		'dayUrl'               => '/Admin/Parter/day',
		'monthUrl'             => '/Admin/Parter/month',
	);


	public $pageTypes = array(
		'0' => '全部',
		'1' => "导航",
		'2' => '网址大全',
		'3' => '新闻页',
		'4' => '内置书签页',
	);

	public $priceTypes = array(
		'1' => '按PV ',
		'2' => '按UV',
		'3' => '按月',
		'4' => '按年'
	);

	public $checkStatus = array(
		'0' => '待审核',
		'1' => '已审核',
	);

	public $payStatus = array(
		'0' => '待收款',
		'1' => '确认收款',
		'2' => '已收款'
	);

	public $confirmStatus = array(
		'0' => '待CP确认',
		'1' => 'CP已确认',
	);

	public $modelTypes = array(
		'1' => 'CPC',
		'2' => 'CPA',
		'3' => 'CPS',
		'4' => 'CPT'
	);

	//账号管理
	public function accountAction() {
		$postData = $this->getInput(array('page', 'pid'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (intval($postData['pid'])) {
			$where['id'] = $postData['pid'];
		}
		list($total, $dataList) = Gionee_Service_Parter::getList($page, $this->pageSize, $where, array(
			'status'       => 'DESC',
			'created_time' => 'DESC'
		));
		$this->assign('list', $dataList);
		$parters = Gionee_Service_Parter::getsBy(array(), array('id' => 'DESC'));
		$this->assign('parters', $parters);
		$this->assign('pid', $postData['pid']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['content'] . "?" . http_build_query($postData) . "&"));
	}

	public function  accountEditAction() {
		$id = $this->getInput('id');
		if (!empty($id)) {
			$data = Gionee_Service_Parter::get($id);
			$this->assign('v', $data);
		}
	}

	public function accountEditPostAction() {
		$params = $this->getInput(array('id', 'name', 'account', 'password', 'status'));
		if (intval($params['id'])) {
			$params['edit_time'] = time();
			$data                = Gionee_Service_Parter::get($params['id']);
			if ($data['password'] != md5(trim($params['password']))) {
				$params['password'] = md5($params['password']);
			}
			$res = Gionee_Service_Parter::update($params, $params['id']);
			if ($res) {
				Gionee_Service_Business::editBy(array('status' => 0), array(
					'parter_id' => $params['id'],
					'status'    => 1
				));
				Gionee_Service_ParterUrl::editBy(array('status' => 0), array('pid' => $params['id'], 'status' => 1));
			}

		} else {
			$info = Gionee_Service_Parter::getsBy(array('account' => $params['account']));
			if (!empty($info)) {
				$this->output('-1', '该用户名已经存在！');
			}
			$params['created_time'] = time();
			$params['password']     = md5($params['password']);
			$res                    = Gionee_Service_Parter::add($params);
		}
		$this->output('0', '操作成功');
	}

	public function accountDelAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			Gionee_Service_Parter::delete($id);
			$this->output('0', '操作成功');
		}
	}

	//资质管理
	public function qualificationAction() {
		$page = $this->getInput('page');
		$page = max($page, 1);
		list($total, $dataList) = Gionee_Service_Qualification::getList($page, $this->pageSize, array(), array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$parnter                     = Gionee_Service_Parter::get($v['parter_id']);
			$dataList[$k]['parter_name'] = $parnter['name'];
		}
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['qualificationUrl'] . "?"));
	}

	public function editQualificationAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$data = Gionee_Service_Qualification::get($id);
			$this->assign('data', $data);
		}
		$partners = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('parters', $partners);
	}

	public function postQualificationAction() {
		$postData = $this->getInput(array(
			'parter_id',
			'company_name',
			'bank_name',
			'bank_number',
			'tax_number',
			'company_address',
			'company_tel',
			'bill_type',
			'bill_content',
			'receiver_name',
			'receiver_tel',
			'receiver_address',
			'tax_image',
			'email',
			'id'
		));
		if ($postData['id']) {
			$postData['edit_time'] = time();
			$postData['edit_name'] = $this->userInfo['username'];
			$res                   = Gionee_Service_Qualification::edit($postData, $postData['id']);
		} else {
			$postData['created_time'] = time();
			$postData['created_name'] = $this->userInfo['username'];
			$res                      = Gionee_Service_Qualification::add($postData);
		}

		if ($res) {
			$this->output('0', '操作成功！');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	public function delQualifitionAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_Qualification::delete($id);
		if ($res) {
			$this->output('0', '成功！');
		} else {
			$this->output('-1', '失败！');
		}
	}


	//业务管理
	public function businessAction() {
		$postData = $this->getInput(array('page', 'pid'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (intval($postData['pid'])) {
			$where['parter_id'] = $postData['pid'];
		}
		$page = max($page, 1);
		list($total, $data) = Gionee_Service_Business::getList($page, $this->pageSize, $where, array(
			'status'       => 'DESC',
			'parter_id'    => 'DESC',
			'created_time' => 'DESC'
		));
		foreach ($data as $k => $v) {
			$parter                  = Gionee_Service_Parter::get($v['parter_id']);
			$data[$k]['parter_name'] = $parter['name'];
		}
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('parters', $parters);
		$this->assign('params', $postData);
		$this->assign('priceTypes', $this->priceTypes);
		$this->assign('dataList', $data);
		$this->assign('modelTypes', $this->modelTypes);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['bussinessUrl'] . "?pid=" . $postData['pid'] . "&"));
	}

	public function editBussinessAction() {
		$id   = $this->getInput('id');
		$data = array();
		if (intval($id)) {
			$data = Gionee_Service_Business::get($id);
		} else {
			$data['start_time'] = strtotime('now');
			$data['end_time']   = strtotime('+1 year');

		}
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1));
		$this->assign('parters', $parters);
		$this->assign('priceTypes', $this->priceTypes);
		$this->assign('data', $data);
		$this->assign('modelTypes', $this->modelTypes);
	}

	public function postBussinessAction() {
		$postData = $this->getInput(array(
			'parter_id',
			'name',
			'model',
			'price',
			'status',
			'id',
			'sdate',
			'edate',
			'price_type'
		));
		if (empty($postData['parter_id']) || empty($postData['name']) || empty($postData['model'])) {
			$this->output('-1', '参数不能为空！');
		}
		!$postData['sdate'] && $sDate = date('Y-m-d', strtotime("now"));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime("+1 year"));
		if (intval($postData['id'])) { //编辑
			if (!$postData['status']) { //如果是关闭该业务
				$postData['closed_time'] = time();
			}
			$res = Gionee_Service_Business::edit($postData, $postData['id']);
		} else {
			$postData['created_time'] = strtotime('now');
			$res                      = Gionee_Service_Business::add($postData);
		}
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	public function delBusinessAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_Business::delete($id);
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	//业务链接
	public function linkListAction() {
		$params = array();
		$bid    = $this->getInput('bid');
		if (intval($bid)) {
			$params['bid'] = $bid;
		}
		$page = $this->getInput('id');
		$page = max(1, $page);
		list($total, $dataList) = Gionee_Service_ParterUrl::getList($page, $this->pageSize, $params, array('id' => "DESC"));
		foreach ($dataList as $k => $v) {
			$business               = Gionee_Service_Business::get($v['bid']);
			$dataList[$k]['b_name'] = $business['name'];
			$parter                 = Gionee_Service_Parter::get($v['pid']);
			$dataList[$k]['p_name'] = $parter['name'];
		}
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['linkListUrl'] . "{?bid=$bid&}"));
	}

	public function editLinkAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$data = Gionee_Service_ParterUrl::get($id);
			$this->assign('data', $data);
			$business = Gionee_Service_Business::getsBy(array('parter_id' => $data['pid']));
			$this->assign('business', $business);
		}
		$parters = Gionee_Service_Parter::getsBy(array("status" => '1'), array('id' => "DESC"));
		$this->assign('parters', $parters);
	}

	public function postLinkAction() {
		$postData = $this->getInput(array('id', 'pid', 'bid', 'url', 'url_name', 'status'));
		if (empty($postData['pid']) || empty($postData['bid'])) {
			$this->output('-1', '请选择合作商或业务！');
		}
		if (intval($postData['id'])) {
			$postData['edit_time'] = time();
			$res                   = Gionee_Service_ParterUrl::edit($postData, $postData['id']);
		} else {
			$postData['created_time'] = time();
			$res                      = Gionee_Service_ParterUrl::add($postData);
		}
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	public function ajaxGetBussinessListAction() {
		$pid  = $this->getInput('pid');
		$data = Gionee_Service_Business::getsBy(array('parter_id' => $pid), array('id' => 'DESC'));
		$this->output('0', '', $data);
	}

	public function ajaxGetUrlByBidAction() {
		$bid  = $this->getInput('bid');
		$data = Gionee_Service_ParterUrl::getsBy(array('bid' => $bid));
		$this->output('0', '', $data);
	}

	public function delLinkAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_ParterUrl::delete($id);
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}


	/**
	 * 业务查询
	 */
	public function clicksAction() {
		$postData = $this->getInput(array('sdate', 'edate', 'page', 'pid', 'bid', 'pageType', 'keywords', 'export'));
		list($postData['sdate'], $postData['edate'], $page, $where) = $this->_initlizeParams($postData['sdate'], $postData['edate'], $postData['page'], $postData['pid'], $postData['bid'], $postData['keywords']);
		list($total, $urlIds) = Gionee_Service_ParterUrl::getUrlIdList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$data = array();
		if (!empty($urlIds)) {
			$data = Gionee_Service_Business::getBussinessDetailInfo($urlIds, $postData['sdate'], $postData['edate'], $postData['pageType']);
		}
		if ($postData['export']) {
			$this->_export('click', $data, $postData['sdate'], $postData['edate'], 'CP日点击报表');
			exit();
		}
		$dateList = array();
		for ($i = strtotime($postData['sdate']); $i <= strtotime($postData['edate']); $i += 86400) {
			$dateList[] = date('Y-m-d', $i);
		}
		if ($postData['pid']) {
			$businessList = Gionee_Service_Business::getsBy(array('parter_id' => $postData['pid']), array('id' => 'DESC'));
			$this->assign('businessList', $businessList);
		}
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('pageTypes', $this->pageTypes);
		$this->assign('parters', $parters);
		$this->assign('params', $postData);
		$this->assign('date', $dateList);
		$this->assign('data', $data);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['clicksUrl'] . "?" . http_build_query($postData) . "&"));
	}


	/**
	 *日报表
	 */
	public function dayAction() {
		$postData = $this->getInput(array('page', 'sdate', 'edate', 'pid', 'bid', 'keywords', 'export', 'pageType'));
		list($postData['sdate'], $postData['edate'], $page, $where) = $this->_initlizeParams($postData['sdate'], $postData['edate'], $postData['page'], $postData['pid'], $postData['bid'], $postData['keywords']);
		list($total, $urlIds) = Gionee_Service_ParterUrl::getUrlIdList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$data = array();
		if (!empty($urlIds)) {
			$data = Gionee_Service_Business::getBussinessDetailInfo($urlIds, $postData['sdate'], $postData['edate'], $postData['pageType']);
		}
		foreach ($data as $m => $n) {
			foreach ($n['clicks'] as $s => $t) {
				$days = 1;
				if ($n['price_type'] == 3) {//按月结算时,得到当月最后一天
					$temp = explode($s, '-');
					$days = Common::getMonthLastDay($temp[0], $temp[1]);
				}
				//如果已关闭或过期，就不计算收入
				if ((empty($n['status']) && date('Y-m-d', $n['closed_time']) < $s) || !(date('Y-m-d', $n['start_time']) <= $s && $s <= date('Y-m-d', $n['end_time']))) {
					$data[$m]['clicks'][$s] = 0;
				} else {
					$data[$m]['clicks'][$s] = Gionee_Service_Receipt::getIncomeByType($n['price_type'], $t, $n['price'], $days);
				}
			}
		}
		if ($postData['export']) {
			$this->_export('day', $data, $postData['sdate'], $postData['edate'], 'CP日收入报表');
			exit();
		}
		for ($i = strtotime($postData['sdate']); $i <= strtotime($postData['edate']); $i += 86400) {
			$dateList[] = date('Y-m-d', $i);
		}
		if ($postData['pid']) {
			$businessList = Gionee_Service_Business::getsBy(array('parter_id' => $postData['pid']), array('id' => 'DESC'));
			$this->assign('list', $businessList);
		}
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('parters', $parters);
		$this->assign('date', $dateList);
		$this->assign('data', $data);
		$this->assign('priceTypes', $this->priceTypes);
		$this->assign('params', $postData);
		$this->assign('pageTypes', $this->pageTypes);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['dayUrl'] . "?" . http_build_query($postData) . "&"));
	}

	/**
	 * 月对账表
	 */

	public function monthAction() {
		$postData = $this->getInput(array('page', 'year', 'month', 'export', 'status', 'pid', 'bid', 'receipt_status'));
		!$postData['year'] && $postData['year'] = date('Y', strtotime('now'));
		!$postData['month'] && $postData['month'] = date('m', time());
		$date  = $postData['year'] . '-' . $postData['month'];
		$page  = max(1, $postData['page']);
		$where = array();
		if (intval($postData['pid'])) {
			$where['pid'] = $postData['pid'];
		}
		if (intval($postData['bid'])) {
			$where['bid'] = $postData['bid'];
		}
		if (isset($postData['receipt_status']) && $postData['receipt_status'] >= 0) {
			$where['receipt_status'] = $postData['receipt_status'];
		}
		list($total, $dataList) = Gionee_Service_Receipt::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($dataList as $key => $val) {
			$ywInfo                         = Gionee_Service_Business::get($val['bid']);
			$dataList[$key]['businessName'] = $ywInfo['name'];
			$parter                         = Gionee_Service_Parter::get($val['pid']);
			$dataList[$key]['parterName']   = $parter['name'];
		}
		if ($postData['export']) {
			$this->_export('month', $dataList, $date, $date, 'CP月对账报表');
			exit();
		}
		$this->assign('data', $dataList);
		unset($postData['page']);
		unset($postData['export']);
		$queryParams = http_build_query($postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['monthUrl'] . "?" . http_build_query($postData) . "&"));
		$this->assign('payStatus', $this->payStatus);
		$this->assign('checkStatus', $this->checkStatus);
		$this->assign('confirmStatus', $this->confirmStatus);
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('parters', $parters);
		$this->assign('params', $postData);
		if ($postData['pid']) {
			$bussiness = Gionee_Service_Business::getsBy(array('parter_id' => $postData['pid']));
			$this->assign('bussiness', $bussiness);
		}
	}

	/**
	 * 信息审对
	 * @return boolean
	 */
	public function authAction() {
		$id = $this->getInput('id');
		if (!intval($id)) return false;
		$data      = Gionee_Service_Receipt::get($id);
		$bussiness = Gionee_Service_Business::get($data['bid']);
		$parter    = Gionee_Service_Parter::get($data['pid']);
		$this->assign('busInfo', $bussiness);
		$this->assign('parter', $parter);
		$this->assign('receiveStatus', $this->payStatus);
		$this->assign('authStatus', $this->checkStatus);
		$this->assign('priceTypes', $this->priceTypes);
		$this->assign('confirmStatus', $this->confirmStatus);
		$this->assign('data', $data);
	}

	/**
	 * 审核提交
	 */
	public function authPostAction() {
		$postData = $this->getInput(array("real_money", "reason", "mid"));
		if (!is_numeric($postData['real_money'])) $this->output('-1', '金额格式有错');
		$postData['edit_time'] = time();
		$postData['edit_user'] = $this->userInfo['id'];
		$info                  = Gionee_Service_Receipt::get($postData['mid']);
		if ($info['real_money'] != $postData['real_money']) {
			$postData['check_status']   = 0;
			$postData['confirm_status'] = 0;
			$postData['receipt_status'] = 0;
		}
		$res = Gionee_Service_Receipt::edit($postData, $postData['mid']);
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	/**
	 * 审核状态
	 */
	public function ajaxPassCheckAction() {
		$id = $this->getInput('id');
		if (!intval($id)) $this->output('-1', '参数有错!');
		$ret = Gionee_Service_Receipt::edit(array('check_status' => 1), $id);
		if ($ret) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	/**
	 * 收款状态更改
	 *
	 */
	public function ajaxChangePayStatusAction() {
		$id = $this->getInput('id');
		if (!intval($id)) $this->output('-1', '参数有错!');
		$ret = Gionee_Service_Receipt::edit(array('receipt_status' => 1), $id);
		if ($ret) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	/**
	 *
	 */
	private function _initlizeParams($sdate, $edate, $page, $pid, $bid, $keywords, $pageType = 0) {
		!$sdate && $sdate = date('Y-m-d', strtotime('-8 day'));
		!$edate && $edate = date('Y-m-d', strtotime('+1 day'));
		$page     = max(1, $page);
		$dateList = $res = $temp = $where = array();
		if (!empty($keywords)) {
			$where['name'] = array("LIKE", $keywords);
		}
		if ($pid) {
			$where['parter_id'] = $pid;
		}
		if ($bid) {
			$where['id'] = $bid;
		}

		return array($sdate, $edate, $page, $where);
	}

	private function _export($type, $data, $sdate, $edate, $filename = '报表') {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GBK');
		$filename .= $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'GBK', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($type) {

			case 'click': {
				$dateList = array();
				for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
					$dateList[] = date('Y-m-d', $i);
				}
				fputcsv($out, array_merge(array('业务名称', '所属CP', '所属页面'), $dateList));
				foreach ($data as $k => $v) {
					$temp = array($v['name'], $v['parter_name'], '导航');
					foreach ($dateList as $m) {
						array_push($temp, $v['clicks'][$m] ? $v['clicks'][$m] : 0);
					}
					fputcsv($out, $temp);
				}
				break;
			}

			case 'day': {
				$dateList = array();
				for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
					$dateList[] = date('Y-m-d', $i);
				}
				$attri = array_merge(array('业务名称', '所属CP', '所属页面', '计价方式', '单价'), $dateList);
				fputcsv($out, $attri);
				foreach ($data as $k => $v) {
					$temp = array(
						$v['name'],
						$v['parter_name'],
						'导航',
						$this->priceTypes[$v['price_type']],
						$v['price']
					);
					foreach ($dateList as $m) {
						array_push($temp, $v['clicks'][$m] ? $v['clicks'][$m] : 0);
					}
					fputcsv($out, $temp);
				}
				break;
			}
			case 'month': {
				fputcsv($out, array(
					'序列号',
					'月份',
					'业务名称',
					'合作商',
					'PV值',
					'结算方式',
					'后台结算金额',
					'实际金额',
					'修改原因',
					'审核状态',
					'收款状态'
				));
				foreach ($data as $k => $v) {
					fputcsv(($out), array(
						$v['id'],
						$v['date'],
						$v['businessName'],
						$v['parterName'],
						$v['pv'],
						$v['price_type'] == 1 ? ($v['price'] . "*PV") : $this->priceTypes[$v['price_type']],
						$v['money'],
						$v['real_money'],
						$v['reason'],
						$this->checkStatus[$v['check_status']],
						$this->payStatus[$v['receipt_status']]
					));
				}
				break;
			}
		}
	}

	//上传图片
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = Common::upload('img', 'qualification');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}