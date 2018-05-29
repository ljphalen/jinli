<?php
/*
 * 后台应用审核
 */
class TestlogAction extends SystemAction {
	
	protected $_export_config = array(
			'created_at' => array('提交日期','date','function','"Y-m-d H:i:s",{{field_val}}'),
			'checked_at' => array('审核日期','date','function','"Y-m-d H:i:s",{{field_val}}'),
			'app_name' => array('应用名称'),
			'version_name' => array('版本名称'),
			'result_id' => array('审核结果','resTxt','callback','{{field_val}}'),
			'remarks' => array('审核备注'),
			'admin_id' => array('审核人','admin_nikename','callback','{{field_val}}'),
	);
	
	function _filter(&$map){
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		if(isset($_search['app_name']) && $_search['app_name'])
			$map['app_name'] = $_search['app_name'];
		if(isset($_search['package']) && $_search['package'])
			$map['package'] = $_search['package'];
		if(isset($_GET['apk_id']) && $_GET['apk_id'])
			$map['apk_id'] = intval($_GET['apk_id']);
		if(isset($_search['apk_id']) && $_search['apk_id'])
			$map['apk_id'] = intval($_search['apk_id']);
		if(isset($_search['account']) && $_search['account'])
			$map['account'] = $_search['account'];
		if(isset($_search['timeStart']) && $_search['timeStart'])
		{
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
	}
	
	function _before_index(){
		$select = $this->getSelect('_search[admin_id]', D("Admin")->field('id, nickname as name')->select());
		$this->assign("adminList", $select);
	}
	
	/**
	 * 完成审核操作
	 */
	function verifyed()
	{
		$apk_id = intval($_REQUEST['apk_id']);
		$result_id = intval($_REQUEST['result_id']);
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
		
		$Testlog = D('Testlog');
    	$Testlog->create();
    	
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
			$Testlog->checked_at = time();
			$Testlog->add();
			$this->success("审核结果提交成功");
		} 
		else 
		{
			$this->error('审核结果提交失败');
		}
		
	}	
	
	function resTxt($val)
	{
		return  $val ==1 ?"通过":'未通过';
	}
	
	function admin_nikename($admin_id)
	{
		return D("Admin")->where(array("id"=>$admin_id))->getField('nickname');
	}
}