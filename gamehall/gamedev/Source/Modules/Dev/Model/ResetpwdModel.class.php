<?php
/**
 * 用户忘记密码流水类
 *
 * @name ResetpwdModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2012-03-21 17:27:30
 */

class ResetpwdModel extends RelationModel {
	protected $trueTableName = "`reset_passwords`";
	
	/**
	 * 创建一条记录
	 * @param integer $accountId
	 * @param string $email
	 */
	public function createResetpwd($accountId, $email) {
		$code = generateKey ();
		$data ['code'] = $code;
		$data ['email'] = $email;
		$data ['account_id'] = $accountId;
		$data ['created_at'] = currentDate ();
		if ($res = $this->data ( $data )->add ()) {
			return $code;
		} else {
			return false;
		}
	}
	
	/**
	 * 修改一条记录
	 * @param string $code
	 */
	public function updateReasetpwd($code) {
		$data ['reseted_at'] = currentDate ();
		$data ['state'] = 0;
		$data ['updated_at'] = currentDate ();
		return $this->where ( "code='$code'" )->save ( $data );
	}
	
	/**
	 * 根据唯一码获取一条记录
	 * @param string $code
	 */
	public function getResetAtByCode($code) {
		return $this->where ( "code='$code'" )->find ();
	}
}