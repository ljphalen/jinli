<?php
/*
 * 后台应用审核
 */
class VeryfyAction extends SystemAction {
	
	//pkID 	AppID 	提交日期 	测试日期 	开发者帐号 	合作方式 	签名状态 	应用名称 	包名 	已通过安检 	版本名称
	//			'email' => array('注册帐号','getAccountField','callback','{{field_val}},email','account_id'),
	protected $_export_config = array(
		'id' => array('apkID'),
		'app_id' => array('AppID'),
		'created_at' => array('提交日期','exdate','function','"Y-m-d H:i:s",{{field_val}}'),
		'checked_at' => array('测试日期','exdate','function','"Y-m-d H:i:s",{{field_val}}'),
		'email' => array('开发者帐号','getAccountField','callback','{{field_val}},email','author_id'),
		'company' => array('企业名称','getAccountInfoFiled','callback','{{field_val}},company','author_id'),
		'is_join' => array('合作方式','ApksModel::getJoin','function'),
		'sign' => array('签名状态','ApksModel::getSign','function'),
		'app_name' => array('应用名称'),
		'category_p' => array('分类名称','ApksModel::getCategory','function'),
		'package' => array('包名'),
		'safe_status' => array('已通过安检','ApksModel::allSafeTxt','function'),
		'version_name' => array('版本名称'),
		'version_type' => array('版本类型','releaseStatus','m_callback','app_id,id'),
		'status' => array('测试状态','getApkStatus','callback'),
		'apk_md5' => array('云测状态','getTestinStatusByMd5','callback'),
		'online_time' => array('期望上线日期','getOnlinetime','callback'),
	);
	
	function _filter(&$map){
		$search = $map = MAP();
		$safe_status = ApkSafeModel :: STATUS_SUC ;
		$_search = $_REQUEST['_search'];
		$status = $_search['status'];
		
		if(!empty($status)){
			if ($status=="all") {
				unset($map['status']);
			}else {
				$map['status']  = $status;
			}
		}
		
		$author = $_search['author'];
		if(isset($author) && $author)
		{
			$account = D("Dev://Accounts")->where(array("email"=>array("like", str_replace("*", "%", $author))))->getField('id',true);
			if(empty($account))
				$this->error("指定的开发者不存在");
			$map['author_id'] = array("IN", join(",", $account));
		}
		//查询审核人
		$account = $_search['account'];
		if(!empty($account))
		{
			$admin_id = D("Admin")->where(array('account'=>$account ))->getField('id');
			if(empty($admin_id))
				$this->error('指定管理员不存在');
				
			$apk_ids = D("Testlog")->where(array("admin_id"=>$admin_id))->distinct(true)->getField("apk_id", true);
			if(!empty($apk_ids))
				$map["id"] = array("in", $apk_ids);
		}
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map["created_at"] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map["created_at"] = array('ELT',$timeEnd);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map["created_at"] = array("between", array($timeStart, $timeEnd));
		}

		//查询不同账号审核状态下的审核应用
		if(isset($_search["acc_status"]) && in_array($_search["acc_status"], array("-2", "0", "1", "2"))) {
			$author_ids = D("AccountInfos")->where(array("status"=>$_search["acc_status"]))->getField('account_id', true);
			if(is_null($author_ids) || empty($author_ids) || !is_array($author_ids))
				$author_ids = array(-9999);
			
			if(isset($map['author_id'])) {
				$map['_string'] = sprintf("( author_id in (%s) and author_id in (%s) )", next($map['author_id']), join(",", $author_ids));
				unset($map['author_id']);
			}else{
				$map['author_id'] = array("IN", $author_ids);
			}
		}
		
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
	}
	
	function edit(){
		$apk_id = $this->_get("id","intval",0);
		$apkinfo = D("Dev://Apks")->find($apk_id);
		//验证用户当前状态是否审核通过
		if ( !empty($apkinfo)) {
			$accountStatus = D("Dev://Accountinfo")->where(array("account_id"=>$apkinfo['author_id']))->getField("status");
			if ( intval($accountStatus) !== AccountinfoModel::STATUS_SUC)
			{
				$_REQUEST['callbackType'] = 'closeCurrent';
				echo '<script type="text/javascript">
						$(document).ready(function(){
							alert("该帐号未审核或者审核未通过。");
							 $.pdialog.closeCurrent(); 
							});
					</script>';
				return ;
				//$this->error("该帐号未审核或者审核未通过。","closeCurrent");
			}
				
		}
		
		$this->_getLebals();
		
		parent::edit();
	}
	
	/**
	 * 完成审核操作
	 */
	function verifyed()
	{
		$map['id'] = $apk_id = intval($_REQUEST['apk_id']);
		$result_id = intval($_REQUEST['result_id']);
		$reason_id = intval($_REQUEST['reason_id']);
		
		if(empty($apk_id) || !$apk=D("Dev://Apks")->find($apk_id))
			$this->error("参数不正确或者应用不存在，请检查！");
		
		//验证用户当前状态是否审核通过
		if (!empty($apk)) {
			$accountStatus = D("Dev://Accountinfo")->where(array("account_id"=>$apk['author_id']))->getField("status");
			if (intval($accountStatus) !== AccountinfoModel::STATUS_SUC)
				$this->error("该帐号未审核或者审核未通过。");
		}
		
		if($apk['status'] >= 2)
			$this->error("不能审核该版本，当前状态:".D("Dev://Apks")->getApkStatusByStatus($apk['status']));
		
		//上传文件开始
		$uploadList = Helper("Upload")->_upload("test");
		if (is_array ( $uploadList )) {
			$test_log_file = $_POST['doc_file'] = $uploadList[0]['filepath'];
		}
		//上传文件结束
		
		//更新APK包状态
		$status = ($result_id == 1) ? 2 : -1;
		
		$data = array();
		$data['status'] = $status;
		$data['checked_at'] = time();
		
		//v3.0 审核通过后期待立即上线，异步任务会定时检测并自动上线
		if($apk['onlinetime_type'] == 1)
			$data['online_time'] = date("Y-m-d H:i:00", $data['checked_at']);
		
		$res = D("Apks")->data( $data )->where($map)->save();
		if($res){
			$Testlog = D('Testlog');
	    	$Testlog->create();
	    	$Testlog->checked_at = time();
			$Testlog->add();
			$notice = $this->_request('notice',"intval",0);
			if(isset($notice) && ($notice==1)){
				$apkinfo = D("Apks")->where(array("id"=>$apk_id))->find();
				$account_info = D("Dev://Accounts")->getAccountAll($apkinfo['author_id']);
				$reason_info = D("Reason")->where(array("reason_id"=>$reason_id))->find();
				
				$authMsg = ($result_id == 1) ? "通过" : "不通过";
				//发送邮件
				$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
				
				//邮件地址使用联系信息中的联系地址
				$sendemail = empty($account_info['contact_email']) ? $account_info['email'] : $account_info['contact_email'];
				$subject = "开发者应用审核";
				$body = "亲爱的开发者，您好：<br>";
				$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您上传的 《".$apkinfo['app_name'].$apkinfo['version_name'].'》版本审核'.$authMsg."。";
				if ($status === -1) {
					$body .= "原因如下:<br>&nbsp;&nbsp;&nbsp;&nbsp;".$reason_info['reason_content']."&nbsp;&nbsp;".nl2br($this->_post('remarks'));
					
					//v3.0增加错误日志发送
					if(!empty($test_log_file) && is_file(Helper('Apk')->get_path('test') . $test_log_file)){
						$body .= sprintf('<br>原因如下:<a href="%s" target="_blank">点击下载详细测试文档</a>', Helper('Apk')->get_url('test') . $test_log_file);
					}
				}
				$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
				$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
				
				smtp_mail ( $sendemail, $subject, $body );
			}

			$this->success("提交成功","closeCurrent");
		} 
		else 
		{
			$this->error('提交失败',"closeCurrent");
		}
	}
	// 文件上传
	public function _upload($savePath, $ext=array("doc","txt")) {
		import ( "Extend.General.UploadFile", LIB_PATH );
		// 导入上传类
		$upload = new UploadFile ();
		// 设置上传文件大小(70K)
		$upload->maxSize = 1024*1024*30;		//目前是5M
		// 设置上传文件类型
		$upload->allowExts = $ext;
		// 设置附件上传目录
		$upload->savePath = $savePath;
		// 设置保存文件名字符
		$upload->charset = 'utf-8';
		// 设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;
		// 设置需要生成缩略图的文件后缀
		$upload->thumbPrefix = 'm_,s_'; // 生产2张缩略图
		                                // 设置缩略图最大宽度
		$upload->thumbMaxWidth = '400,100';
		// 设置缩略图最大高度
		$upload->thumbMaxHeight = '400,100';
		// 设置上传文件规则
		$upload->saveRule = uniqid;
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
		return false;
	}
	
	public function releaseStatus($app_id,$id)
	{
		return D("Dev://Apks")->getReleaseStatus($app_id,$id);
	}
	
	protected function getTestinStatusByMd5($md5)
	{
		$result = D("Testin")->getStatusByMd5($md5);
		return $result ? strip_tags($result) : "";
	}
	
	protected function getOnlinetime($datetime)
	{
		return $datetime ? $datetime : '立即上线';
	}
}