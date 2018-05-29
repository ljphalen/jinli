<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 商品管理
 * @author huangsg
 *
 */
class Brand_GoodsController extends Admin_BaseController {
	public $actions = array(
		'indexUrl' => '/Admin/Brand_Goods/index',
		'addUrl' => '/Admin/Brand_Goods/add',
		'addPostUrl' => '/Admin/Brand_Goods/add_post',
		'editUrl' => '/Admin/Brand_Goods/edit',
		'editPostUrl' => '/Admin/Brand_Goods/edit_post',
		'deleteUrl' => '/Admin/Brand_Goods/delete',
		
		'step1Url' => '/Admin/Brand_Goods/add_step1',
		'step2Url' => '/Admin/Brand_Goods/add_step2',
			
		'uploadUrl' => '/Admin/Brand_Goods/upload',
		'uploadPostUrl' => '/Admin/Brand_Goods/upload_post',
		'uploadImgUrl' => '/Admin/Brand_Goods/uploadImg',
	);
	
	public $perpage = 20;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$search = array();
		$param = $this->getInput(array('brand_id', 'status', 'start_time', 'end_time', 'time_type'));
		if (!empty($param['brand_id'])) $search['brand_id'] = $param['brand_id'];
		if (!empty($param['status'])) $search['status'] = $param['status'];
		if (!empty($param['time_type'])){
			$timeColumn = $param['time_type'] == 1 ? 'start_time' : 'end_time';
			if ($param['start_time']) $search[$timeColumn] = array('>=', strtotime($param['start_time'] . ':00'));
			if ($param['end_time']) $search[$timeColumn] = array('<=', strtotime($param['end_time'] . ':59'));
			if ($param['start_time'] && $param['end_time']) {
				$search[$timeColumn] = array(
						array('>=', strtotime($param['start_time'] . ':00')),
						array('<=', strtotime($param['end_time'] . ':59'))
				);
			}
		}
		
		list($total, $list) = Gou_Service_BrandGoods::getList($page, $perpage, $search);
		$this->assign('list', $list);
		$this->assign('param', $param);
		
		$brand_list = Gou_Service_Brand::getAllBrand();
		$brand_Arr = array();
		foreach ($brand_list as $val){
			$brand_Arr[$val['id']] = $val['title'];
		}
		$this->assign('brand_arr', $brand_Arr);
		
		$this->assign('brand_id', $param['brand_id']);
		$url = $this->actions['indexUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		$Brand_id = $this->getInput('Brand_id');
		$this->assign('Brand_id', $Brand_id);
	}
	
	public function add_step1Action() {
		$brand_id = $this->getInput('brand_id');
		$page = intval($this->getInput('page'));
		$cid = intval($this->getInput('cid'));
		$keyword = $this->getInput('keyword');	
		if ($page < 1) $page = 1;

		$topApi  = new Api_Top_Service();
		
		$ret = $topApi->taobaoTbkItemsGet(array(
				'page_no'=>$page, 
				'page_size'=>$this->perpage, 
				'cid'=>$cid, 
				'keyword'=>$keyword, 
				'is_mobile'=>"true"));
	
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];

		//get taobao num_iids
		$num_iids = array();
		foreach($goods as $key=>$value) {
			$num_iids[] = $value['num_iid'];
		}
		
		//get info infos;
		if (count($num_iids)) {
			//$infos = $topApi->getItemInfos($num_iids);
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>implode(',',$num_iids)));
			if (isset($infos['num_iid'])) $infos = array($infos);
			$infos = Common::resetKey($infos, 'num_iid');
		}
		
		$this->assign('goods', $goods);
		$this->assign('infos', $infos);
		$this->assign('brand_id', $brand_id);
		
		$this->assign('cid', $cid);
		$this->assign('keyword', $keyword);
		
		$url = $this->actions['step1Url'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	* add Mallgoods page show
	*/
	public function add_step2Action() {
		$brand_id = $this->getInput('brand_id');
		$num_iid = $this->getInput('num_iid');
		
		$brand_list = Gou_Service_Brand::getAllBrand();
		$this->assign('brand_list', $brand_list);
		
		//检查商品是否已经添加过
		$isExist = Gou_Service_BrandGoods::checkBrandGoodsByNumiid($num_iid);
		$this->assign('isExist', $isExist);
		
		$topApi  = new Api_Top_Service();
		$info = $topApi->getTbkItemInfo(array('num_iids'=>$num_iid));
		$this->assign('info', $info);
		$this->assign('brand_id', $brand_id);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'num_iid', 'sort', 'status', 'img', 'brand_id', 
					'start_time', 'end_time'));
		$info = $this->_checkData($info);
		$ret = Gou_Service_BrandGoods::addBrandgoods($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_BrandGoods::getBrandgoods($id);
		$this->assign('info', $info);
		
		$brand_list = Gou_Service_Brand::getAllBrand();
		$this->assign('brand_list', $brand_list);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'num_iid', 'sort', 'status', 'img', 'brand_id',
				'start_time', 'end_time'));
		$info = $this->_checkData($info);
		$ret = Gou_Service_BrandGoods::updateBrandgoods($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_BrandGoods::getBrandgoods($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_BrandGoods::deleteBrandgoods($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['brand_id']) $this->output(-1, '请选择对应的品牌.');
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['num_iid']) $this->output(-1, '商品淘宝ID不能为空，请返回重新操作.');
		if (!$info['img']) $this->output(-1, '图标不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strtotime($info['start_time']) > strtotime($info['end_time']))
			$this->output(-1, '开始时间不能晚于结束时间.');
		if (!$info['img']) $this->output(-1, '图标不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
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
		$ret = Common::upload('imgFile', 'Gou_Brand');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Gou_Brand');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}