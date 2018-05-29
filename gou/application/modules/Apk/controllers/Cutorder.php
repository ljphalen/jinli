<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CutorderController extends Apk_BaseController {
    
    public $actions = array(
        'listUrl' => '/Cutorder/list',
        'editUrl' => '/Cutorder/edit',
        'editPostUrl' => '/Cutorder/editPost',
    );
	/**
	 * create order 
	 */
	public function createAction() {
        $id = intval($this->getPost('goods_id'));
        $goods = Cut_Service_Goods::getGoods($id);
        $uid =  Common::getAndroidtUid();
         
        if (!$goods) $this->output(-1, '创建订单失败,商品信息不存在.');
        if (!$uid) $this->output(-1, '请在购物大厅客户端下单.');
        $stroe = Cut_Service_Store::getStore($goods['store_id']);
        
        //判断商品是否可以下单
        $status_arr = Cut_Service_Goods::getCutStatus($goods['id'], $uid, 'detail');
        $status = $status_arr[$goods['id']];
        
        if($status['cut_code'] == 0) $this->output(-1, '该商品暂时不能下单.');
        if($status['cut_code'] == 1) $this->output(-1, '该商品暂时不能下单.');
        if($status['cut_code'] == 6) $this->output(-1, '对不起，别人抢先买走了！');
        
        $goods['current_price'] = $status['current_price'];
        $goods['title'] = html_entity_decode($stroe['title']);
        
		$mobile = $this->getPost('mobile');
		$buyer_name = $this->getPost('buyer_name');
		$province = $this->getPost('province');
		$city = $this->getPost('city');
		$country = $this->getPost('country');
		$detail_address = $this->getPost('detail_address');
		$postcode = $this->getPost('postcode');
		$gbook = $this->getPost('gbook');
		
		
		//check
		if (!$buyer_name) $this->output(-1, '请填写收货人姓名.');
		if (!$mobile) $this->output(-1, '请输入您的手机号码.');
		if(!Common::checkMobile($mobile))  $this->output(-1, '手机号码输入有误.');
		if (!$province) $this->output(-1, '请选择省份.');
		if (!$city) $this->output(-1, '请选择城市.');
		if (!$country) $this->output(-1, '请选择区/县.');
		if (!$detail_address) $this->output(-1, '请填写街道地址.');
		if (!$postcode)  $this->output(-1, '请填写邮编.');
		if (Util_String::strlen($postcode) != 6 && is_int($postcode) == false)  $this->output(-1, '邮编填写错误.');
		
		/*$lockName = 'LOCK_' . $this->userInfo['id'] . '_' . $goods['id'];
		if (Common::getLockHandle()->lock($lockName) === false) $this->output(-1, "创建订单失败.");*/
		
		$address = array(
			'buyer_name'=>$buyer_name,
			'mobile'=>$mobile,
			'province'=>$province,
			'city'=>$city,
			'country'=>$country,
			'detail_address'=>$detail_address,
			'postcode'=>$postcode,
		);
		
		$ret = Cut_Service_Order::create($goods, $address, array('gbook'=>$gbook));
		//Common::getLockHandle()->unlock($lockName); //解锁
		if (!$ret) $this->output(-1, '对不起，别人抢先买走了！');
		
		//sms
		if($goods['current_price'] > '1000.00') {
		    Common::sms('15818682200', '有砍价订单额金大于1000元，请关注');
		}

		list($order_id, $token) = $ret;
		$webroot = Common::getWebRoot();
		$url = $webroot . '/cutorder/pay?token=' . $token;
		$api = new Api_Alipay_Pay();
		$url = $api->getPayUrl($token);
		$this->output(0,'订单创建成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	/**
	 * pay
	 */
	public function payAction() {
		$token = $this->getInput('token');
		if (!$token) $this->output(-1, '支付失败.');
		
		$order = Gou_Service_Order::getBy(array('token'=>$token));
		if (empty($order)) $this->output(-1, '支付失败.');
		if($order['status'] != 1) $this->output(-1, '订单状态已变更，不能支付.');
		if ($order['out_uid'] != Common::getAndroidtUid()) {
		    $this->output(-1, '非法请求.');
		}
		
		$api = new Api_Alipay_Pay();
		$url = $api->getPayUrl($order['token']);
		//$url = Api_Gionee_Pay::getPayUrl($order['out_trade_no']);
		$this->output(0,'', array('type'=>'redirect', 'url'=>$url));
	}
	
		
	
	/**
	 * order edit 编辑订单
	 */
	public function editAction() {
	    $trade_no = $this->getInput('trade_no');
	    $uid =  Common::getAndroidtUid();
	     
	    if (!$uid) $this->redirect(Common::getWebRoot().$this->actions['listUrl']);
	    $order = Gou_Service_Order::getByTradeNo($trade_no);
	    if (!$order)  $this->redirect(Common::getWebRoot().$this->actions['listUrl']);
	    if ($order['status'] != 1)  $this->redirect(Common::getWebRoot().$this->actions['listUrl']);
	    if ($order['out_uid'] != $uid)  $this->redirect(Common::getWebRoot().$this->actions['listUrl']);
	    $address = Gou_Service_Order::getOrderAddress($order['id']);
	    $this->assign('info', $order);
	    $this->assign('address', $address);
	    $this->assign('title', '修改订单');
	}
	
	/**
	 * order edit 编辑订单
	 */
	public function editPostAction() {
	    $trade_no = $this->getInput('trade_no');
	    $uid =  Common::getAndroidtUid();
	    
	    $order = Gou_Service_Order::getByTradeNo($trade_no);
	    if (!$order) $this->output(-1, '订单不存在.');
	    if ($order['status'] != 1) $this->output(-1, '此订单不能编辑.');
	    if ($order['out_uid'] != $uid) $this->output(-1, '此订单不能取消.');
	    
	    $mobile = $this->getPost('mobile');
		$buyer_name = $this->getPost('buyer_name');
		$province = $this->getPost('province');
		$city = $this->getPost('city');
		$country = $this->getPost('country');
		$detail_address = $this->getPost('detail_address');
		$postcode = $this->getPost('postcode');
		$gbook = $this->getPost('gbook');
		
		
		//check
		if (!$buyer_name) $this->output(-1, '请填写收货人姓名.');
		if (!$mobile) $this->output(-1, '请输入您的手机号码.');
		if(!Common::checkMobile($mobile))  $this->output(-1, '手机号码输入有误.');
		if (!$province) $this->output(-1, '请选择省份.');
		if (!$city) $this->output(-1, '请选择城市.');
		if (!$country) $this->output(-1, '请选择区/县.');
		if (!$detail_address) $this->output(-1, '请填写街道地址.');
		if (!$postcode)  $this->output(-1, '请填写邮编.');
		if (Util_String::strlen($postcode) != 6 && is_int($postcode) == false)  $this->output(-1, '邮编填写错误.');
		
		$address = array(
			'buyer_name'=>$buyer_name,
			'mobile'=>$mobile,
			'province'=>$province,
			'city'=>$city,
			'country'=>$country,
			'detail_address'=>$detail_address,
			'postcode'=>$postcode,
		);
		$order_data = array(
		    'buyer_name' => $address['buyer_name'],
		    'phone' => $address['mobile'],
		);
	    
		$order_ret = Gou_Service_Order::updateOrder($order_data, $order['id']);
		if (!$order_ret) $this->output(-1, '订单修改失败.');
		
		$add_ret = Gou_Service_Order::updateAddressByOrderId($address, $order['id']);
		if (!$add_ret) $this->output(-1, '订单修改失败.');
		
		$webroot = Common::getWebRoot();
		$url = $webroot . $this->actions['listUrl'];
		$this->output(0,'订单修改成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	
	/**
	 * cancel 取消订单
	 */
	public function cancelAction() {
	    $trade_no = $this->getInput('trade_no');
	    $uid =  Common::getAndroidtUid();
	     
	    if (!$uid) $this->output(-1, '非法请求.');
	    $order = Gou_Service_Order::getByTradeNo($trade_no);
	    if (!$order) $this->output(-1, '订单不存在.');
	    if ($order['status'] != 1) $this->output(-1, '此订单不能取消.');
	    if ($order['out_uid'] != $uid) $this->output(-1, '此订单不能取消.');
	    $ret = Cut_Service_Order::cancelOrder($order['trade_no']);
	    if(!$ret)  $this->output(-1, '订单取消失败.');
	    $this->output(0, '订单取消成功.');
	}
	
	/**
	 * callback 
	 */
	public function callbackAction() {
	    $params = $this->getInput(array('sign', 'result', 'out_trade_no', 'trade_no', 'request_token'));
	    
	    $api = new Api_Alipay_Notify();
	    $verify = $api->verifyReturn($params);
	    if($verify) {
	        //处理订单业务
	        $order = Gou_Service_Order::getByTradeNo($params['trade_no']);
	        if($order['status'] < 2 && $params['result'] == 'success') {
	            $api = new Api_Alipay_Query();
	            $info = $api->queryOrder($params['out_trade_no']);
	            if($info['trade_status'] == 'TRADE_SUCCESS') {
	                //更新订单状态
	                $update_data = array(
    	                'status'=>2,
    	                'pay_time'=>strtotime($info['gmt_payment']),
    	                'out_trade_no'=>$info['trade_no'],
	                );
	                Gou_Service_Order::updateOrder($update_data, $order['id']);
	                
	                //log
	                $log = array(
    	                'order_id'=>$order['id'],
    	                'order_type'=>1,
    	                'uid'=>0,
    	                'create_time'=>time(),
    	                'update_data'=>json_encode(array('status' => 2))
	                );
	                Gou_Service_Order::addOrderLog($log);
	            }
	        }
	    }
	    
	    $webroot = Common::getWebRoot();
	    $this->redirect($webroot.$this->actions['listUrl']);
	    
	}
	
	/**
	 * 订单列表
	 */
	public function listAction() {
		$this->assign('title', '我的砍价订单');
	}
	
}