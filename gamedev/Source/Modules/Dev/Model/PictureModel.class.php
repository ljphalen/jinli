<?php
/**
 * 应用model类
 *
 * @name PictureModel.class.php
 * @author gaochao
 * @datetime 2013-12-18
 */
class PictureModel extends Model
{
	protected $trueTableName = 'app_picture';
	
	/**
	 * 获取apk的icon和截图信息
	 * @param int $appId
	 * @param int $apkId
	 * @param array $map
	 */
	function getApkIcon($appId, $apkId=0, $map=array())
	{
		$where = array("app_id"=>$appId, "status"=>1);
		if (intval($apkId) > 0) {
			$where = array_merge($where, array("apk_id"=>$apkId));
		}
		if (!empty($map))
			$where = array_merge($where, $map);
		$result = $this->where($where)->field(array("file_path","id"))->order(array('id'=>'desc','type'=>'desc'))->find();
		
		//线上出现一个无法复现的问题，进行在线调试
		if($_GET["app_debug"] == 'shuhai') {
			dump($this->_sql());
			dump($result);
		}
		return $result;
	}
	
	/**
	 * 获取apk的icon和截图信息
	 * @param int $appId
	 * @param int $apkId
	 * @param array $map
	 */
	function getApkScreen($appId, $apkId, $map=array())
	{
		$where = array("app_id"=>$appId, "apk_id"=>$apkId, "status"=>1);
		if (!empty($map))
			$where = array_merge($where, $map);
		return $this->where($where)->field(array("file_path","id"))->order(array('type'=>'desc'))->select();
	}
	
	/**
	 * 获取当前版本的icon，如果当前版本的截图没有的话就获取以前版本的的icon
	 * @param int $appId
	 * @param int $apkId
	 * @param array $map
	 */
	function getApkIconOlder($appId, $apkId=0, $map=array())
	{
		$icon = $this->getApkIcon($appId, $apkId, $map);

		if(!empty($icon))
			return $icon;
		
		$where = array("app_id"=>$appId, "status"=>1);
		if (intval($apkId) > 0)
			$where = array_merge($where, array("apk_id"=>array("elt", $apkId)));
		if (!empty($map))
			$where = array_merge($where, $map);
		return $this->where($where)->field(array("file_path","id"))->order(array('id'=>'desc','type'=>'desc'))->find();
	}
}
