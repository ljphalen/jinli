<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Fate extends Common_Service_Base{
	//百万级概率
	private static $_maxRate = 1000000;
	/**
	 * 开始抽奖
	 * Enter description here ...
	 */
	public static function fate($data) {
		if (!$data['uname']) return array();
		
		$time = Common::getTime();
		$imeicrc = crc32($data['imei']);
		
		$lottery_data = array(
				'thePrizeName'=>'无奖品',
				'thePrizeState'=>0,
				'thePrizeIndex'=>-1,
				'thePrizeChance'=>-1,
				'thePrizeUname'=>$data['uname'],
				'thePrizeCode'=>''
		);
			
		$log_data = array(
				'create_time'=>$time,
				'status'=>0,
				'lottery_id'=>0,
				'duijiang_code'=>''
		);
		
		//检测用户是否在线
		$online_status = Account_Service_User::checkOnline($data['uname'], $data['imei']);
		if(!$online_status) return $lottery_data;
		$lottery_data['thePrizeState'] = 1;
		
		//获取抽奖规则
		$activityInfo = Client_Service_Activity::getOnlineActivityInfo();
		if(!$activityInfo) return '';
		
		//当前正在抽奖的活动id和接口传过来的不一样
		if($activityInfo['id'] != $data['activity_id']) return '';
		
		//判断当前传过来的版本比线上的版本还低
		if(strnatcmp($data['version'], $activityInfo['min_version']) < 0) return '';
		
		/*
		//当前抽奖的状态(抽奖未开始为-1，抽奖开始为0，抽奖抽奖已经结束为1)
		$activity_status = self::_activityData($activityInfo);
		//当前活动没有开始或者活动已经结束
		if($activity_status == 2) return '';
		*/
			
		//获取用户当天日志
		$logs = Client_Service_FateLog::getFateLogsByUname($data['uname'],$activityInfo['id']);
		
		/*
		//当前抽奖没有开始或者抽奖已经结束
		if($activity_status == -1 || $activity_status == 1) {
			$lottery_data['thePrizeState'] = $activity_status;
			$lottery_data['thePrizeChance'] = $activityInfo['number'] - count($logs);
			return $lottery_data;
		}*/
		
		//开始事务
		$trans = parent::beginTransaction();
		try {
			
			
			//判断用户是否超过抽奖次数限制
			if (count($logs) >= $activityInfo['number']) return $lottery_data;
			
			//剩余抽奖次数
			$lottery_data['thePrizeChance'] =  $activityInfo['number'] - count($logs) - 1;
			
			//写抽奖日志
			$usr_log = array(
					'activity_id'=>$activityInfo['id'],
					'lottery_id'=>0,
					'uname'=>$data['uname'],
					'imei'=>$data['imei'],
					'imeicrc'=>$imeicrc,
					'create_time'=>'',
					'status'=>0,
					'grant_status'=>0,
					'grant_time'=>'',
					'duijiang_code'=>'',
					'remark'=>'',
					'label_status'=>0
					);
			
			$log_id = Client_Service_FateLog::addFateLog($usr_log);
			if (!$log_id) throw new Exception('Add FateLog fail.', -203);
						
			//抽将开始
			$jiangxian = array();
			$jianx_info = Client_Service_FateRule::getFateRuleByActivityId($activityInfo['id']);

			//组装中奖数组
			$prize_info = self::_initPrize($jianx_info);
			//初始化活动奖池数据
			$config = self::_initConfig($prize_info,$activityInfo['id']);
			//抽奖 获取奖项(0为未中奖，1为一等奖，2为二等奖，3为三等奖)
			$prize = self::_process($config);
			

			//判断边界必中奖
			foreach($config as $k=>$v){
				if($v['p'] == self::$_maxRate){
					$prize = $k;
				}
			}
			
			
			//中奖,更新用户数据
			if ($prize) {
				//取出某一个奖项的中奖时间间隔
				$space_time = $jianx_info[$prize - 1]['space_time'];
				//取出该奖品的当天奖品数量
				$num = $jianx_info[$prize - 1]['num'];
				//取出该奖品的奖品名称
				$award_name = $jianx_info[$prize - 1]['award_name'];
				//查找该奖项当天中奖的数量
				$jx_num = Client_Service_FateLog::countFateLogsEveryByLotteryId($activityInfo['id'],$prize);
				//判断奖品是否还有,奖品数量为0，即未中奖
				if($jx_num >= $num){
					//未中奖-更新日志状态
					$ret = self::_updateFateLog($log_data,$log_id);
					if (!$ret) throw new Exception('Update FateLog fail.', -205);
				} else {
					//先取出该奖项最后中奖的时间
					$log = Client_Service_FateLog::getFateLogsByLotteryId($activityInfo['id'],$prize);
					//(该奖项还没中出,那么该奖项中奖的时间为：该奖项发布的开始时间加上该奖项中奖的时间间隔;
					//已经中出,那么该奖项中奖的时间为：该奖项最后一次中奖时间加上该奖项中奖的时间间隔)
					$start_time = ($log ? $log['create_time'] : $activityInfo['start_time']);
					//判断该奖项是否在限定的时间间隔内出现(该奖项开始时间加上该奖项中奖时间间隔,大于当前时间,未中奖.反之，中奖)
					$flag = (($start_time + $space_time) > $time ? 1 : 0);
					
					if($flag){
						//未中奖-更新日志状态
						$ret = self::_updateFateLog($log_data,$log_id);
						if (!$ret) throw new Exception('Update FateLog fail.', -206);
					} else {
						//中奖-更新日志状态
						$log_data['status'] = 1;
						$log_data['lottery_id'] = $prize;
						$log_data['duijiang_code'] =  self::getMakeStr(6);
						$ret = self::_updateFateLog($log_data,$log_id);
						if (!$ret) throw new Exception('Update FateLog fail.', -207);
						//更改中奖数组的值
						$lottery_data['thePrizeName'] = $award_name;
						$lottery_data['thePrizeIndex'] = $prize - 1;
						$lottery_data['thePrizeCode'] = $log_data['duijiang_code'];
					}
				}
				
			}
			//未中奖,更新用户数据
			$ret = self::_updateFateLog($log_data,$log_id);
			if (!$ret) throw new Exception('Update FateLog fail.', -208);
		
		//事务提交
		if($trans)  parent::commit();
		return $lottery_data;
		
		} catch (Exception $e) {
			parent::rollBack();
			//剩余抽奖次数
			$lottery_data['thePrizeChance'] =  $activityInfo['number'] - count($logs);
			return $lottery_data;
		}
	}
	
	/**
	 * 中奖，未中奖都写入日志
	 * @param unknown_type $data
	 * @param unknown_type $log_id
	 * @return Ambigous <boolean, number>
	 */
	private static function _updateFateLog($data, $log_id) {
		return 	Client_Service_FateLog::updateFateLog($data,$log_id);
	}
	
	/**
	 * 组装中奖数组
	 * @param unknown_type $data
	 * @return multitype:multitype:unknown
	 */
	private static function _initPrize($data){
		$config = array();
		foreach($data as $key=>$value){
			$config[$key+1] = array(
					 'p'=>$value['probability'],
					 'n'=>$value['num'],
					);
		}
		return $config;
	}
	
	/**
	 * 根据奖项配置进行抽奖,返回中奖奖项
	 * @param array $config
	 * $config = array(
	 *			1 =>array(
	 *				    'p'=>3,
	 *					'n'=>0
	 *					),
	 *			2 =>array(
	 *					'p'=>97,
	 *					'n'=>9
	 *			),
	 *			3 =>array(
	 *					'p'=>0,
	 *					'n'=>30
	 *			),
	 *	);
	 *@return int
	 */
	
	private static function _process($config){
		$max = self::$_maxRate-1;
		$luck = mt_rand(0, $max);
		$result = $range = 0;
		foreach( $config as $key => $value ){
			if (($value['p'] == 0) || ($value['n'] == 0)) continue;
			if (($luck >= $range) && ($luck < $range + $value['p'])) {
				$result = $key;
				break;
			} else {
				$range += $value['p'];
			}
		}
		return $result;
	}
	
	/**
	 * 获取随机中英文数值作为兑奖码
	 * @param unknown_type $length
	 * @return string
	 */
	private static function getMakeStr($length)
	{
		$possible = "0123456789"."abcdefghijklmnopqrstuvwxyz"."ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$str = "";
		while (strlen($str) < $length){
			$str .= substr($possible, (rand() % strlen($possible)), 1);
		}
		return $str ;
	}
	/**
	 * 初始化奖项配置参数
	 * @param unknown_type $prizeData
	 * @param unknown_type $imeicrc
	 * @return number
	 */
	private static function _initConfig($prizeData,$activity_id){
		//获取奖项配置参数
		$curr_time = date('Y-m-d',Common::getTime());
		$start_time = strtotime($curr_time.' 00:00:00');
		$end_time = strtotime($curr_time.' 23:59:59');
		$params = array(
				'activity_id' => $activity_id,
				'lottery_id' => array('!=', 0),
				'create_time' => array(
						array('>=', $start_time), array('<=', $end_time)
				),
		);
		//分别计算出每个奖项当天的中奖数量
		$prizeNums = Client_Service_FateLog::groupBy($params);
		if($prizeNums){
			$prizeNums = Common::resetKey($prizeNums, 'lottery_id');
			//动态配置抽奖参数
			foreach ($prizeNums as $key => $value){
				$tmp = ($prizeData[$key]['n'] != 0 && $prizeData[$key]['n'] >0) ? ($prizeData[$key]['n']- $value['num']) : 0 ;
				$prizeData[$key]['n'] = ($tmp == 0) ? 0 : $tmp;
			}
		}
		return $prizeData;
	}
	
	/**
	 * 抽奖的状态
	 * @param unknown_type $data
	 * @return number
	 */
	private  static function _activityData($data) {
		if(!$data)  return 2;
		$curr_time = Common::getTime();
		//活动未开始
		if( $data['online_start_time'] > $curr_time || $data['online_end_time'] < $curr_time ) return 2;
		//抽奖未开始
		if( $data['online_start_time'] <= $curr_time  && $data['start_time'] > $curr_time ) return -1;
		//抽奖开始
		if( $data['start_time'] <= $curr_time  && $data['end_time'] >= $curr_time ) return 0;
		//抽奖已经结束
		if( $data['end_time'] < $curr_time && $data['online_end_time'] >= $curr_time ) return 1;
	}
}
