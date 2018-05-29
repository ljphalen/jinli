<?php
/*
 * 应用上下线
 */
class AppoptAction extends SystemAction {
	
	protected $_export_config = array(
			'id' => array('apkID'),
			'app_id' => array('AppID'),
			'created_at' => array('提交日期','exdate','function','Y-m-d H:i:s",{{field_val}}'),
			'checked_at' => array('测试日期','exdate','function','Y-m-d H:i:s",{{field_val}}'),
			'onlined_at' => array('上线日期','exdate','function','Y-m-d H:i:s",{{field_val}}'),
			'email' => array('开发者帐号','getAccountField','callback','{{field_val}},email','author_id'),
			'company' => array('企业名称','getAccountInfoFiled','callback','{{field_val}},company','author_id'),
			'app_name' => array('应用名称'),
			'package' => array('包名'),
			'version_name' => array('版本名称'),
			'status' => array('应用状态','apkStatus', 'callback'),
			'admin_test' => array('测试人员','testlog', 'callback','{{field_val}}','id'),
			'admin_online' => array('上线人员','testlog', 'callback','{{field_val}}','id'),
	);
	
	function _filter(&$map){
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		$method = $_REQUEST['method'];
		if(isset($method) && $method=="offline")
			$map['status'] = array('in','-2,-3');
		elseif(isset($method) && $method=="online")
			$map['status']  = array('in','3');
		else
			$map['status']  = array('in','2,4');//审核通过和设置为自动上线的应用

		$author = $_search['author'];
		if(isset($author) && $author)
		{
			$account = D("Dev://Accounts")->getUserByEmail($author);
			$map['author_id'] = $account['id'];
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
		$key = $_REQUEST['method']=='passed'?"checked_at":($_REQUEST['method']=='online'?"onlined_at":"offlined_at");
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map[$key] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map["created_at"] = array('ELT',$timeEnd);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map[$key] = array("between", array($timeStart, $timeEnd));
		}
		
		if(isset($_search['app_cert']) && in_array($_search['app_cert'], array("0", "1")))
			$map['app_cert'] = $_search['app_cert'];
		else
			unset($map['app_cert']);
		
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
		
	}
	function edit(){
		$apk_id = $this->_get("id","intval",0);
		$apkinfo = D("Dev://Apks")->find($apk_id);
		//验证用户当前状态是否审核通过
		if (!empty($apkinfo)) {
			$accountStatus = D("Dev://Accountinfo")->where(array("account_id"=>$apkinfo['author_id']))->getField("status");
			if (intval($accountStatus) !== AccountinfoModel::STATUS_SUC)
				$this->error("该帐号未审核或者审核未通过。");
		}
		$this->_getLebals();
	
		parent::edit();
	}
	/**
	 * 下线提示
	 */
	function offedit()
	{
		$apk_id = $_REQUEST['id'];
		$apkinfo = D("Apks")->where ( array ('id' => $apk_id) )->find ();
		$this->assign('vo',$apkinfo);
		$this->display();
	}
	/**
	 * 认领下线提示
	 */
	function offgetedit()
	{
		$apk_id = $_REQUEST['id'];
		$apkinfo = D("Apks")->where ( array ('id' => $apk_id) )->find ();
		$this->assign('vo',$apkinfo);
		$this->display();
	}
	/**
	 * 测试
	 */
	function test()
	{
		//$res = D("Appopt")->closeAccount(6);
		$res = D("Reason")->getReasonList(1);
		print_r($res);
		exit;
	}
	/*
	 * 认领下线应用
	 */
	function offget()
	{
		$app_id = intval($_REQUEST['app_id']);
		if(empty($app_id)) $this->error("应用参数丢失，请检查！");
		$status = -3;
		$map['app_id'] = $app_id;
		$res = D("Apks")->data(array('status'=>$status))->where($map)->save();
		$res = D("Apps")->data(array('status'=>$status))->where(array("id"=>$app_id))->save();
		if($res){
			$this->success("认领下线成功");
		} 
		else 
		{
			$this->error('认领下线失败');
		}
	}
	/**
	 * 应用上线操作
	 */
	function online()
	{
		$apk_id = intval($_REQUEST['apk_id']);
		$result_id = intval($_REQUEST['result_id']);
		$online_time = $this->_request("online_time", "trim", "");
		$app_belong = $this->_request("app_belong", "intval", 2);
		$save_type = $this->_request("save_type", "trim", "");
		
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
    	
    	$map['id'] = $apk_id;
    	
    	$apkinfo = D("Apks")->where(array("id"=>$apk_id))->find();
    	
    	//apk所属用户如果已被封号，则不处理
    	$accountsInfo = D("Accounts")->where(array("id"=>$apkinfo['author_id']))->find();
    	if ($accountsInfo['status'] != 2) {
    	    return $this->error('上线失败，apk所属用户异常或已被封号');
    	}
    	
		//更新APK包状态(状态为上线)
		if ($save_type == "save") {		//保存操作
			$data = array('app_belong'=>$app_belong, "onlined_at"=>strtotime($online_time), 'online_time'=>$online_time);
			$res = D("Apks")->data($data)->where($map)->save();
			if(false === $res)	$this->error("保存失败");
			$this->success("保存成功");
		}
		elseif($save_type == "autoOnline")	{		// 保存并自动上线操作
			$status = 4;	//保存并自动上线状态
			$data = array('app_belong'=>$app_belong, 'status'=>$status, "onlined_at"=>strtotime($online_time), 'online_time'=>$online_time );
			$res = D("Apks")->data($data)->where($map)->save();
			if(false === $res)	$this->error("保存并自动上线失败");
			$this->success("保存并自动上线成功");
		}else {		// 立即上线操作
			$status = 3;
			$data = array('online_time'=>$online_time,'app_belong'=>$app_belong,'status'=>$status, "onlined_at"=>time() );
			$res = D("Apks")->data($data)->where($map)->save();
		}
		
		//更新当前app的主apk版本id和状态
		$map_s['version_code']  = array('gt',$apkinfo['version_code']);
		$map_s['app_id']  = $apkinfo['app_id'];
		
		$apkinfo_s = D("Apks")->where($map_s)->find();
		if(empty($apkinfo_s)){
			$app_status = 1;
			$appArr = array();
			$appArr['status'] = $app_status;
			$appArr['main_apk_id'] = $apk_id;
			$appArr['updated_at'] = time();
			$res = D("Apps")->data($appArr)->where(array("id"=>$apkinfo['app_id']))->save();
		}
		//更新应用的名称
		$appName = D("Apps")->where(array("id"=>$apkinfo['app_id']))->getField("app_name");
		if ($apkinfo['app_name'] != $appName) {
			$data_s1 = array("app_name"=>$apkinfo['app_name']);
			$res = D("Apps")->data($data_s1)->where(array("id"=>$apkinfo['app_id']))->save();
		}
		
		//同步成功
		if($res){
			//对其它低上线版本的apk进行下线处理
			$outline_map = array("app_id"=>$apkinfo['app_id'],"status"=>3,"id"=>array("neq",$apk_id));
			$res = D("Apks")->data(array('status'=>-2,"offlined_at"=>time() ))->where($outline_map)->save();
			
			//差分包自动上线
			D("Bspackage")->online($apk_id);
			
			//同步数据
			$sync = helper("Sync")->done($apkinfo['app_id'], 'online');
			$sync = $sync == 'ok' ? "，同步成功" : "，同步失败，请手工同步";
			
			$account_info = D("Dev://Accounts")->getAccountAll($apkinfo['author_id']);
			//发送邮件
			$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
			$sendemail = empty($account_info['contact_email']) ? $account_info['email'] : $account_info['contact_email'];
			$subject = "开发者应用上线";
			$body = "亲爱的开发者，您好：<br>";
			$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您上传的 《".$apkinfo['app_name'].$apkinfo['version_name'].'》版本已经上线。';
			$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
			$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
			smtp_mail ( $sendemail, $subject, $body );
			
			Log::write("上线成功: \n".print_r($apkinfo, true));
			Log::write("同步数据{$sync}");
			$this->success("上线成功{$sync}");
		}else{
			Log::write("上线失败: \n".print_r($apkinfo, true));
			$this->error('上线失败');
		}
	}	
	
	/**
	 * 应用下线操作
	 */
	function offline()
	{
		$apk_id = intval($_REQUEST['apk_id']);
		$reason_id = intval($_REQUEST['reason_id']);
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
		$notice = $_REQUEST['notice'];
		$apkinfo = D("Apks")->where(array("id"=>$apk_id))->find();
		$reason_info = D("Reason")->where(array("reason_id"=>$reason_id))->find();
		$account_info = D("Dev://Accounts")->getAccountAll($apkinfo['author_id']);
		if(isset($notice) && ($notice==1)){
			//发送邮件
			$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
			$sendemail = empty($account_info['contact_email']) ? $account_info['email'] : $account_info['contact_email'];
			$subject = "开发者应用下线";
			$body = "亲爱的开发者，您好：<br>";
			$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您上传的 《".$apkinfo['app_name'].$apkinfo['version_name'].'》版本已下线，原因如下:<br>';
			$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$reason_info['reason_content']."&nbsp;&nbsp;&nbsp;".$this->_post('remarks');
			$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
			$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
			smtp_mail ( $sendemail, $subject, $body );
		}

		//更新APK包状态
    	$map['id'] = $apk_id;
		$status = -2;
		$data = array();
		$data['status'] = $status;
		$data['offlined_at'] = time();
		$res = D("Apks")->data($data)->where($map)->save();
		
		//更新APP状态
		$apkinfo_old = D("Apks")->where(array("app_id"=>$apkinfo['app_id'],"status"=>3))->find();
    	if(empty($apkinfo_old)){
    		$status_old = $apkinfo_old['status'];
			$res = D("Apps")->data( array('status'=>$status ))->where(array("id"=>$apkinfo['app_id']))->save();
    	}
    	    	
		if($res){
			//同步数据
			helper("Sync")->done($apkinfo['app_id'], 'offline');
			$this->success("应用下线成功");
		} 
		else 
		{
			$this->error('应用下线失败');
		}
		
	}	
	
	/**
	 * 应用同步数据的操作
	 */
	function syncdata($apk_id,$method_name) {
		//取得应用的相关信息
		$apkinfo = D("Apks")->where(array("id"=>$apk_id))->find();
		//判断是否存在
		if($apkinfo['app_id']) {
			//同步数据
			$res_sync = helper("Sync")->done($apkinfo['app_id'], $method_name);
			if($res_sync == 'ok') {
				 return  true ;
			} else {
				 return  false ;
			}
			
		} else { 
			return  false ;
		}
	}
	
	public function testlog($apk_id)
	{
		$vo = D('Apks')->find($apk_id);
		$con['package'] = $vo['package'];
		$con['version_code'] = $vo['version_code'];
		$testlog_info = D("Testlog")->where($con)->order('created_at desc')->find();
		$test_admin = D("Admin")->find($testlog_info['admin_id']);
		return $test_admin['account'];
	}
	
	public function apkStatus($status)
	{
		return D('Apks')->getApkStatusByStatus($status);
	}
	
}