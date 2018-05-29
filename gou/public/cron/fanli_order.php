<?php 
include 'common.php';
/**
 * 订单数据
 */
$page = 1;
$page_size = 100;

$mobiles = Common::getConfig('siteConfig', 'mobiles');

$topApi = new Api_Top_Service();

//订单查询范围的起始日期
$start_time = Gou_Service_Config::getValue('sync_fanli_order_time');
if(!$start_time) $start_time = strtotime('2013-12-17 17:35:00');
$end_time = time();
//记录此次同步订单时间
Gou_Service_Config::setValue('sync_fanli_order_time', $end_time);
$span = 600;
$mobile = '15888388336';

//一次只能取600秒内的订单
$count = ceil(($end_time - $start_time) / $span);
do {
	
	for($i=0; $i< $count; $i++) {
		$s_time = date('Y-m-d H:i:s' , $start_time + $i * $span);
		
		$result = $topApi->getTaokeRebateReport(array('start_time'=>$s_time, 'span'=>$span, 'page_size'=>$page_size, 'page_no'=>$page,));
		if ($result) {
 			$total = count($result);
 			//返利比例
 			$rate = Gou_Service_Config::getValue('fanli_commission_rate');
 			if(!$rate) $rate = 20;
 			$fanli_rate = $rate / 100;
 			
 			foreach($result as $key=>$value) {
 				//查询日志，如果已经返还，不再返还
 				$order = Fanli_Service_Order::getBy(array('trade_id'=>$value['trade_id']));
 				if ($order) continue;
 			
 				//计算返利
 				$fanli = Common::money($value['commission'] * $fanli_rate);
 			
 				//解析渠道号和UID
 				if(!$value['outer_code']) continue;
 				$out_code = explode("X", $value['outer_code']);
 				$user_id = $out_code[1]; 			
 				if (!$user_id) continue;
 				
 				//获取用户信息
 				$user = Fanli_Service_User::getUser($user_id); 				
 				if (!$user) continue;
 				
 				//更新用户账户余额
 				$user_data = array('money'=>$user['money'] + $fanli);
 				$user_ret = Fanli_Service_User::updateUser($user_data, $user_id);
 				if(!$user_ret){
 					Common::log(array('user_id'=>$user_id, 'trade_id'=>$value['trade_id'] ,'msg'=> '更新用户账户余额失败'), 'fanli_order.log');
 					Common::sms($mobile, 'user_id'.$user_id.'trade_id'.$value['trade_id'].'更新用户账户余额失败');
 				}
 				
 				//记录订单
 				$order_data = array(
 						'trade_parent_id'=>$value['trade_parent_id'],		
 						'trade_id'=>$value['trade_id'],
 						'real_pay_fee'=>$value['real_pay_fee'],
 						'commission_rate'=>$value['commission_rate'],
 						'commission'=>$value['commission'],
 						'fanli'=>$fanli,
 						'outer_code'=>$value['outer_code'],
 						'create_time'=>strtotime($value['create_time']),
 						'pay_time'=>strtotime($value['pay_time']),
 						'pay_price'=>$value['pay_price'],
 						'num_iid'=>$value['num_iid'],
 						'item_title'=>$value['item_title'],
 						'category_id'=>$value['category_id'],
 						'category_name'=>$value['category_name'],
 						'user_id'=>$user_id,
 				);
 				$order_ret = Fanli_Service_Order::addOrder($order_data);
 				if(!$order_ret) {
 					Common::log(array('user_id'=>$user_id, 'trade_id'=>$value['trade_id'] ,'msg'=> '订单插入失败'), 'fanli_order.log');
 					Common::sms($mobile, 'user_id'.$user_id.'trade_id'.$value['trade_id'].'订单插入失败');
 				}
 			}
		}
	}
	$page++;
	sleep(2);
} while ($total == ($page * $page_size));

echo CRON_SUCCESS;
?>
