<?php
class ApiAction extends Action
{	
	function get()
	{
		$appId = $this->_get("appid", "intval", 0);
		$apkInfo = helper("Sync")->get($appId);

		header('Content-type: application/json');
		echo json_encode($apkInfo);
		die;
	}
	
	function getapk()
	{
		$apkId = $this->_get("apkid", "intval", 0);
		$apkInfo = helper("Sync")->getapk($apkId);
	
		header('Content-type: application/json');
		echo json_encode($apkInfo);
		die;
	}
	
	function sign()
	{
		$appId = $this->_get("appid", "intval", 0);
		$sign = helper("Sync")->_rsaVerify($appId);
		dump($appId);
		dump($sign);
	}
}