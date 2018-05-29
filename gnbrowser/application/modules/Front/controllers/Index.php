<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

	public $actions = array(
			'aboutUrl' => '/index/about',
			'indexUrl' => '/index/index',
		);

	public function indexAction() {
		
		//统计首页点击量
		Common::getCache()->increment('3g_index_pv');
			
		//轮播广告
 		list(, $ads) = Gionee_Service_Ad::getCanUseAds(0, 5, array('ad_type'=>1));
 		$this->assign('ads', $ads);
 		
 		//系列
 		list(,$series) = Gionee_Service_Series::getAllSeries();
 		$this->assign('series',$series);
		$this->assign('pageTitle','首页');
		$this->assign('nav','1-1');
	}
}
