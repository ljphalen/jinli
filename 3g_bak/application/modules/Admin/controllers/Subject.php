<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 */
class SubjectController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Subject/index',
		'addUrl'        => '/Admin/Subject/add',
		'addPostUrl'    => '/Admin/Subject/add_post',
		'editUrl'       => '/Admin/Subject/edit',
		'editPostUrl'   => '/Admin/Subject/edit_post',
		'deleteUrl'     => '/Admin/Subject/delete',
		'viewUrl'       => '/subject',
		'uploadUrl'     => '/Admin/Subject/upload',
		'uploadPostUrl' => '/Admin/Subject/uploadPost',
		'usersDetail'   => '/Admin/Subject/detail',

	);

	public $perpage = 20;

	public $channel = array(
		0 => '所有',
		1 => '金立购',
		2 => '游戏',
		3 => 'IMC',
		4 => '官网导航'
	);

	/**
	 *
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$channel = $this->getInput('channel');
		$perpage = $this->perpage;

		$search = array();
		if ($channel) $search['channel'] = $channel;

		list($total, $channels) = Gionee_Service_Subject::getList($page, $perpage, $search);
		$this->assign('channel', $this->channel);
		$this->assign('channels', $channels);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 *
	 */
	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Subject::getSubject(intval($id));
		$this->assign('channel', $this->channel);
		$this->assign('info', $info);
	}

	/**
	 *
	 */
	public function addAction() {
		$this->assign('channel', $this->channel);
	}

	/**
	 *
	 */
	public function add_postAction() {
		Admin_Service_Access::pass('add');
		$info   = $this->getPost(array('title', 'content', 'status', 'channel', 'hide_title'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_Subject::addSubject($info);
		Admin_Service_Log::op($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 */
	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array('id', 'title', 'content', 'status', 'channel', 'hide_title'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Subject::updateSubject($info, intval($info['id']));
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 */
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '专题名称不能为空.');
		if (!$info['content']) $this->output(-1, '内容不能为空.');
		return $info;
	}

	/**
	 *
	 */
	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id   = $this->getInput('id');
		$info = Gionee_Service_Subject::getSubject($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_Subject::deleteSubject($id);
		Admin_Service_Log::op($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


	/**
	 * 图片上传
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *提交上传图片功能
	 */
	public function uploadPostAction() {
		$ret   = Common::upload('img', 'subject');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

}
