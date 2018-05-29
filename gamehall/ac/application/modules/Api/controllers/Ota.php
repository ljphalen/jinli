<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * OTA API V1
 * @author fanch
 *
 */
class OtaController extends Api_BaseController{
	
	/**
	 * ota 获取 应用版本信息 只针对预安装的内容
	 */
	public function syncAction() {
		$time = $this->getInput('_t');
		$responseTime = -1;
		if($time) $responseTime = $time;
		$data = $tmp = array();
		$data['time'] = Common::getTime();
		$versionData = Resource_Service_AppsVersion::getsBy(array('create_time' => array('>',$responseTime)));

		if($versionData) {
			$appIds = array_keys(Common::resetKey($versionData, 'app_id'));
			//只获取预安装的内容
			$appData = Resource_Service_Apps::getsBy(array('id' => array('IN',$appIds), 'belong'=> '1'));
			$appData = Common::resetKey($appData, 'id');
			foreach ($versionData as $key => $value){
				if($appData[$value['app_id']]) {
					$tmp[] = array(
						'name' => html_entity_decode($appData[$value['app_id']]['name']),	
						'package' => $value['package'],
						'versioncode' => $value['version_code'],
						'versionname' => html_entity_decode($value['version']),
						'apkpath' => Common::getDownloadPath() . $value['link']
					);
				}
			}
		}
		$data['versions'] = $tmp;
		$this->output(0, '',$data);
	}
}