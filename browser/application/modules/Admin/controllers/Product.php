<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ProductController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Product/index',
		'addUrl' => '/Admin/Product/add',
		'addPostUrl' => '/Admin/Product/add_post',
		'editUrl' => '/Admin/Product/edit',
		'editPostUrl' => '/Admin/Product/edit_post',
		'deleteUrl' => '/Admin/Product/delete',
		'manageUrl' => '/Admin/Product/manage',
		'uploadUrl' => '/Admin/Ad/upload',
		'uploadPostUrl' => '/Admin/Ad/upload_post',

	);
	public $perpage = 20;
	public $types = array( 
                        '天鉴', 
                        'e-life', 
                        '语音王', 
                        '普及');
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $products) = Browser_Service_Product::getList($page, $perpage);
		
		$this->assign('products', $products);
	    $this->assign('types', $this->types);	
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function addAction(){
	        $this->assign('types', $this->types);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function add_postAction(){
		$info = $this->getPost(array('title', 'price', 'type', 'mark', 'sort', 'param', 'descrip', 'is_new'));
		if (!$info['mark']) $this->output(-1, '机型不能为空.');
		$ret = Browser_Service_Product::addProduct($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Browser_Service_Product::getProduct(intval($id)); 
	        $this->assign('types', $this->types);	
	        $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('title', 'price', 'type', 'mark', 'sort', 'param', 'descrip', 'id', 'is_new'));
		if (!$info['mark']) $this->output(-1, '机型不能为空.');
		$ret = Browser_Service_Product::updateProduct($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */


	public function manageAction() {
		$series = $this->getInput('series');	
		$mark = $this->getInput('mark');	
		$files = Browser_Service_Product::getMarks($series, $mark);	
		print_r($files);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Product::getProduct($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$ret = Browser_Service_Product::deleteProduct($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = $this->_upload('img');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('ad/upload.phtml');
		exit;
        }

	/**
	 * 
	 * Enter description here ...
	 */

	private function _upload($name) {
		$img = $_FILES[$name]; 
		if ($img['error'] == 4) {
			exit(json_encode(array('error' => 1,'message' => '请选择要上传的图片！')));
		}
		$allowType = array('jpg' => '','jpeg' => '','png' => '','gif' => '');
		$savePath = BASE_PATH . 'data/attachs/product/' . date('Ym'); 
		$uploader = new Util_Upload($allowType);
		if (!$ret = $uploader->upload('img', date('His'), $savePath)) {
			return Common::formatMsg(-1, '上传失败');
		}
		$url = '/product/'.date('Ym') .'/'. $ret['newName'];
		return Common::formatMsg(0, '', $url);
	}
}
