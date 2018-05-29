<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderController extends Front_BaseController {
	/**
	 * 创建订单
	 * 传入参数： product_id 产品ID, ucenter_id 用户中心ID
	 * 
	 */
	public function createAction(){
		//产品ID
		$product_id = $this->getInput('product_id');
		if(empty($product_id)) exit('产品ID不能为空');
		//ucenter id
		$ucenter_id = $this->getInput('ucenter_id');
		if(empty($ucenter_id)) exit('用户中心用户ID不能为空');

		//查看是否已经购买过
		/*	
		$data = array(
			'product_id' => $product_id,
			'ucenter_id' => $ucenter_id,
		);
		$order = Theme_Service_Order::getBy($data);
		if(!empty($order) && $order['status'] == 5){
			$return_arr['status'] = 'failure';
			$return_arr['error_code'] = 1;//已经购买
			die(json_encode($return_arr));
		}
		//Common::v($order);
		*/

		//获取产品信息
		$product = Theme_Service_File::get($product_id);
		//你的支付标题
		$subject = '[主题公园]'.$product['title'];
		//价格
		$deal_price = $product['price'];
		if(empty($deal_price)){
			die('免费主题不能进行支付');
		}
		//总价
		$total_fee = $deal_price*1;
		$total_fee = sprintf('%.2f', $total_fee);

		//下单用户信息
		$params = array('user_sys_id'=>$ucenter_id);
		$member = Theme_Service_Ucenter::getBy($params);

		//如果用户信息不存在，则插入用户信息
		if(empty($member)){
			$data = array(
				'user_sys_id' => $ucenter_id,
				'register_time' => time(),
			);
			Theme_Service_Ucenter::insert($data);
			$member_id = Common_Dao_Base::getLastInsertId();
		} else {
			$member_id = $member['user_id'];
		}

		//创建服务器订单
		if(!empty($order)){
			$order_id = $order['order_id'];
		} else {
			$data = array(
				'created_time' => time(),
				'uid' => $product['user_id'],
				'status' => 1,
				'member_id' => $member_id,
				'ucenter_id' => $ucenter_id,
				'product_id' => $product_id,
				'product_name' => $subject,
				'desc' => '',
				'price_id' => $product['price_id'],
				'price' => $deal_price,
				'total' => $total_fee,
			);
			Theme_Service_Order::insert($data);
			$order_id = Common_Dao_Base::getLastInsertId();
		}

		//请求支付中心，支付中心创建支付中心订单，返回支付中心订单ID
		$dst_url = "https://pay.gionee.com/order/create";
		//$dst_url = "http://test3.gionee.com/pay/order/create";
		$api_key = Common::getConfig('siteConfig', 'payAppId');
		$post_arr['api_key'] = $api_key;
		$post_arr['subject'] = $subject;
		$post_arr['out_order_no'] = $order_id;
		$post_arr['deliver_type'] = '1';
		$post_arr['deal_price'] = (string)$deal_price;
		$post_arr['total_fee'] = (string)$total_fee;
		$post_arr['submit_time'] = date('YmdHis');
		$admin_root = Yaf_Application::app()->getConfig()->webroot;
		$post_arr['notify_url'] = $admin_root . "/order/notify";
		$post_arr['sign'] = Theme_Service_Order::rsa_sign($post_arr);

		//Common::v($post_arr);
		$json = json_encode($post_arr);
		
		$return_json = Theme_Service_Order::https_curl($dst_url,$json);
		$return_arr = json_decode($return_json,1);
		
		//Common::v($return_arr);
		//订单创建成功的状态码判断
		if ($return_arr['status'] == '200010000') {
			if($order_id != $return_arr['out_order_no']){
				exit('返回外部订单号不匹配');
			}

			if($api_key != $return_arr['api_key']){
				exit('返回较验证apiKey出错');
			}
			//写入支付中心订单号
			$data = array(
				'order_sn' => $return_arr['order_no'],
				'status' => 2,
			);
			Theme_Service_Order::update($data, $order_id);
			$return_arr['status'] = 'success';
		} else {
			$return_arr['status'] = 'failure';
		}
		die(json_encode($return_arr));
	}

	//支付中心回调，确认支付完成
	public function notifyAction(){
		$contents = $_POST;

		if(!Theme_Service_Order::rsa_verify($contents)){
			die('error sign');
		}

		$order_id = $this->getInput('out_order_no');

		if(empty($order_id)){
			die('order no is empty');
		}
		$data = array(
			'status' => 5,
			'pay_time' => time()
		);
		Theme_Service_Order::update($data, $order_id);
		exit;
	}

	//支付完成确认，供客户端查询
	public function checkAction(){
		$order_sn = $this->getInput('order_sn');
		$params = array(
			'status' => 5,
			'order_sn' => $order_sn,
		);
		$order = Theme_Service_Order::getBy($params);
		if(!empty($order)){
			$return['status'] = false;
		} else {
			$return['status'] = true;
		}
		echo json_encode($return);
		exit;
	}

	//购买记录
	public function boughtlistAction(){
		$ucenter_id = $this->getInput('ucenter_id');

		$orders = Theme_Service_Order::getsBy(array('ucenter_id' => $ucenter_id));

		$list = array();
		foreach($orders as $order){
			$temp = array();
			$temp['id'] = $order['product_id'];
			$temp['name'] = $order['product_name'];
			$temp['type'] = $order['product_type'];
			$temp['money'] = $order['price'];
			$temp['date'] = date('Y.m.d', $order['created_time']);

			$list[] = $temp;
		}

		echo json_encode(array('recordList' => $list));
		exit;
	}

	//主题价格
	public function getpriceAction(){
		$themeId = $this->getInput('id');
		if(!$themeId) exit('id不能为空');
		$type = $this->getInput('type');
		if($type == 1){
			$theme = Theme_Service_File::get($themeId);

			$price['id'] = $theme['id'];
			$price['price'] = $theme['price'];
			$price['type'] = 1;
			echo json_encode($price);
		}
		exit;
	}

	//获取所有主题的价格
	public function getallpirceAction(){
		$type =  $this->getInput('type');
		if($type == 1){
			$themes = Theme_Service_File::getAll();
			$list = array();
			foreach($themes as $theme){
				$temp = array();
				$temp['id'] = $theme['id'];
				$temp['price'] = $theme['price'];
				$temp['type'] = 1;
				$list[] = $temp;
			} 
			echo json_encode(array('priceList' => $list));
		}
		exit;
	} 
}
