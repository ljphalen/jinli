<?php
/**
 * 360安全检查
 * @author jiazhu
 */
class SafeQihuModel extends SafeModel
{
	protected $trueTableName = "apk_safe";

	/*
	 * 发送请求
	 */
	public function requestScan($apk_info)
	{
		//$scan_parm="{\"scanlist\":[{\"sid\":\"123\", \"url\":\"http://dl.test.com/test.apk\", \"md5\":\"3d41f29d762ec547bfa4b42f57f3dc7c\"}]}";
		$apk_path = Helper("Apk")->get_url('apk').$apk_info['file_path'];
		
		$scan_parm = array('scanlist' => array(array('sid' => $apk_info['id'],'url' => $apk_path, 'md5' => $apk_info['apk_md5'])));
		$scan_json = json_encode($scan_parm);
		
		$apk_key = C('SAFE_QIHU_APP_KEY');
		if(empty($apk_key))
			$apk_key = APP_DEBUG ? 'da7aca2c63dc6262166ee182b16370b2' : '62cdb3bc8b00abd29161bdd82689c78d';
		
		$tmp_file = DATA_PATH.md5($apk_path);
		file_put_contents($tmp_file, $apk_path);

		$api_url = sprintf("http://scan.shouji.360.cn/ScanUpload?UserKey=%s&ReqType=Url&RspType=Callback", $apk_key);
		$shell = sprintf('curl -s -F "file=@%s" "%s" ', $tmp_file, $api_url);
		$request_res = shell_exec($shell);

		$xml = simplexml_load_string($request_res);
		$rep = (string)$xml->file->state;

		$deal_status = $rep > 0 ? ApkSafeModel::DEAL_STATUS_SEND : ApkSafeModel::DEAL_STATUS_ERROR;
		
		unlink($tmp_file);
		return array('deal_status' => $deal_status, 'request_res' => json_encode($request_res));
	}
	
	/**
	 * {"state":"1","md5":"11575fefa00e3d7439a1590033916cd8","type":"apk","sign":"安全/谨慎/危险/未发现已知恶意","desc":{},"safe":{},"pms":{},"prop":{}}
	 * @param array $data
	 */
	public function scanResult($sign, $data)
	{
		$safe_status = $data['state'] == 1 ? ApkSafeModel::STATUS_SUC : ApkSafeModel::STATUS_FAIL;
		$apk_md5 = $data['md5'];
		$new_data = array(
				'safe_status' => $safe_status,
				'response_res' => array("sign"=>$data["sign"], "safe"=>$data["safe"], "desc"=>$data["desc"])
		);
		$this->dealScan($apk_md5, $new_data, parent::SITE_QIHU);
		return true;
	}
	
	/**
	 * 在管理后台，展现安全结果
	 * @param json $json 返回json数据
	 */
	public static function show($json)
	{
		if (empty($json)) return false;
		$str = '';
		$data = json_decode($json,true);

		if (!empty($data))
		{
			$str .= '<b>文件md5:</b> '.$data['md5']."<br/>";
			$str .= '<b>安全类型:</b> '.$data['sign']."<br/>";
			
			if(!empty($data['safe']))
			$str .= '<b>广告软件标记:</b> '.$data['safe']."<br/>";
			if(!empty($data['desc']))
				$str .= '<b>详情:</b> '.$data['desc']."<br/>";
		}
		return $str;
	}
}