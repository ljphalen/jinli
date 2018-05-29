<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Ad_IndexAdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad_IndexAd/index',
		'addUrl' => '/Admin/Ad_IndexAd/add',
		'addPostUrl' => '/Admin/Ad_IndexAd/add_post',
		'editUrl' => '/Admin/Ad_IndexAd/edit',
		'editPostUrl' => '/Admin/Ad_IndexAd/edit_post',
		'deleteUrl' => '/Admin/Ad_IndexAd/delete',
		'uploadUrl' => '/Admin/Ad_IndexAd/upload',
		'uploadPostUrl' => '/Admin/Ad_IndexAd/upload_post',
		'uploadImgUrl' => '/Admin/Ad_IndexAd/uploadImg',
	);
	
	public $perpage = 20;
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$title = $this->getInput('title');
		$status = intval($this->getInput('status'));
		$search_time = $this->getInput(array('start_time','end_time'));
		if ($page < 1) $page = 1;
		$params = array();
		$search = array();
		if($title) {
			$search['title'] = $title;
			$params['title'] = array('LIKE', $title);
		}
		if($search_time['start_time']) {
		    $params['start_time'] = array('>=', strtotime($search_time['start_time']));
		}
		if($search_time['end_time']) {
		    $params['end_time'] = array('<=', strtotime($search_time['end_time']));
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
                list($total, $ads) = Game_Service_H5IndexAd::getPageList($page, $perpage, $params);
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_H5IndexAd::getById(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title',  'button_title', 'button_url','start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$result = Game_Service_H5IndexAd::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}	
		
	public function _getMsg($adInfo,$tip,$ad_ptype,$link) {
			if(!$adInfo)  {
				$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
			} else {
				$tmp = $this->_getData($ad_ptype);
				if (in_array($link,$tmp)) $this->output(-1, $tip.'链接ID已经存在不能重复添加');
			}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','sort', 'title',  'button_title', 'button_url','start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$ret = Game_Service_H5IndexAd::updateById($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_H5IndexAd::getById($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Game_Service_H5IndexAd::deleteById($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
