<?php
/**
 * 应用model类
 *
 * @name AppsModel.class.php
 * @author gaochao
 * @datetime 2013-12-18
 */
class AppsModel extends Model{
	protected $trueTableName = 'apps';
	
	/**
	 * 获取应用的信息
	 * @param int $id
	 * @return array
	 */
	public function getAppInfoById($id) {
		$res = $this->where ( array ("id" => $id ) )->find ();
		return $res;
	}
	/**
	 * 根据app的包信息获取应用记录
	 * @param unknown $package
	 * @return Ambigous <mixed, boolean, NULL, multitype:>
	 */
	public function getAppInfoByPkg($package) {
		$res = $this->where ( array ("package" => $package ) )->find ();
		return $res;
	}
	/*
	 * 修改应用信息
	 */
	public function updAppInfo($id, $data=array()){
		$where = array('id'=>$id);
		$data['brief'] = "你好啊";
		$res = $this->where($where)->data($data)->save();
		return $res;
	}
	
	/**
	 * 获取应用的状态
	 * @param int $appId
	 * @return array
	 */
	function getAppStatus($appId){
		$where = array("id"=>$appId);
		return $this->where($where)->getField("status");
	}
	
	function getAppIdByUid($package, $uid){
		$where = array("package"=>$package,"author_id"=>$uid);
		return $this->where($where)->getField("id");
	}
}