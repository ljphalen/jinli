<?php

class AppAdaptersModel extends RelationModel{
	protected $trueTableName = 'app_adapters';
	/**
	 * 返回apk信息
	 * @param int $appid
	 * @param int $apkid
	 */
	public function  appAdaptersInfo($appid,$apkid)
	{
		$info = $this->where(array('app_id'=>$appid,'apk_id'=>$apkid))->select();
		//Log::write($this->getLastSql());
		return $info;
	}
}

?>