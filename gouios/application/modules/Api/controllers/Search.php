<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 搜索
 *
 */
class SearchController extends Api_BaseController {
	
	/**
	 * 关键字
	 */
	public function keywordsAction() {
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		//cache version
		$version = Gou_Service_Config::getValue('Keywords_Version');
		$data = array();
		
		if($client_data_version < $version) {
			list(,$list) =  Gou_Service_Keywords::getList(1, 10, array('status'=>1));
			
			foreach ($list as $key=>$value) {
				$data[] = $value['keyword'];
			}
		}
		
		$webroot = Common::getWebRoot();
		$action = $webroot.'/search/search';
		
		$this->output(0, '',  array('keywords'=>$data, 'form_action'=>$action, 'version'=>$version));
	}
	
	/**
	 * 搜索
	 */
	public function searchAction() {
		$keyword = trim(urldecode($this->getInput('keyword')));
		
		if($keyword) {
			$info = array(
					'keyword'=>$keyword,
					'keyword_md5'=>md5($keyword),
					'create_time'=>Common::getTime(),
					'dateline'=>date('Y-m-d', Common::getTime())
			);
			Gou_Service_KeywordsLog::addKeywordsLog($info);
		}
				
		//$url = sprintf('http://r.m.taobao.com/s?p=mm_32564804_3417008_14492854&q=%s', urlencode($keyword));
		$url = sprintf('http://ai.m.taobao.com/search.html?pid=mm_32564804_6072328_25180609&q=%s', urlencode($keyword));
		$this->redirect($url);
		//$this->output(0, '',  'success');
	}
}
