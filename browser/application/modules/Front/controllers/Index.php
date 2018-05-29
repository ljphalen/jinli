<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

	public $actions = array(
			'indexUrl' => '/index/index',
		);

	public function indexAction() {
		
		//统计首页点击量
		
		Common::getCache()->increment('browser_index_pv');
			
		//今日推荐的头条
		$commend_top = Browser_Service_News::getNewsList(array(1), 1, array('istop'=>1));
		
		//今日推荐列表
		$commend_list = Browser_Service_News::getNewsList(array(1), 4, array('istop'=>0));
		
		//热门资讯头条
		$hot_top = Browser_Service_News::getNewsList(array(2,3,4,5), 1, array('istop'=>1));
		
		//热门资讯列表
		$hot_list = Browser_Service_News::getNewsList(array(2, 3,4,5), 4, array('istop'=>0, 'status'=>1));
 		
 		$this->assign('commend_top',$commend_top[0]);
 		$this->assign('commend_list',$commend_list);
 		$this->assign('hot_top',$hot_top[0]);
 		$this->assign('hot_list',$hot_list);
		$this->assign('pageTitle','手机网站导航');
	}

}
