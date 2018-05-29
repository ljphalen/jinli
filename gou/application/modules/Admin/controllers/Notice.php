<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class NoticeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Notice/index',
		'addUrl' => '/Admin/Notice/add',
		'addPostUrl' => '/Admin/Notice/add_post',
		'editUrl' => '/Admin/Notice/edit',
		'editPostUrl' => '/Admin/Notice/edit_post',
		'deleteUrl' => '/Admin/Notice/delete',
	);
	
	public $appCacheName = array('APPC_Front_Index_index', 'APPC_Channel_Index_index');
	public $perpage = 20;
	public $channels = array(
			1=>'H5',
			2=>'客户端',
			3=>'超级文件夹'
		
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		list($total, $notices) = Gou_Service_Notice::getList($page, $perpage);
		$this->assign('notices', $notices);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
		$this->assign('channels', $this->channels);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Notice::getNotice(intval($id));
		$this->assign('info', $info);
		$this->assign('channels', $this->channels);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('channels', $this->channels);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'channel_id', 'link', 'start_time', 'end_time','hits', 'status'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Notice::addNotice($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'channel_id', 'link', 'start_time', 'end_time','hits', 'status'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Notice::updateNotice($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
		if(!$info['link']) $this->output(-1, '链接不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Notice::getNotice($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Notice::deleteNotice($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}
