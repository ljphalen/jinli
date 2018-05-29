<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 抽奖活动
 * @author lichanghua
 * 
 */
class Festival_Service_Monprize extends Common_Service_Base{
	
	/**
	 * 产生唯一13位的数字userID 
	 */
	public static function generateUUID(){
		static $being_timestamp = 1409846400;// 2014-09-05
		$time = explode(' ', microtime());
		$id = ($time[1] - $being_timestamp) . sprintf('%04u', substr($time[0], 2, 5));
		return $id;
	}
	
	/**
	 * 抽奖资格判断
	 * @param unknown $uuid
	 */
	public static function checkChance($uuid){
		$flag = 1;
		$curr_time = date('Y-m-d',Common::getTime());
		$start_time = date('Y-m-d H:i:s', strtotime($curr_time.' 00:00:00'));
		$end_time = date('Y-m-d H:i:s',  strtotime($curr_time.' 23:59:59'));
		$params = array(
				'user_id' => intval($uuid),
				'create_time' => array(
						array('>=', $start_time), array('<=', $end_time)
				),
		);
		//判断当天是否中奖
		$log = Festival_Service_Log::getByLog($params);
		//已中过奖不再有抽奖机会
		if($log) $flag = 0;
		return $flag;		
	}
	
	/**
	 * 开始抽奖
	 * @param int $uuid
	 * @param string $day
	 * @param int $num
	 * @param array $config
	 */
	public static function start($uuid, $day, $num, $config){
		$cacheKey = "index-lot-".$day;
		$cache = Cache_Factory::getCache ();
		$data = $cache->get($cacheKey );
		
		//前天
		$before = date("Y-m-d",strtotime($day." -1 day"));
		//昨天
		$yesterday = date("Y-m-d",strtotime($day." -2 day"));
		
		$before_cacheKey = "index-lot-".$before;
		$yesterday_cacheKey = "index-lot-".$yesterday;
		
		$data_before = $cache->get($before_cacheKey );
		$data_yesterday = $cache->get($yesterday_cacheKey );
		
				
		if ($data || $data_before || $data_yesterday) {
			$prize = self::_getPoolDate($data,$day);
		} else {
		    $start_time = strtotime($day.' 00:00:00');
		    $end_time = strtotime($day.' 23:59:59');
			$params = array(
					'prize' => array('!=',0),
					'create_time' => array(
							array('>=', $start_time), array('<=', $end_time)
					),
			);
			$prize_num = Festival_Service_Log::getByLog($params);
			
			//当天的奖品已经抽完
			if($prize_num == $num)  return 0;
			
			//生成当天中奖时间点
			$prize_time = self::_prizeDate($day, $num);
			$store = self::_getStore($config);
			
			//初始化生成奖池
			$pool = array_combine($prize_time, $store);
			foreach($pool as $key=>$value){
				$pool[$key] = $key.",".$value;
			}
			
			$cache->set ( $cacheKey, $pool, 3*60*60*24 );
			//抽奖
			$prize = self::_getPoolDate($pool, $day);
		}
		return $prize;
	}
	
	/**
	 * 获取奖品
	 * @param unknown_type $pool
	 * @return Ambigous <>|number
	 */
	private static function _getPoolDate($pool, $day){
		$cache = Cache_Factory::getCache ();
		$cacheKey = "index-lot-".$day;
		
		//前天
		$before = date("Y-m-d",strtotime($day." -1 day"));
		//昨天
		$yesterday = date("Y-m-d",strtotime($day." -2 day"));
		
		$before_cacheKey = "index-lot-".$before;
		$yesterday_cacheKey = "index-lot-".$yesterday;
		
		$data_before = $cache->get($before_cacheKey );
		$data_yesterday = $cache->get($yesterday_cacheKey );
		
		//var_dump($data_before,$data_yesterday);
		
		//前天，昨天如果奖品还有的话，就从前天，昨天的奖品里面给
		$time = Common::getTime();
		if($data_before || $data_yesterday){
			$curr_jp = ($data_before ? $data_before : $data_yesterday);
			$curr_cacheKey = ($data_before ? $before_cacheKey : $yesterday_cacheKey);
			$jp = current($curr_jp);
			//echo "<pre>";
			//print_r($jp);
			$jp = explode(',',$jp);
			if(array_key_exists($jp[0],$curr_jp) && $jp[0]){
				unset($curr_jp[$jp[0]]);
				$cache->set ( $curr_cacheKey, $curr_jp, 3*60*60*24 );
				//echo $jp[1];
				return $jp[1];
 			}
		} else {
			//前天，昨天都没有的话，就从当前奖池取
			$jiangp = current($pool);
			$jiangp = explode(',',$jiangp);
			if($jiangp[0] <= $time && array_key_exists($jiangp[0],$pool) && $jiangp[0]){  //中奖
				unset($pool[$jiangp[0]]);
				$cache->set ( $cacheKey, $pool, 3*60*60*24 );
				return $jiangp[1];
			} else {                  //不中奖
				return 0;
			}
		}
		
		
	}
	
	/**
	 * 生成中奖时间点 (每天６点到23点之间)
	 * @param unknown_type $day
	 * @param unknown_type $num
	 * @return number
	 */
	private static function _prizeDate($day,$num=150){
		for($i=0;$i<$num;$i++){
			//第一，二天中奖时间间隔为432，第三天中奖时间间隔为429;
			$start = strtotime($day . "06:00:00");//当天6点时间戳开始
			$loop = ($num == 150 ? 432 : 429);
			$prize[] = $start + $i * $loop;
		}
		return $prize;
	}
	
	/**
	 * 每天的奖品库
	 * @param unknown_type $config
	 * @return multitype:unknown
	 */
	private static function _getStore($config){
		$store = array();
		foreach ($config as $key => $value){
			for ($i = 0; $i < $value; $i++){
				$store[] = $key;
			}
		}
		shuffle($store);
		return $store;
	}
}
