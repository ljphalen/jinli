<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Amigo_OrderController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/Amigo_order/index',
		'editUrl'=>'/admin/Amigo_order/edit',
		'editPostUrl'=>'/admin/Amigo_order/edit_post',
		'addUrl'=>'/admin/Amigo_order/add',
		'exportUrl'=>'/admin/Amigo_order/export',
		'addPostUrl'=>'/admin/Amigo_order/add_post',
		'syncUrl'=>'/admin/Amigo_order/sync',
			
		'editOrderAjax'=>'/admin/Amigo_order/editorder',
	);
	
	// 0.Amigo商城使用，1.积分换购使用, 2.话费充值
	public $show_type = 0;
	public $perpage = 20;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
		
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('buyer_name', 'status', 'phone', 
				'trade_no', 'goods_id', 'start_time', 'end_time',
				'order_by', 'order_type'));
		if ($page < 1) $page = 1;
		
		//排序方式
		$order_by = $params['order_by'] ? $params['order_by'] : 'create_time';
		
		$search = array();
		if ($params['buyer_name']) $search['buyer_name'] = $params['buyer_name'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['order_type']) $search['order_type'] = $params['order_type'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		$search['show_type'] = $show_type;
		
		if ($params['phone']) $search['phone'] = $params['phone'];
		
		list($total, $order) = Gou_Service_Order::search($page, $this->perpage, $search, array($order_by=>'DESC'));
		
		$this->assign('result', $order);
		$this->assign('total', $total);
		
		foreach($order as $key=>$value) {
			$order_ids[] = $value['id'];
			$goods_ids[] = $value['goods_id'];
		}
		
		$goods = Gou_Service_LocalGoods::getLocalGoodsByIds(array_unique($goods_ids));
		$goods = Common::resetKey($goods, 'id');
		$this->assign('goods', $goods);
		
		$this->assign('params', $params);
		$this->assign('show_type', $show_type);
		
		//get pager
		$url = $this->actions['indexUrl'] .'/?show_type=' . $show_type . '&' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
	}
	
	public function editAction() {
		$id = $this->getInput('id');
		$order = Gou_Service_Order::getOrder($id);
		$this->assign('order', $order);
		
		$shippingArr = array();
		if(!empty($order['express_code'])){
			$shippingArr = explode(',', html_entity_decode($order['express_code']));
		}
		$this->assign('shippingArr', $shippingArr);
		
		$address = Gou_Service_Order::getOrderAddress($order['id']);
		$address = Gou_Service_UserAddress::cookUserAddress($address);
		$this->assign('address', $address);
		
		$goods = Gou_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$this->assign('goods', $goods);
		
		$supplier = Gou_Service_Supplier::getSupplier($order['supplier']);
		$this->assign('supplier', $supplier);
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
		
		$addressSub = Gou_Service_Order::getOrderAddressSub($order['id']);
		$this->assign('addressSub', $addressSub);
		
		$shippingList = Amigo_Service_Shipping::getAll();
		$this->assign('shipping', $shippingList);
		
		//订单操作日志
		$log = Gou_Service_Order::getOrderLog($id, 1);
		$this->assign('log', $log);
		
		//退款状态
		$order_refund = Gou_Service_OrderRefund::getBy(array('trade_no'=>$order['trade_no']));
		$this->assign('order_refund', $order_refund);
	}
	
	public function editorderAction() {
		$data = $this->getPost(array('order_id', 'isaddress'));
		if (empty($data['order_id'])) $this->output(-1, '操作失败.');
		
		//修改订单
		if ($data['isaddress'] == 0){
			$editdata = $this->getPost(array('cloumn', 'value'));
			if (empty($editdata['cloumn']) || empty($editdata['value'])){
				echo 0;exit;
			}
			
			//修改订单商品数量
			$logFlag = false;
			$orderInfo = Gou_Service_Order::getOrder($data['order_id']);
			if ($editdata['cloumn'] == 'number' && $orderInfo['number'] != $editdata['value']){
				$goods = Gou_Service_LocalGoods::getLocalGoods($orderInfo['goods_id']);
				$updateData = array(
					'number'=>$editdata['value'],
					'deal_price'=>$editdata['value'] * $goods['price'],
					'real_price'=>$editdata['value'] * $goods['price'],
				);
				$ret = Gou_Service_Order::updateOrder($updateData, $data['order_id']);
				$logFlag = true;
			} else {	//修改收获人信息
				$address = Gou_Service_Order::getOrderAddressSub($data['order_id'], true);
				if ($address[$editdata['cloumn']] != $editdata['value']){
					$updateData = array($editdata['cloumn']=>$editdata['value']);
					$ret = Gou_Service_Order::updateOrderAddress($updateData, $address['id']);
					
					//修改订单表中冗余数据
					if($editdata['cloumn'] == 'mobile' || $editdata['cloumn'] == 'buyer_name'){
						$updateDataSub = $editdata['cloumn'] == 'mobile' ? array('phone'=>$editdata['value']) : $updateData;
						Gou_Service_Order::updateOrder($updateDataSub, $data['order_id']);
					}
					$logFlag = true;
				}
			}
			
			if (!$ret) {
				echo 0; exit;
			}
			
			if ($logFlag){
				$log = array(
					'order_id'=>$data['order_id'],
					'order_type'=>1,
					'uid'=>$this->userInfo['uid'],
					'create_time'=>time(),
					'update_data'=>json_encode($updateData)
				);
				Gou_Service_Order::addOrderLog($log);
			}
			
		} else {	//处理省市区数据更新
			$address = Gou_Service_Order::getOrderAddressSub($data['order_id'], true);
			$editdata = $this->getPost(array('province', 'city', 'country', 'detail_address'));
			$ret = Gou_Service_Order::updateOrderAddress($editdata, $address['id']);
			if (!$ret) {
				echo 0; exit;
			}
			
			$i = 0;
			$log = array();
			if ($address['province'] != $editdata['province']){
				$log['province'] = $this->explodeAddr($editdata['province']);
				$i++;
			}
			
			if ($address['city'] != $editdata['city']){
				$log['city'] = $this->explodeAddr($editdata['city']);
				$i++;
			}
			
			if ($address['country'] != $editdata['country']){
				$log['country'] = $this->explodeAddr($editdata['country']);
				$i++;
			}
			
			if ($address['detail_address'] != $editdata['detail_address']){
				$log['detail_address'] = $this->explodeAddr($editdata['detail_address']);
				$i++;
			}
			
			if (!empty($i)){
				$log = array(
					'order_id'=>$data['order_id'],
					'order_type'=>1,
					'uid'=>$this->userInfo['uid'],
					'create_time'=>time(),
					'update_data'=>json_encode($log)
				);
				Gou_Service_Order::addOrderLog($log);
			}
		}
		
		echo 1; exit;
	}
	
	private function explodeAddr($addr){
		$rs = explode('|', $addr);
		return $rs[0];
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$id = intval($this->getPost('id'));
		$status = intval($this->getPost('status'));
		$shippingCrop = $this->getPost('shippingCrop');
		$express_code = $this->getPost('express_code');
		$remark = $this->getPost('remark');
		$refund_reason = $this->getPost('refund_reason');
		$order = Gou_Service_Order::getOrder($id);
		if (!$order) $this->output(-1, '参数错误.');
		if($status == 4 && (empty($express_code) || empty($shippingCrop)))  $this->output(-1, '请选择快递公司和填写快递单号.');
		if ($order['status'] < 10 && $status < $order['status']) $this->output(-1, '订单状态更新错误.');
		if(in_array($order['status'], array(1,6)) && $status > 9) $this->output(-1, '订单状态更新错误.');
		
		//订单成功
		if ($status == 5) {
			$ret = Api_Gionee_Pay::codOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
			if (!$ret) $this->output(-1, '操作失败.');
		}
		
		if ($status == 6) {
			$ret = Api_Gionee_Pay::cancelOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
			if (!$ret) $this->output(-1, '操作失败.');
			
			//取消订单更新库存
			$rs = Gou_Service_LocalGoods::addStock($order['goods_id'], $order['number']);
			if (!$rs) $this->output(-1, '库存更新失败.');
		}
		
		if($express_code && (!in_array($status, array(4,5,6,7,8,9,10,11,12,13)))) $this->output(-1, '未发货前有快递单号吗？.');
		
		if(!empty($express_code)){
			$express_code = $shippingCrop . ',' . $express_code;
		}
		
		//退款
		if($status == 10 && $order['status'] != 10) {
		    if(!$refund_reason) $this->output(-1, '请填写退款原因.');
		    if($order['pay_type'] == 2) $this->output(-1, '货到付款的订单，不能申请退款');
		    list($total, ) = Gou_Service_OrderRefund::getsBy(array('trade_no'=>$order['trade_no'], 'out_refund_no'=>array('!=', '')), array('id'=>'DESC'));
		    if($total >= 9) $this->output(-1, '退款操作失败,次数超过9次');
		    $refund_ret = Gou_Service_OrderRefund::create_refund($order['trade_no'], $refund_reason);
		    if (!$refund_ret) $this->output(-1, '退款操作失败.');
		}
		
		//取消退款
		if($status == 11 && $order['status'] != 11) {
		    $refund_ret = Gou_Service_OrderRefund::cancleRefund($order['trade_no']);
		    if (!$refund_ret) $this->output(-1, '取消退款操作失败.');
		}
		
		$ret = Gou_Service_Order::updateOrder(array('status'=>$status, 'express_code'=>$express_code, 'remark'=>$remark, 'refund_reason'=>$refund_reason), $id);
		if (!$ret) $this->output(-1, '操作失败.');
		
		//记录订单操作日志
		$update_log = array();
		$i = 0;
		if($order['status'] != $status){
			$update_log['status'] = $status;
			$i++;
		}
		
		if ($order['express_code'] != $express_code){
			$update_log['express_code'] = $express_code;
			$i++;
		}
		
		if (!empty($i)){
			$log = array(
				'order_id'=>$id,
				'order_type'=>1,
				'uid'=>$this->userInfo['uid'],
				'create_time'=>time(),
				'update_data'=>json_encode($update_log)
			);
			Gou_Service_Order::addOrderLog($log);
		}
		
		//sms
		if($order['status'] != 3 && ($status == 3)) Common::sms($order['phone'], '尊敬的用户，您购买的商品将在72小时内发货。感谢您的订购，祝您生活愉快！');
		if($order['status'] != 4 && ($status == 4)) {
			$express_code = explode(',', html_entity_decode($express_code));
			if($express_code[0] && $express_code[1]) {
				Common::sms($order['phone'], '尊敬的用户，您购买的商品已由 '.$express_code[0].' 物流配送中，运单号：'.$express_code[1].'，感谢您的支持，祝您生活愉快！');
			}
		}
		
		$this->output(0,'操作成功.');
	}
	
	public function syncAction() {
		$page = 1;
		$rsync = 0;
		
		$start_time = strtotime("-1 month");
		$end_time = Common::getTime();
		do {
			list($total, $result) = Gou_Service_Order::search($page, 100, array('start_time'=>$start_time, 'end_time'=>$end_time, 'status'=>1));
			if (!$total) break;
			
			foreach($result as $key=>$value) {
				$result = Api_Gionee_Pay::getOrder(array('trade_no'=>$value['trade_no'], 'create_time'=>$value['create_time']));
				if ($result['process_status'] == 3 && $value['status'] == 1) {
					$rsync++;
					$ret = Gou_Service_Order::updateByOutTradeNo(array('status'=>2, 'pay_time'=>strtotime($result['close_time'])), $value["out_trade_no"]);
					if (!$ret) $this->output(-1, '订单同步失败.');
				}
			}
			$page++;
		} while ($total>(($page -1) * 100));
		$this->output(0, '订单同步成功,共同步'.$rsync.'个订单.');
	}
	
	/**
	 *
	 * Get order list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('username', 'status', 'out_trade_no', 
				'trade_no', 'goods_id', 'start_time', 'end_time', 'show_type',
				'order_by', 'order_type'));
		if ($page < 1) $page = 1;

		//排序方式
		$order_by = $params['order_by'] ? $params['order_by'] : 'create_time';
	
		$search = array();
		if ($params['username']) $search['username'] = $params['username'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['out_trade_no']) $search['out_trade_no'] = $params['out_trade_no'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['order_type']) $search['order_type'] = $params['order_type'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		$search['show_type'] = $params['show_type'];
		
		list($total, $order) = Gou_Service_Order::search(0, 10000, $search, array($order_by=>'DESC'));
		
		foreach($order as $key=>$value) {
			$order_ids[] = $value['id'];
			$goods_ids[] = $value['goods_id'];
			$supplier_ids[] = $value['supplier'];
		}
		$address = Gou_Service_Order::getAddressByOrderIds(array_unique($order_ids));
		$address = Common::resetKey($address, 'order_id');
		$address = Gou_Service_UserAddress::cookUserAddress($address);
		
		$goods = Gou_Service_LocalGoods::getLocalGoodsByIds(array_unique($goods_ids));
		$goods = Common::resetKey($goods, 'id');
		
		$suppliers = Gou_Service_Supplier::getSuppliersByIds(array_unique($supplier_ids));
		$suppliers = Common::resetKey($suppliers, 'id');
		
		$file_content = "";
		$file_content .= "\"订单ID\",";
		$file_content .= "\"商品名称\",";
		$file_content .= "\"供应商\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\"付款时间\",";
		$file_content .= "\"订单状态\",";
		$file_content .= "\"商品价格\",";
		$file_content .= "\"实付款\",";
		$file_content .= "\"商品数量\",";
		$file_content .= "\"付款方式\",";
		$file_content .= "\"收货人\",";
		$file_content .= "\"手机号\",";
		$file_content .= "\"地址\",";
		$file_content .= "\"邮编\",";
		$file_content .= "\"物流公司\",";
		$file_content .= "\"快递单号\",";
		$file_content .= "\r\n";
		foreach ($order as $key=>$value) {
			$order_address  = Gou_Service_UserAddress::cookUserAddress($address[$value['id']]);
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$pay_time = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : '';
			$phone = $goods[$value['goods_id']]['goods_type'] == 1 ? $value['phone'] : $address[$value['id']]['mobile'];
			$payType = $value['pay_type'] == 1 ? '在线支付' : '货到付款';
			list($shippingCrop, $express_code) = explode(',', $value['express_code']);
			
			$file_content .= "\"	" . $value['trade_no'] . "\",";
			$file_content .= "\"" . html_entity_decode($goods[$value['goods_id']]['title']) . "\",";
			$file_content .= "\"" . $suppliers[$value['supplier']]['name'] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . $pay_time . "\",";
			$file_content .= "\"" . Gou_Service_Order::orderStatus($value['status']) . "\",";
			$file_content .= "\"" . $value['deal_price'] . "\",";
			$file_content .= "\"" . $value['real_price'] . "\",";
			$file_content .= "\"" . $value['number'] . "\",";
			$file_content .= "\"" . $payType . "\",";
			$file_content .= "\"" . $address[$value['id']]['buyer_name'] . "\",";
			$file_content .= "\"" . $phone . "\",";
			$file_content .= "\"" . $order_address['adds']. $order_address['detail_address'] . "\",";
			$file_content .= "\"" . $address[$value['id']]['postcode'] . "\",";
			$file_content .= "\"" . $shippingCrop . "\",";
			$file_content .= "\"" . $express_code . "\",";
			$file_content .= "\r\n";
			
		}
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		Util_DownFile::outputFile($file_content, date('Y-m-d') . '.csv');
	}
}
