<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Apk_Service_Aapt
 * @author fanch
 *
 */
class Apk_Service_Aapt extends Common_Service_Base{
	
	/**
	 * 获取apk包信息
	 * @param apk file full path $file
	 * @return boolean|array()
	 */
	public static function info($file) {
		if (!$file) return false;
		if (strpos($file, ".apk") === false) return false;
		if (!file_exists($file)) return false;
		$cmd = self::getCmd($file);
		exec($cmd, $output, $return);
		if ($return !=0) return false;
		$output = implode("\n", $output);

		#内部名称,软件唯一的
		$pattern_sys_name = "/package: name='(.*)'/isU";
		preg_match($pattern_sys_name, $output, $m);
		$info['packagename']=$m[1];
		
		#作者
		$info['author']='';
		
		#截图
		$info['screen']='';
		
		#对外显示的版本名称
		$pattern_version = "/versionName='(.*)'/isU";
		preg_match($pattern_version, $output, $m);
		$info['version1']=$m[1];
		
		#内部版本名称,用于检查升级
		$pattern_version_code = "/versionCode='(.*)'/isU";
		preg_match($pattern_version_code, $output, $m);
		$info['version_code1']=$m[1];

		#apk权限
		$permission = array();
		$pattern_perm = "/uses-permission:'(.*)'/isU";
		preg_match_all ( $pattern_perm, $output, $m );
		if ($m) {
			$cnt = count ( $m [1] );
			for($i = 0; $i < $cnt; $i ++) {
				$permission[] = $m [1] [$i];
			}
		}
		$info ['permission'] = $permission;
		
		//apk图标
		$pattern_name = "/icon='(.*)'/isU";
		preg_match ( $pattern_name, $output, $m );
		$info ['iconurl'] = $m [1];

		return $info;
	}
	
	/**
	 * get command 
	 * @param apk full path $file
	 */
	private static function getCmd($file) {
		$cmdfile = BASE_PATH . 'library/Apk/Cmd/aapt';
		return escapeshellcmd(sprintf("%s d badging %s", $cmdfile, realpath($file)));
	}
}