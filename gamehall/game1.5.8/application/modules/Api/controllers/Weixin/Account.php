<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class WeiXin_AccountController extends Api_BaseController {

    const ACCOUNT_SECRET_KEY = 'f84c04c1611366ce43d3cd07b8c65217';
    
	 /**
	  * 我的A币和A券的总数,快要过期的A券额度
	  */
	 public function myBalanceAction(){
	 	$uuid = trim($this->getInput('puuid'));
	 	$token =  strtolower(trim($this->getInput('token')));
	 	if (!$uuid || !$token || ($token != $this->getToken($uuid))) {
	 		$this->clientOutput(array(
	 		                'success' => false,
	 		                'msg' => '非法请求',
	 		));
	 	}
	 	
	 	$rs_list = $this->getBalanceRemote($uuid);
	 	$data['data']  = array();
 		//A券
 		$data['data']['ATick'] = $rs_list['voucher']?strval(number_format($rs_list['voucher'], 2, '.', '' )):strval(0);
 		//A币
 		$data['data']['ACoin'] = $rs_list['gold_coin']?strval(number_format($rs_list['gold_coin'], 2, '.', '')):strval(0);
 		
 		$params = array('uuid' => $uuid);
 		$userInfo = Account_Service_User::getUserInfo($params);
 		$data['data']['uname'] = $userInfo['uname'];
 		$data['data']['nickName'] = html_entity_decode($userInfo['nickname']);
 		
 		if ($data['data']['ATick'] > 0) {
 		    $data['data']['willExpireTick'] = strval($this->getWillExpireTickTotal($uuid));
 		} else {
 		    $data['data']['willExpireTick'] = '0';
 		}
	 	$this->clientOutput($data);
	 }	
	 
	 private function getToken($uuid) {
	     return md5(self::ACCOUNT_SECRET_KEY.$uuid);
	 }
	 
	 /**
	  * post到支付服务器
	  * @author yinjiayan
	  * @param unknown $uuid
	  * @return mixed
	  */
	 private function getBalanceRemote($uuid) {
	     $payment_arr = Common::getConfig('paymentConfig','payment_send');
	     $api_key    = $payment_arr['api_key'];
	     $url      = $payment_arr['coin_url'];
	     $ciphertext= $payment_arr['ciphertext'];
	     //加密的密文
	     $params['api_key'] = $api_key;
	     $params['uuid']    = $uuid;
	     $token = md5($ciphertext.$api_key.$uuid);
	     $params['token']   = $token;
	     $json_data = json_encode($params);
	     $result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));
	     return json_decode($result->data,true);
	 }
	 
	 private function getWillExpireTickTotal($uuid) {
	     $tomorrowEnd = Util_TimeConvert::floor(strtotime('+2 day'), Util_TimeConvert::RADIX_DAY);
	     $dataList = Client_Service_TicketTrade::getListByEndTime($uuid, time(), $tomorrowEnd);
	     $total = 0;
	     if ($dataList) {
	     	foreach ($dataList as $tick){
	     	    $balance = $tick['denomination'] - $tick['use_denomination'];
	     	    $total += $balance;
	     	}
	     }
	     return $total;
	     
	     list($total,$dataList) = Client_Service_TicketTrade::getList($page,$this->perpage,array('status'=>1,'uuid'=>$uuid),array('end_time'=>'DESC'));
	     $params = $temp =  array();
	     $str = '';
	     foreach ($dataList as $v){
	         $temp[]['ano'] = $v['out_order_id'];
	         $str .=$v['out_order_id'];
	     }
	 }
}