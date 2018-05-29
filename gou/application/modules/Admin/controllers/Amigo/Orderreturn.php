<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Amigo_OrderreturnController extends Admin_BaseController {
	public $actions = array(
		'listUrl' =>'/admin/Amigo_orderreturn/index',
		'detailUrl'=>'/admin/Amigo_orderreturn/detail',
		'optionUrl'=>'/admin/Amigo_orderreturn/option',
	);
	
	public $perpage = 20;
	
	/**
	 * 订单列表
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('truename', 'status', 'phone',
				'order_return_id', 'type_id', 'start_time', 'end_time'));
		if ($page < 1) $page = 1;
		
		$search = array();
		if ($params['truename']) $search['truename'] = $params['truename'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['phone']) $search['phone'] = $params['phone'];
		if ($params['order_return_id']) $search['order_return_id'] = $params['order_return_id'];
		if ($params['type_id']) $search['type_id'] = $params['type_id'];
		if ($params['start_time']) $search['create_time'] = array('>=', strtotime($params['start_time'] . ':00'));
		if ($params['end_time']) $search['create_time'] = array('<=', strtotime($params['end_time'] . ':59'));
		if ($params['start_time'] && $params['end_time']) {
			$search['create_time'] = array(
				array('>=', strtotime($params['start_time'] . ':00')),
				array('<=', strtotime($params['end_time'] . ':59'))
			);
		}
		
		list($total, $list) = Amigo_Service_Orderreturn::getList($page, $this->perpage, $search);
		
		$this->assign('list', $list);
		$this->assign('params', $params);
		$this->assign('status', Amigo_Service_Orderreturn::$order_status);
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 订单明细
	 */
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$info = Amigo_Service_Orderreturn::getOne($id);
		
		//获取原订单商品信息
		$orderInfo = Gou_Service_Order::getOrder($info['order_id']);
		$goods = Gou_Service_LocalGoods::getLocalGoods($orderInfo['goods_id']);
		
		$address = Gou_Service_Order::getOrderAddress($info['order_id']);
		$address = Gou_Service_UserAddress::cookUserAddress($address);
		$this->assign('address', $address);
		
		$reason = Amigo_Service_Reason::getOne($info['reason_id']);
		
		$log = Gou_Service_Order::getOrderLog($id, 2);
		
		$this->assign('reason', $reason['reason']);
		$this->assign('address', $address);
		$this->assign('order', $orderInfo);
		$this->assign('goods', $goods);
		$this->assign('info', $info);
		$this->assign('status', Amigo_Service_Orderreturn::$order_status);
		$this->assign('log', $log);
	}
	
	/**
	 * 订单操作
	 */
	public function optionAction(){
		$id = intval($this->getInput('id'));
		$params  = $this->getInput(array('status', 'remark'));
		if (empty($id)) $this->output(-1, '操作失败.');
		$order_info = Amigo_Service_Orderreturn::getOne($id);
		if (empty($order_info)) $this->output(-1, '操作失败,订单信息不存在.');
		/* if ($params['status'] < $order_info['status'] && !in_array($order_info['status'], array(6, 7)))
			$this->output(-1, '操作失败,订单状态操作不合法。'); */
		
		$params['type_id'] = $params['status'] < 6 ? 2 : 1;
		$rs = Amigo_Service_Orderreturn::update($params, $id);
		if (!$rs) $this->output(-1, '操作失败.');
		
		//记录订单操作日志
		$update_log = array();
		$i = 0;
		if($order_info['status'] != $params['status']){
			$update_log['status'] = $params['status'];
			$i++;
		}
		
		if (!empty($i)){
			$log = array(
				'order_id'=>$id,
				'order_type'=>2,
				'uid'=>$this->userInfo['uid'],
				'create_time'=>time(),
				'update_data'=>json_encode($update_log)
			);
			Gou_Service_Order::addOrderLog($log);
		}
		
		$this->output(0, '操作成功.');
	}
}