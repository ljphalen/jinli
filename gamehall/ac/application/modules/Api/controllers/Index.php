<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * API V1
 * @author fanch
 *
 */
class IndexController extends Api_BaseController{
	
	public function indexAction() {
		exit("API v1.0");
	}
	
	/**
	 * 客户端通过机型获取APP应用
	 * ptype：手机机型 version：数据版本 imei：手机imei号 client：客户端版本号 
	 */
	public function appsAction(){
		$request = $this->getInput(array('imei', 'ptype', 'version', 'client'));
		if(!$request['ptype']) $this->output(-1,'Illegal Request.', array('sign'=>'GioneePreinstallation'));
		if(!$request['version']) $this->output(-1,'Illegal Request.', array('sign'=>'GioneePreinstallation'));
		$version = $request['version'];
		$dataVersion = Resource_Service_Config::getValue('Apps_Data_Version');
		if ($version == $dataVersion) exit();
	
		$params = array(
			'p_title'=>array('FIND',$request['ptype']),
			'status'=>1
		);
		//获取机组ID
		$pgroups = Resource_Service_Pgroup::getsBy($params);
		//机型没分配机组，选择默认机组。
		$pgroupIds = array(1);
		if($pgroups){
			$pgroupIds = array_keys(Common::resetKey($pgroups, 'id'));
		}

		//根据机组获取对应的app数据。
		$temp = array();
		$idxParams = array(
				'pgroup_id'=>array('IN',$pgroupIds)
		);
		//是否读取增量数据
		if($request['version'] != '-1') $idxParams['create_time'] = array('>', $request['version']);
		$idxApps =Resource_Service_IdxAppsPgroup::getsBy($idxParams, array('sort'=>'DESC', 'id'=>'DESC'));
		if($idxApps){
			$appIds = array_keys(Common::resetKey($idxApps, 'app_id'));
			$apps = Resource_Service_Apps::getsBy(array('id'=> array('IN', $appIds)));
			if($apps){
				$apps = Common::resetKey($apps, 'id');
				foreach ($idxApps as $key => $value){
					$appId = $value['app_id'];
					$app =$apps[$appId];
					$temp[]=array(
						'appId' => "{$appId}",
						'packageName' => $app['package'],
						'appName' => html_entity_decode($app['name']),
						'appUrl' => Common::getDownloadPath() . $app['link'],
						'appSize'=>$app['size'] . 'MB',
						'appIconUrl' => Common::getAttachPath() . $app['icon'],
						'appClass' =>"{$app['class']}",
						'sdkVersion' => "{$app['min_os']}"
					);
				}
			}
		}
		
		//响应请求数据
		$response = array(
			'sign'=>'GioneePreinstallation',
			'version' => $dataVersion,
			'items' => $temp
		);
		
		$this->output(0,'',$response);
	}
}