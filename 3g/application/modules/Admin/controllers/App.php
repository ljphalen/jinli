<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class AppController
 * 轻应用
 */
class AppController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/App/index',
		'addUrl'        => '/Admin/App/add',
		'addPostUrl'    => '/Admin/App/add_post',
		'editUrl'       => '/Admin/App/edit',
		'editPostUrl'   => '/Admin/App/edit_post',
		'deleteUrl'     => '/Admin/App/delete',
		'uploadUrl'     => '/Admin/App/upload',
		'uploadPostUrl' => '/Admin/App/upload_post',
	);

	public $perpage = 20;
	public $types;//分类

	public function init() {
		parent::init();
		list(, $this->types) = Gionee_Service_AppType::getAllAppType();
	}

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param   = $this->getInput(array('type_id', 'order_by'));

		//排序方式
		$order_by = $param['order_by'] ? $param['order_by'] : 'id';

		$search = array();

		if ($param['type_id'] != '') $search['type_id'] = $param['type_id'];
		$search['order_by'] = $param['order_by'];
		list($total, $recmarks) = Gionee_Service_App::getList($page, $perpage, $search, array(
			$order_by => 'DESC',
			'id'      => 'DESC'
		));
		$this->assign('recmarks', $recmarks);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('types', Common::resetKey($this->types, 'id'));
		$this->assign('order_by', $order_by);
		$this->assign('param', $param);
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_App::getApp(intval($id));
		$this->assign('info', $info);
		$this->assign('types', $this->types);
	}

	public function addAction() {
		$this->assign('types', $this->types);
	}

	public function add_postAction() {
		$info   = $this->getPost(array(
			'name',
			'link',
			'img',
			'sort',
			'hits',
			'status',
			'is_recommend',
			'type_id',
			'descrip',
			'star'
		));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_App::addApp($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'name',
			'link',
			'img',
			'sort',
			'hits',
			'status',
			'is_recommend',
			'type_id',
			'descrip',
			'star'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_App::updateApp($info, intval($info['id']));
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) {
			$this->output(-1, '名称不能为空.');
		}
		if (!$info['link']) {
			$this->output(-1, '链接地址不能为空.');
		}
		if (!$info['star']) {
			$this->output(-1, '星级请填写1-5之间的数字.');
		}
		if ($info['star'] > 5 || $info['star'] < 1) {
			$this->output(-1, '星级请填写1-5之间的数字.');
		}
		if (!$info['img']) {
			$this->output(-1, '图片不能为空.');
		}
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_App::getApp($id);
		if ($info && $info['id'] == 0) {
			$this->output(-1, '无法删除');
		}
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_App::deleteApp($id);
		if (!$result) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
