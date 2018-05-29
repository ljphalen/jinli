<?php
/**
 * 用户帐户认证信息修改历史的model类
 *
 * @name AuthHistoryModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */

class AuthHistoryModel extends RelationModel {
	
	protected $trueTableName = 'account_auth_history';
	
	public function addAuthHistory($data=array()) {
		return $this->data($data)->add();
	}
	
	public function editAuthHistory($where=null,$data=array()){
		return $this->where($where)->save($data);
	}
	
	public function getAuthHistory($where=null,$field="*"){
		return $this->where($where)->field($field)->find();
	}
}
?>