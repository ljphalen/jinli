<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Widget_PushController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Widget_Push/index',
		'editUrl'     => '/Admin/Widget_Push/edit',
		'editPostUrl' => '/Admin/Widget_Push/edit_post',
		'deleteUrl'   => '/Admin/Widget_Push/delete',
	);
	public $perpage = 20;

	public function indexAction() {
		$page  = intval($this->getInput('page'));
		$list  = Widget_Service_Push::getList($page, $this->perpage);
		$total = Widget_Service_Push::getTotal();
		$this->assign('list', $list);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl'] . '/?'));
	}

	public function configAction() {
		$info = $this->getPost(array('push_appid', 'push_appwd'));

		if (!empty($info['push_appid'])) {
			if (!$info['push_appid']) {
				$this->output(-1, 'ID不能为空.');
			}

			if (!$info['push_appwd']) {
				$this->output(-1, '密码不能为空.');
			}

			Widget_Service_Config::setValue('push_appid', $info['push_appid']);
			Widget_Service_Config::setValue('push_appwd', $info['push_appwd']);

			$this->output(0, '操作成功.');
		}

		$configs = Widget_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	public function editAction() {

	}

	public function edit_postAction() {
		$info = $this->getPost(array('title', 'content', 'type', 'msg_body'));
		if (empty($info['title'])) {
			$this->output(-1, '标题不能为空.');
		}
		if (empty($info['content'])) {
			$this->output(-1, '内容不能为空.');
		}
		if (empty($info['type'])) {
			$this->output(-1, '类型不能为空.');
		}

		if ($info['type'] == 'cp') {
			$info['msg_body'] = Widget_Service_Push::buildBody($info['msg_body']);
		}

		if (empty($info['msg_body'])) {
			$this->output(-1, '消息不能为空.');
		}

		$info['created_at'] = time();

		if ($info['type'] == 'cp') {
			$info['msg_body'] = json_encode($info['msg_body']);
		}

		$id = Widget_Service_Push::add($info);

		if (!$id) {
			$this->output(-1, '操作失败.');
		}

		$response = Widget_Service_Push::toPush($id, json_encode($info));
		Widget_Service_Push::set(array('response' => $response), $id);

		$this->output(0, '操作成功.');
	}

	/**
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Widget_Service_Push::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}

		$ret = Widget_Service_Push::del($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}


}
