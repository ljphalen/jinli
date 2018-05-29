<?php
/**
 * 开发者审核信息的model类
 *
 * @name VerifyModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class VerifyModel extends RelationModel {
	protected $trueTableName = 'think_verify';
	public function addAccountVerify($accountId) {
		$data ['account_id'] = $accountId;
		$data ['created_at'] = currentDate ();
		return $this->data ( $data )->add ();
	}
	public function updateAccVerify($accountId, $data = array()) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->save ( $data );
	}
	public function getAccVerify($accountId) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->find ();
	}
}