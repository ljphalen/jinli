<?php
/**
 * 联系人管理的model类
 *
 * @name ContactModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2014-07-20 14:27:30
 */
class ContactModel extends Model{
	protected $trueTableName = 'account_contact';
	
	//只在申请AppKey时验证
	public $_validate_appkey = array(
			array('contact_business', 'require', '负责业务必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('own_app', 'require', '负责应用必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('contact_name', 'require', '联系人姓名必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('contact_mobile', 'require', '联系人电话必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('contact_email', 'require', '联系人邮箱必须填写', Model::MUST_VALIDATE, '', Model::MODEL_BOTH),
			array('contact_qq', 'require', "联系人qq必须填写", Model::MUST_VALIDATE, 'in', Model::MODEL_BOTH),
	);
	
	function _list($uid){
		return $this->where(array("account_id"=>$uid, "is_delete"=>0))->select();
	}
	
	function _getOne($id, $author_id){
		return $this->where(array("id"=>$id, "account_id"=>$author_id, "is_delete"=>0))->find();
	}
	
	function _add($data){
		$data['created_at'] = $data['updated_at'] = time();
		return $this->add($data);
	}
	
	function _update($id, $author_id, $data){
		$data['updated_at'] = time();
		return $this->where(array("id"=>$id, "account_id"=>$author_id))->save($data);
	}
	
	function _del($id, $author_id){
		return $this->_update($id, $author_id, array("is_delete"=>1));
	}
}