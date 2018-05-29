<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题管理
 * @author rainkid
 */
class GamesubjectController extends Admin_BaseController {

	public $actions = array(
		'listUrl'      => '/Admin/Gamesubject/index',
		'addUrl'       => '/Admin/Gamesubject/add',
		'addPostUrl'   => '/Admin/Gamesubject/add_post',
		'editUrl'      => '/Admin/Gamesubject/edit',
		'editPostUrl'  => '/Admin/Gamesubject/edit_post',
		'deleteUrl'    => '/Admin/Gamesubject/delete',
		'viewUrl'      => '/subject',
		'uploadImgUrl' => '/Admin/Gamesubject/uploadImg',
	);

	public $perpage = 20;
	public $channel = 2;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $channels) = Gionee_Service_Subject::getList($page, $perpage, array('channel' => $this->channel));

		$this->assign('channel', $this->channel);
		$this->assign('channels', $channels);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Subject::getSubject(intval($id));
		$this->assign('info', $info);
	}

	public function addAction() {
	}


	public function add_postAction() {
		$info            = $this->getPost(array('title', 'content'));
		$info['channel'] = $this->channel;
		$info            = $this->_cookData($info);
		$result          = Gionee_Service_Subject::addSubject($info);
		if (!$result) $this->output(-1, '操作失败');
		Admin_Service_Log::op($info);
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'content'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Subject::updateSubject($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		Admin_Service_Log::op($info);
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '专题名称不能为空.');
		if (!$info['content']) $this->output(-1, '内容不能为空.');
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Subject::getSubject($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_Subject::deleteSubject($id);
		if (!$result) $this->output(-1, '操作失败');
		Admin_Service_Log::op($id);
		$this->output(0, '操作成功');
	}

	public function uploadImgAction() {
		$ret     = Common::upload('imgFile', 'subject');
		$webroot = Common::getCurHost();
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $webroot . '/attachs' . $ret['data'])));
	}
}