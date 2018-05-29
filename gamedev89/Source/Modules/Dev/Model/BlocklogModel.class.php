<?php
/**
 * 封号操作日志表
 * @author jiazhu
 *
 */
class BlocklogModel extends Model
{
	protected $trueTableName = 'think_blocklog';
	
	CONST STATUS_BLOCK = -1; 	//封号
	CONST STATUS_DEBLOCK = 1;	//解封
	
	//deblock_status  	是否解封处理 0：未 1：已处理 
	CONST DEBLOCK_STATUS_INIT = 0;
	CONST DEBLOCK_STATUS_SUC = 1;
	
	/**
	 * 封停原因
	 * @param int $val
	 */
	public static function getReason($val=null)
	{
		$arr = array(
			1 => '应用多次侵权',
			2 => '多次上传非法应用',
			3 => '....',
			100 => '其他',
		);
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