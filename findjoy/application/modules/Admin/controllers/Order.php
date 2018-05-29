<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class OrderController
 * @author milo
 */
class OrderController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Order/index',
		'addUrl' => '/Admin/Order/add',
		'detailUrl' => '/Admin/Order/detail',
		'addPostUrl' => '/Admin/Order/add_post',
		'editUrl' => '/Admin/Order/edit',
		'editPostUrl' => '/Admin/Order/edit_post',
		'deleteUrl' => '/Admin/Order/delete',
		'exportUrl' => '/Admin/Order/export',
	);
	
	public $show_type = 0;
	public $perpage = 20;
	

	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$param = $this->getInput(array('out_trade_no', 'trade_no', 'status', 'get_token', 'buyer_name', 'phone'));
		if ($page < 1) $page = 1;
		//下单时间或者提货日期
		$timer = $this->getInput(array('time_type', 'to_time', 'from_time'));
		$condition = array();
		$timeColumn = $timer['time_type'] == 1 ? 'create_time' : 'get_date';
		//下单时间需要转换时间戳
		if ($timer['time_type'] == 1) {
			if ($timer['from_time']) $condition[$timeColumn] = array('>=', strtotime($timer['from_time']));
			if ($timer['to_time']) $condition[$timeColumn] = array('<=', strtotime($timer['to_time']));
			if ($timer['from_time'] && $timer['to_time']) {
				$condition[$timeColumn] = array(
					array('>=', strtotime($timer['from_time'])),
					array('<=', strtotime($timer['to_time']))
				);
			}
		//提货日期使用date格式
		}elseif($timer['time_type'] == 2){
			if ($timer['from_time']) $condition[$timeColumn] = array('>=', $timer['from_time']);
			if ($timer['to_time']) $condition[$timeColumn] = array('<=', $timer['to_time']);
			if ($timer['from_time'] && $timer['to_time']) {
				$condition[$timeColumn] = array(
					array('>=', $timer['from_time']),
					array('<=', $timer['to_time'])
				);
			}
		}
		//合并时间条件和其他条件
		$condition = array_merge(array_filter($param),$condition);

		list($total, $order) = Fj_Service_Order::getList($page, $this->perpage, $condition, array('id'=>'DESC'));
		$this->assign('data', $order);
		$this->assign('total', $total);
		$this->assign('search', array_merge($param,$timer));
		$this->assign('order_status', Fj_Service_Order::orderStatus());
		
		//get time
		$time = Fj_Service_Order::getTime();
		$this->assign('get_time', $time);

		//get pager
		$url = $this->actions['listUrl'] . '/?' . http_build_query(array_merge($param,$timer)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	


	public function detailAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Order::get(intval($id));
		$address = Fj_Service_Address::get($info['address_id']);

		//detail 
		$details = Fj_Service_Order::getsDetailByTradeNo($info['trade_no']);
		
		$goods_ids = array_keys(Common::resetKey($details, 'goods_id'));
		if(!empty($goods_ids))
		list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
		$goods = Common::resetKey($goods, 'id');
		
		$this->assign('order', $info);
		$this->assign('address', $address);
		$this->assign('details', $details);
		$this->assign('goods', $goods);
		$this->assign('orderStatus', Fj_Service_Order::orderStatus());
	}



	/**
	 *
	 * Get order list
	 */
	public function exportAction() {
		$param = $this->getInput(array('out_trade_no', 'trade_no', 'status', 'get_token', 'buyer_name', 'phone'));

		//下单时间或者提货日期
		$timer = $this->getInput(array('time_type', 'to_time', 'from_time'));
		$condition = array();
		$timeColumn = $timer['time_type'] == 1 ? 'create_time' : 'get_date';
		//下单时间需要转换时间戳
		if ($timer['time_type'] == 1) {
			if ($timer['from_time']) $condition[$timeColumn] = array('>=', strtotime($timer['from_time']));
			if ($timer['to_time']) $condition[$timeColumn] = array('<=', strtotime($timer['to_time']));
			if ($timer['from_time'] && $timer['to_time']) {
				$condition[$timeColumn] = array(
					array('>=', strtotime($timer['from_time'])),
					array('<=', strtotime($timer['to_time']))
				);
			}
			//提货日期使用date格式
		}elseif($timer['time_type'] == 2){
			if ($timer['from_time']) $condition[$timeColumn] = array('>=', $timer['from_time']);
			if ($timer['to_time']) $condition[$timeColumn] = array('<=', $timer['to_time']);
			if ($timer['from_time'] && $timer['to_time']) {
				$condition[$timeColumn] = array(
					array('>=', $timer['from_time']),
					array('<=', $timer['to_time'])
				);
			}
		}
		//合并时间条件和其他条件
		$condition = array_merge(array_filter($param),$condition);
		list(,$order) = Fj_Service_Order::getList(0, 1000, $condition, array('trade_no'=>'DESC'));

		foreach($order as $key=>$value) {
			$order_ids[] = $value['id'];
			$goods_ids[] = $value['goods_id'];
			$supplier_ids[] = $value['supplier'];
		}

		$file_content = "";
		$file_content .= "\"UID\",";
		$file_content .= "\"open_id\",";
		$file_content .= "\"订单号\",";
		$file_content .= "\"提货码\",";
		$file_content .= "\"提货人\",";
		$file_content .= "\"提货人电话\",";
		$file_content .= "\"外部订单号\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\"付款时间\",";
		$file_content .= "\"提货时间\",";
		$file_content .= "\"实付款\",";
		$file_content .= "\"状态\",";
		$file_content .= "\"提货地点\",";
		$file_content .= "\r\n";
		$time = Fj_Service_Order::getTime();
		foreach ($order as $key=>$value) {
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$pay_time = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : '';
			$status = Fj_Service_Order::orderStatus();
			$address =  Fj_Service_Address::get($value['address_id']);;
			$file_content .= "\"" . $value['uid'] . "\",";
			$file_content .= "\"" . $value['out_uid'] . "\",";
			$file_content .= "\"'". $value['trade_no'] . "\",";
			$file_content .= "\"" . $value['get_token'] . "\",";
			$file_content .= "\"" . $value['buyer_name'] . "\",";
			$file_content .= "\"" . $value['phone'] . "\",";
			$file_content .= "\"" . $value['out_trade_no'] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . $pay_time . "\",";
			$file_content .= "\"" . $value['get_date'] ." ".$time[$value['get_time_id']]. "\",";
			$file_content .= "\"" . $value['deal_price'] . "\",";
			$file_content .= "\"" . $status[$value['status']] . "\",";
			$file_content .= "\"" . $address['detail_address']. "\",";
			$file_content .= "\r\n";

		}
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		Util_DownFile::outputFile($file_content, date('Y-m-d') . '.csv');
	}

	public function editAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Order::get(intval($id));

		$address = Fj_Service_Address::get($info['address_id']);
		$this->assign('order', $info);
		$this->assign('address', $address);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		
		$ret = Fj_Service_Order::updateOrder($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功.'); 		
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Order::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$this->output(0, '操作成功');
	}

}
