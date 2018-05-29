<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * 话费充值订单管理功能
 * @author huangsg
 *
 */

class Recharge_OrdersController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Recharge_Orders/index',
		'editUrl'=>'/admin/Recharge_Orders/edit',
		'editPostUrl'=>'/admin/Recharge_Orders/edit_post',
		'exportUrl'=>'/admin/Recharge_Orders/export',
		'checkUrl'=>'/admin/Recharge_Orders/check',
		'recheckUrl'=>'/admin/Recharge_Orders/recheck',
		'rechargeUrl'=>'/admin/Recharge_Orders/recharge',
	);
	
	// 0.Amigo商城使用，1.积分换购使用, 2.话费充值使用
	public $show_type = 2;
	public $perpage = 20;
	
	public $rec_status = array(
		1=>'成功',
		2=>'失败',
		3=>'充值中',
		4=>'未充值'
	);
	
	public $pay_channel_array = array(
		'101'=>'支付宝',
		'103'=>'财付通'
	);
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$params  = $this->getInput(array('rec_status', 'status', 'phone',
				'trade_no', 'out_trade_no', 'start_time', 'end_time', 'gionee_order_no', 'pay_channel_billno'));
		$order_by = 'create_time';
		
		$search = array();
		if ($params['rec_status']) $search['rec_status'] = $params['rec_status'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		if ($params['pay_channel_billno']) $search['pay_channel_billno'] = $params['pay_channel_billno'];
		if ($params['out_trade_no']) $search['out_trade_no'] = $params['out_trade_no'];
		if ($params['gionee_order_no']) $search['gionee_order_no'] = $params['gionee_order_no'];
		if ($params['phone']) $search['phone'] = $params['phone'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		$search['show_type'] = $this->show_type;
		$this->assign('params', $params);
		
		list($total, $order) = Gou_Service_Order::search($page, $this->perpage, $search, array($order_by=>'DESC'));
		
		$this->assign('result', $order);
		$url = $this->actions['indexUrl'] .'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
		
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
		$this->assign('rec_status', $this->rec_status);
	}
	
	public function editAction() {
		$id = $this->getInput('id');
		$order = Gou_Service_Order::getOrder($id);
		$this->assign('order', $order);
		$this->assign('orderStatus', Gou_Service_Order::orderStatus());
		
		//refund 
		$order_refund = Gou_Service_OrderRefund::getBy(array('trade_no'=>$order['trade_no']));
		$this->assign('order_refund', $order_refund);
		
		//订单操作日志
		$log = Gou_Service_Order::getOrderLog($id, 1);
		$this->assign('log', $log);
		$this->assign('rec_status', $this->rec_status);
		$this->assign('pay_channel_array', $this->pay_channel_array);
	}
	
	public function edit_postAction() {
		$id = intval($this->getPost('id'));
		$status = intval($this->getPost('status'));
		$remark = $this->getPost('remark');
		$order = Gou_Service_Order::getOrder($id);
		if (!$order) $this->output(-1, '参数错误.');
		
		if ($order['status'] < 10 && $status < $order['status']) $this->output(-1, '订单状态更新错误.');
		if(in_array($order['status'], array(1,6)) && $status > 9) $this->output(-1, '订单状态更新错误.');
		
	    //退款
		if($status == 10 && $order['status'] != 10) {
		    if($order['rec_status'] != 2)  $this->output(-1, '此订单暂时不能申请退款.');
		    list($total, ) = Gou_Service_OrderRefund::getsBy(array('trade_no'=>$order['trade_no'], 'out_refund_no'=>array('!=', '')), array('id'=>'DESC'));
		    if($total >= 9) $this->output(-1, '退款操作失败,次数超过9次');
		    $refund_ret = Gou_Service_OrderRefund::create_refund($order['trade_no'], '充值失败');
		    if (!$refund_ret) $this->output(-1, '退款操作失败.');
		}
		
		//取消退款
		if($status == 11 && $order['status'] != 11) {
		    $refund_ret = Gou_Service_OrderRefund::cancleRefund($order['trade_no']);
		    if (!$refund_ret) $this->output(-1, '取消退款操作失败.');
		}
		
		//记录订单操作日志
		$update_log = array();
		$i = 0;
		if($order['status'] != $status){
		    $update_log['status'] = $status;
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
				
		$ret = Gou_Service_Order::updateOrder(array('status'=>$status, 'remark'=>$remark), $id);
		if (!$ret) $this->output(-1, '操作失败.');
		
		
		$this->output(0,'操作成功.');
	}
	
	/**
	 *
	 * Get order list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$params  = $this->getInput(array('rec_status', 'status', 'phone',
				'trade_no', 'start_time', 'end_time', 'gionee_order_no'));
		$order_by = 'create_time';
	
		$search = array();
		if ($params['rec_status']) $search['rec_status'] = $params['rec_status'];
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['trade_no']) $search['trade_no'] = $params['trade_no'];
		if ($params['gionee_order_no']) $search['gionee_order_no'] = $params['gionee_order_no'];
		if ($params['phone']) $search['phone'] = $params['phone'];
		if ($params['start_time']) $search['start_time'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end_time'] = strtotime($params['end_time']);
		$search['show_type'] = $this->show_type;


        $file = realpath(dirname(Common::getConfig('siteConfig', 'attachPath'))) .'/attachs/export.csv';
        $header = array('订单号', '金立订单号','欧飞订单号', '外部订单号', '手机号', '充值面额', '成本金额', '销售金额', '下单时间', '充值状态', '订单状态', '欧飞消息', '渠道号', '支付渠道', '支付渠道的流水号');

        $header = sprintf("%s\r\n", implode(",", $header));
        $header = mb_convert_encoding($header, 'gb2312', 'UTF-8');

        $ret = Util_File::write($file, $header);

        // 一次最多拿100000条数据。分100次获取
        $per = 1000;
        $page = $total = 0;

        do {
            list($total, $rs) = Gou_Service_Order::search($page, $per, $search, array($order_by=>'DESC'));
            if ($total == 0) break;

            $rs_str = "";
            foreach ($rs as $key=>$value) {
                $create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
                $pay_status = $this->rec_status[$value['rec_status']];
                $order_status = Gou_Service_Order::orderStatus($value['status']);
                $rs_array = array(
                    $value['trade_no']."\t",
                    $value['gionee_order_no'],
                    $value['rec_order_id'],
                    $value['out_trade_no'],
                    $value['phone']."\t",
                    $value['rec_cardnum'],
                    $value['rec_price'],
                    $value['real_price'],
                    $create_time,
                    $pay_status,
                    $order_status,
                    html_entity_decode($value['rec_msg']),
                    $value['channel_id'],
                    $this->pay_channel_array[$value['pay_channel']],
                    $value['pay_channel_billno']."\t");

                $rs_str .= sprintf("%s\r\n", implode(",", $rs_array));
            }
            $page++;
            $rs_str = mb_convert_encoding($rs_str, 'gb2312', 'UTF-8');
            Util_File::write($file, $rs_str, Util_File::APPEND_WRITE);
        } while ($total>($page * $per));
        $this->redirect(Common::getAttachPath() . '/export.csv');
	}

	/**
	 * 充值状态查询
	 */
	public function checkAction() {
		$trade_no = $this->getPost('trade_no');
		if($trade_no) {
			$order = Gou_Service_Order::getByTradeNo($trade_no);
			if($order) {
				$ret = Api_Ofpay_Recharge::query($trade_no);
				if($ret == 1 && $order['rec_status'] == 3 && $order['status'] == 2) {
					Gou_Service_Order::updateOrder(array('rec_status'=>1, 'status'=>5), $order['id']);
				}
				switch ($ret){
					case 1:
						$status = '充值成功';
						break;
					case 9:
						$status = '充值失败';
						break;
					case 0:
						$status = '充值中';
						break;
					case -1:
						$status = '未找到订单';
						break;
					default:
						$status = '未知错误';
				}
				$this->assign('status', array('code'=>$ret, 'status'=>$status));
				$this->assign('order', $order);
			}
		}
	}
	
	/**
	 * 补发
	 */
	public function recheckAction() {
		$trade_no = $this->getPost('trade_no');
		$ret = Api_Ofpay_Recharge::reissue($trade_no);
		if($ret['msginfo']['retcode'] == 1) {
			$this->output(0,'补发成功.');
		} else {
			$this->output(-1, '补发失败.');
		}
	}
	
	/**
	 * 补充
	 */
	public function rechargeAction() {
		$trade_no = $this->getPost('trade_no');
		Api_Ofpay_Recharge::recharge($trade_no);
		$this->output(0,'已发送补充请求，请稍后查看补充结果.');
	}
}