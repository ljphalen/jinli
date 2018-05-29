<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化游戏资源获取接口
 * v1.5.4 增加
 * @author fanch
 *
 */
class Local_GameController extends Api_BaseController {
	

	public function categorylistAction(){
		$ver = $this->getInput('dataVersion');
		$dataver = Game_Service_Config::getValue('DATA_VERSION_CATEGORYLIST');
		$dataver = $dataver ? $dataver : 0;
		if ($ver && ($ver == $dataver)) $this->versionOutput(0, '', array(), $dataver);
		$categoryData = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1,'editable'=>array('!=', 1)), array('sort'=>'desc'));
		$response = array();
		foreach ($categoryData as $value){
			$response[]=array('index'=>$value['id'],'typeName'=>$value['title']);
		}
		$this->versionOutput(0, '', $response, $dataver);
	}
	
	
	
	public function attributeListAction(){
		$attributeData = $this->getAttributeCacheData ();
		$this->formatOutputData ( $attributeData);

	}


	private function formatOutputData($attributeData) {
		$data['success'] = true;
		$data['msg'] = '';
		$data['sign'] = 'GioneeGameHall';
		$data['data']['items']= $attributeData;
		$this->clientOutput($data);
	}

	
	private function getAttributeCacheData() {
		$attributeType = Resource_Service_Attribute::GAME_ATTRIBUTE;
		$version = Resource_Service_Attribute::getDataVersion ($attributeType);
		$localCache  = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
		$dataKeyName = Resource_Service_Attribute::getDataKeyName ($attributeType, $version);
		$attributeData = $localCache->get($dataKeyName);
		if($attributeData === false ){
			$attributeData = $this->getGameAtrributeListFromDb ($attributeType);
			$attributeData = $this->formatAttributeData ( $attributeData );
			$localCache->set($dataKeyName,  $attributeData, Resource_Service_Attribute::CACHE_EXPIRE);
		}
		return $attributeData;
	}

	
	private function formatAttributeData($attributeData) {
		$items = array();
		$attachPath = Common::getAttachPath();
		foreach ($attributeData as $key=>$val){
			$items[] = array('id'=>$val['id'],
					         'type'=>$val['parent_id'],
					         'iconUrl'=>$val['img']?$attachPath.$val['img']:'',
					         'name'=>$val['title']
					     );
		}
		return $items;
	}

	
	private function getGameAtrributeListFromDb($attributeType) {
		$attributeData = Resource_Service_Attribute::getsBy(array('at_type'=>$attributeType), array('id'=>'desc'));
		return $attributeData;
	}

	
	
}
