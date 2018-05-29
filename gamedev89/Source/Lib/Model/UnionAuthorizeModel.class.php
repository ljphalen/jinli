<?php
/**
 * 联运相关模型
 * @author shuhai
 */
class UnionAuthorizeModel extends Model
{
	protected $trueTableName = 'union_authorize';

	protected $_auto = array ( 
		array('created_at', 'time', Model::MODEL_INSERT, 'function'),
		array('admin_id',	'admin_id', Model::MODEL_INSERT, 'function'),
		array('account',	'admin_account', Model::MODEL_INSERT, 'function'),
	);
	
	function log($data)
	{
		$this->create($data);
		return $this->add();
	}
	
	function get_log($id)
	{
		return $this->order(array("id"=>"desc"))->where(array("app_id"=>$id))->getField('note');
	}
}