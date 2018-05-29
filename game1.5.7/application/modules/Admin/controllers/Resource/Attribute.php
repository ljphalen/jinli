<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_AttributeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Attribute/index',
		'addUrl' => '/Admin/Resource_Attribute/add',
		'addPostUrl' => '/Admin/Resource_Attribute/add_post',
		'editUrl' => '/Admin/Resource_Attribute/edit',
		'editPostUrl' => '/Admin/Resource_Attribute/edit_post',
		'deleteUrl' => '/Admin/Resource_Attribute/delete',
		'uploadUrl' => '/Admin/Resource_Attribute/upload',
		'uploadPostUrl' => '/Admin/Resource_Attribute/upload_post',
		'uploadImgUrl' => '/Admin/Resource_Attribute/uploadImg'
	);
	
	public $perpage = 20;
	public $at_ptypes = array (
			2 => '属性列表',
			3 => '资费列表',
			4 => '分辨率列表',
			5 => '版本列表',
			6 => '运营商列表',
			7 => '游戏属性',
			8 => '标签类型',
			9 => '游戏支持设备',
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$sid = intval($this->getInput('sid'));
		if(!$sid){
			$type = 2;
		} else {
			$type = $sid;
		}
		$this->cookieParams();
		$perpage = $this->perpage;
		list($total, $result) = Resource_Service_Attribute::getList($page, $perpage,array('at_type'=>$type));
		$this->assign('result', $result);
		$this->assign('sid', $type);
		$this->assign('at_ptypes', $this->at_ptypes);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getBy(array('id'=>$id));
		$this->assign('sid', $info['at_type']);
		$this->assign('info', $info);
		$this->assign('at_ptypes', $this->at_ptypes);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Attribute::getBy(array('id'=>$id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('at_ptypes', $this->at_ptypes);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('title', 'at_type','status','img','sort'));
		$info = $this->_cookData($info);
		$result = Resource_Service_Attribute::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->_changeDataNotify();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$sid = intval($this->getInput('sid'));
		$info = $this->getPost(array('id', 'title', 'at_type','status','img','sort'));
		$info = $this->_cookData($info);
		$ret = Resource_Service_Attribute::updateAttributeStatus($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->_changeDataNotify();
		$this->output(0, '操作成功.');
		$this->assign('sid', $sid);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['at_type']) $this->output(-1, '类型不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$sid = $this->getInput('sid');
		$info = Resource_Service_Attribute::getBy(array('id'=>$id));
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Resource_Service_Attribute::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->_changeDataNotify();
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'Attribute');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Attribute');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	
	/**
	 * 数据变更通知
	 */
	private function _changeDataNotify(){
		//属性缓存变更通知
		Resource_Service_Attribute::notifyCache(1);
	}
}
