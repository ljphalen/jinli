<?php
class ReasonModel extends RelationModel
{
	protected $trueTableName = 'think_reason';
	protected $tablePrefix = 'think_';
	
	// 原因类型： 1：测试不通过原因   2-下线原因  3-用户注册审核原因 
	CONST TYPE_TEST = 1;
	CONST TYPE_OFFLINE = 2;
	CONST TYPE_AUDIT = 3;
	/*
	 * 获取原因列表
	 */
	function getReasonList($type)
	{
		if(empty($type)) {
			return FALSE;
		}
		$rs = D("Reason")->where(array("type"=>$type))->select();
		$reasonArr = array();
		if(!empty($rs)){
			foreach ($rs as $key=>$value){
				$rid = $value['reason_id'];
				$reasonArr[$rid] = $value['reason_content'];
			}
		}
		if($rs===false) {
			return false;
		}else {
			return $reasonArr;
		}
	}
	
}
?>