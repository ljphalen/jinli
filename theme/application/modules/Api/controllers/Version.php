<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class VersionController extends Api_BaseController{
	
	public function indexAction() {
		echo Theme_Service_Config::getValue('file_version');
		exit();
	}
	
	public function typeAction() {
		echo Theme_Service_Config::getValue('type_version');
		exit();
	}
	
	public function fileAction() {
		$since = $this->getInput('since');
		if(!$since) return false;
		$file = Theme_Service_File::getBy(array('since'=>$since));
		echo $file['update_time'];
		exit();
	}
}