<?php
/**
 * 用户税务的model类
 * 
 * @author shuhai
 */
class AccountTaxModel extends Model {
	protected $trueTableName = 'account_tax';
	
	CONST OK = 1;	
	CONST ING = 0;
	const DENY = -1;
	const NIL = -2;
	protected $status = array("1"=>"审核通过", "0"=>"未审核", "-1"=>"未通过", "-2"=>"未填写");

	protected $_auto = array (
			array('status','0'),
			array('created_at', 'time', Model::MODEL_INSERT, 'function'),
			array('updated_at', 'time', Model::MODEL_UPDATE, 'function'),
	);
	
	function status_to_string($account_id)
	{
		$s = $this->where(array("account_id" => $account_id))->getField("status");
		return $this->status[$s];
	}
	
	function status_to_int($account_id)
	{
		$s = $this->where(array("account_id" => $account_id))->getField("status");
		if(is_null($s))
			$s = -2;
		return $s;
	}
	
	function save_tax($data = array())
	{
		if(empty($data["account_id"]))
			return false;
		$s = $this->status_to_int($data["account_id"]);
		
		if($s == self::NIL)
		{
			$data = $this->create($data, MODEL::MODEL_INSERT);
			return $this->add();
		}
		else
		{
			$data = $this->create($data, MODEL::MODEL_UPDATE);
			return $this->where(array("account_id" => $data["account_id"]))->save($data);
		}
	}
}