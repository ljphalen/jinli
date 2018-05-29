<?php
/**
 * 生成应用文件的存放目录，及访问链接
 * 'FILE_PATH_DIR' => '/files/Data/Attachments/dev/',          //应用文件存放路径，保留反斜杠 x/x/b/a.pdf
 * 'FILE_PATH_URL' => 'http://1.cdn.gionee.com/dev/',          //应用文件访问URL，保留反斜杠 x/x/b/a.pdf
 * 'GAMEDL_PATH_URL' => 'http://gamedl.cdn.gionee.com/dev/',   //apk包跟差分文件访问URL，保留反斜杠 x/x/b/a.pdf
 *	
 * 获取存储位置统一使用 get_path，获取链接前缀统一使用 get_url，方便扩展二级域名
 *
 * @author shuhai
 */
class ApkHelper
{
	protected $basePath;
	protected $baseURL;
	protected $gamedlURL;
	protected $pathArray;
	
	function __construct()
	{
		$apkConfig = C ( 'APK' );
		$this->basePath = $apkConfig ['FILE_PATH_DIR'];
		$this->baseURL = $apkConfig ['FILE_PATH_URL'];
		$this->gamedlURL = $apkConfig ['GAMEDL_PATH_URL'];
		
		$this->pathArray = array(

				"apk"		=> "apks/",
				"screen"	=> "screens/",
				"icon"		=> "icons/",
				"patch"		=> "patches/",
				"image"		=> "images/",
				"doc"		=> "doc/",
				"user"		=> "user/",
				"test"		=> "test/",
				"ftp"		=> "ftp/",
		);
	}

	function get_path($type = "apk")
	{
		if(empty($type) || empty($this->pathArray[$type]))
			return $this->basePath;
		
		return $this->basePath . $this->pathArray[$type];
	}

	function get_url($type = "apk")
	{
		if(empty($type) || empty($this->pathArray[$type]))
			return $this->baseURL;
		
		if (in_array($type, array("apk","patch")))
			return $this->gamedlURL . $this->pathArray[$type];
		
		return $this->baseURL . $this->pathArray[$type];
	}
}
