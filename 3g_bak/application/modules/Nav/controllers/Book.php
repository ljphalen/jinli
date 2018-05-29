<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 二级栏目-阅读
 */
class BookController extends Front_BaseController {

	//首页
	public function indexAction() {

	}

	//分类信息
	public function cateAction() {

	}


	//排行榜
	public function rankAction() {

	}

	//书本详情
	public function detailAction() {
		}

	//章节信息
	public function chapterAction(){
		$postData = $this->getInput(array('bid','page','orderType','pageSize'));
		if(empty($postData['bid']) ) $this->output('-1','参数有错');
		$bookInfo = Gionee_Service_Book::BookDao()->getBy(array("bid"=>$postData['bid']));
		if(empty($bookInfo)) $this->output('-1','书本不存在!');
		$page = max($postData['page'],1);
		$pageSize =max($postData['pageSize'],20);
		$orderType = in_array($postData['orderType'],array('asc','desc'))?$postData['orderType']:'asc';
		$chapterList = Gionee_Service_Book::BookChapterDao()->getList(($page-1)*$pageSize,$pageSize,array('bid'=>$postData['bid']),array('id'=>$orderType));
	}
	
	
	public function selfAction() {

	}
}