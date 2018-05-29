<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ProductController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Product/index',
		'addUrl'        => '/Admin/Product/add',
		'addPostUrl'    => '/Admin/Product/add_post',
		'editUrl'       => '/Admin/Product/edit',
		'editPostUrl'   => '/Admin/Product/edit_post',
		'deleteUrl'     => '/Admin/Product/delete',
		'deleteImgUrl'  => '/Admin/Product/delete_img',
		'uploadUrl'     => '/Admin/Product/upload',
		'uploadPostUrl' => '/Admin/Product/upload_post',
		'uploadImgUrl'  => '/Admin/Product/uploadImg',

	);
	public $perpage = 20;

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$param  = $this->getInput(array('series_id', 'model_id'));
		$search = array();
		if ($param['series_id']) $search['series_id'] = $param['series_id'];
		if ($param['model_id']) $search['model_id'] = $param['model_id'];

		list($total, $products) = Gionee_Service_Product::getList($page, $perpage, $search);
		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();

		$this->assign('series', $series);
		$this->assign('models', $models);
		$this->assign('seriess', Common::resetKey($series, 'id'));
		$this->assign('modelss', Common::resetKey($models, 'id'));
		$this->assign('products', $products);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function addAction() {
		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();
		//属性
		list(, $attributs) = Gionee_Service_ProductAttribute::getAllProductAttribute();
		$this->assign('series', $series);
		$this->assign('models', $models);
		$this->assign('attributes', $attributs);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function add_postAction() {
		$info = $this->getPost(array(
			'title',
			'price',
			'series_id',
			'model_id',
			'sort',
			'buy_url',
			'descrip',
			'attribute_id',
			'img'
		));
		$simg = $this->getPost('simg');
		$info = $this->_cookData($info);

		if (!$simg) $this->output('-1', '至少上传一张预览图片.');

		$product = Gionee_Service_Product::getProductByModelId($info['model_id']);
		if ($product) $this->output('-1', '该机型已添加，请不要重复添加.');

		$info['attribute_id'] = implode(',', $info['attribute_id']);
		$ret                  = Gionee_Service_Product::addProduct($info);
		if (!$ret) $this->output(-1, '操作失败.');

		$gimgs = array();
		foreach ($simg as $key => $value) {
			if ($value != '') {
				$gimgs[] = array('pid' => $ret, 'img' => $value);
			}
		}
		$ret = Gionee_Service_ProductImg::addProductImg($gimgs);
		if (!$ret) $this->output(-1, '-操作失败.');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Product::getProduct(intval($id));

		//机型 系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		list(, $models) = Gionee_Service_Models::getAllModels();

		//属性
		list(, $attributs) = Gionee_Service_ProductAttribute::getAllProductAttribute();

		list(, $pimgs) = Gionee_Service_ProductImg::getList(0, 10, array('pid' => intval($id)), array('id' => 'ASC'));
		$this->assign('pimgs', $pimgs);

		$this->assign('series', $series);
		$this->assign('models', $models);
		$this->assign('attributes', $attributs);
		$this->assign('info', $info);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function edit_postAction() {
		$info                 = $this->getPost(array(
			'id',
			'title',
			'price',
			'series_id',
			'model_id',
			'sort',
			'buy_url',
			'descrip',
			'attribute_id',
			'img'
		));
		$info                 = $this->_cookData($info);
		$info['attribute_id'] = implode(',', $info['attribute_id']);
		$ret                  = Gionee_Service_Product::updateProduct($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');

		//修改的图片
		$upImgs = $this->getPost('upImg');
		foreach ($upImgs as $key => $value) {
			if ($key && $value) {
				Gionee_Service_ProductImg::updateProductImg(array('img' => $value), $key);
			}
		}
		//新增加的图片
		$simg = $this->getPost('simg');
		if ($simg[0] != null) {
			$gimgs = array();
			foreach ($simg as $key => $value) {
				if ($value != '') {
					$gimgs[] = array('pid' => $info['id'], 'img' => $value);
				}
			}
			$ret = Gionee_Service_ProductImg::addProductImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Product::getProduct($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Gionee_Service_ProductImg::deleteByProductId($id);
		$ret = Gionee_Service_Product::deleteProduct($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '产品名称不能为空.');
		if (!$info['series_id']) $this->output(-1, '系列不能为空.');
		if (!$info['model_id']) $this->output(-1, '机型不能为空.');
		if (!$info['attribute_id']) $this->output(-1, '属性不能为空.');
		if (!$info['img']) $this->output(-1, '主图不能为空.');
		if ($info['buy_url'] && (strpos($info['buy_url'], 'http://') === false || !strpos($info['buy_url'], 'https://') === false)) {
			$this->output(-1, '购买地址不规范.');
		}
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
	 */
	public function uploadImgAction() {
		$ret        = Common::upload('imgFile', 'product');
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getImgPath() . $ret['data'])));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = $this->_upload('img');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *
	 * Enter description here ...
	 */

	private function _upload($name) {
		$img = $_FILES[$name];
		if ($img['error'] == 4) {
			exit(json_encode(array('error' => 1, 'message' => '请选择要上传的图片！')));
		}
		$allowType = array('jpg' => '', 'jpeg' => '', 'png' => '', 'gif' => '');
		$savePath  = BASE_PATH . 'data/attachs/product/' . date('Ym');
		$uploader  = new Util_Upload($allowType);
		if (!$ret = $uploader->upload('img', date('His'), $savePath)) {
			return Common::formatMsg(-1, '上传失败');
		}
		$url = '/product/' . date('Ym') . '/' . $ret['newName'];
		return Common::formatMsg(0, '', $url);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ProductImg::getProductImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_ProductImg::deleteProductImg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
