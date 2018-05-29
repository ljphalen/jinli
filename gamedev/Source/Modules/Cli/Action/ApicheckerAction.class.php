<?php
/**
 * Api同步应用到运营后台，接口检查工具
 * @author shuhai
 *
 */
class ApicheckerAction extends CliBaseAction
{
	function index()
	{
		$host = $this->_get('host', 'trim', 'dev.gionee.local');
		$apps = D('Dev://Apks')->where(array("status"=>"3"))->getField('app_id', true);
		foreach ($apps as $app_id)
		{
			$url = sprintf("http://%s/api/get/appid/%d", $host, $app_id);
			$data= `curl -s {$url}`;
			$data=json_decode($data, true);
			
			//检查接口中的图片和icon是否正常
			$this->printf("appid:%s, apkid:%s, picture:%s, icon:%s, url: %s",
					$app_id, $data["data"]["apkid"],
					count(explode("|", $data["data"]['imgs'])),
					count(explode("|", $data["data"]['icon'])),
					$url
			);
		}
	}
}