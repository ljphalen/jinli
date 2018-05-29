<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends App_BaseController {
	
	public $actions =array(
			'index' => '/search/index',
	);
	public $perpage = 8;

	/**
	 * 
	 */
	public function indexAction() {
		//默认关键字
		$keyword = Gou_Service_Config::getValue('gou_client_keyword');
		$refer = $this->getInput('refer');
		
		//hash
		$app_taobao_search =  json_decode(Gou_Service_Config::getValue('app_taobao_search'), true);
		$action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APP, $app_taobao_search['module_id'],
		        $app_taobao_search['channel_id'], 0, $app_taobao_search['url'], 'app淘宝搜索', $app_taobao_search['channel_code']);
		
		
		//keywords
		list(,$list) =  Client_Service_Keywords::getList(1, 10, array('status'=>1));
		$this->assign('keyword', $keyword);
		$this->assign('refer', $refer);
		$this->assign('list', $list);
		$this->assign('action', $action);
		$this->assign('title', '搜索');
	}
	
}
