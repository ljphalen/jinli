<?php
/*
 * 操作日志
 */
class OptlogAction extends SystemAction {
	
	function _filter(&$map){
		$search = $map = MAP();
		
		$apk_id = intval($_REQUEST['apk_id']);
		if(!empty($apk_id))
			$map['apk_id'] = $apk_id;
		
		$search = $map;
	}
	/**
	 * 完成审核操作
	 */
	function verifyed()
	{
		$apk_id = intval($_REQUEST['apk_id']);
		$result_id = intval($_REQUEST['result_id']);
		if(empty($apk_id)) $this->error("应用参数丢失，请检查！");
		
//		$Optlog = D('Optlog');
//    	$Optlog->create();
    	
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
			$Optlog->add();
			$this->success("审核结果提交成功");
		} 
		else 
		{
			$this->error('审核结果提交失败');
		}
		
	}	
}