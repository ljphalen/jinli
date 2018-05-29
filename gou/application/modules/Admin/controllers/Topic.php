<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class TopicController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Topic/index',
		'addUrl' => '/Admin/Topic/add',
		'addPostUrl' => '/Admin/Topic/add_post',
		'editUrl' => '/Admin/Topic/edit',
		'editPostUrl' => '/Admin/Topic/edit_post',
		'deleteUrl' => '/Admin/Topic/delete',
		'uploadUrl' => '/Admin/Topic/upload',
		'uploadPostUrl' => '/Admin/Topic/upload_post',
		'uploadImgUrl' => '/Admin/Topic/uploadImg',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $topics) = Gou_Service_Topic::getList($page, $perpage);
		$this->assign('topics', $topics);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Topic::getTopic(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Topic::getTopic(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
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
		$info = $this->getPost(array('title', 'share_content', 'content'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Topic::addTopic($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'share_content', 'content'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Topic::updateTopic($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['content']) $this->output(-1, '内容不能为空.');
		if(!$info['share_content']) $this->output(-1, '分享内容不能为空.');
        if(mb_strlen($info['share_content'],'utf-8')>120)$this->output(-1, '分享内容字数不能超过120个.');
		$info['content'] = $this->updateImgUrl($info['content']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Topic::getTopic($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Topic::deleteTopic($id);
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'topic');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'subject');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
