<?php

/**
 * 礼包模型
 * @author shuhai
 */
class GiftCodesModel extends Model
{
	protected $trueTableName = 'gift_codes';
	
	public static $status = array (
			'0' => '未审核',
			'1' => '审核中',
			'2' => '审核通过',
			'3' => '已领取',
			'4' => '已过期' 
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