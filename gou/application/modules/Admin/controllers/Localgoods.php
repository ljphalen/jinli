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
		'deleteImgUrl' => '/Admin/LocalGoods/deleteimg',
	);
	
	// 0.Amigo商城使用，1.积分换购使用
	public $show_type = 0;
	public $perpage = 20;
	
	//商品类型
	public $goods_types =  array(
			2=>'实物商品',
			1=>'话费',
			3=>'阅读币'
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$status = $this->getInput('status');
		$doing = $this->getInput('doing');
		$goods_type = $this->getInput('goods_type');
		$title = $this->getInput('title');
		
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
		
		if ($page < 1) $page = 1;
		//list(, $suppliers)  = Gou_Service_Supplier::getAllSupplier();
		$suppliers = Gou_Service_Supplier::getListBy(array('show_type'=>$show_type));
		$suppliers = Common::resetKey($suppliers, 'id');
		$this->assign("suppliers", $suppliers);
		if ($status) $search['status'] = $status - 1;
		if ($goods_type) $search['goods_type'] = $goods_type;
		if ($doing) $search['doing'] = $doing;
		if ($title) $search['title'] = array('LIKE',$title);
		$search['show_type'] = $show_type;
		
		list($total, $localgoods) = Gou_Service_LocalGoods::search($page, 
				$this->perpage, $search, array('sort'=>'DESC','id'=>'DESC'));
        $search['title'] = $title;
		$this->assign('localgoods', $localgoods);
		$this->assign('total', $total);
		$this->assign('status', $status);
		$this->assign('doing', $doing);
		$this->assign('goods_types', $this->goods_types);
		$this->assign('search', $search);
		$this->assign('show_type', $show_type);
		
		//get pager
		$url = $this->actions['listUrl'] .'/?show_type=' . $show_type . '&' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
		
		$id = $this->getInput('id');
		//list(,$suppliers)  = Gou_Service_Supplier::getAllSupplierSort();
		$suppliers = Gou_Service_Supplier::getListBy(array('show_type'=>$show_type));
		$info = Gou_Service_LocalGoods::getLocalGoods(intval($id));
		$suppliers = Common::resetKey($suppliers, 'id');
		
		list(,$goods_imgs) = Gou_Service_LocalGoodsImg::getList(0, 10, array('goods_id'=>intval($id)), array('id'=>'ASC'));
		$this->assign('goods_imgs', $goods_imgs);
		
		$this->assign('suppliers', $suppliers);
		$this->assign('info', $info);
		$this->assign('goods_types', $this->goods_types);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$show_type = intval($this->getInput('show_type'));
		$show_type = !empty($show_type) ? $show_type : $this->show_type;
		//list($total,$suppliers)  = Gou_Service_Supplier::getAllSupplierSort();	
		$suppliers = Gou_Service_Supplier::getListBy(array('show_type'=>$show_type));
		$this->assign('suppliers', $suppliers);
		$this->assign('goods_types', $this->goods_types);
		$this->assign('show_type', $show_type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'img', 'gold_coin',
				'silver_coin', 'price', 'supplier', 'start_time', 'end_time', 
				'iscash', 'stock_num', 'limit_num','purchase_num', 'show_type',
				'is_new_user','isrecommend','status','descrip', 'goods_type'));
		
		$simg = $this->getPost('simg');
		if (!$simg) $this->output('-1', '至少上传一张预览图片.');
		
		$info = $this->_cookData($info);
		$result = Gou_Service_LocalGoods::addLocalGoods($info);
		if (!$result) $this->output(-1, '操作失败');
		
		//images
		$gimgs = array();
		foreach($simg as $key=>$value) {
			if ($value != '') {
				$gimgs[] = array('goods_id'=>$result, 'img'=>$value);
			}
		}
		$ret = Gou_Service_LocalGoodsImg::addGoodsImg($gimgs);
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'img', 'gold_coin',
				'silver_coin', 'price', 'supplier', 'start_time', 
				'end_time', 'iscash', 'stock_num', 'limit_num',
				'purchase_num', 'is_new_user','isrecommend','show_type',
				'status','descrip', 'goods_type'));
		$info = $this->_cookData($info);
		
		$simg = $this->getPost('simg');
		if (!$simg) $this->output('-1', '至少上传一张预览图片.');
		
		$ret = Gou_Service_LocalGoods::updateLocalGoods($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		//修改的图片
		$upImgs = $this->getPost('upImg');
		foreach($upImgs as $key=>$value) {
			if ($key && $value) {
				Gou_Service_LocalGoodsImg::updateGoodsImg(array('img'=>$value), $key);
			}
		}
		//新增加的图片
		$simg = $this->getPost('simg');
		if ($simg[0] != null) {
			$gimgs = array();
			foreach($simg as $key=>$value) {
				if ($value != '') {
					$gimgs[] = array('goods_id'=>$info['id'], 'img'=>$value);
				}
			}
			$ret = Gou_Service_LocalGoodsImg::addGoodsImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '商品名称不能为空.');
		if (Util_String::strlen($info['title']) > 16) $this->output('-1', '商品标题不能超过16个字.');
		
		if(!$info['img']) $this->output(-1, '商品图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		if ($info['iscash'] == 2 || $info['iscash'] == 1) {
			if (($info['price'] - $info['silver_coin']) < 0) {
				$this->output(-1, '商品价格不能低于银币使用上限.');
			}
		}
		$info['descrip'] = $this->updateImgUrl($info['descrip']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_LocalGoods::getLocalGoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		Gou_Service_LocalGoodsImg::deleteByGoodsId($id);
		$result = Gou_Service_LocalGoods::deleteLocalGoods($id);
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
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
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
    
    /**
     *
     * Enter description here ...
     */
    public function deleteimgAction() {
    	$id = $this->getInput('id');
    	$info = Gou_Service_LocalGoodsImg::getGoodsImg($id);
    	if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
    	Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
    	$result = Gou_Service_LocalGoodsImg::deleteGoodsImg($id);
    	if (!$result) $this->output(-1, '操作失败');
    	$this->output(0, '操作成功');
    }
}
