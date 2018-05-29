<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RedirectController extends Front_BaseController {
	
	/**
	 * 第三方链接统计
	 * @return boolean
	 */
	public function indexAction(){
		$url = html_entity_decode(urldecode($this->getInput('url')));
		$this->redirect($url);
	}
	
	/**
	 * 浏览器搜索跳转
	 * @return boolean
	 */
	public function browserSearchAction(){
		$keyword = trim($this->getInput('keyword'));
		
		if($keyword) {
			$info = array(
					'keyword'=>$keyword,
					'keyword_md5'=>md5($keyword),
					'create_time'=>Common::getTime(),
					'dateline'=>date('Y-m-d', Common::getTime())
			);
			Client_Service_KeywordsLog::addKeywordsLog($info);
		}
				
		$url = sprintf('%s%s',Common::getConfig ('apiConfig', 'taobao_search_pid_h5'), urlencode($keyword));
		$this->redirect($url);
	}
}
