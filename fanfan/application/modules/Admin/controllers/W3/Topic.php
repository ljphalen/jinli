<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class W3_TopicController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/W3_Topic/list',
		'editUrl'       => '/Admin/W3_Topic/edit',
		'deleteUrl'     => '/Admin/W3_Topic/delete',
		'uploadUrl'     => '/Admin/Widget_Down/upload',
		'uploadPostUrl' => '/Admin/Widget_Down/upload_post',
	);

	public $perpage = 20;


	public function editAction() {
		$this->_post();

		$id   = $this->getInput('id');
		$info = W3_Service_Topic::get(intval($id));

		$this->assign('info', $info);
	}

	public function listAction() {
		$param   = array();
		$perpage = 20;
		$orderby = array('id' => 'DESC');
		$page    = intval($this->getInput('page'));
		$total   = W3_Service_Topic::getTotal($param);
		$list    = W3_Service_Topic::getList($page, $perpage, $param, $orderby);
		$this->assign('total', $total);
		$this->assign('list', $list);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	public function _post() {
		$info = $this->getPost(array('id', 'title', 'content', 'create_time'));
		if (empty($info['title'])) {
			return false;
		}

		if (!$info['title']) {
			$this->output(-1, '标题不能为空.');
		} else if (!$info['content']) {
			$this->output(-1, '内容不能为空.');
		}

		if (empty($info['create_time'])) {
			$info['create_time'] = time();
		} else {
			$info['create_time'] = strtotime($info['create_time']);
		}

		if (empty($info['id'])) {
			$ret = W3_Service_Topic::add($info);
		} else {
			$ret = W3_Service_Topic::edit($info, $info['id']);
		}

		if (!$ret) {
			$this->output(-1, '操作失败.');
		}

		$this->output(0, '操作成功.');
		exit;
	}


	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = W3_Service_Topic::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}

		$ret = W3_Service_Topic::del($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}
}
