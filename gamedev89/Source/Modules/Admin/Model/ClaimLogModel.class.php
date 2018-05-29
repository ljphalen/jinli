<?php
/**
 * 认领应用的日志model
 * @author Xia
 * @datetime 2014-02-23 01:35
 */
class ClaimLogModel extends Model {
	protected $trueTableName = "think_claim_log";
	
	function addClaimLog($data=array()){
		$data['created_at'] = time();
		$data['admin_id'] = admin_id();
		return $this->add($data);
	}
}