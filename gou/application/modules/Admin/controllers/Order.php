<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class OrderController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/order/index',
		'editUrl'=>'/admin/order/edit',
		'editPostUrl'=>'/admin/order/edit_post',
		'addUrl'=>'/admin/order/add',
		'exportUrl'=>'/admin/order/export',
		'addPostUrl'=>'/admin/order/add_post',
		'syncUrl'=>'/admin/order/sync'
	);
	
	// 0.Amigo商城使用，1.积分换购使用
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
		$params  = $this->getInput(array('username', 'status', 'out_trade_no', 
				'trade_no', 'goods_id', 'start_time', 'end_time',
				'order_by', 'order_type'));
		if ($page < 1) $page = 1;
		
		//排序方式
		$order_by = $params['order_by'] ? $params['order_by'] : 'pay_time';
		
		$search = array();
		if ($params['username']) $search['username'] = $params['username'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['out_trade_no']) $search['out_trade_no'] = $params['out_trade_no'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['order_type']) $search['order_type'] = $params['order_type'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		$search['show_type'] = $show_type;
		
		list($total, $order) = Gou_Service_Order::search($page, $this->perpage, $search, array($order_by=>'DESC'));

		$this->assign('result', $order);
		$this->assign('total', $total);
		
		foreach($order as $key=>$value) {
			$order_ids[] = $value['id'];
			$goods_ids[] = $value['goods_id'];
		}
		
		$address = Gou_Service_Order::getAddressByOrderIds(array_unique($order_ids));
		$address = Common::resetKey($address, 'order_id');
		$address = Gou_Service_UserAddress::cookUserAddress($address);
		$this->assign('address', $address);	
	
		$goods = Gou_Service_LocalGoods::getLocalGoodsByIds(array_unique($goods_ids));
		$goods = Common::resetKey($goods, 'id');
		$this->assign('goods', $goods);
		
		$this->assign('params', $params);
		$this->assign('show_type', $show_type);
		
		//get pager
		$url = $this->actions['indexUrl'] .'/?show_type=' . $show_type . '&' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
		$this->cookieParams();
	}
	
	public function editAction() {
		$id = $this->getInput('id');
		$order = Gou_Service_Order::getOrder($id);
		$this->assign('order', $order);
		
		$address = Gou_Service_Order::getOrderAddress($order['id']);
		$address = Gou_Service_UserAddress::cookUserAddress($address);
		$this->assign('address', $address);
		
		$goods = Gou_Service_LocalGoods::getLocalGoods($order['goods_id']);
		$this->assign('goods', $goods);
		
		$supplier = Gou_Service_Supplier::getSupplier($order['supplier']);
		$this->assign('supplier', $supplier);
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$id = intval($this->getPost('id'));
		$status = intval($this->getPost('status'));
		$express_code = $this->getPost('express_code');
		
		if($status == 4 && !$express_code)  $this->output(-1, '请填写快递名称和快递单号.');
		
		$order = Gou_Service_Order::getOrder($id);
		if (!$order) $this->output(-1, '参数错误.');
		if ($status < $order['status']) $this->output(-1, '订单状态更新错误.');
		//订单成功
		if ($status == 5) {
			$ret = Api_Gionee_Pay::codOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
			if (!$ret) $this->output(-1, '操作失败.');
		}
		if ($status == 6) {
			$ret = Api_Gionee_Pay::cancelOrder(array('trade_no'=>$order['trade_no'], 'create_time'=>$order['create_time']));
			if (!$ret) $this->output(-1, '操作失败.');
		}
		
		if($express_code && (!in_array($status, array(4,5,6)))) $this->output(-1, '未发货前有快递单号吗？.');
		
		$ret = Gou_Service_Order::updateOrder(array('status'=>$status, 'express_code'=>$express_code), $id);
		if (!$ret) $this->output(-1, '操作失败.');
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
		$order_by = $params['order_by'] ? $params['order_by'] : 'pay_time';
	
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
		
		list($total, $order) = Gou_Service_Order::search(0, 1000, $search, array($order_by=>'DESC'));
		
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
		$file_content .= "\"用户名\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\"付款时间\",";
		$file_content .= "\"商品价格\",";
		$file_content .= "\"实付款\",";
		$file_content .= "\"收货人\",";
		$file_content .= "\"手机号\",";
		$file_content .= "\"地址\",";
		$file_content .= "\"邮编\",";
		$file_content .= "\r\n";
		foreach ($order as $key=>$value) {
			$order_address  = Gou_Service_UserAddress::cookUserAddress($address[$value['id']]);
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$pay_time = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : '';
			$phone = $goods[$value['goods_id']]['goods_type'] == 1 ? $value['phone'] : $address[$value['id']]['mobile'];
			
			$file_content .= "\"	" . $value['trade_no'] . "\",";
			$file_content .= "\"" . html_entity_decode($goods[$value['goods_id']]['title']) . "\",";
			$file_content .= "\"" . $suppliers[$value['supplier']]['name'] . "\",";
			$file_content .= "\"" . $value['username'] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . $pay_time . "\",";
			$file_content .= "\"" . $value['deal_price'] . "\",";
			$file_content .= "\"" . $value['real_price'] . "\",";
			$file_content .= "\"" . $address[$value['id']]['buyer_name'] . "\",";
			$file_content .= "\"" . $phone . "\",";
			$file_content .= "\"" . $order_address['adds']. $order_address['detail_address'] . "\",";
			$file_content .= "\"" . $address[$value['id']]['postcode'] . "\",";
			$file_content .= "\r\n";
			
		}
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		Util_DownFile::outputFile($file_content, date('Y-m-d') . '.csv');
	}
}
