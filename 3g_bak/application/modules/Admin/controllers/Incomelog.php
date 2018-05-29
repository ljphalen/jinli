<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 收入管理
 * @author huwei
 */
class IncomelogController extends Admin_BaseController {

	public $actions = array(
		'listUrl' => '/Admin/Incomelog/index',
		'editUrl' => '/Admin/Incomelog/edit',
		'delUrl'  => '/Admin/Incomelog/del',
	);

	//收入统计
	public function indexAction() {
		$page = $this->getInput('page');
		$page = max($page, 1);
		list($total, $dataList) = Gionee_Service_Income::getList($page, 20, array(), array('get_time' => 'DESC'));
		$this->assign('list', $dataList);
		$this->assign('pager', Common::getPages($total, $page, 20, $this->actions['listUrl'] . "/?"));
	}


	public function editAction() {
		if (!empty($_POST['income'])) {
			$postData = $this->getInput(array('get_time', 'income', 'cps', 'cpc', 'cpt', 'cpa', 'push', 'font', 'id'));
			if (!$postData['get_time'] || !$postData['income']) $this->output('-1', '日期或总收入不能为空！');
			$postData['add_time'] = time();
			$postData['add_user'] = $this->userInfo['username'];
			$postData['get_time'] = strtotime($postData['get_time']);
			$total                = bcadd($postData['cps'], bcadd($postData['cpc'], bcadd($postData['cpt'], bcadd($postData['cpa'], $postData['push'], 2), 2), 2), 2);
			if ($postData['income'] != $total) {
				$this->output('-1', '总收入与其它各项收入之和不相等，请检查！');
			}
			if (!empty($postData['id'])) {
				$ret = Gionee_Service_Income::update($postData, $postData['id']);
			} else {
				$ret = Gionee_Service_Income::add($postData);
			}
			if ($ret) {
				$this->output('0', '添加成功！');
			} else {
				$this->output('-1', '添加失败！');
			}
		} else {
			$id = $this->getInput('id');
			if ($id) {
				$data = Gionee_Service_Income::get($id);
				$this->assign('data', $data);
			} else {
				$this->assign('data', array('get_time' => time()));
			}
		}

	}

	public function delAction() {
		$id  = $this->getInput('id');
		$ret = Gionee_Service_Income::delete($id);
		if ($ret) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '编辑失败');
		}
	}


}