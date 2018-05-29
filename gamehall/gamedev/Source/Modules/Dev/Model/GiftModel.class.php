<?php

/**
 * 礼包模型
 * @author shuhai
 */
class GiftModel extends Model
{
	protected $trueTableName = 'gifts';
	
	protected $_auto = array ( 
        array('ctime','time',self::MODEL_INSERT,'function'),
		array('status','0',self::MODEL_INSERT),
        array('update_time','time',self::MODEL_BOTH,'function'),	
    );

	public static $status = array(
			"-1"	=>"审核不通过",
			"0"		=>"未提交",
			"1"		=>"审核中",
			"2"		=>"审核通过",
			"3"		=>"已下线",
			"4"		=>"已过期",
	);

	/**
	 * 获得状态
	 * @param int $status
	 */
	public static function getStatus($status=null)
	{
		return self::$status[$status];
	}
}