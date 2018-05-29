<?php
class AnnouncementModel extends Model
{
	protected $trueTableName = 'announcement';
	
	// 是否删除(1:未删除 2:已删除)
	const STATUS_NO_DELETE = 1;
	const STATUS_IS_DELETE = 2;
	
	//状态 状态 1:预发布  2:已发布  3:已下线  4:仅保存
	CONST STATUS_PRE_RELEASE = 1;
	CONST STATUS_RELEASE = 2;
	CONST STATUS_DOWN = 3;
	CONST STATUS_SAVE = 4;
	
	protected $_auto = array ( 
        //array('mold',self::MOLD_ARTICLE,self::MODEL_INSERT),  	
//         array('add_time','time',1,'function'), 		
//         array('update_time','time',self::MODEL_BOTH,'function'),	
    );
	
	/**
	 * 获得公告状态
	 * @param int $status
	 */
	public static function getStatus($status=null)
	{
		$arr = array(
			self::STATUS_PRE_RELEASE => '预发布',
			self::STATUS_RELEASE => '已发布',
			self::STATUS_DOWN => '已下线',
			self::STATUS_SAVE => '仅保存',
		);
		if ($status !== null)
		{
			return @$arr[$status];
		}else 
		{
			return $arr;
		}
	}
	
	public function getAnnouncement1(){
		
		$where = array();
		$nowTime = date ( 'Y-m-d H:i:s' );
		$where['start_time'] = array('elt', $nowTime);
		$where['end_time'] = array('egt', $nowTime);
		$where['is_deleted'] = array('NEQ',self::STATUS_IS_DELETE);
		$where["status"] = self::STATUS_PRE_RELEASE;
		return $this->where($where)->find();
	}
}