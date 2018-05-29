<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderController extends Front_BaseController {

	/**
	 * create order
	 */
	public function createAction() {
	    $userInfo = $this->userInfo;
	    if (!$userInfo) $this->output(-1, '非法请求.');
	    $cart_ids = $this->getPost('cart_ids');
	    $cart_ids = explode(',', html_entity_decode($cart_ids));
	    if(!$cart_ids)  $this->output(-1, '购物车没有商品.');
	    
	    list($total, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id'], 'id'=>array('IN', $cart_ids)), array('id'=>'DESC'));
	    $ids = array_keys(Common::resetKey($cart_list, 'id'));
	    if($total == 0)  $this->output(-1, '购物车没有商品.');
		
	    //get params;
		$phone = $this->getPost('phone');
		$buyer_name = $this->getPost('buyer_name');
		$address_id = $this->getPost('address_id');
		$date = $this->getPost('date');
		$time_id = $this->getPost('time_id');
		
		//check
		if (!$buyer_name) $this->output(-1, '请填写收货人姓名.');
		if (!$phone) $this->output(-1, '请输入您的手机号码.');
		if(!Common::checkMobile($phone))  $this->output(-1, '手机号码输入有误.');
		if (!$date) $this->output(-1, '请选择提货日期.');
		if (!$time_id) $this->output(-1, '请选择提货时间.');
		
		//购物车
		$carts = Fj_Service_Cart::getsBy(array('id'=>array('IN', $cart_ids), 'uid'=>$userInfo['id']), array('id'=>'DESC'));
		$goods_ids = array_keys(Common::resetKey($carts, 'goods_id'));
		
		//计算商品价格
		list(, $price) = Fj_Service_Cart::getCartInfo($cart_ids);
		
		//检查商品是否可购买, 如库存不足、已下架、等
		list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
		
		foreach ($goods as $key=>$value) {
		    /* if($value['stock_num'] < $cart_list[$value['id']]['goods_num']) {
		        $this->output(-1, '您选购的商品['.$value['title'].']库存不足，请返回购物车修改');
		    } */
		    if($value['status'] == 0) {
		        $this->output(-1, '您选购的商品['.$value['title'].']已下架，请返回购物车修改');
		    }
		}
		
		$data = array(
			'buyer_name'=>$buyer_name,
			'phone'=>$phone,
			'address_id'=>$address_id,
			'date'=>$date,
			'time_id'=>$time_id,
			'cart_ids'=>$cart_ids,
			'uid'=>$userInfo['id'],
		    'open_id'=>$userInfo['open_id'],
		);
		
		$order_id = Fj_Service_Order::create($data);
		//Common::getLockHandle()->unlock($lockName); //解锁
		if (!$order_id) $this->output(-1, '创建订单失败.');

		$order = Fj_Service_Order::getOrder($order_id);
		$webroot = Common::getWebRoot();
		$url = $webroot . '/order/pay?trade_no=' . $order['trade_no'];
		//$url = $webroot . '/order/list';
		$this->output(0,'订单创建成功.', array('type'=>'redirect', 'url'=>$url));
	}
	
	
	/**
	 * 请求支付
	 */
	public function payAction() {
	    $code = $this->getInput('code');
	    $user = new WeiXin_Server_User();
	    
	    $trade_no = $this->getInput('trade_no');
	    if (!$trade_no) $this->output(-1, '支付失败.');
	    
	    $order = Fj_Service_Order::getByTradeNo($trade_no);
	    if (!$order) $this->output(-1, '支付失败.');
	    if($order['status'] == 2) {
	        $this->redirect(Common::getWebRoot().'/order/detail?trade_no='.$order['trade_no']);
	        exit;
	    }
	    
	    //order_detail 
	    $detail = Fj_Service_Order::getsDetailByTradeNo($trade_no);
	    if($detail) {
	        $goods = Fj_Service_Goods::getGoods($detail[0]['goods_id']);
	        $title = html_entity_decode($goods['title']);
	        $title = count($detail) > 1 ? $title.'...' : $title;
	    }
	    
	    $real_price = $order['real_price'] * 100;
	    
 	    if (!$code) {
	        $url = $user->createOauthUrlForCode(WeiXin_Config::PAY_CALL_URL.'?trade_no='.$trade_no, false);
	        $this->redirect($url);
	        exit;
	    }
	    //=========步骤2：使用统一支付接口，获取prepay_id============
	    $user->setCode($code);
	    $user->getToken();
	    $openid = $user->openid;


	    //使用统一支付接口
	    $unifiedOrder = new WeiXin_Client_Pay();
	
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("openid","$openid");//商品描述
	    $unifiedOrder->setParameter("body","$title");//商品描述
	    //自定义订单号，此处仅作举例
	    $timeStamp = time();
	    //$out_trade_no = WeiXin_Config::APPID."$timeStamp";
	    $unifiedOrder->setParameter("out_trade_no","$trade_no");//商户订单号
	    $unifiedOrder->setParameter("total_fee","$real_price");//总金额
	    $unifiedOrder->setParameter("notify_url",WeiXin_Config::NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
       //$unifiedOrder->setParameter("product_id","1122");//商品ID
	
        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $user->setPrepayId($prepay_id);

        $jsApiParams = $user->getParameters();
        $this->assign('jsApiParameters', $jsApiParams);
        $this->assign('trade_no', $order['trade_no']);
    }

	
	/**
	 * order detail 订单详情
	 */
	public function detailAction() {
		/* //取消自动载入视图
		Yaf_Dispatcher::getInstance()->disableView();
		$this->initView(); */

		$trade_no = $this->getInput('trade_no');
		$order = Fj_Service_Order::getByTradeNo($trade_no);
		
		if($order['status'] == 1) {
		    $query = new WeiXin_Client_OrderQuery();
		    $query->setParameter('out_trade_no', $order['trade_no']);
		    $info = $query->getResult();
		    
		    if($info['result_code'] == 'SUCCESS' && $info['trade_state'] == 'SUCCESS') {
    		    $order_data = array(
                    'out_trade_no'=>$info['transaction_id'],
                    'pay_time'=>$info['time_end'],
                    'status'=>2
                );
                Fj_Service_Order::updateOrder($order_data, $order['id']);
                $order['status'] = 2;
		    }
		}
		

		$address = array();
		$goods = array();

		if(!empty($order)){
			$orderStatus = Fj_Service_Order::orderStatus();
			$order['statusMsg'] = isset($orderStatus[$order['status']])?$orderStatus[$order['status']]:'';

			$address = Fj_Service_Address::get($order['address_id']);

			$orderDetail = Fj_Service_Order::getsDetailByTradeNo($order['trade_no']);
			$orderDetail = Common::resetKey($orderDetail, 'goods_id');
			
			$goodsID = array_keys($orderDetail);
			if(!empty($goodsID))
				list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN', $goodsID)), array('id'=>'DESC'));

			$totalGoods = 0;
			foreach($goods as &$item){
				$item['trade_no'] = $orderDetail[$item['id']]['trade_no'];
				$item['goods_num'] = $orderDetail[$item['id']]['goods_num'];
				$item['deal_price'] = $orderDetail[$item['id']]['deal_price'];
				$totalGoods += $item['goods_num'];
			}

			$order['total_goods_num'] = $totalGoods;
			//$dayDiff = Common::dateDiff(Common::getTime(), $order['take_time'], array('day' => '86400', 'hour' => '3600'));
			//$order['day_diff'] = sprintf('%d天$d小时', $dayDiff[0], $dayDiff[1]);
			//$order['take_time_f'] = date('Y年m月d日', $order['take_time']);
			
			//get time
			$time = Fj_Service_Order::getTime();
			$order['get_time'] = $time[$order['get_time_id']];
		}
		
		$this->assign('goods', $goods);
		$this->assign('order', $order);
		$this->assign('address', $address);
		$this->assign('title', '订单详情');

		echo $this->render('default-' . $order['status']);
		exit;
	}
	
	
	/**
	 * order edit 编辑订单
	 */
	public function editAction() {
	    $trade_no = $this->getInput('trade_no');
	     
	    $order = Fj_Service_Order::getByTradeNo($trade_no);
	    if (!$order) $this->output(-1, '订单不存在.');
	    if ($order['status'] != 1) $this->output(-1, '此订单不能编辑1.');
	    if ($order['uid'] != $this->userInfo['id']) $this->output(-1, '此订单不能编辑2.');
	     
	    $address_id = $this->getPost('address_id');
	    $date = $this->getPost('date');
	    $time_id = $this->getPost('time_id');
	    
	    $up_date = array();
	    if($address_id) $up_date['address_id'] = $address_id;
	    if($date) $up_date['get_date'] = $date;
	    if($time_id) $up_date['get_time_id'] = $time_id;
	
	    if($up_date) {
	        $order_ret = Fj_Service_Order::updateOrder($up_date, $order['id']);
	        if (!$order_ret) $this->output(-1, '订单修改失败.');
	    }
	
	    $this->output(0,'订单修改成功.');
	}
	

	/**
	 * cancel 取消订单
	 */
	public function cancelAction() {
	    $trade_no = $this->getInput('trade_no');
	    $userInfo =  $this->userInfo;
	
	    if (!$userInfo) $this->output(-1, '非法请求.');
	    $order = Fj_Service_Order::getByTradeNo($trade_no);
	    if (!$order) $this->output(-1, '订单不存在.');
	    if ($order['status'] != 1) $this->output(-1, '此订单不能取消.');
	    if ($order['uid'] != $userInfo['id']) $this->output(-1, '此订单不能取消.');
	    $ret = Fj_Service_Order::updateOrder(array('status'=>4), $order['id']);
	    if(!$ret)  $this->output(-1, '订单取消失败.');
	    $url = Common::getWebRoot().'/order/list';
	    $this->output(0, '订单取消成功.',array('type'=>'redirect', 'url'=>$url));
	}
	
	
	/**
	 * search
	 */
	public function listAction() {
	    $id = Common::encrypt($this->userInfo['id'],'ENCODE');
	    $this->assign('id', $id);
		$this->assign('title', '订单列表');
	}
	
}