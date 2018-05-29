<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Ad_TuijianController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad_Tuijian/index',
		'addUrl' => '/Admin/Ad_Tuijian/add',
		'addPostUrl' => '/Admin/Ad_Tuijian/add_post',
		'editUrl' => '/Admin/Ad_Tuijian/edit',
		'editPostUrl' => '/Admin/Ad_Tuijian/edit_post',
		'deleteUrl' => '/Admin/Ad_Tuijian/delete',
		'batchUpdateUrl'=>'/Admin/Ad_Tuijian/batchUpdate'
	);
	
	public $perpage = 20;
	
	public $ntype = array(
			1 => '资讯',
			2 => '评测',
			3 => '活动'
		
	);
	
	public $ctype = array(
			1 => '疯玩网',
			2 => '自发布'
			
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$params= $this->getInput(array('title','ctype'));
		$serach =  array();
		$search['ntype'] = 2;
		if($params['title'])  $search['title']  = array('LIKE',$params['title']);
		if($params['ctype'])  $search['ctype']  = $params['ctype'];
		$tmp = array();
		$news = Client_Service_News::getNewsByIds($search);
		$news = Common::resetKey($news, 'id');
		$nids = array_unique(array_keys($news));
		if (nids) {
			$tmp['n_id'] = array('IN',$nids);
		}
		
		list($total, $tjnews) = Client_Service_Tuijian::getList($page, $this->perpage, $tmp);
		
		$this->assign('ctype', $this->ctype);
		$this->assign('tjnews', $tjnews);
		$this->assign('news', $news);
		$this->assign('total', $total);
		$this->assign('search', $params);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$tj_info = Client_Service_Tuijian::getTuijian($id);
		$info = Client_Service_News::getNews(intval($tj_info['n_id']));
		$this->assign('tj_info', $tj_info);
		$this->assign('info', $info);
		$this->assign('type', $this->ntype);
	}
	
	
    //批量上线，下线，排序
	public function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort','start_time','end_time'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='add'){
			$info['start_time'] = strtotime($info['start_time']);
			$info['end_time'] = strtotime($info['end_time']);
			if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
			if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
			if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
			$ret = Client_Service_Tuijian::addTuijians($info['ids'],$info['start_time'],$info['end_time']);
		} else if($info['action'] =='sort'){
			$ret = Client_Service_Tuijian::sortTuijians($info['sort']);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','ctype'));
		$search = array();
		$search['status'] = 1;
		$search['ntype'] = 2;
		if ($s['title']) $search['title'] = array('LIKE',$s['title']);;
		if ($s['ctype']) $search['ctype'] = $s['ctype'];
		list($total,$news) = Client_Service_News::getUseAdNews($page, $this->perpage, $search);
		list(,$new_ids) = Client_Service_Tuijian::getAllTuijian();
		$new_ids = Common::resetKey($new_ids, 'n_id');
		$new_ids = array_unique(array_keys($new_ids));
		
		$this->assign('new_ids', $new_ids);
		$this->assign('ctype', $this->ctype);
		$this->assign('news', $news);
		$this->assign('total', $total);
		$this->assign('search', $s);
		$url = $this->actions['addUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'status', 'ntype', 'n_id', 'start_time', 'end_time', 'update_time'));
		$time = Common::getTime();
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		$info['update_time'] = $time;
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		$ret = Client_Service_Tuijian::updateTuijian($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$result = Client_Service_Tuijian::deleteTuijian($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
