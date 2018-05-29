<?php
class AuthlogModel extends RelationModel
{
	protected $trueTableName = 'think_authlog';
	protected $tablePrefix = 'think_';
	
	public $_auto		=	array(
		array('dateline','time',self::MODEL_BOTH,'function'),
		//array('user_id','Agent_id',self::MODEL_BOTH,'function'),
	);
	
	protected $_link = array(
		'Admin'	=>	array(
			'mapping_type'	=>	BELONGS_TO,
			'foreign_key'	=>	'user_id',
		),
	);
	
	
	//============ 自定义 fouction ==========================
	
	CONST AUDITED_YES = 1;
	CONST AUDITED_NO = -1;
	CONST AUDITED_TEMP = -2;

	/**
	 * 获得审核状态
	 * @param int $val
	 */
	public static function getAudited($val)
	{
		$arr = array(
			self::AUDITED_YES => '通过',
			self::AUDITED_NO => '不通过',
			//self::AUDITED_TEMP => '挂起',
		);
		if ($val ===null)
		{
			return $arr;
		}else
		{
			return @$arr[$val];
		}
	}
	
	/**
	 * 获得失败原因
	 * @param int $val
	 */
	public static function getReason($val=null)
	{
		$arr = D('Admin://Reason')->getReasonList(3);
		$arr[0] =  '无';
		$arr[100000] = '其他';
		ksort($arr);
		if ($val ===null)
		{
			return $arr;
		}else
		{
			return @$arr[$val];
		}
	}
}
?>