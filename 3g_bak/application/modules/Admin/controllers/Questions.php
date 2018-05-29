<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class QuestionsController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Questions/index',
		'addUrl'        => '/Admin/Questions/add',
		'addPostUrl'    => '/Admin/Questions/add_post',
		'editUrl'       => '/Admin/Questions/edit',
		'editPostUrl'   => '/Admin/Questions/edit_post',
		'deleteUrl'     => '/Admin/Questions/delete',
		'importUrl'     => '/Admin/Questions/import',
		'importPostUrl' => '/Admin/Questions/import_post',

	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $questions) = Gionee_Service_Questions::getList($page, $perpage);

		$this->assign('questions', $questions);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	public function addAction() {
	}

	public function add_postAction() {
		$info = $this->getPost(array('sort', 'question', 'answer', 'status'));
		if (!$info['question']) $this->output(-1, '问题不能为空.');
		if (!$info['answer']) $this->output(-1, '答案不能为空.');
		$ret = Gionee_Service_Questions::addQuestions($info);
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Questions::getQuestions(intval($id));
		$this->assign('info', $info);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'question', 'answer', 'status'));
		if (!$info['question']) $this->output(-1, '问题不能为空.');
		if (!$info['answer']) $this->output(-1, '答案不能为空.');
		$ret = Gionee_Service_Questions::updateQuestions($info, $info['id']);
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Questions::getQuestions($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_Questions::deleteQuestions($id);
		Admin_Service_Log::op($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


	public function importAction() {
	}

	public function import_postAction() {
		$content = @ file_get_contents($_FILES['content']['tmp_name']);
		if (!$content) $this->output(-1, '请选择文件');

		$list = explode("\n", $content);

		$data = array();
		foreach ($list as $key => $value) {
			if ($value) {
				$item = explode('|', $value);
				if ($item[0] && $item[1]) {
					$data[$key]['id']       = '';
					$data[$key]['question'] = $item[0];
					$data[$key]['answer']   = $item[1];
					$data[$key]['sort']     = 0;
					$data[$key]['status']   = 1;
				}
			}
		}

		$ret = Gionee_Service_Questions::batchaddQuestions($data);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功');
	}
}
