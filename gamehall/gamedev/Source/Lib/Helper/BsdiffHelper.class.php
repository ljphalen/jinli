<?php
/**
 * 差分包处理逻辑
 * @author shuhai
 */
class BsdiffHelper
{
	public $bsdiff;
	public $bspatch;
	function __construct()
	{
		$this->bsdiff = C("BSDIFF_PATH") ? C("BSDIFF_PATH") : '/usr/bin/bsdiff';
		$this->bspatch = C("BSPATCH_PATH") ? C("BSPATCH_PATH") : '/usr/bin/bspatch';
	}
	
	function make_patch($app_id)
	{
		$apks = D("Dev://Apks")->getField("id, file_path");
	}
	
	/**
	 * 产生文件存放的文件目录
	 * 格式：
	 * 模块名/6位年月/2位日/两位随机数/随机文件名.扩展名
	 */
	protected function file_path_generator($model="diff")
	{
		$model = empty($model) ? 'upload' : strtolower($model);
		$path = sprintf("%s/%s\//%0.4s/", $model, date("Ym/d"), md5(uniqid(date("His"))));
		$path = preg_replace('@[/\\\\]+@is', '/', $path);
		return $path;
	}
}