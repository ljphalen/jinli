<?php
class ArticleModel extends Model
{
	protected $trueTableName = 'article';
	
	//内容模型
	CONST MOLD_ARTICLE = 1;		//文章
	CONST MOLD_DOWNLOAND = 2;	//下载
	
	//状态 状态 -2：删除 -1：下线 0：保存 1：上线 
	CONST STATUS_DEL = -2;
	CONST STATUS_DOWN = -1;
	CONST STATUS_SAVE = 0;
	CONST STATUS_SUC = 1;
	
	protected $_auto = array ( 
        //array('mold',self::MOLD_ARTICLE,self::MODEL_INSERT),  	
        array('add_time','time',1,'function'), 		
        array('update_time','time',self::MODEL_BOTH,'function'),	
    );
	
	/**
	 * 文章分类，临时数组,需优化为二维数组
	 * @param int $id 分类ID
	 */
	public function getMold($mold=null)
	{
		$arr = array(
			self::MOLD_ARTICLE => '文章',
			self::MOLD_DOWNLOAND => 'SDK下载'
		);
		if ($mold !== null)
		{
			return @$arr[$mold];
		}else 
		{
			return $arr;
		}
	}
    
	/**
	 * 文章分类，临时数组,需优化为二维数组
	 * @param int $id 分类ID
	 */
	public function getCategory($id=null)
	{
		$arr = array(
			1 => '注册申请',
			2 => '应用发布',
			3 => '网游联运',
			6 => '单机合作',
			5 => '商务合作',
			7 => '新闻公告',
			4 => '其它文档',
		);
		if ($id !== null)
		{
			return @$arr[$id];
		}else 
		{
			return $arr;
		}
	}
	
	/**
	 * 获得内容状态
	 * @param int $status
	 */
	public static function getStatus($status=null)
	{
		$arr = array(
			self::STATUS_DEL => '删除',
			self::STATUS_DOWN => '下线',
			self::STATUS_SAVE => '未发布',
			self::STATUS_SUC => '已发布'
		);
		if ($status !== null)
		{
			return @$arr[$status];
		}else 
		{
			return $arr;
		}
	}
	
	
}