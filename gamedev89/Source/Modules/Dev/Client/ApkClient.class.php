<?php
/**
 * apk应用处理的逻辑类
 *
 * @name ApkClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class ApkClient extends ApksModel {
	private static $daoObj;
	private $filePath; // 解压后临时文件目录(项目路径/Data/Attachments/dev/apks/2013/12/28/1388200562641/)
	private $apkPath; // Apk文件上传目录(项目路径/Data/Attachments/dev/apks/2013/12/28/)
	private $apkConfig; // Apk配置
	private $appId;
	private $otype; // 操作类型，0：上传新应用，1：更新版本，2：重新上传
	private $apkFileInfo;
	private $apkFileName;
	private $apkErrId = 9; // 错误ID，默认为文件名为空
	private $apkErr = array (
			1 => '产品已存在',
			2 => '您所上传的产品正在审核中',
			3 => '包名出错',
			4 => '未能查找到您要升级的产品',
			5 => '当前版本已是最新版本',
			6 => '创建Apk文件失败',
			7 => '解压文件时出错，可能为上传失败或者包损坏，请重试',
			8 => '读入Apk文件时出错，可能为上传失败或者包损坏，请重试',
			9 => '文件名为空',
			10 => '文件类型错误',
			11 => '包名与升级目标产品包名不一致',
			12 => '包异常',
			13 => '操作发生错误' 
	);
	private $screenPic = array('normal'=>11,'small'=>22,'large'=>33,'xlarge'=>44);
	
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Apks" );
		}
		return self::$daoObj;
	}
	function ApkClient() {
		$this->apkConfig = C ( 'APK' );
	}
	/**
	 * 处理上传Apk文件
	 * @param int $uid
	 * @param string $userName
	 * @param string $filename
	 * @param int $appId
	 * @param int $otype	操作类型(0: 新增apk, 1: 更新apk, 2: 重新上传apk)
	 * @param int $apkId
	 * @return int | array
	 */
	public function apk($uid, $userName, $filename, $appId, $otype = 0, $apkId = 0) {
		set_time_limit(0);
		$this->appId = $appId;
		$this->otype = $otype;
		if (! $filename)
			return $this->setErr ( 9 );
		$this->apkFileName = $filename;
		
		$this->apkFileInfo = $apkFileInfo = $this->readApkConfig ( $filename );
		
		// 判断读取Apk文件是否出错
		if (! $apkFileInfo || is_numeric ( $apkFileInfo ))
			return $this->setErr ( 8 );
			
		// 获取包名
		$package = $apkFileInfo ['info'] ['package'];
		// 判断包名是否存在
		if (! $package)
			return $this->setErr ( 3 );
		// 获取VersionCode
		$version_code = $apkFileInfo ['info'] ['version_code'];
		
		$appdata ['app_name'] = trim($apkFileInfo ['info'] ['name']);
		$appdata ['created_at'] = time ();
		$appdata ['updated_at'] = time ();
		$date = date ( 'Y-m-d' ); // 创建日期
		$appsModel = D ( "Apps" );
		// 检查是否已经存在此包
		$map = array ('package' => $package);
		APP_DEBUG && Log::write(print_r($map, true), "package_map");
		// 先判断此包是否存在apps表中
		$app_count = $appsModel->where ( $map )->count ();
		APP_DEBUG && Log::write(print_r($appsModel->_sql(), true), "appinfo_sql");
		APP_DEBUG && Log::write($app_count, "app_count");
		// 判断此包是否已经存在
		if ($app_count) {				//已经存在此包
			//包名与升级目标产品包名不一致
			if(!empty($appId)){
				$app_map = array("package"=>$package, "id"=>$appId);
				$app_count = $appsModel->where ( $app_map )->count ();
				APP_DEBUG && Log::write(print_r($appsModel->_sql(), true), "app_count_sql");
				if($app_count==0)	return $this->setErr(11);
			}
			// 判断当前apk版本是否为本人所有	
			$ownner_map = array("package"=>$package, "author_id"=>$uid);
			$ownner_apps_count = $appsModel->where($ownner_map)->count();
			$ownner_apks_count = self::dao()->where($ownner_map)->count();
			if ($ownner_apps_count==0 && $ownner_apks_count==0) {
				// 获取其他用户提交的apk状态为非初始状态0的总数
				$apks_count = self::dao()->where(array('package' => $package, "status"=>array("neq", self::APK_EDIT)))->count();
				APP_DEBUG && Log::write(print_r($apks_count, true), "other_uid_apks_count");
				if ($apks_count == 0) {
					$data = array_merge($appdata, $map);
					$data['status'] = 0;
					$data['author_id'] = $uid;
					$data = array_map("trim", $data);
					$this->appId = $appsModel->add($data);
				}else{		// 别人已经上传此应用, 并且为非初始状态
					return $this->setErr ( 1 );		// 包已经存在,不是自己上传
				}
			}else{
				$apkInfo = self::dao()->where($ownner_map)->order("id desc")->find();
				// 包名已经存在，也是自己上传，而且类型为上传新应用，则提示包已经存在
				//if ($otype == "0") 	return $this->setErr( 1 );
				// 检测上传版本是否小于已经存在的版本(上传新app和更新版本时)
				if ($otype != "2"){
					$version_code_compare_map = array_merge($ownner_map, array("version_code"=>array('gt', $version_code)));
					$is_laster_version = self::dao()->where($version_code_compare_map)->count();
					
					if($is_laster_version)	return $this->setErr ( 5 );
				}
				// 判断包名是否一致
				if ($package != $apkInfo ['package']) 	return $this->setErr ( 11 );
				//获取本人的appid
				$this->appId = $appsModel->getAppIdByUid($package, $uid);
				$date = substr ( $apkInfo ['created_at'], 0, 10 );
				$apkId = ($otype == "2" && !empty($apkId)) ? $apkId : $apkInfo['id'];
			}
		}else {		// 不存在此包
			//更新版本的时候,上传的应用和当前的应用不一致
			if(!empty($appId))	return $this->setErr(11);
			$data = array_merge($appdata, $map);
			$data['status'] = 0;
			$data['author_id'] = $uid;
			$this->appId = $appsModel->add($data);
		}
		
		$appdata['app_id'] = $this->appId;
		$apkdata = array_merge($appdata, $map);
		// 新增APK信息(上传新应用和更新版本时)
		if ($otype != "2") {
			unset($apkdata['updated_at']);
			$apkdata ['author_id'] = $uid;
			$apkdata ['uploader_id'] = $uid;
			$apkdata ['author_name'] = $userName;
			$apkdata ['status'] = parent::APK_EDIT;
			
			$apkdata = array_map("trim", $apkdata);
			$apkId = self::dao()->add ($apkdata);
			APP_DEBUG && Log::write(self::dao()->_sql(),"add_apk_sql");
		}
		if(empty($apkId))	return $this->setErr(4);
		
		//获取上传apk文件存放路径(绝对路径)
		$apksPath = Helper("Apk")->get_path("apk");
		//设置icon上传路径(绝对路径)
		$iconsPath = Helper("Apk")->get_path("icon") . str_replace ( "-", "/", $date ) . "/";
		
		//获取icon图片路径
		$iconfrom = $apkFileInfo ['info'] ['icon'];
		if (strripos ( $iconfrom, '.' )) {
			$icon = $iconsPath . $apkId . date ( 'ymdHi' ) . substr ( $iconfrom, strripos ( $iconfrom, '.' ) );
		} else {
			$icon = $iconsPath . $apkId . date ( 'ymdHi' ) . substr ( $iconfrom, strripos ( $iconfrom, '/' ) + 1 ) . '.png';
		}
		
		// 创建文件目录
		mkdirs ( $apksPath );
		mkdirs ( $iconsPath );

		// 解包
		$unzip = $this->unzipApk ( $filename );
		if ($unzip)
			return $this->setErr ( $unzip ); // 解包出错
		
		list ( $_icon_width, $_icon_height, $_icon_type, $_icon_attr ) = getimagesize ( $this->filePath . $iconfrom );
		if ($_icon_width != $_icon_height || !in_array($_icon_height, array('72', '96', '144')))
			$_icon_support = false;
		else
			$_icon_support = $_icon_width == 72 ? 2 : ($_icon_width == 96 ? 3 : 4);
		
		// 移动图标
		$this->mvFile ( $this->filePath . $iconfrom, $icon );
		$this->rmFile ( $this->filePath );		//删除临时目录
		
		$apkFileData = $map;
		
		$apkFileData ['file_path'] = substr($this->apkPath . $filename, strpos($this->apkPath . $filename, "apks/")+5);
		if ($apkFileInfo ['info'] ['version_code'])
			$apkFileData ['version_code'] = $apkFileInfo ['info'] ['version_code'];
		if ($apkFileInfo ['info'] ['version_name'])
			$apkFileData ['version_name'] = $apkFileInfo ['info'] ['version_name'];
		if ($apkFileInfo ['info'] ['min_sdk_version'])
			$apkFileData ['min_sdk_version'] = $apkFileInfo ['info'] ['min_sdk_version'];
		if ($apkFileInfo ['permission'])
			$apkFileData ['uses_permission'] = $apkFileInfo ['permission'];
		if(!empty($icon))
			$apkFileData ['icon'] = $icon;
		if ($apkFileInfo ['SUPPORTS-SCREENS'] [0])
			$apkFileData ['supports_screens'] = $apkFileInfo ['SUPPORTS-SCREENS'] [0];
		if ($apkFileInfo ['DENSITIES'] [0])
			$apkFileData ['densities'] = $apkFileInfo ['DENSITIES'] [0];
		if ($apkFileInfo ['size'])
			$apkFileData ['file_size'] = $apkFileInfo ['size'];
		if (isset ( $apkFileInfo ['targetSdkVersion'] [0] )) {
			$targetSdkVersion = $apkFileInfo ['targetSdkVersion'] [0]; // 目标SDK
		} else {
			$targetSdkVersion = 0;
		}
		$apkFileData ['apk_md5'] = md5_file ( $this->apkPath . $filename );
		$targetSdkVersion = str_replace ( "'", '', $targetSdkVersion );
		if ($targetSdkVersion)
			$apkFileData ['target_sdk_version'] = $targetSdkVersion;
		if ($apkFileInfo ['info'] ['min_sdk_version'])
			$min_sdk_version = $apkFileInfo ['info'] ['min_sdk_version'];
			
		//获取支持的频幕大小
		$screens = str_replace("'", "", $apkFileData ['supports_screens']);
		$screens = explode ( " ", trim($screens));
		$screenArr = array();
		foreach ( $screens as $v ) {
			$screen = trim($v);
			if(!empty($screen))
				$screenArr[] = $this->screenPic[$screen];
		}
		
		if(!empty($screenArr))
			$apkFileData['reso'] = implode("-", $screenArr);
		// 更新Apk文件记录
		$where = array("id"=>$apkId);
		$apkFileData['bsdiff'] = 0;
		$apkFileData['sign'] = 0;
		$apkFileData['safe_status'] = 0;
		$res = self::dao()->where($where)->save ($apkFileData);
		if (false === $res) {
			return $this->setErr(13);
		}
		
		// 插入版本适应表
		foreach ( $this->apkConfig ['SDK_VER'] as $k => $v ) {
			if ($k >= $min_sdk_version && ($k <= $targetSdkVersion || ! $targetSdkVersion)) {
				$adaptersData [$v] = 1;
			} else {
				$adaptersData [$v] = 0;
			}
		}
		
		// 添加icon
		if($_icon_support != false){
			$icon = str_replace(Helper("Apk")->get_path("icon"), "", $icon);
			$iconData = array (
					'app_id' => $this->appId,
					'apk_id' => $apkId,
					'created_at' => time(),
					'updated_at' => time(),
					'status' => 1,
					'file_size' => 0,
					'file_path' => $icon,
					'type' => $_icon_support,
			);
			$appsModel = D ( 'app_picture' );
			$iconid = $appsModel->add ($iconData);
			if (APP_DEBUG)	Log::write($appsModel->_sql(), "add_app_picture_sql");
		}
		
		//同步联运的appid
		D("Union")->set_key_appid($package, $uid, $this->appId);
		
		return array("app_id"=>$this->appId, "apk_id"=>$apkId);
	}
	
	/**
	 * 解压Apk文件
	 *
	 * @param string $filename
	 *        	Apk文件名
	 */
	public function unzipApk($filename) {
		if (APP_DEBUG)	Log::write ( '解压Apk文件开始', 0 );
		if (! $this->apkPath)
			$this->setPath (); // 设置Apk文件路径
		$this->filePath = $this->apkPath . substr ( $filename, 0, strripos ( $filename, '.' ) ) . '/';
		$command = 'unzip -n ' . $this->apkPath . $filename . '  -d ' . $this->filePath;
		exec ( $command, $out, $returnval );
		if ($returnval) {
			if (APP_DEBUG)	Log::write ( "\r\n解压文件时出错，返回值：" . $returnval . '。应用ID：' . $this->appId . '。执行命令行：' . $command, 1 );
			if ($returnval != 2)
				return $this->setErr ( 7 );
		}
		
		return 0;
	}
	/**
	 * 读入指定Apk配置文件
	 *
	 * @param unknown_type $xmlFile
	 *        	apk配置文件XML路径
	 */
	public function readManifest($xmlFile = null) {
		if (! $xmlFile) {
			$xmlFile = $this->filePath . 'AndroidManifest.xml';
		}
		$xml_parser = xml_parser_create ();
		$data = file_get_contents ( $xmlFile );
		xml_parse_into_struct ( $xml_parser, $data, $vals, $index );
		xml_parser_free ( $xml_parser );
		$data = array ();
		foreach ( $vals as $v ) {
			if (isset ( $v ['attributes'] )) {
				$data [$v ['tag']] [] = $v ['attributes'];
			}
		}
		
		$apkInfo ['package'] = $data ['MANIFEST'] [0] ['PACKAGE'];
		$apkInfo ['version_code'] = $data ['MANIFEST'] [0] ['ANDROID:VERSIONCODE'];
		$apkInfo ['version_name'] = $data ['MANIFEST'] [0] ['ANDROID:VERSIONNAME'];
		$apkInfo ['min_sdk_version'] = $data ['USES-SDK'] [0] ['ANDROID:MINSDKVERSION'];
		$apkInfo ['icon'] = $data ['APPLICATION'] [0] ['ANDROID:ICON'];
		$apkInfo ['name'] = $data ['APPLICATION'] [0] ['ANDROID:LABEL'];
		
		$data ['APKINFO'] = $apkInfo;
		return $data;
	}
	/**
	 * 读入Akp配置文件
	 *
	 * @param string $filename	(2014/01/22/139040220276.apk)	        	
	 * @param string $apkPath        	
	 */
	public function readApkConfig($filename, $apkPath = NULl) {
		// 设置apk文件路径
		if (! $this->apkPath)
			$this->setPath ( $apkPath ); 
		$command = $this->apkConfig ['APKTOOL'] . 'aapt d badging ' . $this->apkPath . $filename;
		if (APP_DEBUG)
			Log::write($command, "readApkConfig_command");
		exec ( $command, $data, $returnval );
		if ($returnval)
			return $this->setErr ( 8 ); // 读入出错
		if (APP_DEBUG)
			Log::write(var_export($data, true), "readApkConfig_command_result");
		$permission = '';
		$datastr = implode ( $data );
		$pattern_perm = "/uses-permission:'(.*)'/isU";
		preg_match_all ( $pattern_perm, $datastr, $m );
		
		foreach ( $data as $v ) {
			$tmp = explode ( ':', $v );
			if (count ( $tmp ) > 1) {
				$info [mb_strtoupper ( $tmp [0] )] [] = $tmp [1];
			} else {
				$info [] = $v;
			}
		}
		
		if ($m) {
			$cnt = count ( $m [1] );
			for($i = 0; $i < $cnt; $i ++) {
				$permission .= $permission ? "|" . $m [1] [$i] : $m [1] [$i];
			}
		}
		
		$pattern_name = "/name='(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['package'] = $m [1];
		
		// 名
		$pattern_name = "/label='(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['name'] = trim($m [1],"  ");
		
		// 图标
		$pattern_name = "/icon='(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['icon'] = $m [1];
		
		$pattern_name = "/versionCode='(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['version_code'] = $m [1] ? trim($m [1]) : 0;
		
		$pattern_name = "/versionName='(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['version_name'] = trim($m [1]);
		
		$pattern_name = "/sdkVersion[:=]'(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['min_sdk_version'] = $m [1];
		$info ['permission'] = $permission;
		
		$pattern_name = "/targetSdkVersion[:=]'(.*)'/isU";
		preg_match ( $pattern_name, $datastr, $m );
		$info ['info'] ['target_sdk_version'] = $m [1];
		$info ['size'] = filesize ( $this->apkPath . $filename );
		return $info;
	}
	/**
	 * 设置apk文件所在目录
	 *
	 * @param unknown_type $path        	
	 */
	public function setPath($path = null) {
		if ($path) {
			$this->apkPath = $path;
		} else {
			
			$this->apkPath = $this->apkConfig ['PATH'];
		}
	}
	/**
	 * 删除目录或者文件
	 *
	 * @param string $from        	
	 * @return int
	 */
	private function rmFile($from) {
		$command = 'rm -rf ' . $from;
		// dump($command);
		exec ( $command, $out, $returnval );
		
		return $returnval;
	}
	/**
	 * 移动文件
	 *
	 * @param unknown_type $from        	
	 * @param unknown_type $to        	
	 */
	public function mvFile($from, $to) {
		$command = 'cp -R ' . $from . ' ' . $to; // windows下命令
		if (APP_DEBUG)	Log::write($command, "mvFile_command");
		exec ( $command, $out, $returnval );
		
		return $returnval;
	}
	/**
	 * 设置错误
	 *
	 * @param int $code        	
	 */
	public function setErr($errCode, $removeFile=false) {
		if (APP_DEBUG)	Log::write ( $this->apkErr [$errCode], "error_code" );
		$filepath = substr ( $this->apkFileName, 0, strrpos ( $this->apkFileName, '.' ) );
		if($removeFile){
			$this->rmFile ( $this->apkPath . $filepath );
			$this->rmFile ( $this->apkPath . $this->apkFileName );
		}
		
		$this->apkErrId = $errCode;
		return $errCode;
	}
	/**
	 * 获取错误信息
	 * @param int $code
	 * @return multitype:number NULL string Ambigous <mixed, boolean, NULL, multitype:>
	 */
	public function getErr($code = null) {
		if ($code == 1) {
			$appinfo = D ( 'Apps' )->getAppInfoByPkg($this->apkFileInfo ['info'] ['package']);
		}
		$msg = $this->apkErr [$code];
		if ($this->apkFileInfo ['info']) {
			$msg .= "，包名：" . $this->apkFileInfo ['info'] ['package'] . '。版本号：' . $this->apkFileInfo ['info'] ['version_code'];
		}
		if (intval ( $code ))
			return array (
					'IsErr' => 1,
					'code' => $code,
					'error' => $msg,
					'package' => $this->apkFileInfo ['info'] ['package'],
					'appinfo' => $appinfo 
			);
	}
	/**
	 * 返回
	 */
	public function __get($param) {
		return $this->{$param};
	}
}

?>