<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class NewsController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => 'news/index',
	);

	public $perpage = 100;


	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$tops = Browser_Service_News::getTopNews();
		$tops['content'] = Util_String::substr(html_entity_decode($tops['content']), 0, 120);
		
		list($total, $news) = Browser_Service_News::getList($page, $perpage);
		$this->assign('tops', $tops);
		$this->assign('news', $news);
		$this->assign('pageTitle','公司新闻');
	}	

	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Browser_Service_News::getNews(intval($id));
		$info['content'] = html_entity_decode($info['content']);
		$this->assign('info', $info);
		$this->assign('pageTitle',$info['title'].' - 公司新闻');
	}
}
