<?php
/**
 * apk安全检测model
 * @author jiazhu
 * @version 2014-01-04
 */
class ApkSafeModel extends Model
{
	protected 	$trueTableName = '`apk_safe`';
	
	//处理状态 deal_status  	0：未处理 1：已发送；2：处理完成 -1：处理失败 -2：请求失败 
	CONST DEAL_STATUS_INIT = 0;
	CONST DEAL_STATUS_SEND = 1;
	CONST DEAL_STATUS_SUC = 2;
	CONST DEAL_STATUS_FAIL = -1;
	CONST DEAL_STATUS_ERROR = -2;
	
	// status 第三方审核结果 -1：失败 0：未处理 1：成功 
	CONST STATUS_FAIL = -1;
	CONST STATUS_INIT = 0;
	CONST STATUS_SUC = 1;
	
	//第三方检查  
	CONST MOLD_BAIDU = 1;	//百度检测
	CONST MOLD_TENCENT = 2; //腾讯检测
	
	//检测站点与mold转换
	public $site_mold =  array(
			SafeModel::SITE_BAIDU => self::MOLD_BAIDU,
			SafeModel::SITE_TENCENT => self::MOLD_TENCENT,
		);
	/**
	 * 检测站点与mold转换
	 * @param string $site
	 */
	public static function siteMold($site)
	{
		$arr = array(
			SafeModel::SITE_BAIDU => self::MOLD_BAIDU,
			SafeModel::SITE_TENCENT => self::MOLD_TENCENT,
		);
		return $arr[$site];
	}
	
	/**
	 * 获得厂商名称
	 * @param int $mold
	 */
	public static function getSite($mold=null)
	{
		$site = array(
			self::MOLD_BAIDU => SafeModel::SITE_BAIDU,
			self::MOLD_TENCENT => SafeModel::SITE_TENCENT,
		);
		if ($mold == null)
		{
			return $site;			
		}else 
		{
			return $site[$mold];
		}
	}
	
	/**
	 * 获得状态文字提示
	 * @param int $status
	 */
	public static function statusTxt($status=null)
	{
		$arr = array(	self::STATUS_FAIL => '未通过',
						self::STATUS_INIT => '未处理',
						self::STATUS_SUC => '通过',
				);
		if ($status == null)
		{
			return $arr;
		}else 
		{
			return $arr[$status];
		}		
	}
	
	/**
	 * 获得安全检测详情
	 * @param int $apk_id
	 */
	public function detail($apk_id)
	{
		if (empty($apk_id)) return false;
		$res = $this->field(' apk_id , apk_md5 ,mold ,status , response_res ')->where('apk_id='.$apk_id)->order('id desc')->select();
		return $res;
	}
	
	public function apkStatus($apk_id,$mold =self::MOLD_BAIDU)
	{
		$row = $this->where('apk_id='.$apk_id.' and mold='.$mold)->find();
		if (empty($row))
		{
			return '';
		}else 
		{
			return self::statusTxt($row['status']);
		}
	}
}