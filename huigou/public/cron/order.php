<?php 
include 'common.php';
/**
 * 订单数据
 */
$page = 1;

$session = Gc_Service_Config::getValue('GOU_ADMIN_TOKEN');
$mobiles = Common::getConfig('siteConfig', 'mobiles');

if (!$session) {
	Common::sms($mobiles, '[' . ENV . ']' . '淘宝客session已失效,请及时登录后台.');	
	exit;
}

$topApi = new Api_Top_Service();
do {
	//查询30天以内的订单
	for($i=0; $i<30; $i++) {
		$date = date('Ymd', strtotime("-".$i." day"));
		$ret = $topApi->getTaokeReport(array('page_no'=>1, 'session'=>$session, 'page_size'=>100, 'date'=>$date));
		if ($ret['code']) {
			Common::sms($mobiles, '[' . ENV . ']' . '淘宝客session已失效,请及时登录后台.');
			break;
		}
		$total = $ret['taobaoke_report']['total_results'];
		if ($total) {
			$result = $ret['taobaoke_report']['taobaoke_report_members']['taobaoke_report_member'];
			if ($total == 1) $result  = array($result);
			
			$rate = Gc_Service_Config::getValue('gc_silver_coin_rate') / 100;
			foreach($result as $key=>$value) {
				//查询日志，如果已经返还，不再返还
				$log = Gc_Service_TaokeCoinLog::getCoinLogByTradeId($value['trade_id']);
				if ($log) continue;
				
				//计算返银币数量
				$coin = Common::money($value['commission'] * $value['commission_rate'] * $rate);
				if ($coin == 0) continue;
				
				//解析渠道号和UID
				$out_code = explode("H", $value['outer_code']);
				$uid = $out_code[1];
// 				$uid = 1;
				
				if (!$uid) continue;
				//获了用户信息
				$user = Gc_Service_User::getUser($uid);
				if (!$user) continue;
				$return = Api_Gionee_Pay::coinAdd(array(
						'out_uid'=>$user['out_uid'],
						'coin_type'=>'2',
						'coin'=>Common::money($coin),
						'msg'=>"购买淘客商品，奖励".$value['trade_id']."银币。"));
				Gc_Service_TaokeCoinLog::addCoinLog(array(
					'uid'=>$uid,
					'out_uid'=>$user['out_uid'],
					'trade_id'=>$value['trade_id'],
					'coin_type'=>'2',
					'coin'=>$coin
				));
			}
		}
		
	}
	$page++;
	sleep(2);
} while ($total>($page * 100));

echo CRON_SUCCESS;
?>