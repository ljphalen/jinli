<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 新闻
 */
class NewsController extends Front_BaseController {

	public $actions = array(
		'aboutUrl' => '/news/about',
		'indexUrl' => '/news/index',
	);

	public function indexAction() {
		//统计新闻pv
		Gionee_Service_Log::pvLog('3g_news');
		//统计新闻UV
		$t_bi = $this->getSource();
		Gionee_Service_Log::uvLog('3g_news', $t_bi);
	}

	public function listAction() {
	}

	public function detailAction() {
	}
}