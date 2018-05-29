<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ForumreplyController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/ForumReply/index',
		'editUrl' => '/Admin/ForumReply/edit',
		'editPostUrl' => '/Admin/ForumReply/edit_post',
		'deleteUrl' => '/Admin/ForumReply/delete',
		'deleteImgUrl' => '/Admin/ForumReply/delete_img',

	);
	public $perpage = 20;
	public $status = array (
			0 => '未审核',
			1 => '审核通过',
			2 => '审核未通过',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$param = $this->getInput(array('status'));
		$search = array();
		if ($param['status']) $search['status'] = $param['status'];
		
		list($total, $forums) = Gou_Service_ForumReply::getList($page, $perpage, $search, array('id'=>'DESC'));		
		
		$this->assign('forums', $forums);
		$this->assign('status', $this->status);
	    $this->assign('search', $search);
	    $url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_ForumReply::getForumReply(intval($id));		
		$this->assign('status', $this->status);		
       $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'content', 'status'));
		$info = $this->_cookData($info);
		
		$ret = Gou_Service_ForumReply::updateForumReply($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ForumReply::getForumReply($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$ret = Gou_Service_ForumReply::deleteForumReply($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['content']) $this->output(-1, '内容不能为空.');
		return $info;
	}
}
