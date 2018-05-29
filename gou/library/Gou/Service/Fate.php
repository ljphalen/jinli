<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Gou_Service_Fate{
	
	/**
	 * Enter description here ...
	 */
	public static function fate($data) {
		$data = self::_cookData($data);
		if (Common::isError($data)) return $data;
		
		$count = Gou_Service_FateLog::getCountByMobile($mobile, $start, $end);
		//获取抽奖规则
		
		$rule = self::_getRule();
		if (!$rule) {
			self::_updatFateInfo($data, false);
			return Common::formatMsg(-1, '啊偶，差一点就中奖了，再接再厉！');
		}
		
		//缓存控制
		$fatedKey = $data['mobile'] . Util_Http::getClientIp() . 'fated';
		$cache = Common::getCache();
		
		if ($cache->get($fatedKey)) {
			return Common::formatMsg(-1, '啊偶，差一点就中奖了，再接再厉！');
		}
		
		//生成奖池
		$pool = self::_getFatePool($rule['rate']);
		$fate_num = self::_getFateNum();
		
		//更新用户数据
		$data['rule_id'] = $rule['id'];
		if (in_array($fate_num, $pool)) {
			$data['price'] = $rule['price'];
			$data['status'] = 1;
			$data['fate_times'] = 1;
			self::_updatFateInfo($data, true);
			return $rule;
		}
		self::_updatFateInfo($data, false);
		return Common::formatMsg(-1, "啊偶，差一点就中奖了，再接再厉！");
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return multitype:unknown_type |unknown
	 */
	public static function checkFate($data) {
		$data = self::_cookData($data);
		if (Common::isError($data)) return $data;
		
		$logs = Gou_Service_FateLog::getFateLogsByMobile($data['mobile']);
		if (count($logs) == 0) return Common::formatMsg(-1, '手机号码未参加过抽奖.');
		
		foreach ($logs as $key=>$value) {
			if ($data['mobile'] == $value['mobile'] && $data['question'] == $data['question'] &&
					$data['answer'] == $value['answer']) {
				Gou_Service_FateLog::updateFateLog(array('status'=>2, 'confirm_time'=>Common::getTime()), $value['id']);
				return $value;
			} 
		}
		return Common::formatMsg(-1, '通关密语输错咯，再试一次?');
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return multitype:unknown_type |unknown
	 */
	private static function _cookData($data) {
		if (!$data['mobile']) return Common::formatMsg(-1, "手机号码不能为空.");
		if (!preg_match('/^1[3458]\d{9}$/', $data['mobile'])) return Common::formatMsg(-1, "手机号码格式不正确");
		if (!$data['question']) return Common::formatMsg(-1, '通关密语不能为空.');
		if (!$data['answer']) return Common::formatMsg(-1, '通关密语答案不能为空.');
		return $data;
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return Ambigous <boolean, number>|boolean
	 */
	private static function _updatFateInfo($data, $isfate) {
		Gou_Service_FateLog::addFateLog($data);
		$user = Gou_Service_FateUser::getByMobile($data['mobile']);
		if ($user) {
			if ($isfate) $updata['fate_times'] = $user['fate_times'] + 1;
			$updata['total_times'] = $user['total_times'] + 1;
			return Gou_Service_FateUser::updateFateUser($updata, $user['id']);
			if (!$ret) return false;
		}
		return Gou_Service_FateUser::addFateUser($data);
	}
	
	/**
	 * 获取奖池
	 * @param unknown_type $rate
	 */
	private static function _getFatePool($rate) {
		$pool = array();
		while(count($pool) < $rate){ 
		   $num = rand(0, 99); 
		   if(!in_array($num, $pool)){ 
		      $pool[] = $num; 
		   } 
		} 
		return $pool;
	}
	

	/**
	 * 
	 * @return number
	 */
	private static function _getFateNum() {
		return rand(0, 99);
	}
	
	/**
	 * 
	 * @return Ambigous <NULL, multitype:>
	 */
	private static function _getRule() {
		//获取今天的抽奖日志
		$today = Common::getTime();
		$tomorow = strtotime("+1 day", $today);
		
		$start = mktime(0, 0, 0, date('m', $today), date('d', $today), date('y', $today));
		$end = mktime(0, 0, 0, date('m', $tomorow), date('d', $tomorow), date('y', $tomorow));
		
		$logs = Gou_Service_FateLog::getFateLogsByTime($start, $end);
		if (count($logs) > 0) {
			$rule_used = array();
			foreach ($logs as $key=>$value) {
				if (!isset($rule_used[$value['rule_id']]))  $rule_used[$value['rule_id']] = 0;
				$rule_used[$value['rule_id']] ++ ;
			}
		}
		
		//获取抽奖规则
		list(,$rules) = Gou_Service_FateRule::getAllFateRule();
		$rule_can_use = array();
		if (count($logs) > 0) {
			//计算可用抽奖规则
			foreach ($rules as $key=>$value) {
				if (!in_array($value['id'], array_keys($rule_used))) {
					$rule_can_use[] = $value;
				} else if($rule_used[$value['id']] < $value['times']){
					$rule_can_use[] = $value;
				}
			}
		} else {
			$rule_can_use = $rules;
		}
		if (count($rule_can_use) == 0) return false;
		if (count($rule_can_use) == 1) return $rule_can_use[0];
		$rand = rand(0, count($rule_can_use) - 1);
		return $rule_can_use[$rand];
	}
}
