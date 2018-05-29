<?php
/**
 * 应用model类
 * 状态(-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:未提交审核, 1:审核中/待审核, 2:审核通过, 3:已上线)
 *
 * @name ApksModel.class.php
 * @author gaochao
 * @datetime 2013-12-18
 */
class ApksModel extends Model{
	protected $trueTableName = '`apks`';
	
	const APK_EDIT = 0; // 未提交
	const APK_AUDITING = 1; // 审核中/待审核
	const APK_AUDIT_NOT_PASS = - 1; // 审核不通过
	const APK_TEST_PASS = 2; // 审核通过
	const APK_ONLINE = 3; // 已上线
	const APK_OFFLINE = - 2; // 已下线
	const APK_AUTO_ONLINE = 4; // 自动上线
	
	protected $status = array (
				self::APK_EDIT => '未提交',
				self::APK_AUDITING => '审核中',
				self::APK_AUDIT_NOT_PASS => '审核不通过',
				self::APK_TEST_PASS => '审核通过',
				self::APK_ONLINE => '已上线',
				self::APK_OFFLINE => '已下线',
				self::APK_AUTO_ONLINE => '自动上线',
		);

	/*
	 * 安全检查结果，采用位运算的方式 2的几次方 1，2，4，8 ..  ｜： 加权限  ＆检测权限是否存在 ^ :减权限
	 */	
	CONST SAFE_STATUS_BAIDU = 1; 	//百度
	CONST SAFE_STATUS_TENCENT = 2; //腾讯
	CONST SAFE_STATUS_ALL = 3;	   //检测是否都通过，每增加值时，其值对应增加
	
	//合作方式
	CONST JOIN_COMMON = 2;	//普通
	CONST JOIN_UION = 1;		//联运
	
	//签名状态(0:未签名, -1:签名出错, 1:签名成功) 
	CONST SIGN_INIT = 0;
	CONST SIGN_ERROR = -1;
	CONST SIGN_SUC = 1;
	
	/*
	 * 站点转换为对应的值
	 */
	public static function siteStaus($site)
	{
		$arr = array(
			SafeModel::SITE_BAIDU => self::SAFE_STATUS_BAIDU,
			SafeModel::SITE_TENCENT => self::SAFE_STATUS_TENCENT,
		);
		return $arr[$site];
	} 
	
	/**
	 * 获得合作方式
	 * @param int $is_join 合作类型
	 */
	public static function getJoin($is_join=null)
	{
		$arr = array(
			self::JOIN_COMMON => '普通',
			self::JOIN_UION => '联运',
		);
		return isset($arr[$is_join]) ? $arr[$is_join] : $arr[self::JOIN_COMMON];
	} 
	
	/**
	 * 获得签名状态
	 * @param int $sign
	 */
	public static function getSign($sign=null)
	{
		$arr = array(
			self::SIGN_INIT => '未签',
			self::SIGN_SUC => '已签',
			self::SIGN_ERROR => '出错',
		);
		if ($sign === null)
		{
			return $arr;
		}else 
		{
			return $arr[$sign];
		}
	}
	
	/**
	 * 根据状态位，返回已经审核通过的安全厂商
	 * @param unknown_type $safe_status
	 */
	public static function allSafeName($safe_status)
	{
		$txt_arr = array();
		if ($safe_status & self::SAFE_STATUS_BAIDU) $txt_arr[] = '百度';
		if ($safe_status & self::SAFE_STATUS_TENCENT) $txt_arr[] = '腾讯';
		return $txt_arr;
	}
	
	public static function allSafeTxt($safe_status)
	{
		$all_safe =self::allSafeName($safe_status);
		return empty($all_safe)?'--':implode(',',$all_safe);
	}
		
	/**
	 * 获取apk的状态
	 * @param int $apk_id
	 */
	function status_to_string($apk_id)
	{
		$s = $this->where(array("id" => $apk_id))->getField("status");
		return $this->status[$s];
	}
	
	/**
	 * 获取应用中最新的apk信息
	 * @param int $uid
	 * @param int $appId
	 * @return array
	 */
	public function getApkByAppId($appId,$map=array()){
		$where = array("app_id"=>$appId);
		if (!empty($map))
			$where += $map;

		$res = $this->where($where)->order("id desc")->find();
		return $res;
	}
	
	/**
	 * 获取用户应用 apk 的审核状态
	 * @param int $uid
	 * @param int boolen
	 * @return array
	 */
	function getApkStatusCount($uid, $all=true){
		$where = array("author_id"=>$uid);
		if (!$all) {
			$where['status'] = array("in",array_keys($this->status));
			return $this->where($where)->group("status")->field("status, count(id) as count")->select();
		}else{
			return $this->where($where)->count();
		}
	}
	
	function getApkStatusByStatus($status){
		return $this->status[$status];
	}
	
	//根据应用ID获取指定字段
	function getFieldById($appId, $field="package")
	{
		$map['app_id'] = $appId;
		if(is_array($appId))
			$map = $appId;

		if(in_array($field, array("icon", "safe", "screen"))){
			$apk_id = $this->where($map)->order(array("status"=>"desc"))->getField("id");
			if($field == "icon")
				return D("Dev://Picture")->getApkIcon($appId, $apk_id, array("type"=>array('in', "2,5")));
			if($field == "screen")
				return D("Dev://Picture")->getApkIcon($appId, $apk_id, array("type"=>1));
			
		}else{
			return $this->where($map)->order(array("status"=>"desc"))->getField($field);
		}
	}
	
	/**
	 * 有某一个版本处于审核通过及以上，新的版本即认为是更新版本，否则为发布新游戏
	 * @param int $appId
	 */
	function getReleaseStatus($appId, $apk_id)
	{
		return $this->where(array("app_id"=>$appId, "status"=>array("gt", 1), "id"=>array("lt", $apk_id)))->count() ? '更新版本' : '新发布';
	}
}