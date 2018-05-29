<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class StartController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Start/index',
		'addUrl' => '/Admin/Start/add',
		'addPostUrl' => '/Admin/Start/add_post',
		'editUrl' => '/Admin/Start/edit',
		'editPostUrl' => '/Admin/Start/edit_post',
		'deleteUrl' => '/Admin/Start/delete',
		'uploadUrl' => '/Admin/Start/upload',
		'uploadPostUrl' => '/Admin/Start/upload_post',
	);
	
	public $perpage = 20;
	public $versionName = 'Start_Version';
	
	//广告状态
	public $status = array(
			1 => '开启',
			2 => '关闭'
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		

		list($total, $starts) = Gou_Service_Start::getList($page, $perpage);
		$this->assign('starts', $starts);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] .'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Start::getStart(intval($id));
		$this->assign('status', $this->status);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Start::addStart($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);		
		$ret = Gou_Service_Start::updateStart($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Start::getStart($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Start::deleteStart($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'start');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
