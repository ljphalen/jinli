<?php
class TestlogModel extends RelationModel
{
	protected $trueTableName = 'think_optlog';
	protected $tablePrefix = 'think_';
	
	public $_auto		=	array(
			array('admin_id','admin_id',self::MODEL_INSERT,'function'),
			array('created_at','time',self::MODEL_INSERT,'function'),
	);
	
	/*
	 * 操作日志状态位 1：测试通过 2：测试不通过 3：上线 4 下线  5 认领
	 */
	CONST TEST_SUC = 1;
	CONST TEST_NO = 2;
	CONST APK_ONLINE = 3;
	CONST APK_DOWNLINE = 4;
	CONST APK_CLAIM = 5;
	
	/**
	 * 获取动作类型
	 * @param int $opt
	 */
	public static function getAOpt($opt = null)
	{
		$arr = array(
			self::TEST_SUC => '测试通过',
			self::TEST_NO => '测试不通过',
			self::APK_ONLINE => '应用上线',
			self::APK_DOWNLINE => '应用下线',
			self::APK_CLAIM => '应用认领',
		);
		if ($opt === null)
		{
			return $arr;
		}else
		{
			return $arr[$opt];
		}
		
		
		
		
	}
}