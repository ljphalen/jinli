<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_FreqController extends Admin_BaseController {

	public $actions = array(
		'listUrl'     => '/Admin/Widget_Freq/index',
		'editUrl'     => '/Admin/Widget_Freq/edit',
		'delUrl'     => '/Admin/Widget_Freq/del',
	);

	public $perpage = 20;

	public function indexAction() {
		$list  = Widget_Service_Freq::all(array('sort'=>'DESC'));
		$this->assign('list', $list);
		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);

	}

	public function editAction() {
		$info = $this->getPost(array('id', 'type', 'url_id', 'num', 'sort', 'status', 'history_day'));
		$info['time'] = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);

		if (!empty($info['url_id'])) {
			if (!$info['url_id']) {
				$this->output(-1, '内容源不能为空.');
			}

			if (!$info['num']) {
				$this->output(-1, '数量不能为空.');
			}

			if (!$info['history_day']) {
				$this->output(-1, '几天前数据不能为空.');
			}

			if (!$info['sort']) {
				//$this->output(-1, '排序不能为空.');
			}

			if (Widget_Service_Freq::existUrlId($info['url_id'], $info['id'])) {
				$this->output(-1, '来源已存在.');
			}

			if (!empty($info['id'])) {
				$info['last_ids'] = '';
				$info['last_date'] = '';
				$ret = Widget_Service_Freq::set($info, $info['id']);
			} else {
				$ret = Widget_Service_Freq::add($info);
			}

			if (!$ret) {
				$this->output(-1, '操作失败.');
			}
			$this->output(0, '操作成功.');
		}

		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpUrls', $cpUrls);

		$id   = $this->getInput('id');
		$info = Widget_Service_Freq::get(intval($id));
		$this->assign('info', $info);

		$this->assign('types', Widget_Service_Column::$types);
	}

	public function delAction() {
		$id   = $this->getInput('id');
		$info = Widget_Service_Freq::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}

		$ret = Widget_Service_Freq::del($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}


}
