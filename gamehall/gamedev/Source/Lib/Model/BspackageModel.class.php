<?php
/**
 * 差分包模型
 * @author shuhai
 */
class BspackageModel extends Model
{
	protected $trueTableName = 'bspackage';
	
	public $_status = array("-3"=>"文件过期", "-2" =>"生成失败", "-1"=>"已下线", "0"=>"已生成", "1"=>"已上线");

	protected $_auto = array ( 
		array('status','0'),
		array('created_at', 'time', Model::MODEL_INSERT, 'function'),
		array('updated_at', 'time', Model::MODEL_UPDATE, 'function'),
	);
	
	function getStatus($status)
	{
		return $this->_status[$status];
	}
	
	/**
	 * 上线指定版本的差分包
	 * @param int $apkId
	 */
	function online($apkId)
	{
		$where = array("status"=>0);
		$where["b_apk_id"] = $apkId;
		$data = array("status"=>1, "updated_at"=>time(), "admin_id"=>1);
		$res = $this->where($where)->save($data);
		
		Log::write($this->_sql(),"online_apks_sql_bspackage");
		return $res;
	}
}