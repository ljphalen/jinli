<?php
/**
 * 应用model类
 *
 * @name PictureModel.class.php
 * @author gaochao
 * @datetime 2013-12-18
 */
class PictureModel extends Model{
	protected $trueTableName = '`app_picture`';
	
	/**
	 * 获取apk的icon和截图信息
	 * @param int $appId
	 * @param int $apkId
	 * @param array $map
	 */
	function getApkIcon($appId,$apkId,$map=array()){
		$where = array("app_id"=>$appId,"apk_id"=>$apkId,"status"=>1);
		if (!empty($map))	$where += $map; 
		$func = $map['type']==1 ? "select" : "find";
		return $this->where($where)->field(array("file_path","id"))->order(array('id'=>'asc','type'=>'asc'))->{$func}();
	}
}
