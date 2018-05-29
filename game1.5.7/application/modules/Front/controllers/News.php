<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class NewsController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => '/Front/News/index',
		'detailUrl' => '/Front/News/detail',
	);

	public $perpage = 10;
	public $news_type = array(1=>'资讯',3=>'活动');

	/**
	 * 新闻页面
	 */
	public function indexAction() {
		Common::addSEO($this,'资讯活动详情页');
		$ntype = intval($this->getInput('ntype'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		//获取全部新闻
		if($ntype){
			$param['ntype'] = $ntype;
		}else{
			$param['ntype'] = array('IN',array(1,3));
		}
		$param['status'] = 1 ;
		$search['ntype'] = $ntype;
		list($news_all_total, $news_all) = Client_Service_News::getUseNews($page, $this->perpage,$param);
		$this->assign('news_all', $news_all);
		//分页
		$url = $this->actions['listUrl'].'/?'. http_build_query($search).'&';
		$paper_all = Common::getPages($news_all_total, $page, $this->perpage, $url,'',1);
		$this->assign('paper_all',$paper_all );

		$this->assign('ntype', $ntype);
		$this->assign('news_type', $this->news_type);
		
		//下载排行
		$this->assign('downgames', $this->getDowloadRank());
	}
	
	/**
	 * 新闻明细
	 */	
	public function detailAction(){
		$id = intval($this->getInput('id'));

		//取得相关的新闻内容
		$news_info = Client_Service_News::getNews($id);
		
		//没有记录跳转
		if(!$news_info){
			$str  = $this->redirect('/Front/Error/index/');
			exit;
		}
		
		$type = array(1 => '资讯', 3 => '活动');
		//下载排行
		$this->assign('downgames', $this->getDowloadRank());
		$tj_object = 'newsdetail'.$id;
		$this->assign('tj_object', $tj_object);
		$this->assign('tj_intersrc', $tj_object);
		$this->assign('news_info', $news_info);
		$this->assign('ntype', $type);
		//取得配置文件
		$ami_web_new_share_text = Game_Service_Config::getValue('ami_web_new_share_text');
		$this->assign('ami_web_new_share_text', $ami_web_new_share_text);
		
	}
}