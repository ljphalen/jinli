<?php
/*
 * 应用上下线
 */
class AppoptAction extends SystemAction {
	
	function _filter(&$map){
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		$method = $_REQUEST['method'];
		if(isset($method) && $method=="offline")
			$map['status'] = array('in','-2,-3');
		elseif(isset($method) && $method=="online")
			$map['status']  = array('in','3');
		else
			$map['status']  = array('in','2');
		
		if(isset($_search['app_name']) && $_search['app_name'])
		{
			$map['app_name'] = $_search['app_name'];
		}
		if(isset($_search['package']) && $_search['package'])
		{
			$map['package'] = $_search['package'];
		}
		$author = $_search['author'];
		if(isset($author) && $author)
		{
			$account = D("Dev://Accounts")->getUserByEmail($author);
			$map['author_id'] = $account['id'];
		}
		$account = $_search['account'];
		if(isset($account) && $account)
		{
			$admin_info = D("Admin")->where(array('account'=>$account ))->find();
			if(!empty($admin_info)){
				$map['admin_id'] = intval($admin_info['id']);
			}
		}
		$key = $_REQUEST['method']=='passed'?"checked_at":($_REQUEST['method']=='online'?"onlined_at":"offlined_at");
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map[$key] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map[$key] = array('EGT',$timeStart);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map[$key] = array("between", array($timeStart, $timeEnd));
		}
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
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
		
//		$Testlog = D('Testlog');
//    	$Testlog->create();
    	
    	$map['id'] = $apk_id;
    	
    	$apkinfo = D("Apks")->where(array("id"=>$apk_id))->find();
    	
		//更新APK包状态(状态为上线)
		$status = 3;
		$res = D("Apks")->data(array('status'=>$status, "onlined_at"=>time() ))->where($map)->save();
		
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
		
		// 同步数据
		$rs = $this->syncdata($apk_id,'offline');
		if($rs){
			$flag = 1;
		}else{
			$status_old = 3;
			$res = D("Apps")->data( array('status'=>$status_old ))->where(array("id"=>$apkinfo['app_id']))->save();
			$flag = 0;
		}
		
		//同步成功
		if($res && $flag){
			//对其它低上线版本的apk进行下线处理
			$outline_map = array("app_id"=>$apkinfo['app_id'],"status"=>3,"id"=>array("neq",$apk_id));
			$res = D("Apks")->data(array('status'=>-2,"offlined_at"=>time() ))->where($outline_map)->save();
	
			$account_info = D("Dev://Accounts")->getAccountAll($apkinfo['author_id']);
			//发送邮件
			$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
			$sendemail = $account_info['email'];
			$subject = "开发者应用上线";
			$body = "亲爱的开发者，您好：<br>";
			$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您上传的 《".$apkinfo['app_name'].$apkinfo['version_name'].'》版本已经上线。';
			$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
			$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
			smtp_mail ( $sendemail, $subject, $body );
			$this->success("上线成功");
		} 
		else 
		{
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
			$sendemail = $account_info['email'];
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
    	
    	// 同步数据
    	$rs = $this->syncdata($apk_id,'online');
    	if($rs){
    		$flag = 1;
    	}else{
    		$status_old = $apkinfo_old['status'];
    		$res = D("Apps")->data( array('status'=>$status_old ))->where(array("id"=>$apkinfo['app_id']))->save();
    		$flag = 0;
    	}
    	
		if($res && $flag){
			$this->success("审核结果提交成功");
		} 
		else 
		{
			$this->error('审核结果提交失败');
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
	
}