<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户中心交易订单管理类
 */
class OrderController extends Admin_BaseController {

	public $actions  = array(
		'indexURL'    => '/Admin/Order/index',
		'editURL'     => '/Admin/Order/edit',
		'editPostURL' => '/Admin/Order/editPost',
		'detailURL'   => '/Admin/Order/detail'
	);
	public $pageSize = 20;

	public function indexAction() {
		$data   = $this->getInput(array(
			'page',
			'type',
			'sdate',
			'edate',
			'order_sn',
			'username',
			'export',
			'order_type',
			'recharge_number',
		));
		$page   = max($data['page'], 1);
		$params = array();
		$sDate  = $data['sdate'];
		$eDate  = $data['edate'];
		!$sDate && $sDate = date('Y-m-d', strtotime("-30 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("+1 day"));


		$params['add_time'] = array(
			array('>=', strtotime($sDate . ' 00:00:00')),
			array('<=', strtotime($eDate . ' 23:59:59'))
		);

		if (!empty($data['order_type'])) {
			$params['order_type'] = $data['order_type'];
		}
		if (intval($data['order_sn'])) { //订单号
			$params['order_sn'] = $data['order_sn'];
		}

		if(intval($data['recharge_number'])){
			$params['recharge_number'] = $data['recharge_number'];
		}
		
		if (intval($data['username'])) { //用户手机号
			$userInfo = Gionee_Service_User::getUserByName($data['username']);
			if (!empty($userInfo)) {
				$params['uid'] = $userInfo['id'];
			}
		}
		if (in_array($data['type'], array('-1', '1', '0'))) {
			switch ($data['type']) {
				case '0': { //待处理订单
					$params['order_status'] = array('IN', array(0, 2));//待处理或处理中
					$params['pay_status']   = array('IN', array(0, 1));//待支付或已支付
					break;
				}
				case '1': {//已完成订单
					$params['order_status'] = 1;
					$params['pay_status']   = 1;
					break;
				}
				case '-1': {//已取消订单
					$params['order_status'] = -1;
					break;
				}
				default:
					break;
			}
			list($total, $dataList) = $this->_getDoneOrderInfo($page, $this->pageSize, $params, array('id' => 'DESC'));//已完成订单
		}
		if ($data['export']) {
			$this->_export('order', $sDate, $eDate, $dataList);
			exit();
		}
		$this->assign('type', $data['type']);
		$this->assign('data', $dataList);
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('order_sn', $data['order_sn']);
		$this->assign('username', $data['username']);
		$this->assign('rechargeNum', $data['recharge_number']);
		$this->assign('order_type', $data['order_type']);
		$this->assign('orderTypes', Common::getConfig('userConfig', 'virtual_type_list'));
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexURL'] . "?type={$data['type']}&" . http_build_query($params) . "&"));
	}

	//订单详情
	public function detailAction() {
		$order_id   = $this->getInput('order_id');
		$orderGoods = User_Service_OrderGoods::getsBy(array('order_id' => $order_id), array());
		$orderMsg   = User_Service_Order::get($order_id);
		$config     = Common::getConfig('userConfig');
		$actions    = User_Service_Actions::getsBy(array('order_id' => $order_id), array('add_time' => 'DESC'));
		if (intval($orderMsg['shipping_id'])) {
			$shippingMsg = User_Service_Shipping::get($orderMsg['shipping_id']);
			if (!empty($shippingMsg)) {
				$area = Gionee_Service_Area::getsBy(array(
					'id' => array(
						"IN",
						array(
							$shippingMsg['province_id'],
							$shippingMsg['city_id']
						)
					)
				));
				foreach ($area as $v) {
					if ($v['parent_id']) {
						$shippingMsg['province'] = $v['name'];
					} else {
						$shippingMsg['city'] = $v['name'];
					}
				}
			}
			$userMsg = Gionee_Service_User::getUser($orderMsg['uid']);
			$this->assign('user', $userMsg);
			$this->assign('shippingMsg', $shippingMsg);
		}
		$this->assign('actions', $actions);
		$this->assign('config', $config);
		$this->assign('order_id', $order_id);
		$this->assign('goods', $orderGoods);
		$s = Common::getConfig('userConfig', 'express_name_list');
		$this->assign('expressList', Common::getConfig('userConfig', 'express_name_list'));
		$this->assign('order', $orderMsg);
	}

	public function newAction() {
		$types = Common::getConfig('userConfig', 'innermsg_type_list');
		$this->assign('types', $types);
	}

	public function addNewAction() {
		$cardId = $this->getInput('card_id');
		if (empty($cardId)) {
			$this->output('-1', '请选择要添加的类别');
		}
		if (!empty($_FILES['data'])) {
			$file = $_FILES['data']['tmp_name'];
			$this->_generateOrder($file, $cardId);
		}
		$this->output('0', '添加成功');
	}

	private function _generateOrder($file, $cardId) {
		$cardMsg = User_Service_CardInfo::get($cardId);
		$row     = 1;
		$filds   = array('recharge_number', 'username', 'goods_id');
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$temp                    = array();
					$cardMsg                 = User_Service_CardInfo::get($cardId);
					$temp['order_type']      = $cardMsg['group_type'];
					$temp['order_amount']    = $cardMsg['card_value'];
					$temp['add_time']        = time();
					$temp['pay_time']        = time();
					$temp['pay_status']      = 1;
					$temp['shipping_status'] = 1;
					$temp['order_status']    = 0;
					$temp['user_ip']         = Util_Http::getClientIp();
					$temp['card_id']         = $cardId;
					$temp['order_sn']        = User_Service_Order::createOrderId();
					$temp['recharge_number'] = $data[0];
					if (!empty($data[1])) {
						$user        = Gionee_Service_User::getUserByName($data[1]);
						$temp['uid'] = $user['id'];
					}
					$orderId = User_Service_Order::add($temp);
					if (intval($orderId) && intval($data[2])) {
						$goods = User_Service_Commodities::get($data[2]);
						User_Service_OrderGoods::addOrderGoodsInfo($orderId, 1, $goods);
						User_Service_Order::update(array('total_cost_scores' => $goods['scores']), $orderId);
					}
				}
				$row++;
			}
		}
		fclose($handle);
	}

	//ajax得到礼品卡分类信息
	public function AjaxGetCardMsgAction() {
		$postData = $this->getInput(array('type_id', 'group_type'));
		if ($postData['type_id']) {
			$data = User_Service_CardInfo::getsBy(array('type_id' => $postData['type_id']));
		} else {
			$params = array();
			$group  = array('type_id');
			if ($postData['group_type']) {
				$params['group_type'] = $postData['group_type'];
			}
			$data = User_Service_CardInfo::getCetegory($params, $group);
		}
		$this->output('0', '', ($data));
	}

	public function  reissueAction() {
		$orderId   = $this->getInput('id');
		$orderInfo = User_Service_Order::get($orderId);

		$ofpayReq = new Vendor_Ofpay();
		$res      = $ofpayReq->req_reissue($orderInfo['order_sn']);
		//$res       = Api_Ofpay_Recharge::reissue($orderInfo['order_sn']);
		if ($res['msginfo']['retcode'] == 1) {
			$this->output('0', '成功', array('msg' => '成功'));
		} else {
			$this->output('-1', '失败', array('msg' => $res['msginfo']['err_msg']));
		}
	}

	//修改订单状态
	public function authAction() {
		$postData = $this->getInput(array(
			'pay_status',
			'order_status',
			'shipping_status',
			'express_id',
			'express_order'
		));
		$note     = $this->getInput('note');
		$orderId  = $this->getInput('order_id');
		if (!$orderId) {
			$this->output('-1', '参数有错!');
		}
		$ret   = User_Service_Order::update($postData, $orderId);
		$order = User_Service_Order::get($orderId);
		list($uid, $scores, $names) = $this->_getDataByOrderId($orderId);
		if ($order['order_type'] == 5 && in_array($postData['order_status'], array('1', '2', '-1'))) {
			if ($postData['order_status'] == '1') {
				Common_Service_User::changeUserScores($orderId, '401', '-');//扣除冻结金币
			}
			if ($postData['order_status'] == '-1') {
				$out = Common_Service_User::changeUserScores($orderId, '205');
			}
			$this->_sendMsgForEntityStatausChanged($order, $postData['order_status'], $uid);
		} else {
			if ($postData['order_status'] == '-1' && $postData['pay_status'] == '3') { //订单取消且要退还积分时
				$this->_backScoreAndSendMsg($uid, $orderId, $scores, $names, 'back_scores_tpl');
			} elseif ($postData['order_status'] == '1') {
				Common_Service_User::changeUserScores($orderId, '401', '-');//扣除冻结金币
			}
		}
		//写管理员操作日志
		$result = $this->adminOperationLog($orderId, $note);
		if ($ret) {
			$this->output('0', '操作成功');
		} else {
			$this->output("-1", '操作失败！');
		}
	}


	private function _sendMsgForEntityStatausChanged($order, $orderStatus, $uid) {
		Common_Service_User::changeUserScores($order['id'], '401', '-');
		$orderGoods     = User_Service_OrderGoods::getBy(array('order_id' => $order['id']));
		$config         = Common::getConfig('userConfig');
		$StatusList     = $config['statusFlag'];
		$shippingStatus = $config['shipping_status'];
		$expressList    = $config['express_name_list'];
		$shippingMsg    = User_Service_Shipping::get($order['shipping_id']);
		$area           = Gionee_Service_Area::getsBy(array(
			'id' => array(
				'IN',
				array(
					$shippingMsg['province_id'],
					$shippingMsg['city_id']
				)
			)
		));
		$temp           = array();
		foreach ($area as $k => $v) {
			if (empty($v['parent_id'])) {
				$temp['province_name'] = $v['name'];
			} else {
				$temp['city_name'] = $v['name'];
			}
		}
		$activityName = '';
		$goodsInfo = User_Service_Commodities::get($orderGoods['goods_id']);
		if($goodsInfo['event_flag']){
			$tpl = 'entity_activity_order_tpl';
			$config = Common::getConfig('userConfig','activity_name');
			$activityName = $config;
		}else{
			$tpl = 'entity_goods_exchange_tpl';
		}
		$params = array(
			'classify'      => 5,
			'uid'           => $uid,
			'activity_name'=>$activityName,
			'order_amount'  => $order['total_cost_scores'],
			'status'        => $orderStatus,
			'order_status'  => $orderStatus == 2 ? sprintf("%s %s", $StatusList[$orderStatus], $shippingStatus[$order['shipping_status']]) : $StatusList[$orderStatus],
			'goods_name'    => $orderGoods['goods_name'],
			'express_name'  => $expressList[$order['express_id']],
			'express_order' => $order['express_order'],
			'receiver_name' => $shippingMsg['receiver_name'],
			'address'       => sprintf("%s %s %s  ", $temp['province_name'], $temp['city_name'], $shippingMsg['address']),
			'mobile'        => $shippingMsg['mobile'],
		);
		Common_Service_User::sendInnerMsg($params,$tpl);
	}


	private function _backScoreAndSendMsg($uid, $orderId, $scores, $name, $tpl) {
		$out  = Common_Service_User::changeUserScores($orderId, '205');
		$data = array(
			'classify'    => 888,
			'uid'         => $uid,
			'cost_scores' => $scores,
			'goods_name'  => $name,
			'status'      => -1
		);
		Common_Service_User::sendInnerMsg($data, $tpl);
	}

	private function _getDataByOrderId($orderId) {
		$order = User_Service_Order::get($orderId);
		$goods = User_Service_OrderGoods::getsBy(array('order_id' => $orderId), array('id' => 'DESC'));
		$name  = '  ';
		foreach ($goods as $k => $v) {
			$name .= $v['goods_name'] . " ";
		}
		return array($order['uid'], $order['total_cost_scores'], $name);
	}

	// 写管理员操作日志
	public function adminOperationLog($orderId, $note) {
		if (!$orderId) return false;
		$order  = User_Service_Order::get($orderId);
		$params = array(
			'order_id'        => $orderId,
			'action_user'     => $this->userInfo['username'],
			'order_status'    => $order['order_status'],
			'pay_status'      => $order['pay_status'],
			'shipping_status' => $order['shipping_status'],
			'action_note'     => $note,
			'add_time'        => time()
		);
		return User_Service_Actions::add($params);
	}

	/**
	 * 获得待处理订单信息
	 */

	private function _getNeededHandleOrders($page, $pageSize, $where, $orderBy) {
		list($total, $dataList) = User_Service_Order::getList($page, $pageSize, $where, $orderBy);
		$list = $this->_handleOrderInfo($dataList);
		return array($total, $list);
	}

	/**
	 * 返回订单信息
	 */
	private function _getDoneOrderInfo($page, $pageSize, $where, $orderBy) {
		list($total, $list) = User_Service_Order::getList($page, $pageSize, $where, $orderBy);
		$dataList = $this->_handleOrderInfo($list);
		return array($total, $dataList);
	}

	/**
	 * 数据处理
	 * @return multitype:Ambigous <string, multitype:boolean NULL > unknown
	 */
	private function _handleOrderInfo($dataList = array()) {
		if (!is_array($dataList)) return false;
		$config = Common::getConfig('userConfig');
		foreach ($dataList as $k => $v) {
			$dataList[$k]['order_status']    = $v['order_status'] == '-1' ? "<span style='color:red'>" . $config['statusFlag'][$v['order_status']] . "</span>" : $config['statusFlag'][$v['order_status']];
			$dataList[$k]['pay_status']      = $config['pay_status'][$v['pay_status']];
			$dataList[$k]['shipping_status'] = $config['shipping_status'][$v['shipping_status']];
			$actionLog                       = User_Service_Actions::getBy(array('order_id' => $v['id']), array('add_time' => 'DESC'));
			if ($actionLog) {
				$dataList[$k]['last_operator'] = $actionLog['action_user'];
				$dataList[$k]['last_time']     = date('Y-m-d H:i:s', $actionLog['add_time']);
			}
			$userInfo                 = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username'] = $userInfo['real_name'] ? $userInfo['realname'] : $userInfo['username'];
			$goodsList                = User_Service_OrderGoods::getsBy(array('order_id' => $v['id']), array('id' => 'DESC'));
			$ids                      = array();
			foreach ($goodsList as $m => $n) {
				$ids[$n['goods_id']] = $n['goods_name'];
			}
			$dataList[$k]['goods_id']   = implode(',', array_unique(array_keys($ids)));
			$dataList[$k]['goods_name'] = implode(',', array_unique(array_values($ids)));
		}
		return $dataList;
	}

	private function _export($type, $sdate, $edate, $data) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $sdate . '至' . $edate . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($type) {
			case 'order': {
				fputcsv($out, array(
					'ID',
					'订单号',
					'商品ID',
					'商品名称',
					'用户手机号',
					'订单总金额',
					'实际金额',
					'消耗总积分',
					'订单状态',
					'支付状态',
					'配送状态',
					'支付时间',
					'最后更新人',
					'最后更新时间'
				));
				foreach ($data as $k => $v) {
					fputcsv($out, array(
						$v['id'],
						$v['order_sn'],
						$v['goods_id'],
						$v['goods_name'],
						$v['username'],
						$v['order_amount'],
						$v['ordercrach'],
						$v['total_cost_scores'],
						$v['order_status'],
						$v['pay_status'],
						$v['shipping_status'],
						date('Y-m-d H:i:s', $v['pay_time']),
						$v['last_operator'],
						$v['last_time']
					));
				}
			}
				break;
		}
	}
}