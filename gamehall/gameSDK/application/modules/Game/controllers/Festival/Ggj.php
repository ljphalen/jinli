<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 光棍节
 * @author luojiapeng
 *
 */
class Festival_GgjController extends Game_BaseController{
	
	//private  $post_url = 'http://t-pay.gionee.com/payadmin/proxy/hot/present';
	//private  $post_url = 'http://a.game.localhost.gionee.com/api/Test/test';
	private  $post_url = 'http://telemgr.gionee.com/payadmin/proxy/hot/present';
	private  $app_id ='70E62BD218B44EC782D3D17A001B27C8';
	
	/**
	 * 光棍节页面
	 */
	public function indexAction(){

		$pament_list = $this->_payMentList();
		$servicePhone = Game_Service_Config::getValue('game_service_tel');
		$serviceQq = Game_Service_Config::getValue('game_service_qq');
		$this->assign('servicePhone', $servicePhone);
		$this->assign('serviceQq', $serviceQq);
		$this->assign('pament_list', $pament_list);
		$this->assign('source', $this->getSource());
	}
	
	

	/**
	 * 取得积分排行榜
	 */
	public function getPaymentScoreAction(){
		$pament_list = $this->_payMentList();
		echo json_encode($pament_list);
		exit;
		
	}
	
	public function vipAction(){
		$servicePhone = Game_Service_Config::getValue('game_service_tel');
		$serviceQq = Game_Service_Config::getValue('game_service_qq');
		$this->assign('servicePhone', $servicePhone);
		$this->assign('serviceQq', $serviceQq);
	}
	
	private  function _payMentList(){
		$cacheKey = "payment-list-data"; //充值排行榜缓存
		$cache = Cache_Factory::getCache ();
		$cache_data = $cache->get($cacheKey);
		$pament_list = array();
		if(!$cache_data){
			//向支付组发送请求取得数据
			$data = json_encode(array('app_id' => $this->app_id));
			$result = Util_Http::post($this->post_url, $data, array('Content-Type' => 'application/json'));
			$rs_list = json_decode($result->data,true);
			//存入缓存
			$tmp = array();
			foreach ($rs_list['data'] as $val){
				$tmp[] = array('create_time'=>date('m-d H:i',substr($val['rechargeTime'], 0,10)),
						       'recharge_amount'=>intval($val['rechargeAmount']),
						       'mobile_no'=>$val['mobileNo'],
						);
			}
			$pament_list = $tmp;
			if(count($pament_list)){
				$cache->set($cacheKey, $pament_list, 300);//缓存5分钟
			}
		}else{
			$pament_list = $cache_data;
		}
		// 组装数据 小于10条，则补全十条
		$rank_num = count($pament_list);
		$num = array('3','5','7','8');
		$amount= array(3000,1000,300,150,150,30,30,5,5,5);
		$create_time = array('11-21 00:01','11-21 00:02','11-21 00:03','11-21 00:04','11-21 00:05','11-21 00:06','11-21 00:06','11-21 00:08','11-21 00:08','11-21 00:09');
		if($rank_num < 10 ){
			for ( $i = 1; $i <= 10 - $rank_num ;$i ++  ){
				$temp = array('recharge_amount' => ($amount[10-$rank_num-$i] < $pament_list[0]['recharge_amount'])?$pament_list[0]['recharge_amount']:$amount[10-$rank_num-$i],
						'mobile_no'=> '1'. $num[rand(0,3)].rand(2, 9).'****'. rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9),
						'create_time'=> $create_time[10-$rank_num-$i],
				);
				array_unshift($pament_list,$temp);
			}
		}
		return $pament_list;
	}
	
	/**
	 * 统计跳转
	 */
	public function tjAction(){
		$url = html_entity_decode(html_entity_decode($this->getInput('_url')));
		if (strpos($url, '?') === false) {
			$url = $url.'?t_bi='.$this->getSource();
		} else {
			$url = $url.'&t_bi='.$this->getSource();
		}
		$this->redirect($url);
	}
	

}