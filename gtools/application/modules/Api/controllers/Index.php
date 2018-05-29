<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * API V1
 * @author fanch
 *
 */
class IndexController extends Api_BaseController{
	
	public function indexAction() {
		exit("API v1.0");
	}
	
	/**
	 * 获取apkinfo数据
	 */
	public function apkInfoAction(){
		$request = $this->getInput(array('apkpath', 'icopath'));
		$apk = $request['apkpath'];
		$ico = $request['icopath'];
		if (!$apk) exit('Access Denied!');
		//抓包工具下载apk地址  apkpath参数给的是绝对地址
		$file = $apk; 
		if(!file_exists($file)) exit('apk file not found');
		$apkInfo = Apk_Service_Aapt::info($file);
		echo json_encode(array($apkInfo));
		exit;
	}
}