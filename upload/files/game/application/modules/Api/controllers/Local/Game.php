<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化游戏资源获取接口
 * v1.5.4 增加
 * @author fanch
 *
 */
class Local_GameController extends Api_BaseController {
	
	/**
	 * 获取游戏分类数据
	 * { 
	 *	"version":12345, 
	 *	"sign":"GioneeGameHall", 
	 *	"data": [ 
	 *		{ 
	 *			"index": "1", 
	 *			"typeName": "休闲益智" 
	 *		}, 
	 *		{ 
	 *			"index": "2", 
	 *			"typeName": "动作冒险" 
	 *		}, 
	 *		...... 
	 *	 ] 
	 * }
	 */
	public function categorylistAction(){
		header("Content-type: text/html; charset=utf-8");
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
}
