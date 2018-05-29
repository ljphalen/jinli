<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 浏览器书签接口
 * @author rainkid
 *
 */
class BrowserController extends Front_BaseController{
	
	public $actions = array(
		'recmarkUrl' => '/browser/recmark',
		'recsiteUrl' => '/browser/recsite/',
		'recurlUrl' => '/browser/recurl/'
	);
	
	public $model_id = 0;
	public $time = '2012-09-10';
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		//机型
		$model_name = 'GN878';
		if(!$model_name) exit('ERROR_MODEL');
		
		$model = Browser_Service_Models::getModelsByName($model_name);
		if(!$model) exit('ERROR_MODEL');
		
		$this->model_id = $model['id'];
	}

	/**
	 * 书签
	 * 
	 */
	public function recmarkAction() {
		//书签列表
		$remarklist = Browser_Service_Recmark::getCanUseRecmarks($this->model_id);
		
		$markers = array();
		if($remarklist) {
			foreach ($remarklist as $key=>$value) {
				$markers[$key]['name'] = $value['name'];
				$markers[$key]['url'] = $value['link'];
				$markers[$key]['icon'] = base64_encode($value['img']);
			}
		}
		
		$data = array('version'=>strtotime($this->time), 'markers'=> $markers);	
		exit(json_encode($data));
	}
	
	
	/**
	 * 书签
	 *
	 */
	public function recsiteAction() {
		//站点列表
		$recsitelist = Browser_Service_Recsite::getCanUseRecsites($this->model_id);
		
		$recsites = array();
		$path = Common::getConfig('siteConfig', 'attachPath');
		foreach ($recsitelist as $k=>$v) {
			$recsites[$k]['id'] = $v['id'];
			$recsites[$k]['name'] = $v['name'];
			$recsites[$k]['url'] = $v['link'];
			$recsites[$k]['icon'] = Util_Image::base64($path.$v['img']);	
		}
		
		//站点分类
		list(, $types) = Browser_Service_RecsiteType::getAllRecsiteType();
		
		$groups = array();
		foreach ($types as $key=>$value) {
			$groups[$key]['name'] = $value['name'];
		}
		
		$sites = $this->_cookSites($groups, $recsites);
		
			
		$data = array('id'=>1, 'version'=>strtotime($this->time), 'groups'=> $sites);
		exit(json_encode($data));
	}
	
	/**
	 * 推荐网址
	 *
	 */
	public function recurlAction() {
		//书签列表
		$recurllist = Browser_Service_Recurl::getCanUseRecurls($this->model_id);
	
		$urls = array();
		if($recurllist) {
			foreach ($recurllist as $key=>$value) {
				$urls[$key]['name'] = $value['name'];
				$urls[$key]['url'] = $value['link'];
				$urls[$key]['icon'] = base64_encode($value['img']);
			}
		}
	
		$data = array('version'=>strtotime($this->time), 'urlInfos'=> $urls);
		exit(json_encode($data));
	}
	
	/**
	 * test action
	 */
	public function testAction() {
		exit('<font size="100px">' . self::getModel() . '</font>');
	}
	
	/**
	 * get model from user-agent
	 */
	private static function getModel() {
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		preg_match("/GiONEE-(.*)\/Phone/", $ua, $matches);
		//机型
		if($matches[1]) return $matches[1];
		return null;
	}
	
	
	/**
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookSites($groups, $sites) {
		foreach ($sites as $key=>$value) {
			$groups[$key]['sites'][] = $value;
		}
		return $groups;
	}
	
}
