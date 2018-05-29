<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Service_Process
 * @author fanch
 *
 */
class Freedl_Service_Process extends Common_Service_Base{
	
	/**
	 * 日志汇总逻辑处理加入事务
	 * @param array $value
	 * @return boolean
	 */
	public static function cronHandle($value){
		//数据刷新时间
		$value['refresh_time'] = Common::getTime();
		//获取处理的活动数据
		$hd = Freedl_Service_Hd::getFreedl($value['activity_id']);
		if(!$hd) return array(false, false); //原始日志数据异常丢弃
		//获取游戏数据
// 		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
		//获取免流量领取信息
		$receive = Freedl_Service_Receive::getBy(array('activity_id'=>$value['activity_id'], 'imsi'=>$value['imsi']));
		if(!$receive) return array(false, false); //原始日志数据异常丢弃
		//填充领取记录中的用户信息到日志数据中
// 		$value['uuid'] = $receive['uuid'];
// 		$value['uname'] = $receive['uname'];
// 		$value['nickname'] = $receive['nickname'];
		//imsi解析
		$imsiData = Freedl_Service_Imsi::convert($value['imsi']);
		if(!$imsiData) return array(false, false); //原始日志数据异常丢弃
		//活动黑名单规则
		$hdRule = unserialize($hd['blacklist_rule']);
		//黑名单标识
		$flag = false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			switch ($value['ntype']){
				case 1://2g网络
				case 2://3g网络
				case 3://4g网络
					//用户明细(游戏下载|流量消耗)处理
					self::_freedlUserHandel($value, $hd, $receive);
					//用户流量汇总处理
					self::_freedlUseTotalHandel($value, $hd);
					//imsi流量汇总
					self::_freedlImsiTotalHandel($value);
					//imsi用户明细
					self::_freedlUserImsi($value, $hd);
					//月流量汇总
					self::_freedlMonthTotal($value);
					//运营商月流量汇总
					self::_freedlOperatorTotal($value, $imsiData);
					//游戏月流量消耗
					self::_freedlGameTotal($value, $hd);
					//免流量活动参与的人数
					self::_freedlUsers($value, $imsiData);
					//处理黑名单
					list($flag, $msg) = self::_blackListHandle($hdRule, $value);
					if($flag){
						//入库黑名单
						self::_addBlackData($value, $msg);
					}
					//汇总活动流量数据
					self::_freedlHdData($value, $hd);
					break;
				case 4://wifi网络
					//汇总活动流量数据
					self::_freedlHdData($value, $hd);
					break;
			}
			//事务提交
			$result = ($trans) ? parent::commit() : false;
				
			return array($result, $flag);
		} catch (Exception $e) {
			parent::rollBack();
			return array(false, false);
		}
	}
	
	/**
	 * 添加imsi到黑名单Cache中2-5分钟延迟
	 * @param string $imsi
	 * 
	 * cdata 数据结构 黑名单用户永久有效
     * array(
	 * 	'imsi'=>array(imsi值,...),
	 * 	'uuid'=>array(uuid值,...),
	 * 	'imei'=>array(imei值,...)
	 * )
	 */
	public static function addFreedlBlackCache($imsi){
		$cache = Common::getCache();
		$ckey = '-freedl-black';
		$cdata = $cache->get($ckey);
		if(($cdata != false) && (!in_array($imsi, $cdata['imsi']))){
			//加入imsi到该活动的imsi黑名单cache中
			array_push($cdata['imsi'], $imsi);
			$cache->set($ckey, $cdata, 3*60);//cache数据为1天有效
		}
	}
	
	/**
	 * 获取用户流量数据
	 * @param string $imsi
	 */
	public static function getFreedlUserCache($imsi){
		$cache = Common::getCache();
		$ckey = '-fdl-' . $imsi;
		$cdata = $cache->get($ckey);
		if($cdata == false){
			$ret = Freedl_Service_Usertotal::getBy(array('imsi' => $imsi));
			$cdata = ($ret) ? intval($ret['total_consume']) : 0;
			$cache->set($ckey, $cdata, 3*60);//cache数据为1天有效
		}
		return $cdata;
	}
	
	/**
	 * 活动主表数据汇总
	 * @param array $log
	 */
	private static function _freedlHdData($log, $hd){
		$data = array();
		if(in_array($log['ntype'], array(1,2,3))){ //手机流量汇总
			if ($log['upload_size']) $data['phone_consume'] = $hd['phone_consume'] + $log['upload_size'];
		}
		if($log['ntype'] == 4){ ////wifi流量汇总
			if ($log['upload_size']) $data['wifi_consume'] = $hd['wifi_consume'] + $log['upload_size'];
		}
		//活动下载量统计
		$dl = Freedl_Service_Userinfo::countByDlTimes($log['activity_id']);
		if($dl) $data['download'] = $dl;
		$data['refresh_time'] = $log['refresh_time'];
		//更新活动数据
		if($data) Freedl_Service_Hd::updateByFreedl($data, array('id'=>$hd['id']));
	}
	
	/**
	 * 黑名单数据入库
	 * @param array $log
	 * @param string $msg
	 */
	private static function _addBlackData($log, $msg){
		$data = array(
				'utype' => '1', //黑名单自动处理
				'activity_id'=>$log['activity_id'],
				'imsi' => $log['imsi'],
				'uuid' => $log['uuid'],
				'uname' => $log['uname'],
				'nickname' => $log['nickname'],
				'name' => '系统',
				'content' => $msg,
				'num' => 1,
				'model' => $log['model'],
				'version' => $log['version'],
				'sys_version' => $log['sys_version'],
				'client_pkg' => $log['client_pkg'],
				'imei' => $log['imei'],
				'status' => 1,
				'create_time' => Common::getTime(),
		);
		$item = Freedl_Service_Blacklist::getByBlacklist(array('imsi'=>$log['imsi'], 'activity_id'=>$log["activity_id"]));
		if($item){
			$data['num'] = ($item['status'] ? $item['num'] : $item['num'] + 1);
			Freedl_Service_Blacklist::updateBlacklist($data, $item['id']);
		} else {
			Freedl_Service_Blacklist::addBlacklist($data);
		}
	}
	
	/**
	 * imsi用户明细整理
	 * @param array $log
	 * @param array $hd
	 */
	private static function _freedlUserImsi($log){
		$params = array(
				'imsi' => $log['imsi'],
				'activity_id' => $log['activity_id'],
				'uuid' => $log['uuid']
		);
		$ret = Freedl_Service_UserImsi::getBy($params);
		if(!$ret){
			//新增
			$add = array(
					'activity_id'=>$log['activity_id'],
					'imsi'=> $log['imsi'],
					'uuid'=> $log['uuid'],
					'uname'=>$log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=>$log['imei'],
					'model'=>$log['model'],
					'version'=>$log['version'],
					'sys_version'=>$log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_UserImsi::add($add);
		}else {
			$update = array(
					'imsi'=> $log['imsi'],
					'uuid'=> $log['uuid'],
					'uname'=>$log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=>$log['imei'],
					'model'=>$log['model'],
					'version'=>$log['version'],
					'sys_version'=>$log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_UserImsi::updateBy($update, array('id'=>$ret['id']));
		}
	}
	
	/**
	 * 根据用户免流量活动的上传日志，处理活动中黑名单
	 * @param array $rule
	 * @param array $log
	 * @return array
	 */
	private static function _blackListHandle($rule, $log){
		//黑名单标志
		$flag = false;
		//游戏下载次数判定
		$data = $downloads = array();
		$hour = $day = $month = 0;
		$startTime = 0;
		//用户是否从黑名单中有过移除操作
		$ret = Freedl_Service_Blacklist::getByBlacklist(array('imsi'=>$log['imsi'], 'activity_id'=>$log["activity_id"],'status'=>0));
		if($ret){
			$startTime = $ret['remove_time'];
		}
		//一天内下载次数规则判定
		//特定imsi用户本次日志添加时间的当天内下载游戏次数
		$downloads=self::getImsiGamesTimes($log, $startTime);
		list($flag, $msg) = self::_dlCheck($rule[1],  $downloads);
		
		//一小时流量判定
		if(!$flag && empty($msg) && $rule[2]) {
			$hour = Freedl_Service_Userinfo::getTraffic($log, 1, $startTime);
			$hour = $hour ? $hour : 0;
			if($hour > $rule[2]){
				$flag = true;
				$msg = date('Y-m-d:H:i:s').",用户imsi:{$log['imsi']},一小时流量超过{$rule[2]}M。";
			}
		}
		//一天流量判定
		if(!$flag && empty($msg) && $rule[3]) {
			$day = Freedl_Service_Userinfo::getTraffic($log, 2, $startTime);
			$day = $day ? $day : 0;
			if($day > $rule[2]){
				$flag = true;
				$msg = date('Y-m-d:H:i:s').",用户imsi:{$log['imsi']}, 一天流量超过{$rule[3]}M。";
			}	
		}
		//一月流量判定
		if(!$flag && empty($msg) && $rule[4]) {
			$month = Freedl_Service_Userinfo::getTraffic($log, 3, $startTime);
			$month = $month ? $month : 0;
			if($month > $rule[4]){
				$flag = true;
				$msg = date('Y-m-d:H:i:s').",用户imsi:{$log['imsi']}, 一月流量超过{$rule[4]}M。";
			}	
		}
		return array($flag, $msg);
	}

	/**
	 * 黑名单规则：活动中1天内用户下载次数处理
	 * @param int $times
	 * @param array $games
	 * @return array
	 */
	private static function _dlCheck($times, $games){
		$flag = false;
		$msg = '';
		if(!$games) return array($flag, $msg);
		foreach ($games as $key=>$value){
			if($value > $times){
				$flag = true;
				$msg = date('Y-m-d:H:i:s').",游戏id:{$key}, 下载超过{$times}次。";
				break;
			}
		}
		return array($flag, $msg);
	}
	
	/**
	 * 查询该用户在该活动中当天游戏的下载次数
	 * @param array $log
	 */
	private static function getImsiGamesTimes($log, $startTime){
		$tmp = array();
		$ret = Freedl_Service_Userinfo::getDlTimes($log, $startTime);
		if(!$ret) return $tmp;
		foreach ($ret as $value){
			$tmp[$value['game_id']] = $value['dltimes'];
		}
		return $tmp;
	}
		
	/**
	 * 免流量活动参与的人数
	 * @param array $log
	 * @param array $imsiData
	 */
	private static function _freedlUsers($log, $imsiData){
		$params = array(
				'imsi'=>$log['imsi']
		);
		$ret = Freedl_Service_Users::getBy($params);
		if (!$ret) {
			//新增记录
			$add = array(
					'imsi' => $log['imsi'],
					'uuid' => $log['uuid'],
					'uname' => $log['uname'],
					'operator' => $imsiData[0],
					'region' => $imsiData[1],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_Users::insert($add);
		}
	}
	
	/**
	 * 游戏月流量消耗
	 * @param array $log
	 * @param array $hd
	 */
	private static function _freedlGameTotal($log, $hd){
		$params = array(
				'year_month' => date('Y-m', $log['create_time']),
				'game_id' => $log['game_id'],
				'activity_id' => $log['activity_id']
		);
		$ret = Freedl_Service_Gametotal::getBy($params);
		if($ret){
			//更新
			$update = array(
					'month_consume'=>$ret['month_consume']+$log['upload_size'], //消耗量累计
					'refresh_time' =>$log['refresh_time'],
			);
			Freedl_Service_Gametotal::updateBy($update, array('id'=>$ret['id']));
		} else {
			//新增
			$add = array(
					'year_month'=> date('Y-m', $log['create_time']),
					'activity_id' => $log['activity_id'],
					'activity_name' => $hd['title'],
					'game_id' => $log['game_id'],
					'game_name' => $log['game_name'],
					'month_consume'=>$log['upload_size'],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_Gametotal::insert($add);
		}
	}
	
	/**
	 * 运营商月流量消耗
	 * @param array $log
	 * @param array $imsiData 数据格式：array(运营商, 地区编码)
	 */
	private static function _freedlOperatorTotal($log, $imsiData){
		$params = array(
				'year_month'=>date('Y-m', $log['create_time']),
				'operator'=>$imsiData[0], //cmcc|cu|ctc
				'region'=>$imsiData[1] //地区编码
		);
		$ret = Freedl_Service_Operatortotal::getBy($params);
		if($ret){
			//更新
			$update = array(
					'month_consume'=>$ret['month_consume']+$log['upload_size'], //消耗量累计
					'refresh_time' =>$log['refresh_time'],
			);
			Freedl_Service_Operatortotal::updateBy($update, array('id'=>$ret['id']));
		} else {
			//新增
			$add = array(
					'year_month'=> date('Y-m', $log['create_time']),
					'operator'=>$imsiData[0],
					'region'=>$imsiData[1], 
					'month_consume'=>$log['upload_size'],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_Operatortotal::insert($add);
		}
	}
	
	/**
	 * 月流量汇总处理
	 * @param array $log
	 */
	private static function _freedlMonthTotal($log){
		$params = array(
				'year_month'=>date('Y-m', $log['create_time']),
		);
		$ret = Freedl_Service_Monthtotal::getBy($params);
		if($ret){
			//更新数据
			$update=array(
					'month_consume'=>$ret['month_consume']+$log['upload_size'], //消耗量累计
					'refresh_time' =>$log['refresh_time'],
			);
			Freedl_Service_Monthtotal::updateBy($update, array('id'=>$ret['id']));
		}else{
			//新增
			$add = array(
					'year_month'=> date('Y-m', $log['create_time']),
					'month_consume'=>$log['upload_size'],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>Common::getTime()
			);
			Freedl_Service_Monthtotal::insert($add);
		}
	}
	
	
	/**
	 * 用户下载明细表处理
	 * @param array $log
	 * @param array $hd
	 * @param array $receive
	 *
	 */
	private static function _freedlUserHandel($log, $hd, $receive){
		//记录查询条件
		$params = array(
				'activity_id' => $log['activity_id'],
				'imsi' => $log['imsi'],
				'game_id' => $log['game_id'],
				'task_flag' => $log['task_flag']
		);
		$ret = Freedl_Service_Userinfo::getBy($params);
		if($ret){
			//更新数据
			$update=array(
					'status'=>$log['task_status'],
					'consume'=>$ret['consume']+$log['upload_size'], //消耗量累计
					'refresh_time' =>$log['refresh_time'],
			);
			Freedl_Service_Userinfo::updateBy($update, array('id'=>$ret['id']));
		}else{
			//新增
			$add = array(
					'activity_id'=>$log['activity_id'],
					'imsi'=> $log['imsi'],
					'uuid'=> $log['uuid'],
					'uname'=>$log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=>$log['imei'],
					'model'=>$log['model'],
					'version'=>$log['version'],
					'sys_version'=>$log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'year_month'=> date('Y-m'),
					'activity_name'=>$hd['title'],
					'receive_time'=>$receive['create_time'],
					'game_id'=> $log['game_id'],
					'game_name'=>$log['game_name'],
					'size'=> $log['game_size'],
					'task_flag'=> $log['task_flag'],
					'status'=>$log['task_status'],
					'consume'=>$log['upload_size'],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>$log['create_time']
			);
			Freedl_Service_Userinfo::insert($add);
		}
	}
	
	/** 
	 * imsi流量汇总
	 * @param  $log
	 * @return boolean
	 */
	private static function _freedlImsiTotalHandel($log){
		$params = array(
				'imsi'=>$log['imsi'],
		);
		$ret = Freedl_Service_Imsitotal::getBy($params);
		if($ret){
			//更新数据
			$update=array(
					'total_consume' => $ret['total_consume'] + $log['upload_size'], //消耗量累计
					'uuid'=> $log['uuid'],
					'uname'=> $log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=> $log['imei'],
					'model'=>$log['model'],
					'version'=> $log['version'],
					'sys_version'=> $log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'refresh_time' => $log['refresh_time'],
			);
			Freedl_Service_Imsitotal::updateBy($update, array('id'=>$ret['id']));
		}else{
			//新增
			$add = array(
					'imsi'=> $log['imsi'],
					'total_consume'=> $log['upload_size'],
					'uuid'=> $log['uuid'],
					'uname'=> $log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=> $log['imei'],
					'model'=>$log['model'],
					'version'=> $log['version'],
					'sys_version'=> $log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'refresh_time' =>$log['refresh_time'],
					'create_time'=>$log['create_time']
			);
			Freedl_Service_Imsitotal::insert($add);
		}
	}
	
	
	/**
	 * 用户流量汇总处理
	 * @param array $log
	 */
	private static function _freedlUseTotalHandel($log, $hd){
		$params = array(
				'imsi'=>$log['imsi'],
				'activity_id'=>$log['activity_id'],
				
		);
		$ret = Freedl_Service_Usertotal::getBy($params);
		if($ret){
			//更新数据
			$update=array(
					'total_consume' => $ret['total_consume'] + $log['upload_size'], //消耗量累计
					'uuid'=> $log['uuid'],
					'uname'=> $log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=> $log['imei'],
					'model'=>$log['model'],
					'version'=> $log['version'],
					'sys_version'=> $log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'refresh_time' => $log['refresh_time'],
			);
			Freedl_Service_Usertotal::updateBy($update, $params);
		}else{
			//新增
			$add = array(
					'activity_id'=>$log['activity_id'],
					'imsi'=> $log['imsi'],
					'uuid'=> $log['uuid'],
					'total_consume'=> $log['upload_size'],
					'uname'=> $log['uname'],
					'nickname'=>$log['nickname'],
					'imei'=> $log['imei'],
					'model'=>$log['model'],
					'version'=> $log['version'],
					'sys_version'=> $log['sys_version'],
					'client_pkg'=>$log['client_pkg'],
					'refresh_time' =>$log['refresh_time'],
			);
			Freedl_Service_Usertotal::insert($add);
		}
	}
}