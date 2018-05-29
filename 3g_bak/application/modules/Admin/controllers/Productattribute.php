<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ProductattributeController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Productattribute/index',
		'addUrl'        => '/Admin/Productattribute/add',
		'addPostUrl'    => '/Admin/Productattribute/add_post',
		'editUrl'       => '/Admin/Productattribute/edit',
		'editPostUrl'   => '/Admin/Productattribute/edit_post',
		'deleteUrl'     => '/Admin/Productattribute/delete',
		'deleteImgUrl'  => '/Admin/Productattribute/delete_img',
		'uploadUrl'     => '/Admin/Productattribute/upload',
		'uploadPostUrl' => '/Admin/Productattribute/upload_post',
		'uploadImgUrl'  => '/Admin/Productattribute/uploadImg',

	);
	public $perpage = 20;

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $attributes) = Gionee_Service_ProductAttribute::getList($page, $perpage, $search);

		$this->assign('attributes', $attributes);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function addAction() {
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function add_postAction() {
		$info = $this->getPost(array('title', 'icon_url'));
		$info = $this->_cookData($info);

		$ret = Gionee_Service_ProductAttribute::addProductAttribute($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ProductAttribute::getProductAttribute(intval($id));
		$this->assign('info', $info);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'title', 'icon_url'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_ProductAttribute::updateProductAttribute($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ProductAttribute::getProductAttribute($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$ret = Gionee_Service_ProductAttribute::deleteProductAttribute($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '属性名称不能为空.');
		if (!$info['icon_url']) $this->output(-1, '属性图片不能为空.');
		return $info;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = Common::upload('img', 'attribute');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}