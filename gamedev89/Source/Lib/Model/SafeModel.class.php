<?php
/**
 * 安全检测基类
 * @author jiazhu
 *
 */
class SafeModel extends Model
{
	protected $trueTableName = "apk_safe";
	
	CONST SITE_BAIDU = 'Baidu';
	CONST SITE_TENCENT = 'Tencent';
	/**
	 * 发送请求扫描
	 * @param unknown_type $apk_id
	 */
	public function apkScan($apk_id,$site = self::SITE_BAIDU)
	{
		//获得apk info
		$apk_info = D('Dev://Apks')->find($apk_id);
		//实例化第三方对象
		$site_model = D('Safe'.$site);
		
		//第三方发送请求
		$res = $site_model->requestScan($apk_info);
	
		//入apk_safe 表
		$safe_info = array(
			'apk_id' => $apk_info['id'],
			'apk_md5' => $apk_info['apk_md5'],
			'mold' => ApkSafeModel::siteMold($site),
			'status' => ApkSafeModel::STATUS_INIT,
			'deal_status' => $res['deal_status'],
			'request_res' => $res['request_res'],
			'add_time' => time(),
		);
		D('Dev://ApkSafe')->add($safe_info);
		//var_dump(D('Dev://ApkSafe')->getLastSql());
		return true;
	}
	
	/**
	 * 调用所有安全扫描
	 * @param int $apk_id
	 */
	public function scanAll($apk_id)
	{
		if (empty($apk_id)) return false;
		$this->apkScan($apk_id,self::SITE_BAIDU);
		$this->apkScan($apk_id,self::SITE_TENCENT);
		return true;
	}
	
	/**
	 * 扫描结果回调
	 * @param string $sign 签名
	 * @param string $data 返回数据json串
	 * 
	 */
	public function scanResult($sign,$data)
	{
		
	}
	
	/**
	 * 
	 * 扫描结果后续处理
	 * @param string $apk_md5
	 * @param array $data array('safe_status','response_res')
	 * @param string $site
	 */
	public function dealScan($apk_md5,$data,$site=self::SITE_BAIDU)
	{
			//根据md5码获取记录
			$safe_info = D('Dev://ApkSafe')->where(array('apk_md5' => $apk_md5,'mold' => ApkSafeModel::siteMold($site)))->find();
			if (empty($safe_info)) 
			{
				return false;
			}
		
			
			//当成功时，增加apk安全状态
			if ($data['safe_status'] == ApkSafeModel::STATUS_SUC)
			{
				D("Dev://Apks")->where('id='.$safe_info['apk_id'])->save(
								array('safe_status' =>array('exp', 'safe_status |'.ApksModel::siteStaus($site)),
								//'onlined_at' => time()
								 )
								);
			}
			//更新 apk_safe 记录
			$safe_data = array(
				'status' => $data['safe_status'],
				'deal_status' =>  $data['safe_status']?ApkSafeModel::DEAL_STATUS_SUC:ApkSafeModel::DEAL_STATUS_SUC,
				'response_res' => json_encode($data['response_res']),
				'response_time' => time(),
			);
			
			D('Dev://ApkSafe')->where('id='.$safe_info['id'])->save($safe_data);
			return true;
	}
	

	
}