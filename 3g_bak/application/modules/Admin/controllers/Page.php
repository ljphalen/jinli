<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiger
 *
 */
class PageController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Page/index',
		'addUrl'        => '/Admin/Page/add',
		'addPostUrl'    => '/Admin/Page/add_post',
		'editUrl'       => '/Admin/Page/edit',
		'editPostUrl'   => '/Admin/Page/edit_post',
		'deleteUrl'     => '/Admin/Page/delete',
		'uploadUrl'     => '/Admin/Page/upload',
		'uploadPostUrl' => '/Admin/Page/upload_post',
	);

	public $types = array(1 => 'webview', 2 => 'bookmark');

	public function indexAction() {
		$pages = Gionee_Service_Page::getAll(array('sort' => 'ASC', 'id' => 'ASC'));

		$this->assign('types', $this->types);
		$this->assign('pages', $pages);
	}


	public function addAction() {
		$this->assign('types', $this->types);
	}

	public function add_postAction() {
		$info   = $this->getPost(array('name', 'page_type', 'url', 'url_package', 'sort', 'is_default'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_Page::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Page::get(intval($id));
		$this->assign('info', $info);
		$this->assign('types', $this->types);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'page_type', 'url', 'url_package', 'sort', 'is_default'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Page::update($info, intval($info['id']));
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) {
			$this->output(-1, '页面名称不能为空.');
		}
		if ($info['page_type'] == 1) {
			if (!$info['url']) {
				$this->output(-1, '页面地址不能为空.');
			}
			if (strpos($info['url'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '页面地址不规范.');
			}
		}
		if ($info['sort'] === '') {
			$this->output(-1, '排序不能为空');
		}
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Page::get($id);
		if ($info && $info['id'] == 0) {
			$this->output(-1, '无法删除');
		}
		$result = Gionee_Service_Page::delete($id);
		if (!$result) {
			$this->output(-1, '操作失败');
		}
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'Page');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * update_version
	 */
	private function updataVersion() {
		Gionee_Service_Config::setValue('page_version', Common::getTime());
	}
}