<?php
/**
 * 开发者认证信息的model类
 *
 * @name AccountauthModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountauthModel extends RelationModel {
	protected $trueTableName = 'account_auth';
	public function addAccAuth($data = array()) {
		return $this->data ( $data )->add ();
	}
	public function updateAccAuth($accountId, $data = array()) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->save ( $data );
	}
	public function getAccAuth($accountId) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->find ();
	}
}