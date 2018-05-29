<?php
/*
 * 全部应用
 */
class AppallAction extends SystemAction {
	
	//pkID 	AppID 	提交日期 	测试日期 	上线日期 	开发者帐号 	应用名称 	包名 	版本名称 	应用状态 	测试人员  	上线人员
	protected $_export_config = array(
		'id' => array('apkID'),
		'app_id' => array('AppID'),
		'created_at' => array('提交日期','date','function','Y-m-d H:i:s",{{field_val}}'),
		'checked_at' => array('测试日期','date','function','Y-m-d H:i:s",{{field_val}}'),
		'onlined_at' => array('上线日期','date','function','Y-m-d H:i:s",{{field_val}}'),
		'email' => array('开发者帐号','getAccountField','callback','{{field_val}},email','author_id'),
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
		$method = $_search['status'];
		if(isset($method) && $method=="offline")
			$map['status'] = array('in','-2,-3');
		elseif(isset($method) && $method=="online")
			$map['status']  = array('in','3');
		elseif(isset($method) && $method=="passed")
			$map['status']  = array('in','2');
		else
			$map['status']  = array('NOT IN','-1,0,1');

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
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map["checked_at"] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map["checked_at"] = array('EGT',$timeStart);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map["checked_at"] = array("between", array($timeStart, $timeEnd));
		}
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
	}
	/**
	 * 完成审核操作
	 */
	function verifyed()
	{
		$apk_id = intval($_REQUEST['apk_id']);
		$result_id = intval($_REQUEST['result_id']);
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
		
//		$Testlog = D('Testlog');
//    	$Testlog->create();
    	
    	$map['id'] = $apk_id;
		//更新APK包状态
		if($result_id==1){
			$status = 5;
		}
		else{
			$status = 4;
		}
		$res = D("Apks")->data(array('status'=>$status ))->where($map)->save();
		if($res){
			//$Testlog->add();
			$this->success("审核结果提交成功",'closeCurrent');
		} 
		else 
		{
			$this->error('审核结果提交失败');
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