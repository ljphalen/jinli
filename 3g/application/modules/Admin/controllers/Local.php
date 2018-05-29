<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * APP调用本地接口类
 * @author panxb
 *
 */
class LocalController extends Admin_BaseController {

	public $pageSize = '20';

	public $actions = array(
		'deleteUrl' => '/Admin/Local/delete',
		'editUrl'   => '/Admin/Local/add',
	);

	public function addAction() {
		$id   = $this->getInput('id');
		$info = array();
		if ($id) {
			$data = Gionee_Service_LocalInterface::get($id);
			$info = json_decode($data['info']);
		}
		$this->assign('info', $info);
		$this->assign('id', $id);
	}

	/**
	 * 提交处理操作
	 */
	public function addPostAction() {
		$id       = $this->getInput('id');
		$dataList = $this->getInput('From');

		$valideData = array();
		foreach ($dataList as $k => $v) {
			if ($v['key'] != '' || $v['value'] != '' || $v['type'] != '') {
				$valideData[] = $v;
			}
		}
		if ($id) { //编辑模式
			$res = Gionee_Service_LocalInterface::update(array('info' => json_encode($valideData)), $id);
		} else {//添加模式
			$res = Gionee_Service_LocalInterface::add(array('info' => json_encode($valideData)));
		}
		if ($res) $this->output(0, '操作成功'); else $this->output(-1, '操作失败');
	}

	/**
	 * 数据列表
	 */
	public function listAction() {
		$page     = $this->getInput('page');
		$page     = max($page, 1);
		$dataList = Gionee_Service_LocalInterface::getList($page, $this->pageSize, array());
		$this->assign('data', $dataList[1]);
	}

	/**
	 * 删除操作
	 */
	public function deleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_LocalInterface::delete($id);
		if ($res) $this->output(0, '操作成功'); else $this->output(-1, '操作失败');
	}
}