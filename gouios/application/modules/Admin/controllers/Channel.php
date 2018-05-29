<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ChannelController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Channel/index',
		'addUrl' => '/Admin/Channel/add',
		'addPostUrl' => '/Admin/Channel/add_post',
		'editUrl' => '/Admin/Channel/edit',
		'editPostUrl' => '/Admin/Channel/edit_post',
		'deleteUrl' => '/Admin/Channel/delete',
		'uploadUrl' => '/Admin/Channel/upload',
		'uploadPostUrl' => '/Admin/Channel/upload_post',
		'uploadImgUrl' => '/Admin/Channel/uploadImg'
	);
	public $perpage = 20;
	public $versionName = 'Channel_Version';

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		list($total, $channels) = Gou_Service_Channel::getList($page, $this->perpage);	
		$this->assign('channels', $channels);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['indexUrl'] .'/?'));
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'sort', 'link', 'status', 'img','hits', 'start_time', 'end_time', 'is_recommend', 'descript'));
		
		$info = $this->_cookData($info);
		$ret = Gou_Service_Channel::addChannel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_Channel::getChannel(intval($id));
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'sort',  'link', 'status', 'img','hits', 'type_id', 'start_time', 'end_time', 'is_recommend', 'descript'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Channel::updateChannel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Channel::getChannel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_Channel::deleteChannel($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['link'])  $this->output(-1, '链接不能为空.');
		/* if(strpos($data['link'], 'http://') === false || !strpos($data['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		} */
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		return $info;
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
		$ret = Common::upload('imgFile', 'channel');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'channel');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
