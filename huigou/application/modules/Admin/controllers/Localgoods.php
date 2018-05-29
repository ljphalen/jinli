<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class LocalGoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/LocalGoods/index',
		'addUrl' => '/Admin/LocalGoods/add',
		'addPostUrl' => '/Admin/LocalGoods/add_post',
		'editUrl' => '/Admin/LocalGoods/edit',
		'editPostUrl' => '/Admin/LocalGoods/edit_post',
		'deleteUrl' => '/Admin/LocalGoods/delete',
		'uploadUrl' => '/Admin/LocalGoods/upload',
		'uploadPostUrl' => '/Admin/LocalGoods/upload_post',
		'uploadImgUrl' => '/Admin/LocalGoods/uploadImg',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list(, $suppliers)  = Gc_Service_Supplier::getAllSupplier();
		$suppliers = Common::resetKey($suppliers, 'id');
		$this->assign("suppliers", $suppliers);
		//get LocalGoods list
		list($total, $localgoods) = Gc_Service_LocalGoods::getList($page, $this->perpage, $search);
		$this->assign('localgoods', $localgoods);
		$this->assign('total', $total);
		//get pager
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		list(,$suppliers)  = Gc_Service_Supplier::getAllSupplierSort();
		$info = Gc_Service_LocalGoods::getLocalGoods(intval($id));
		$suppliers = Common::resetKey($suppliers, 'id');
		$this->assign('suppliers', $suppliers);
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
	   list($total,$suppliers)  = Gc_Service_Supplier::getAllSupplierSort();
       $this->assign('suppliers', $suppliers);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'img', 'gold_coin','silver_coin', 'price', 'supplier', 'start_time', 'end_time', 'iscash', 'stock_num', 'limit_num','purchase_num', 'is_new_user','isrecommend','status','descrip'));
		$info = $this->_cookData($info);
		$result = Gc_Service_LocalGoods::addLocalGoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'img', 'gold_coin','silver_coin', 'price', 'supplier', 'start_time', 'end_time', 'iscash', 'stock_num', 'limit_num','purchase_num', 'is_new_user','isrecommend','status','descrip'));
		$info = $this->_cookData($info);
		$ret = Gc_Service_LocalGoods::updateLocalGoods($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '商品名称不能为空.'); 
		if(!$info['img']) $this->output(-1, '商品图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		if ($info['iscash'] == 2 || $info['iscash'] == 1) {
			if (($info['price'] - $info['silver_coin']) <= 0) {
				$this->output(-1, '商品价格不能低于银币使用上限.');
			}
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_LocalGoods::getLocalGoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gc_Service_LocalGoods::deleteLocalGoods($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		$ret = Common::upload('imgFile', 'LocalGoods');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $webroot . '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'LocalGoods');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
