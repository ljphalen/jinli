<?php
class AppsAction extends BaseAction {
	private $pageSize = 12;
	private $domainUrl;
	private $detailUrl;
	private $reUploadStatus;
	private $appAll = 9999;
	
	function _initialize() {
		parent::_initialize ();
		
		// 检查开发者资料审核是否通过
		$is_perfect = D ( 'Accountinfo' )->isPerfect ( $this->uid );
		if (! $is_perfect) {
			$data ['msg'] = "请完善您的基本信息";
			header ( 'Location: ' . U ( "Login/perfect/" ) );
		}
		
		$this->domainUrl = U ( "@dev" );
		// 不可编辑状态
		$this->detailUrl = array (
				self::APK_AUDITING,
				self::APK_ONLINE,
				self::APK_OFFLINE,
				self::APK_TEST_PASS 
		);
		// 允许重新上传的状态
		$this->reUploadStatus = array (
				self::APK_EDIT,
				self::APK_AUDIT_NOT_PASS 
		);
		
		loadClient ( Array (
				"Accounts",
				"Accountinfo",
				"Apk" 
		) );
	}
	/*
	 * 应用首页
	 */
	function index() {
		import ( "Extend.General.Page", LIB_PATH ); //导入分页类
		$Apks = D ( "Apks" );
		$Apps = D ( "Apps" );
		
		$status = $this->_get ( "status", "intval", $this->appAll );
		if ($status !== $this->appAll && ! in_array ( $status, array_keys ( $this->apkStatus ) )) {
			$this->error ( "参数错误" );
		}
		$page = $this->_get ( "page", "intval", 1 );
		
		// 先获取所有的应用id
		$userAppIds = $Apps->where ( array (
				'author_id' => $this->uid 
		) )->field ( "group_concat(id) ids" )->find ();
		// 判断是否有应用
		if (empty ( $userAppIds )) { // 无应用
			$apknum = array (
					'status' => array (),
					'all' => 0 
			);
		} else { // 有应用
		       // 获取所有应用中最新的apk id
			$apkIds = $Apks->where ( array (
					'author_id' => $this->uid,
					"app_id" => array (
							"in",
							$userAppIds ['ids'] 
					) 
			) )->group ( "app_id" )->field ( "max(id) maxid" )->select ();
			$apkMap = array (
					'author_id' => $this->uid 
			);
			if (! empty ( $apkIds )) {
				$newIds = array ();
				foreach ( $apkIds as $id ) {
					$newIds [] = $id ['maxid'];
				}
				$apkMap ['id'] = array (
						'in',
						$newIds 
				);
			}
			// 获取各种状态对应的app总数
			$apksNum = $Apks->where ( $apkMap )->group ( "status" )->field ( "status, count(id) as count" )->select ();
			$apkStatus = array ();
			if (! empty ( $apksNum )) {
				foreach ( $apksNum as $v ) {
					$apkStatus [$v ['status']] = $v ['count'];
				}
				$apknum ['status'] = $apkStatus;
			}
			$apknum ['all'] = $count = $Apps->where ( array (
					"author_id" => $this->uid 
			) )->count ();
		}
		
		$statusStyle = array (
				self::APK_EDIT => 'btn-info',
				self::APK_AUDITING => 'btn-info',
				self::APK_AUDIT_NOT_PASS => 'btn-danger',
				self::APK_TEST_PASS => 'btn-primary',
				self::APK_ONLINE => 'btn-success',
				self::APK_OFFLINE => 'btn-danger' 
		);
		
		$map = array ();
		if ($status !== $this->appAll)
			$map ["status"] = $status;
		$map = array_merge ( $map, $apkMap );
		
		$Page = new Page ( $count, $this->pageSize ); // 实例化分页类 传入总记录数
		$Page->setConfig ( "first", "首页" );
		$Page->setConfig ( "prev", "« 上一页" );
		$Page->setConfig ( "next", "下一页 »" );
		$Page->setConfig ( "last", "尾页" );
		$Page->setConfig ( "theme", "<ul class=\"pagination viciao\">%first% %upPage% %linkPage% %downPage% %end%</ul>" );
		$show = $Page->show (); // 分页显示输出
		$apksList = $Apks->where ( $map )->order("id desc")->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		$apkArr = array ();
		foreach ( $apksList as $key => $value ) {
			$apkArr [$key] = $value;
			$apkWhere = array (
					"author_id" => $this->uid,
					"app_id" => $value ['app_id'] 
			);
			$apkArr [$key] ['status_desc'] = $this->apkStatus [$value ['status']];
			$iconInfo = D ( "Picture" )->getApkIcon ( $value ['app_id'], $value ['id'] );
			$apkArr [$key] ['icon'] = Helper ( "Apk" )->get_url ( "icon" ) . $iconInfo ['file_path'];
			$apkArr [$key] ['union'] = D ( 'Union' )->getUnionField ( $value ['package'], $this->uid );
		}
		$pageinfo = array ();
		
		$pageinfo ['curr_url'] = U ( "apps" );
		$pageinfo ['condition'] = array (
				"page" => 1,
				"status" => 1 
		);
		// $pageinfo['pagecount'] = ceil($count/$pagesize);
		$this->assign ( 'page', $show ); // 赋值分页输出
		$this->assign ( 'count', $count );
		$this->assign ( 'pageSize', $this->pageSize );
		$this->assign ( 'pageinfo', $pageinfo );
		$this->assign ( 'apkinfo', $apkArr );
		$this->assign ( 'apknum', $apknum );
		$this->assign ( 'statusStyle', $statusStyle );
		$this->display ();
	}
	function apkUpload() {
		set_time_limit ( 0 );
		$appId = $this->_request ( "appId", "intval", 0 );
		$apkId = $this->_request ( "apkId", "intval", 0 );
		$type = $this->_request ( "type", "intval", 0 );
		$return = array ();
		if (! empty ( $_FILES )) {
			$apkConfig = C ( 'APK' );
			import ( "ORG.Net.UploadFile" );
			$upload = new UploadFile ();
			
			// 设置上传文件大小(300M)
			$upload->maxSize = $apkConfig ['MAXSIZE'];
			// 设置上传文件类型(apk)
			$upload->allowExts = $apkConfig ['EXTS'];
			// 设置附件上传目录(绝对路径)
			$savePath = Helper ( "Apk" )->get_path ( "apk" ) . date ( 'Y/m/d/' );
			if (APP_DEBUG)
				Log::write ( $savePath, Log::DEBUG );
			mkdirs ( $savePath );
			$upload->savePath = $savePath;
			$upload->saveRule = time () . rand ( 0, 1000 );
			if (! $upload->upload ()) {
				// 捕获上传异常
				$return [0] ['error'] = $upload->getErrorMsg ();
			} else {
				// 取得成功上传的文件信息
				$return = $upload->getUploadFileInfo ();
				
				if (APP_DEBUG)
					Log::write ( print_r ( $return, true ), "upload_info" );
				
				$fileName = $return [0] ['savename'];
				
				if (! is_dir ( $savePath )) {
					$return [0] ['error'] = '上传文件发生异常';
				}
				
				// 获取包信息
				$apkClient = new ApkClient ();
				// 设置apk文件路径为绝对路径
				$apkClient->setPath ( $savePath );
				// 处理apk信息
				$apkInfo = $apkClient->apk ( $this->uid, $this->user ['nickname'], $fileName, $appId, $type, $apkId );
				
				if (APP_DEBUG)
					Log::write ( print_r ( $apkInfo, true ), "apk_info" );
				if (is_int ( $apkInfo )) { // 读取包信息失败
					$return [0] ['error'] = $apkClient->getErr ( $apkInfo );
					$return [0] ['errorCode'] = $apkInfo;
					echo json_encode ( array (
							"files" => $return 
					) );
					die ();
				}
				if (is_array ( $apkInfo )) {
					$return [0] ['apk'] = $apkInfo;
				}
			}
		}
		unset ( $return ['savePath'] );
		echo json_encode ( array (
				"files" => $return 
		) );
		die ();
	}
	function ftpUpload() {
		$ftpInfo = D ( "Ftpd" )->getUser ( $this->uid );
		
		//帐号资料未审核通过，不允许使用ftp上传功能
		$model = new AccountinfoModel();
		if($this->user['status'] != $model::STATUS_SUC)
			$this->error("您的资料还未通过审核，暂不能使用FTP上传功能，请等待审核");
		
		if (! empty ( $ftpInfo )) {
			$data = array (
					"account" => $ftpInfo ['User'],
					"pwd" => $ftpInfo ['Password'],
					"server" => C ( "FTPD_HOST" ),
					"port" => C ( "FTPD_PORT" ) 
			);
		}
		$this->assign ( "ftpaccount", $data );
		$this->display ();
	}
	function get_ftpaccount() {
		$model = D ( "Ftpd" );
		$ftpInfo = $model->field ( array ( "ID", "User", "Password" ) )->where ( array ("ID" => $this->uid) )->find ();
		
		if (empty ( $ftpInfo ))
			$ftpInfo = $model->addUser ( $this->uid );
		
		if($this->_get("reset", "intval", 0) > 0)
			$ftpInfo = $model->reset ( $this->uid );
		
		$ftpInfo ["port"] = C ( "FTPD_PORT" );
		$ftpInfo ["host"] = C ( "FTPD_HOST" );
		$ftpInfo ["id"] = $ftpInfo ['ID'];
		$this->ajaxReturn ( $ftpInfo );
	}
	
	/*
	 * 应用管理
	 */
	function manage() {
		$apk_id = $this->_get ( "id", "intval", 0 );
		// 包文件信息
		$Apks = D ( "Apks" );
		$apk_one = $Apks->where ( array (
				'id' => $apk_id,
				"author_id" => $this->uid 
		) )->order ( 'id desc' )->find ();
		if (empty ( $apk_one )) {
			$this->error ( "未能找到对应的APK信息" );
		}
		$apkinfo = $Apks->where ( array (
				'app_id' => $apk_one ['app_id'] 
		) )->order ( 'id desc' )->select ();
		// 应用信息
		$Apps = D ( "Apps" );
		$appinfo = $Apps->where ( array (
				'id' => $apkinfo [0] ['app_id'] 
		) )->find ();
		$appinfo ['union'] = D ( 'Union' )->getUnionField ( $appinfo ['package'], $this->uid );
		// 应用的截图和icon信息
		$Picture = D ( "Picture" );
		foreach ( $apkinfo as $key => $value ) {
			$icon = $Picture->getApkIcon ( $apk_one ['app_id'], $value ['id'] );
			$apkinfo [$key] ['icon_url'] = Helper ( "Apk" )->get_url ( "icon" ) . $icon ['file_path'];
			$appInfoUrl = U ( "apps/info", array (
					'id' => $value ['id'] 
			) );
			$apkinfo [$key] ['status_desc'] = $this->apkStatus [$value ['status']];
			$apkinfo [$key] ['detail_url'] = $appInfoUrl;
		}
		
		$this->assign ( 'apkinfo', $apkinfo );
		$this->assign ( 'appinfo', $appinfo );
		$this->display ();
	}
	/*
	 * 编辑应用信息
	 */
	function info() {
		$apk_id = $this->_get ( "id", "intval", 0 );
		
		// 包文件信息
		$Apks = D ( "Apks" );
		$where = array (
				'author_id' => $this->uid,
				'id' => $apk_id 
		);
		$apkinfo = $Apks->where ( $where )->find ();
		
		if (empty ( $apkinfo ))
			$this->error ( "未能找到对应的APK信息", U ( "Apps/index" ) );
		
		$package = $apkinfo ['package'];
		$app_id = $apkinfo ['app_id'];
		
		// 分辨率信息
		$Reso = D ( "Reso" );
		$resoinfo = $Reso->select ();
		
		// 应用信息
		$Apps = D ( "Apps" );
		$appinfo = $Apps->where ( array (
				'id' => $app_id 
		) )->find ();
		// 获取前一个apk的信息
		$where = array (
				"app_id" => $app_id,
				"id" => array (
						"neq",
						$apkinfo ['id'] 
				),
				"author_id" => $this->uid 
		);
		$preApkinfo = $Apks->where ( $where )->order ( "id desc" )->find ();
		if (APP_DEBUG) {
			Log::write ( print_r ( $preApkinfo, true ), "pre_apk_info" );
			Log::write ( print_r ( $apkinfo, true ), "apk_info" );
		}
		//显示apk已经编辑过的属性值
		if (! empty ( $preApkinfo )) {
			$version_code = $apkinfo ['version_code'];
			$version_name = $apkinfo ['version_name'];
			$min_sdk_version = $apkinfo ['min_sdk_version'];
			$file_path = $apkinfo ['file_path'];
			$file_size = $apkinfo ['file_size'];
			$apk_md5 = $apkinfo ['apk_md5'];
			$created_at = $apkinfo ['created_at'];
			$s = $apkinfo ['status'];
			// 更新版本情况(用前一个apk的已有属性显示在修改信息中)
			if (! empty ( $preApkinfo ['category_two'] ) && empty ( $apkinfo ['category_two'] ) 
					&& empty ( $apkinfo ['developer'] ) && intval ( $apkinfo ['status'] ) == self::APK_EDIT) {
				$apkinfo = $preApkinfo;
				$apkinfo ['id'] = $apk_id;
			} else {
				$apkinfo = array_merge ( $preApkinfo, $apkinfo );
			}
			//不可覆盖的apk属性
			$apkinfo ['status'] = $s;
			$apkinfo ['version_name'] = $version_name;
			$apkinfo ['version_code'] = $version_code;
			$apkinfo ['min_sdk_version'] = $min_sdk_version;
			$apkinfo ['file_path'] = $file_path;
			$apkinfo ['file_size'] = $file_size;
			$apkinfo ['apk_md5'] = $apk_md5;
			$apkinfo ['created_at'] = $created_at;
		}
		$cate_two_info = explode ( '-', $apkinfo ['category_two'] );
		$fee_mode = explode ( "-", $apkinfo ['fee_mode'] );
		
		$apkinfo ['union'] = D ( 'Union' )->getUnionField ( $package, $this->uid );
		
		// 分类信息
		$this->_getCategory ();
		// 获取截图和icon信息
		$this->_screenAndIcon ( $app_id, $apkinfo ['id'] );
		// 获取所有游戏标签信息
		$this->_getLebals ();
		// 获取收费模式信息
		$this->_getFeeType ();
		
		$this->assign ( 'resoinfo', $resoinfo );
		$this->assign ( 'cate_two_info', $cate_two_info );
		$this->assign ( 'fee_mode_keys', $fee_mode );
		$this->assign ( 'apkinfo', $apkinfo );
		$this->assign ( 'appinfo', $appinfo );
		$this->assign ( "reUploadStatus", $this->reUploadStatus );
		
		$template = "info";
		if (in_array ( $apkinfo ['status'], $this->detailUrl )) {
			$template = "detail";
		}
		$this->display ( $template );
	}
	/*
	 * 截图上传
	 */
	function screenUpload() {
		$post = $this->_post ();
		if (empty ( $post ) || empty ( $_FILES ))
			die ( 401 );
		
		$data ['apk_id'] = $post ['apk_id'];
		$data ['app_id'] = $post ['app_id'];
		$data ['type'] = 1;
		
		// 获取统一的截图的路径和url链接
		$screenPath = Helper ( "Apk" )->get_path ( "screen" );
		$screenUrl = Helper ( "Apk" )->get_url ( "screen" );
		$screenshot_path = $screenPath . date ( 'Y/m/d/' );
		
		$upfileinfo = $this->_upload ( $screenshot_path );
		if (is_array ( $upfileinfo )) {
			$savePath = date ( 'Y/m/d/' ) . $upfileinfo [0] ['savename'];
			$picurl = $screenUrl . $savePath;
			list ( $width, $height, $type, $attr ) = $info = getimagesize ( $screenPath . $savePath );
			if ($width != 480 || $height != 800) {
				$return [0] = array (
						'error' => "分辨率不匹配，请确保您的截图分辨率是：480*800",
						'errorCode' => 1 
				);
				die ( json_encode ( $return ) );
			}
			
			import("ORG.Util.Image");
			$image = $screenshot_path.$upfileinfo [0] ['savename'];
			list($thumbName, $thumbExt) = explode(".", $upfileinfo [0] ['savename']);
			$thumbName = sprintf("%s_240x400.%s", $image, $thumbExt);
			Image::thumb($image, $thumbName,'', 240, 400, true);
			
			$Picture = D ( "Picture" );
			$data ['file_size'] = $upfileinfo [0] ['size'];
			$data ['file_ext'] = $upfileinfo [0] ['extension'];
			$data ['file_path'] = $savePath;
			$res = $Picture->data ( $data )->add ();
			if ($res) {
				$return [0] ['picurl'] = $picurl;
				$return [0] ['id'] = $res;
				die ( json_encode ( $return ) );
			} else {
				$upfileinfo = "文件保存失败";
			}
		}
		if (is_string ( $upfileinfo )) {
			$return [0] = array (
					'error' => $upfileinfo,
					'errorCode' => 1 
			);
			die ( json_encode ( $return ) );
		}
	}
	/*
	 * ICON上传
	 */
	function iconUpload() {
		$post = $this->_post ();
		if (empty ( $post ) || empty ( $_FILES ))
			die ( 401 );
		
		$iconId = $post ['icon_id'];
		$data ['apk_id'] = $post ['apk_id'];
		$data ['app_id'] = $post ['app_id'];
		$data ['type'] = $type = $post ['icon_type'];
		// 获取统一的icon的路径和url链接
		$iconPath = Helper ( "Apk" )->get_path ( "icon" );
		$iconUrl = Helper ( "Apk" )->get_url ( "icon" );
		$icon_path = $iconPath . date ( 'Y/m/d/' );
		$limitSize = $type == "2" ? 72 : ($type == "3" ? 96 : 144);
		
		$upfileinfo = $this->_upload ( $icon_path, array (
				"png" 
		) );
		if (is_array ( $upfileinfo )) {
			$savePath = date ( 'Y/m/d/' ) . $upfileinfo [0] ['savename'];
			$picurl = $iconUrl . $savePath;
			list ( $width, $height, $type, $attr ) = getimagesize ( $iconPath . $savePath );
			if ($width != $limitSize && $height != $limitSize) {
				APP_DEBUG && Log::write(sprintf("w:%s, h:%s", $width, $height), "icon_upload");
				$return [0] = array (
						'error' => "请确保您的图标分辨率是：{$limitSize}*{$limitSize}",
						'errorCode' => 1 
				);
				die ( json_encode ( $return ) );
			}
			$Picture = D ( "Picture" );
			$data ['file_size'] = $upfileinfo [0] ['size'];
			$data ['file_ext'] = $upfileinfo [0] ['extension'];
			$data ['file_path'] = $savePath;
			if (! empty ( $iconId )) {
				$res = $Picture->where ( array (
						'id' => $iconId 
				) )->save ( $data );
			} else {
				$res = $Picture->data ( $data )->add ();
			}
			if ($res) {
				$return [0] ['picurl'] = $picurl;
				die ( json_encode ( $return ) );
			} else {
				$upfileinfo = "文件保存失败";
			}
		}
		if (is_string ( $upfileinfo )) {
			$return [0] = array (
					'error' => $upfileinfo,
					'errorCode' => 1 
			);
			die ( json_encode ( $return ) );
		}
	}
	function appsave() {
		$post = $this->_post ();
		
		$app_id = $post ['app_id'];
		$version_code = $post ['version_code'];
		$package = $post ['package'];
		$apk_id = $post ['apk_id'];
		
		$bsdiff = $post ['bsdiff'];
		
		$this->_checkUserByApp ( $app_id, $this->uid );
		
		$_POST ['category_two'] = implode ( '-', $post ['category_two'] );
		$_POST ['fee_mode'] = implode ( '-', $post ['fee_mode'] );
		$_POST ['reso'] = implode ( '-', $post ['reso'] );
		$_POST ['label'] = json_encode ( $post ['label'] );
		
		$Apks = D ( "Apks" );
		if (isset ( $post ['saveSub'] ) && $post ['saveSub'] == "1") {
			empty ( $post ['category_two'] ) && $this->error ( "子分类不能为空！" );
			empty ( $post ['developer'] ) && $this->error ( "开发者不能为空！" );
			empty ( $post ['reso'] ) && $this->error ( "分别率不能为空！" );
			empty ( $post ['description'] ) && $this->error ( "应用介绍不能为空！" );
			$_POST ['status'] = 1;
			
			// 验证是否已经存在此应用(被别人上传,并为初始状态)
			$where = array (
					"package" => $package,
					"status" => array (
							"neq",
							0 
					),
					"author_id" => array (
							"neq",
							$this->uid 
					) 
			);
			$apkInfo = $Apks->where ( $where )->find ();
			if ($apkInfo)
				$this->error ( "产品已存在，请联系管理员申请认领此应用" );
		} else {
			$_POST ['status'] = 0;
		}
		unset ( $_POST ['saveSub'] );
		unset ( $_POST ['edit'] );
		
		$apkStatus = $Apks->where ( array (
				"id" => $apk_id 
		) )->getField ( "status" );
		if (intval ( $apkStatus ) > 1) {
			$this->error ( "当前状态为不可编辑模式" );
		}
		if (! $Apks->create ()) {
			$this->error ( $Apks->getError () );
		} else {
			$res = $Apks->where ( array (
					"id" => $apk_id 
			) )->save ();
			if ($res !== false) {
				// 非联运应用，即时发送安全检测
				if ($_POST ['is_join'] != 1)
					$res = D ( 'Safe' )->scanAll ( $apk_id );
				$this->success ( '保存成功！', U ( "apps/manage", array (
						"id" => $apk_id 
				) ) );
			} else {
				Log::write ( $Apks->_sql (), Log::EMERG );
				Log::write ( $Apks->getDbError (), Log::EMERG );
				$this->error ( '保存失败！' );
			}
		}
	}
	function upload() {
		$this->display ();
	}
	
	/**
	 * 解析ftp app
	 */
	function ftpapp() {
		$filename = $this->_post ( "filename", "trim", "" );
		if (empty ( $filename )) {
			$this->error ( "文件名不能为空" );
		}
		
		$savePath = Helper ( "Apk" )->get_path ( "ftp" ) . "/" . $this->uid . '/';
		Log::write ( "FTP FIND FILE :" . $savePath . $filename );
		if (! file_exists ( $savePath . $filename )) {
			mkdirs ( $savePath );
			
			// 尝试进行文件头信息读取
			$ftpInfo = D ( "Ftpd" )->getUser ( $this->uid );
			$ftp = sprintf ( "ftp://%s:%s@%s:%d/%s", $ftpInfo ["User"], $ftpInfo ["Password"], C ( "FTPD_HOST" ), C ( "FTPD_PORT" ), $filename );
			$shell = "/usr/bin/curl -I $ftp";
			APP_DEBUG && Log::write ( "FTP URL FETCH FILE :" . $shell );
			$result = shell_exec ( $shell );
			
			if (stripos ( $result, "Content-Length" )) {
				set_time_limit ( 0 );
				
				// @TODO 远程文件存在则进行下载，下载过程需要优化
				$dshell = "/usr/bin/curl -s $ftp -o " . $savePath . $filename;
				APP_DEBUG && Log::write ( "FTP URL DOWNLOAD FILE :" . $dshell );
				shell_exec ( $dshell );
				$this->error ( "成功下载FTP文件，请获取文件信息" );
			}
		}
		
		if (file_exists ( $savePath . $filename )) {
			// 获取包信息
			$apkClient = new ApkClient ();
			// 设置apk文件路径为绝对路径
			$apkClient->setPath ( $savePath );
			// 处理apk信息
			$apkInfo = $apkClient->readApkConfig ( $filename );
			if (is_int ( $apkInfo )) {
				$msg = $apkClient->getErr ( $apkInfo );
				$this->error ( $msg ['error'] );
			}
			$apkInfo ['info'] ['pcg'] = $apkInfo ['info'] ['package'];
			$fileSize = showsize ( $apkInfo ['size'] );
			$apkInfo ['info'] ['file_size'] = implode ( "", $fileSize );
			$apkConfig = C ( "apk" );
			$minSDK = $apkInfo ['info'] ['min_sdk_version'];
			$apkInfo ['info'] ['minSDK'] = "Android " . $apkConfig ['SDK_VER'] [$minSDK];
			$this->success ( $apkInfo ['info'] );
		} else {
			Log::write ( $savePath . $filename, "ftp_file" );
			$this->error ( "文件不存在" );
		}
	}
	
	/**
	 * 提交ftp app
	 */
	function ftpappsub() {
		$filename = $this->_post ( "filename", "trim", "" );
		
		// ftp上传目录路径
		$ftpBasePath = Helper ( "Apk" )->get_path ( "ftp" );
		$oldPath = $ftpBasePath . "/" . $this->uid . '/';
		if (! file_exists ( $oldPath . $filename )) {
			$this->error ( "apk文件不存在" );
		}
		// apk文件保存路径
		$apkBasePath = helper ( "Apk" )->get_path ( "apk" );
		$newPath = $apkBasePath . date ( 'Y/m/d/' );
		// 判断新的保存目录是否存在
		if (! is_dir ( $newPath )) {
			@mkdirs ( $newPath );
		}
		// 检查新的apk保存路径是否可写
		if (! is_writeable ( $newPath )) {
			$this->error ( "保存的文件目录不可写" );
		}
		// 生成apk文件名规则
		$ext = array_pop ( explode ( ".", $filename ) );
		$newFilename = uniqid () . "." . $ext;
		// 获取包信息
		$apkClient = new ApkClient ();
		// 复制apk文件
		$apkClient->mvFile ( $oldPath . $filename, $newPath . $newFilename );
		if (! file_exists ( $newPath . $newFilename )) {
			$this->error ( "复制apk文件失败" );
		}
		// 设置apk文件路径为绝对路径
		$apkClient->setPath ( $newPath );
		// 处理apk信息
		$apkInfo = $apkClient->apk ( $this->uid, $this->user ['nickname'], $newFilename, $appId = 0, $type = 0 );
		
		if (APP_DEBUG)
			Log::write ( print_r ( $apkInfo, true ), "apk_info" );
		if (is_int ( $apkInfo )) { // 读取包信息失败
			$errorInfo = $apkClient->getErr ( $apkInfo );
			$this->error ( $errorInfo ['error'] );
		}
		if (is_array ( $apkInfo )) {
			$this->success ( $apkInfo );
		}
	}
	
	// 文件上传
	public function _upload($savePath, $ext = array("jpg","png")) {
		import ( "Extend.General.UploadFile", LIB_PATH );
		// 导入上传类
		$upload = new UploadFile ();
		// 设置上传文件大小(70K)
		$upload->maxSize = 1024 * 1024 * 5; // 目前是5M
		                                // 设置上传文件类型
		$upload->allowExts = $ext;
		// 设置附件上传目录
		$upload->savePath = $savePath;
		// 设置保存文件名字符
		$upload->charset = 'utf-8';
		// 设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;
		// 设置上传文件规则
		$upload->saveRule = "uniqid";
		// 删除原图
		$upload->thumbRemoveOrigin = false;
		if (! $upload->upload ()) {
			// 捕获上传异常
			return $upload->getErrorMsg ();
		} else {
			// 取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	
	// 分类信息
	function _getCategory() {
		$Category = D ( "Category" );
		$cate_game = $Category->where ( array (
				'parent_id' => 100 
		) )->select ();
		$cate_app = $Category->where ( array (
				'parent_id' => 200 
		) )->select ();
		
		$this->assign ( 'cate_game', $cate_game );
		$this->assign ( 'cate_app', $cate_app );
	}
	
	// 标签信息
	function _getLebals() {
		$labelInfo = array ();
		$Label = D ( "Label" );
		$baseLabel = $Label->where ( array (
				'parent_id' => 0 
		) )->select ();
		if (! empty ( $baseLabel )) {
			foreach ( $baseLabel as $item ) {
				$parentId = $item ['id'];
				if ($item ['name'] == '资费方式' || $item ['name'] == '游戏评级')
					continue;
				$childrenLabel = $Label->where ( array (
						'parent_id' => $parentId 
				) )->select ();
				$labelInfo [$parentId] = array (
						'name' => $item ['name'],
						'child' => $childrenLabel 
				);
			}
		}
		
		$this->assign ( 'labelInfo', $labelInfo );
	}
	function _screenAndIcon($appId, $apkId) {
		$iconUrl = Helper ( "Apk" )->get_url ( "icon" );
		// 截图信息
		$Picture = D ( "Picture" );
		$screenshotinfo = $Picture->getApkIcon ( $appId, $apkId, array (
				'type' => 1 
		) );
		// ICON信息
		$smalliconinfo = $Picture->getApkIcon ( $appId, $apkId, array (
				'type' => 2 
		) );
		$middleiconinfo = $Picture->getApkIcon ( $appId, $apkId, array (
				'type' => 3 
		) );
		$bigiconinfo = $Picture->getApkIcon ( $appId, $apkId, array (
				'type' => 4 
		) );
		$this->assign ( 'smalliconurl', empty ( $smalliconinfo ['file_path'] ) ? "" : $iconUrl . $smalliconinfo ['file_path'] );
		$this->assign ( 'middleiconurl', empty ( $middleiconinfo ['file_path'] ) ? "" : $iconUrl . $middleiconinfo ['file_path'] );
		$this->assign ( 'bigiconurl', empty ( $bigiconinfo ['file_path'] ) ? "" : $iconUrl . $bigiconinfo ['file_path'] );
		$this->assign ( 'smallIconId', $smalliconinfo );
		$this->assign ( 'middleIconId', $middleiconinfo );
		$this->assign ( 'bigIconId', $bigiconinfo );
		$this->assign ( 'screenshotinfo', $screenshotinfo );
	}
	
	// 获取收费模式记录
	function _getFeeType() {
		$Feetype = D ( "Admin://Feetype" );
		$fee = $Feetype->select ();
		$this->assign ( "feetype", $fee );
	}
	
	// 检查app的用户归属验证
	function _checkUserByApp($appId, $uid) {
		$Apks = D ( "Apks" );
		$checkUser = $Apks->where ( array (
				"app_id" => $appId,
				"author_id" => $uid 
		) )->count ();
		if (count ( $checkUser ) < 1) {
			$this->error ( "未能找到对应的APK信息" );
		}
	}
}