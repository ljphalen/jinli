<?php
/**
 * 用户帐户详细信息的model类
 *
 * @name AccountinfoModel.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AccountinfoModel extends RelationModel {
	protected $trueTableName = 'account_infos';
	
	//当前状态(-2:封号 -1:编辑状态 0:提交审核状态 1:审核未通过 2:审核通过) 
	CONST STATUS_EDIT = -1;			//编辑中|未提交
	CONST STATUS_CLOSE = -2;		//封号
	CONST STATUS_INIT = 0;			//提交审核|待审核|审核中
	CONST STATUS_NOT = 1;			//审核未通过
	CONST STATUS_SUC = 2;			//审核通过
	
	/**
	 * 获得审核状态
	 * @param int $status
	 */
	public static function getStatus($status=null)
	{
		$arr = array(
			self::STATUS_EDIT => '未提交'/* '编辑状态' */,
			self::STATUS_CLOSE => '封号',
			self::STATUS_INIT => '待审核'/* '审核中' */,
			self::STATUS_NOT => '审核未通过',
			self::STATUS_SUC => '审核通过',
		);
		if ($status !== null)
		{
			return @$arr[$status];
		}else 
		{
			return $arr;
		}
		
	}
	
	public function addAccountInfo($account = array()) {
		return $this->data ( $account )->add ();
	}
	public function updateAccInfo($accountId, $account = array()) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->save ( $account );
	}
	public function getAccInfo($accountId) {
		return $this->where ( array (
				"account_id" => $accountId 
		) )->find ();
	}
	
	/**
	 * 检测资料是否完善
	 * @param int/array $account_id
	 */
	public function isPerfect($account_info)
	{
		if (!is_array($account_info))
		{
			$account_info = $this->getAccInfo($account_info);
		}
		if (empty ( $account_info['company'] ) || empty($account_info['phone']) || empty($account_info['contact_email']))
		{		
			return false;
		}else
		{
			return true;
		}
	}
	
	
	/**
	 * 判断用户税务资料是否完善
	 * @param int $account_id  用户ID
	 */
	public function isTax($account_id)
	{
		$account_info = $this->field(array('tax_number', 'tax_passport'))->where(array("account_id"=>$account_id))->find();
		if (!empty($account_info['tax_number']) && !empty($account_info['tax_passport']) )
		{
			return true;
		}else 
		{
			return false;
		}
	}
	
	/**
	 * 检查注册号或税务号是否唯一
	 * @param int $num 注册号或税务号
	 * @param string $field tax_number|passport_num
	 */
	public function is_unique_num($num, $field='passport_num')
	{
		if(empty($num) || empty($field))
			return true;
		return $this->where(array($field=>$num))->count("id") > 1 ? false : true;
	}
}