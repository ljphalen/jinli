<?php
/**
 * 应用更改同步类
 * @author Xia
 * @datetime 2014-02-25 23:30
 */
class SyncHelper
{
	//Web生成状态，1校验成功，0校验失败
	protected $webp_status = 1;
	
	/**
	 * 应用更改同步接口
	 * @param int $appId 应用ID
	 */
	public function done($appId, $type="offline")
	{
        $back = debug_backtrace();
        Log::write("app:{$appId}, type:{$type}", "APP_SyncHelper");
        Log::write("debug_backtrace:".var_export($back[0], true), "APP_SyncHelper");
		
		//同步接口url
		$syncUrl = $type == "offline" ? C('APP_OFFLINE_NOTYFI_URL') : C('APP_ONLINE_NOTYFI_URL');
		if(empty($syncUrl))
		{
			Log::write("APP_NOTYFI_URL 未定义", log::EMERG);
			D("Syserror")->error("APP_NOTYFI_URL 未定义",
			"APP_NOTYFI_URL 未定义，appid:{$appId}同步失败，需要手工执行同步",
			"修改Data/Config/api.php中的APP_OFFLINE_NOTYFI_URL、APP_ONLINE_NOTYFI_URL配置项",
			$appId,
			"2");
			return false;
		}

		//加密的token验证
		$rsaInfo = self::_rsaVerify($appId);
		
		//获取签名
		$sign = $rsaInfo[0];

		//验证token结果
		$ret = $rsaInfo[1];
		$syncCount = 0;
		while($syncCount < 3){
			$option = array('id'=>$appId,'sign'=>$sign);
			$result = curl_get($syncUrl, $option);

			if($result == 'ok')
				return $result;
			log::write($result, "SYNC_APP");
			$syncCount ++;
		}
		
		if($result != 'ok'){
			D("Syserror")->error("运营后台数据同步失败，appid:{$appId}",
			"appid:{$appId}同步失败，重试{$syncCount}次，返回结果‘{$result}’，需要手工执行同步",
			"php repl.php\nHelper('Sync')->done({$appId}, '{$type}');",
			$appId,
			"2");
		}
		return false;
	}
	
	/**
	 * APPID 转变为 Gameid 接口
	 * @param int  $appId
	 */
	public function getGameId($appId)
	{
		$syncUrl = C('APP_APPID_TO_GAMEID_URL');
		//加密的token验证
		$rsaInfo = self::_rsaVerify($appId);
		//获取签名
		$sign = $rsaInfo[0];
		
		//验证token结果
		$ret = $rsaInfo[1];
		if ($ret){
			$option = array('id'=>$appId,'sign'=>$sign);
			//var_dump($syncUrl, $option);
			return curl_get($syncUrl, $option);
		}
		return false;
	}
	
	public function get($appId)
	{
		//取消签名验证
		if (true){
			$apkFields = "id apkid, app_id appid, app_name name, label, keyword, category_two category, brief resume, 
							fee_type price, language, package, description descrip, is_join cooperate, developer developer,
							created_at create_time, onlined_at online_time, status, author_id, reso, agent, category_p, category_p_son, category_s, category_s_son";
			//只获取线上的版本
			$apkInfo = D("Dev://Apks")->where(array("app_id"=>$appId, "status"=>3))->field($apkFields)->order("id desc")->find();

			if (!empty($apkInfo)) {
				$apkId = $apkInfo['apkid'];
				if (!empty($apkInfo["category_p"]))
					$apkInfo["category"] = $apkInfo["category_p"];
				
				//获取标签表数据
				//线上数据
// 				$labelHash = array(	'network_type'			=>103,
// 									'character'				=>104,
// 									'billing_model'			=>105,
// 									'detail_category'		=>106,
// 									'level'					=>107,
// 									'about'					=>108,
// 									'style'					=>109,
// 				);
				/**
				 *network_type    [联网类型103]
				 *character       [游戏特色104]
				 *billing_model   [资费方式105]
				 *detail_category [详细分类106]
				 *level           [游戏评级107]
				 *about           [内容题材108]
				 *style           [画面风格109]
				 */
				//测试站数据
				$labelHash = array(
						'network_type'			=>115,
						'character'				=>111,
						'billing_model'			=>127,
						'detail_category'		=>112,
						'level'					=>120,
						'about'					=>113,
						'style'					=>114,
				);
				
				//处理标签
				$label = (array)json_decode($apkInfo['label']);
				foreach ($label as $k=>$v)
					$label[intval($k)]=$v;
				
				foreach ($labelHash as $k=>$v)
					$apkInfo[$k] = (string)join("|", $label[(string)$v]);
				
				$apkInfo['label'] = (string)$apkInfo['keyword'];
				
				// 截图信息
				$Picture = D ( "Dev://Picture" );
				$screens = $Picture->getApkScreen($appId, $apkId, array('type'=>1));
				foreach ($screens as $k => $v)
				{
					if(!empty($v['file_path']))
						$apkInfo['imgs'][] = helper("Apk")->get_url("screen").$v['file_path'];
				}
				$apkInfo['imgs'] = join("|", $apkInfo['imgs']);
				
				// ICON信息
				$apkInfo['icon'] = array();
				$s512 = $Picture->getApkIcon($appId, $apkId, array('type'=>5));
				if(empty($s512))
				{
					$apkInfo['icon'][] = $Picture->getApkIconOlder($appId, $apkId, array('type'=>2));
					$apkInfo['icon'][] = $Picture->getApkIconOlder($appId, $apkId, array('type'=>3));
					$apkInfo['icon'][] = $Picture->getApkIconOlder($appId, $apkId, array('type'=>4));
				}else{
					$file_ext = substr(trim($s512["file_path"]), "-3", 3);
					$file_name = substr(trim($s512["file_path"]), 0, -3);
					
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.72.". $file_ext);
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.96.". $file_ext);
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.144.". $file_ext);
				}
				foreach ($apkInfo['icon'] as $k => $v)
				{
					if(empty($v) || empty($v['file_path']))
						unset($apkInfo['icon'][$k]);
					else
						$apkInfo['icon'][$k] = helper("Apk")->get_url("icon").$v['file_path'];
				}
				$apkInfo['icon'] = join("|", $apkInfo['icon']);
				
				$this->webp_checker($apkInfo['imgs'], true);
				$this->webp_checker($apkInfo['icon']);
				$apkInfo['webp'] = $this->webp_status;
				
				//获取差分包信息，输出所有生成的差分包信息
				$bsmodel = D("Dev://Bspackage");
				$bspackage = $bsmodel->where(array("app_id"=>$appId, "b_apk_id"=>$apkId, "status"=>1))
													->field(array(
														"b_apk_id"=>"version_id",
														"s_apk_id"=>"object_id",
														"patch_size"=>"size",
														"patch_path"=>"diff_name",
														"created_at"=>"create_time",
														"updated_at"=>"update_time",
														"admin_id"=>"create_user",
													))->order('id asc')->select();
				
				APP_DEBUG && Log::write($bsmodel->_sql(), Log::SQL);

				$adminList = D("System://Admin")->getField("id,nickname");
				foreach ($bspackage as $k => $v)
				{
					$bspackage[$k]['create_user'] = $bspackage[$k]['modify_user'] = $adminList[$v['create_user']];
					$bspackage[$k]['link'] = helper("Apk")->get_url("patch_api").$bspackage[$k]['diff_name'];
					$bspackage[$k]['diff_name'] = basename($bspackage[$k]['diff_name']);
					$bspackage[$k]['size'] = $this->format_size($bspackage[$k]['size']);
					if(empty($v['update_time']))
						$bspackage[$k]['update_time'] = $bspackage[$k]['create_time'];
				}

				$apkInfo['diffpackages'] = $bspackage;
				$apkInfo['versions'] = $this->get_versions($appId, $apkId);
				
				$apkInfo['safeInfos'] = D('ApkSafe')->detail($apkId);
				$apkInfo['descrip'] = nl2br($apkInfo['descrip']);
				$apkInfo['resolution'] = $apkInfo['reso'];
				
				//获取联运数据
				$union = D("UnionApps")->where(array("author_id"=>$apkInfo["author_id"], "package"=>$apkInfo["package"]))->field("secret_key,api_key")->find();
				$apkInfo['secret_key'] = $union["secret_key"];
				$apkInfo['api_key'] = $union["api_key"];
				
				$apkInfo['author_name'] = D("Accounts")->where(array("id"=>$apkInfo["author_id"]))->getField("email");
				
				return array("success"=>1, "msg"=>'ok', 'data'=>$apkInfo);
			}
			return array("success"=>0, "msg"=>'获取应用的apk信息失败', 'data'=>'');
		}
	}
	
	//获取指定Apk的信息
	public function getApk($apkId)
	{
		//取消签名验证
		if (true){
			$apkFields = "id apkid, app_id appid, app_name name, label, keyword, category_two category, brief resume,
							fee_type price, language, package, description descrip, is_join cooperate, developer developer,
							created_at create_time, onlined_at online_time, status, author_id, reso, agent, category_p, category_p_son, category_s, category_s_son";
			$apkInfo = D("Dev://Apks")->where(array("id"=>$apkId))->field($apkFields)->order("id desc")->find();
	
			if (!empty($apkInfo)) {
				$apkId = $apkInfo['apkid'];
				$appId = $apkInfo['appid'];

				if (!empty($apkInfo["category_p"]))
					$apkInfo["category"] = $apkInfo["category_p"];
	
				//获取标签表数据
// 				$labelHash = array(	'network_type'			=>103,
// 						'character'				=>104,
// 						'billing_model'			=>105,
// 						'detail_category'		=>106,
// 						'level'					=>107,
// 						'about'					=>108,
// 						'style'					=>109,
// 				);
				
				$labelHash = array(
						'network_type'			=>115,
						'character'				=>111,
						'billing_model'			=>127,
						'detail_category'		=>112,
						'level'					=>120,
						'about'					=>113,
						'style'					=>114,
				);
	
				//处理标签
				$label = (array)json_decode($apkInfo['label']);
				foreach ($label as $k=>$v)
					$label[intval($k)]=$v;
	
				foreach ($labelHash as $k=>$v)
					$apkInfo[$k] = (string)join("|", $label[(string)$v]);
	
				$apkInfo['label'] = (string)$apkInfo['keyword'];
	
				// 截图信息
				$Picture = D ( "Dev://Picture" );
				$screens = $Picture->getApkScreen($appId, $apkId, array('type'=>1));
				foreach ($screens as $k => $v)
				{
					if(!empty($v['file_path']))
						$apkInfo['imgs'][] = helper("Apk")->get_url("screen").$v['file_path'];
				}
				$apkInfo['imgs'] = join("|", $apkInfo['imgs']);
				
				// ICON信息
				$s512 = $Picture->getApkIcon($appId, $apkId, array('type'=>5));
				if(empty($s512))
				{
					$apkInfo['icon'][] = $Picture->getApkIcon($appId, $apkId, array('type'=>2));
					$apkInfo['icon'][] = $Picture->getApkIcon($appId, $apkId, array('type'=>3));
					$apkInfo['icon'][] = $Picture->getApkIcon($appId, $apkId, array('type'=>4));
				}else{
					$file_ext = substr(trim($s512["file_path"]), "-3", 3);
					$file_name = substr(trim($s512["file_path"]), 0, -3);
						
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.72.". $file_ext);
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.96.". $file_ext);
					$apkInfo['icon'][] = array("file_path"=>$file_name . "png.144.". $file_ext);
				}
				foreach ($apkInfo['icon'] as $k => $v)
				{
					if(empty($v) || empty($v['file_path']))
						unset($apkInfo['icon'][$k]);
					else
						$apkInfo['icon'][$k] = helper("Apk")->get_url("icon").$v['file_path'];
				}
				$apkInfo['icon'] = join("|", $apkInfo['icon']);
				
				$this->webp_checker($apkInfo['imgs'], true);
				$this->webp_checker($apkInfo['icon']);
				$apkInfo['webp'] = $this->webp_status;
	
				$apkInfo['diffpackages'] = array();
				$apkInfo['versions'] = $this->get_apk_info($apkId);
	
				$apkInfo['safeInfos'] = D('ApkSafe')->detail($apkId);
				$apkInfo['descrip'] = nl2br($apkInfo['descrip']);
				$apkInfo['resolution'] = $apkInfo['reso'];
	
				//获取联运数据
				$union = D("UnionApps")->where(array("author_id"=>$apkInfo["author_id"], "package"=>$apkInfo["package"]))->field("secret_key,api_key")->find();
				$apkInfo['secret_key'] = $union["secret_key"];
				$apkInfo['api_key'] = $union["api_key"];
				
				$apkInfo['author_name'] = D("Accounts")->where(array("id"=>$apkInfo["author_id"]))->getField("email");
	
				return array("success"=>1, "msg"=>'ok', 'data'=>$apkInfo);
			}
			return array("success"=>0, "msg"=>'获取应用的apk信息失败', 'data'=>'');
		}
	}
	
	/**
	 * 接口的签名生成和加密的token生成
	 * @param int $appId
	 * @return array
	 */
	public static function _rsaVerify($appId)
	{
		import ( "Extend.General.Rsa", LIB_PATH);
		$rsa = new Util_Rsa();
		$params = array("id"=>$appId);
		$sign = $rsa->build_mysign($params, self::_getRsa("private"));
		$ret = $rsa->verifySign($params, $sign, self::_getRsa("public"));
		APP_DEBUG && Log::write(json_encode(array($sign, $ret)), "sync_api_rsa_return");
		return array($sign, $ret);
	}
	
	/**
	 * 获取加密密钥文件路径
	 * @param string $fileName
	 * @return string
	 */
	public static function _getRsa($fileName)
	{
		$path = sprintf("%sSecret/rsa_%s_key.pem", DATA_HOME, $fileName);
		return $path;
	}
	
	/**
	 * 格式化文件大小，保留两位小数
	 * @param int $size
	 */
	protected function format_size($size)
	{
		if(empty($size))
			return 0;
		return sprintf("%.2f", $size / 1024 / 1024);
	}
	
	/**
	 * 获取的apk版本
	 * @param int $appId
	 * @return array
	 */
	protected function get_versions($appId, $onlineId=0)
	{
		$apkFields = array(
				"id",
				"app_name",
				"version_name"		=>"version",
				"version_code",
				"file_size"			=>"size",
				"apk_rsa"			=>"fingerprint",
				"apk_md5"			=>"md5_code",
				"file_path"			=>"link",
				"created_at"		=>"create_time",
				"min_sdk_version"	=>"min_sys_version",
				"changelog"			=>"changes",
				"reso"				=>"resolution"
		);
		//只获取线上的版本
		$apkInfo = D("Dev://Apks")->where(array("app_id"=>$appId, "status"=>array('in','3,-2')))->field($apkFields)->order("id desc")->select();
		
		foreach ($apkInfo as $k=>$v)
		{
			$download_name = sprintf("?n=%s%s.apk", trim($apkInfo[$k]["app_name"]), trim($apkInfo[$k]["version"]));
			$apkInfo[$k]['update_time'] = $apkInfo[$k]['create_time'];
			$apkInfo[$k]['link'] = helper("Apk")->get_url("apk_api").$apkInfo[$k]['link'].$download_name;
			$apkInfo[$k]['size'] = $this->format_size($apkInfo[$k]['size']);
			
			//获取最大最小分辨率
			$reso = explode("-", $apkInfo[$k]['resolution']);
			$reso = D("Reso")->where(array("reso_id"=>array("IN", $reso)))->getField("reso_id, reso_name", true);
			$reso = $this->reso_sort($reso);
			$apkInfo[$k]['min_resolution'] = $reso[0];
			$apkInfo[$k]['max_resolution'] = $reso[count($reso)-1];
			
			$apkInfo[$k]['status'] = $onlineId == $apkInfo[$k]['id'] ? 1 : 0;
		}

		return (array)$apkInfo;
	}
	
	/**
	 * 获取的apk版本
	 * @param int $apkId
	 * @return array
	 */
	protected function get_apk_info($apkId)
	{
		$apkFields = array(
				"id",
				"app_name",
				"version_name"		=>"version",
				"version_code",
				"file_size"			=>"size",
				"apk_rsa"			=>"fingerprint",
				"apk_md5"			=>"md5_code",
				"file_path"			=>"link",
				"created_at"		=>"create_time",
				"min_sdk_version"	=>"min_sys_version",
				"changelog"			=>"changes",
				"reso"				=>"resolution",
				"status"
		);
		//只获取线上的版本
		$apkInfo = D("Dev://Apks")->where(array("id"=>$apkId))->field($apkFields)->order("id desc")->select();
	
		foreach ($apkInfo as $k=>$v)
		{
			$apkInfo[$k]['update_time'] = $apkInfo[$k]['create_time'];
			
			$download_name = sprintf("?n=%s%s.apk", trim($apkInfo[$k]["app_name"]), trim($apkInfo[$k]["version"]));
			//接口api单独走一个下载域名，在配置文件中进行配置
			$apkInfo[$k]['link'] = Helper("Apk")->get_url("apk_api").$apkInfo[$k]['link'].$download_name;
			$apkInfo[$k]['size'] = $this->format_size($apkInfo[$k]['size']);
				
			//获取最大最小分辨率
			$reso = explode("-", $apkInfo[$k]['resolution']);
			$reso = D("Reso")->where(array("reso_id"=>array("IN", $reso)))->getField("reso_id, reso_name", true);
			$reso = $this->reso_sort($reso);
			$apkInfo[$k]['min_resolution'] = $reso[0];
			$apkInfo[$k]['max_resolution'] = $reso[count($reso)-1];
				
			$apkInfo[$k]['status'] = $apkInfo[$k]['status'] == 3 ? 1 : 0;
		}
	
		return (array)$apkInfo;
	}
	
	/**
	 * 分辨率排序
	 */
	protected function reso_sort($reso)
	{
		$reso = (array)$reso;
		foreach ($reso as $k=>$r){
			list($w, $h) = explode("*", $r);
			$reso[$k] = $w*$h;
		}
		asort($reso);
		return array_keys($reso);
	}
	
	/**
	 * webp文件生成检查
	 */
	protected function webp_checker($imgs, $isScreen=false)
	{
		if(is_string($imgs))
			$imgs = explode("|", $imgs);
		
		foreach($imgs as $img)
		{
			$img = str_replace(helper("Apk")->get_url("screen"), helper("Apk")->get_path("screen"), $img);
			$img = str_replace(helper("Apk")->get_url("icon"), helper("Apk")->get_path("icon"), $img);
			$webp_img = $img.".webp";

			if(!is_file($webp_img) || mime_content_type($webp_img) !== "application/octet-stream")
				$this->webp_status = 0;

			//240x400截图识别
			if($isScreen)
			{
				$file_ext = substr(trim($img), "-3", 3);
				$screen_img = $img . "_240x400." . $file_ext;
				
				if(!is_file($screen_img))
					$this->webp_status = 0;
				
				$webp_img = $screen_img.".webp";
				if(!is_file($webp_img) || mime_content_type($webp_img) !== "application/octet-stream")
					$this->webp_status = 0;
			}
		}
	}
}
