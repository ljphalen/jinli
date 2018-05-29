<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 *双蛋节
 * @author luojiapeng
 *
 */
class Festival_ChristmasController extends Game_BaseController{	
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
	public function getConsumeRankAction(){
		$pament_list = $this->_payMentList();
		echo json_encode($pament_list);
		exit;
		
	}
	
	public function vipAction(){
		
	}
	
	/**
	 * 组装数据
	 * @return Ambigous <multitype:, multitype:multitype:string number unknown  , unknown>
	 */
	
	private  function _payMentList(){
		$cacheKey = "cousume-list-data"; //充值排行榜缓存
		$cache = Cache_Factory::getCache ();
		$cache_data = $cache->get($cacheKey);
		$pament_list = array();
		if(!$cache_data){
			$params['status'] = 1 ;
			$params['send_type'] = 3 ;
			
			if(Common::getTime() >= 1419350400  && Common::getTime() <= 1419436799){
				$params['sub_send_type'] = 21 ;
			}elseif (Common::getTime() >= 1419436800 && Common::getTime() <= 1419523199){
				$params['sub_send_type'] = 22 ;
			}elseif (Common::getTime() >= 1419523200 && Common::getTime() <= 1419609599){
				$params['sub_send_type'] = 23 ;
			}
			$rs_list = Client_Service_TicketTrade::getConsumeRank(1, 10, $params);
			//存入缓存
			$tmp = array();
			foreach ($rs_list as $val){
				//用户名
				$account = Account_Service_User::getUser(array('uuid'=> $val['uuid']));
				$tmp[] = array('update_time'=> date('m-d H:i',substr($val['update_time'], 0, 10)),
						       'denomination'=> intval($val['denomination']),
						       'uname'=> substr($account['uname'], 0, 3).'****'. substr($account['uname'], 7, 4),
						);	
			}
			$pament_list = $tmp;
			if(count($pament_list)){
				$cache->set($cacheKey, $pament_list, 10);//缓存5分钟
			}
		}else{
			$pament_list = $cache_data;
		}
	
		// 组装数据 小于10条，则补全十条
		$rank_num = count($pament_list);
		$num = array('3','5','7','8');
		$amount= array(4850,850,350,350,350,100,100,100,30,30);
		$create_time = array('11-21 00:01','11-21 00:02','11-21 00:03','11-21 00:04','11-21 00:05','11-21 00:06','11-21 00:06','11-21 00:08','11-21 00:08','11-21 00:09');
		if($rank_num < 10 ){
			for ( $i = 1; $i <= 10 - $rank_num ;$i ++  ){
				$temp = array('denomination' => ($amount[10-$rank_num-$i] < $pament_list[0]['denomination'])?$pament_list[0]['denomination']:$amount[10-$rank_num-$i],
						'uname'=> '1'. $num[rand(0,3)].rand(2, 9).'****'. rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9),
						'update_time'=> $create_time[10-$rank_num-$i],
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