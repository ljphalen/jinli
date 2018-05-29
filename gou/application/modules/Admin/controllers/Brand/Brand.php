<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 品牌
 * @author huangsg
 *
 */
class Brand_BrandController extends Admin_BaseController {
	public $actions = array(
			'indexUrl' => '/Admin/Brand_Brand/index',
			'addUrl' => '/Admin/Brand_Brand/add',
			'addPostUrl' => '/Admin/Brand_Brand/add_post',
			'editUrl' => '/Admin/Brand_Brand/edit',
			'editPostUrl' => '/Admin/Brand_Brand/edit_post',
			'deleteUrl' => '/Admin/Brand_Brand/delete',
				
			'goodsUrl'=>'/Admin/Brand_Goods/index',

			'uploadUrl' => '/Admin/Brand_Brand/upload',
			'uploadPostUrl' => '/Admin/Brand_Brand/upload_post',
			'uploadImgUrl' => '/Admin/Brand_Brand/uploadImg',
	);
	
	public $perpage = 20;
	public $appCacheName = array('APPC_Api_Brand');
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
	
		$search = array();
		$params = $this->getInput(array('title', 'cate_id', 'status', 'start_time', 'end_time', 'time_type', 'is_top'));
		if ($params['title'])  $search['title'] = array('LIKE',$params['title']);
		if ($params['status']) $search['status'] = $params['status'];
		if ($params['is_top']) $search['is_top'] = $params['is_top'];
		if (!empty($params['time_type'])){
			$timeColumn = $params['time_type'] == 1 ? 'start_time' : 'end_time';
			if ($params['start_time']) $search[$timeColumn] = array('>=', strtotime($params['start_time'] . ':00'));
			if ($params['end_time']) $search[$timeColumn] = array('<=', strtotime($params['end_time'] . ':59'));
			if ($params['start_time'] && $params['end_time']) {
				$search[$timeColumn] = array(
					array('>=', strtotime($params['start_time'] . ':00')),
					array('<=', strtotime($params['end_time'] . ':59'))
				);
			}
		}
		
		if (!empty($params['cate_id'])){
			$search['cate_id'] = $params['cate_id'];
			$brandList = Gou_Service_Brand::getBrandByCateId($params['cate_id']);
			$brandList = Common::resetKey($brandList, 'brand_id');
			$brand_ids = array_keys($brandList);
			if (!empty($brand_ids)){
				$search['id'] = array('IN', $brand_ids);
			}else{
				$search['id'] = 0;
			}
		}
		
		list($total, $list) = Gou_Service_Brand::getList($page, $perpage, $search);
		
		$categoryList = Gou_Service_BrandCate::getAllCategory();
		$category = array();
		if (!empty($categoryList)){
			foreach ($categoryList as $val){
				$category[$val['id']] = $val['title'];
			}
		}
		
		foreach ($list as $key=>$val){
			$cates = '';
			$cateArr = Gou_Service_Brand::getBrandCate($val['id']);
			foreach ($cateArr as $v){
				$cates .= $category[$v['cate_id']] . ', ';
			}
			$list[$key]['cate'] = $cates;
		}
		
		$this->assign('category', $category);
		
		$this->assign('list', $list);
		$this->assign('param', $params);
		//$this->assign('cate_id', $params['cate_id']);
		
		$url = $this->actions['indexUrl'].'/?' . http_build_query($search) . '&' . $condition;
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		$category = Gou_Service_BrandCate::getAllCategory();
		$this->assign('category', $category);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'cate_id', 'banner_img', 'brand_img',
				'logo_img', 'start_time', 'end_time', 'status', 'sort', 'is_top', 'brand_desc'));
		$info = $this->_checkData($info);
		$exist = Gou_Service_Brand::getBy(array('title'=>$info['title']));
		if(!empty($exist)) $this->output(-1, '操作失败,该品牌已经添加.');
		$ret = Gou_Service_Brand::addBrand($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$category = Gou_Service_BrandCate::getAllCategory();
		$info = Gou_Service_Brand::getBrand($id);
		$cateArr = Gou_Service_Brand::getBrandCate($id);
		$cates = array();
		foreach ($cateArr as $val){
			$cates[] = $val['cate_id'];
		}
		
		$this->assign('info', $info);
		$this->assign('cates', $cates);
		$this->assign('category', $category);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title', 'cate_id', 'banner_img', 'brand_img',
				'logo_img', 'start_time', 'end_time', 'status', 'sort', 'is_top', 'brand_desc'));
		$info = $this->_checkData($info);
		$ret = Gou_Service_Brand::updateBrand($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_Brand::getBrand($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_Brand::deleteBrand($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (empty($info['cate_id']))  $this->output(-1, '分类不能为空.');
		if ($info['is_top'] == 1 && empty($info['banner_img'])) $this->output(-1, '推荐品牌Banner图不能为空.');
		if (!$info['brand_desc']) $this->output(-1, '品牌描述不能为空.');
		if (!$info['brand_img']) $this->output(-1, '品牌列表图不能为空.');
		if (!$info['logo_img']) $this->output(-1, 'logo不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strtotime($info['start_time']) > strtotime($info['end_time']))
			$this->output(-1, '开始时间不能晚于结束时间.');
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
		$ret = Common::upload('imgFile', 'gou_brand');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'gou_brand');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	
}