<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Service_Permission
 * 免流量涉及的权限service
 * @author fanch
 *
 */
class Freedl_Service_Permission extends Common_Service_Base{
	
	/**
	 * 检查免流量活动是否有效
	 * @param unknown_type $imsi
	 * @return number
	 */
	public static function checkActivity($imsi, $activityId = 0) {
		if (!$imsi) return array();
		$imsiData = Freedl_Service_Imsi::convert($imsi);
		if($imsi == false) return array();
		list($operator, $province) = $imsiData;
		$params = array('htype'=>0);
		$params['status'] = 1;
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		//中国移动活动
		if($operator.$province == 'cmcc19')  $params['htype'] = 1;
		//中国联通
		if($operator.$province == 'cu19')  $params['htype'] = 2;
		
		if($activityId)  $params['id'] = $activityId;
		$info = Freedl_Service_Hd::getByFreedl($params, array('start_time'=>'DESC','id'=>'DESC'));
		if($info) {
			$info['imsiCode'] = $operator . $province;
			return $info;
		}
		return array();
	}
	
	/**
	 * 检查是否为黑名单用户
	 * @param string $imsi
	 * @param string $imei
	 */
	public static function checkBlacklist($imsi, $imei) {
		$cache = Cache_Factory::getCache();
		$ckey = '-freedl-black';
		$data = $cache->get($ckey);
		if(!$data['imsi'] && !$data['imei']){
			//缓存没值，先设置缓存值
			$data = array(
					'imsi'=> self::_getBlackData(1),
					'imei'=> self::_getBlackData(3)
			);
			if($data) $cache->set($ckey, $data, 60*5);
		}
		if(in_array($imei, $data['imei']) && $data['imei'] && $imei) return 1;
		if(in_array($imsi, $data['imsi']) && $data['imsi'] && $imsi) return 1;
		return 0;
	}
	
	/**
	 * 检查是否为已经领取
	 * @param unknown_type $imsi
	 * @param unknown_type $uname
	 * @param unknown_type $imei
	 */
	public static function checkReceive($imsi, $activityId) {
		$cache = Cache_Factory::getCache();
		$ckey = '-freedl-rec' . $activityId . '-' . $imsi;
		$cdata = $cache->get($ckey);
		if($cdata == false){
			$params = array();
			$params['imsi'] = $imsi;
			$params['activity_id'] = $activityId;
			$ret = Freedl_Service_Receive::getBy($params);
			$cdata = ($ret) ? 1 : 0;
			$cache->set($ckey, $cdata, 5*60);//cache数据为5天有效
		}
		return $cdata;
	}
	
	/**
	 * 按照imsi | uuid | imei 查找黑名单数据
	 * @param int $type
	 */
	private static function _getBlackData($type) {
		$tmp = array();
		$ret = Freedl_Service_Blacklist::getsByBlacklist(array('status'=>1,'utype'=>$type));
		foreach($ret as $key=>$value){
			if($type == 1) $key = $value['imsi'];
			if($type == 3) $key = $value['imei'];
			if(!in_array($key, $tmp)){
				$tmp[]= $key;
			}
		}
		return $tmp;
	}
	
	/**
	 * 更新黑名单缓存
	 * @return boolean
	 */
	public static function setBlackData() {
		$cache = Cache_Factory::getCache();
		$ckey = '-freedl-black';
		$cdata = array(
				'imsi'=> self::_getBlackData(1),
				'imei'=> self::_getBlackData(3)
		);
		if($cdata) $cache->set($ckey, $cdata, 60*5);
		return true;
	}
	
	/**
	 * 通过sp参数解析机型，sdk版本，android版本
	 * @param string $sp
	 * @return array
	 */
	public static function getConvertSpData($sp) {
		$arr_sp = explode("_", $sp);
		//获取机型
		$mode = $arr_sp[0];
		//获取sdk版本
		$version = $arr_sp[1];
		//获取android版本
		$sys_version = substr($arr_sp[3],7);
		return array($mode, $version, $sys_version);
	}
	
	/**
	 * 添加领取记录
	 * @param array $request
	 * @param int $activityId
	 * 
	 */
	public static function addReceive($request, $activityId) {
		$sp_arr = self::getConvertSpData($request['sp']);
		$client_pkg = 2;
		if($client_pkg == "com.android.amigame") {
			$client_pkg = 1;
		}
		$data = array(
					'activity_id'=>$activityId,
					'imsi'=>$request['imsi'],
					'imei'=>$request['imei'],
					'model'=>$sp_arr[0],
					'client_pkg'=>$request['client_pkg'],
					'version'=>$sp_arr[1],
					'sys_version'=>$sp_arr[2],
					'create_time'=>Common::getTime(),
				    'status'=>0,
			);
		$ret = Freedl_Service_Receive::add($data);
		return $ret;
	}
}