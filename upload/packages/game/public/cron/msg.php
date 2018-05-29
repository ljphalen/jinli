<?php
include 'common.php';
/**
 * 查找将过期的A券消息
 */

$params = $temp =  $tmp = array();
$str = '';

/**
 * 获取支付返回的A券剩余额
 */
function backTicketRemain($uuid){
	//取得支付配置
	$params = $list = array();
	$payment_arr = Common::getConfig('paymentConfig','payment_send');
	$ciphertext = $payment_arr['ciphertext'];
	$tmp['api_key'] = $payment_arr['api_key'];
	$tmp['uuid']    = $uuid;
	$tmp['token']   =  md5($ciphertext.$tmp['api_key'].$uuid);

	//post到支付服务器
    $json_data = json_encode($tmp);
	$rs = Util_Http::post($payment_arr['coin_url'], $json_data, array('Content-Type' => 'application/json'), 2);
	$aTickNum = 0;
	if( $rs->data){
		$rs_list = json_decode($rs->data,true);
		//A券
		if($rs_list['voucher'] > 0){
			$aTickNum = number_format($rs_list['voucher'], 2,'.', '');
		}
   } 
   return $aTickNum;
}

//添加消息
function addMsgData($data){
	return Game_Service_Msg::addApiMsg($data);
}

//判断A券消息开关
$switch = Game_Service_Msg::checkAddMsg(102);
if(!$switch) exit("tickets expire switch close\r\n");

//查找前一天即将过期的A券
$curr_time = date('Y-m-d',strtotime("+1 day"));
$start_time = strtotime($curr_time.' 00:00:00');//time();
$end_time = strtotime($curr_time.' 23:59:59');
$params['end_time'][0] = array('>=', $start_time);
$params['end_time'][1] = array('<=', $end_time);

$tickets = Client_Service_TicketTrade::getsBy($params, array('id'=>'DESC')); 
if(empty($tickets)) exit("not found tickets\r\n");

print_r($params);

$checkTickets = array();
foreach($tickets as $k=>$v){
	$vTicketsEndTime = date('Y-m-d',$v['end_time']);
	$vTicketsEndTime = strtotime($vTicketsEndTime.' 23:59:59');
	if($vTicketsEndTime - $v['start_time'] >= 24 * 60 * 60){  //有效期1天内的不产生提醒消息
		$checkTickets[$v['uuid']]['num'][] = $v;
		$checkTickets[$v['uuid']]['uuid'] = $v['uuid'];
	}
}
print_r($checkTickets);

if(!$checkTickets) exit;
foreach($checkTickets as $key=>$value){
	$num = 0;
	$num = backTicketRemain($value['uuid']);
	
	$time = time();
	$tite = '您有'.$num.'A券即将过期！';
	$msg = $curr_time.'日24点过期';
	$data = array(
			'type' =>  102,
			'top_type' =>  100,
			'totype' =>  1,
			'title' =>   $tite,
			'msg' =>  $msg,
			'status' =>  0,
			'start_time' =>  $time,
			'end_time' =>  strtotime('2050-01-01'),
			'create_time' =>  $time,
			'sendInput' =>  $value['uuid'],
	);
	
	if($num){ //A券还有余额
		$currentSeverTime = date('Y-m-d', $time);
		$search = array();
		$search['uid'] = $value['uuid'];
		$search['type'] = 102;
		$search['expire_status'] = 0;
		$start_time = strtotime($currentSeverTime.' 00:00:00');
		$end_time = strtotime($currentSeverTime.' 23:59:59');
		$search['create_time'][0] = array('>=', $start_time);
		$search['create_time'][1] = array('<=', $end_time);
		
		$ret = Game_Service_Msg::getMap($search, array('create_time'=>'DESC','id'=>'DESC'));
		print_r($search);
		print_r($ret);
		$currentReadTime = $ret['read_time'] ? date('Y-m-d', $ret['read_time']) : '';
		if(!$ret || ($currentReadTime < $currentSeverTime  && $currentReadTime && $ret)){     
			//如果不存在
			print_r($data);
			$msgId = addMsgData($data);
			
			//设置该账号作废的过期消息
			$parmes = array();
			$parmes['uid'] = $value['uuid'];
			$parmes['type'] = 102;
			$parmes['status'] = 0;
			$parmes['msgid'] = array('<',$msgId);
			$retp = Game_Service_Msg::updateMapBy(array('expire_status'=>1),$parmes);
		} else if(!$currentReadTime && $ret && !$ret['status']){
			//当天有没读取的消息
			$retm = Game_Service_Msg::updateBy(array('title'=>$tite,'msg'=>$msg),array('id'=>$ret['msgid']));
			
			//设置该账号作废的过期消息
			$parmes = array();
			$parmes['uid'] = $value['uuid'];
			$parmes['type'] = 102;
			$parmes['status'] = 0;
			$parmes['msgid'] = array('<',$ret['msgid']);
			$retp = Game_Service_Msg::updateMapBy(array('expire_status'=>1),$parmes);
		}		
	} else {
		$rets = Game_Service_Msg::getsMaps(array('uid'=>$value['uuid'],'type'=>102,'expire_status'=>0), array('create_time'=>'DESC','id'=>'DESC'));
		foreach($rets as $k1=>$v1){
			if(!$ret['read_time'] && $ret){
				//设置该账号作废的过期消息
				$parmes = array();
				$parmes['uid'] = $value['uuid'];
				$parmes['type'] = 102;
				$parmes['status'] = 0;
				$parmes['msgid'] = $v1['msgid'];
				$retp = Game_Service_Msg::updateMapBy(array('expire_status'=>1),$parmes);
			
			}
		}
	}
	echo "{$num} -- {$value['uuid']} -- ok\r\n";
}

echo CRON_SUCCESS;