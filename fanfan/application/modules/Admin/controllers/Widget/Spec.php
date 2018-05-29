<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 机型管理
 */
class Widget_SpecController extends Admin_BaseController {

	public $actions = array(
		'listUrl' => '/Admin/Widget_Spec/index',
		'initUrl' => '/Admin/Widget_Spec/init',
		'editUrl' => '/Admin/Widget_Spec/edit',
		'postUrl' => '/Admin/Widget_Spec/post',
		'delUrl'  => '/Admin/Widget_Spec/del',
	);

	public function indexAction() {
		$type  = $this->getInput('type');
		$where = array('type' => $type);
		$list  = Widget_Service_Spec::getsBy($where);
		$types = Widget_Service_Spec::getTypes();
		$this->assign('list', $list);
		$this->assign('type', $type);
		$this->assign('types', $types);
	}

	public function initAction() {
		$where = array('type' => '预置机型');
		$list  = Widget_Service_Spec::getsBy($where);
		$this->assign('list', $list);
	}

	/**
	 * 提交更新
	 */
	public function postAction() {
		$info = $this->getPost(array('id', 'name', 'type'));
		if (!$info['name']) {
			$this->output(-1, '标题不能为空.');
		}

		if (!$info['type']) {
			$this->output(-1, '分类不能为空.');
		}

		$info['name'] = strtolower($info['name']);
		//$info['url']  = json_encode($_POST['url']);

		$params = array('name' => $info['name']);
		if (!empty($info['id'])) {
			$params['id'] = array('!=', $info['id']);
		}
		$ret = Widget_Service_Spec::getBy($params);
		if ($ret) {
			$this->output(-1, '机型已经存在');
		}

		if (!empty($info['id'])) {
			$ret = Widget_Service_Spec::set($info, $info['id']);
		} else {
			$ret = Widget_Service_Spec::add($info);
		}

		Widget_Service_Config::setValue('column_version', Common::getTime());
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}


	public function editAction() {
		$id   = $this->getInput('id');
		$type   = $this->getInput('type');
		$info = Widget_Service_Spec::get(intval($id));
		$this->assign('cp', Widget_Service_Cp::$CpCate);
		$info['type'] = !empty($type)?$type:$info['type'];
		$info['url'] = json_decode($info['url'], true);
		$this->assign('info', $info);
	}

	public function delAction() {
		$id         = $this->getInput('id');
		$columnList = Widget_Service_Column::getListBySpecId($id);
		if ($columnList) {
			$this->output(-1, '机型下面存在栏目,请移除');
		}
		$ret = Widget_Service_Spec::delete($id);
		Widget_Service_Config::setValue('column_version', Common::getTime());
		if ($ret) {
			$this->output(0, '操作成功');
		}
		$this->output(-1, '操作失败');
	}


}
