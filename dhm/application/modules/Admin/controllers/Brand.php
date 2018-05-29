<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class BrandController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Brand/index',
		'addUrl' => '/Admin/Brand/add',
		'addPostUrl' => '/Admin/Brand/add_post',
		'editUrl' => '/Admin/Brand/edit',
		'editPostUrl' => '/Admin/Brand/edit_post',
		'deleteUrl' => '/Admin/Brand/delete',
		'uploadUrl' => '/Admin/Brand/upload',
		'uploadPostUrl' => '/Admin/Brand/upload_post',
		'uploadImgUrl' => '/Admin/Brand/uploadImg',
		'deleteImgUrl' => '/Admin/Brand/deleteimg',
	);
	
	public $perpage = 20;
	public $categorys;//分类
	public $countrys;//国家
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
	    parent::init();
	    list(, $this->categorys) = Dhm_Service_Category::getsBy(array('root_id'=>0, 'parent_id'=>0), array('id'=>'DESC'));
	    list(, $this->countrys) =  Dhm_Service_Country::getAll();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$category_id = $this->getInput('category_id');
		$country_id = $this->getInput('country_id');
		$name = $this->getInput('name');
		$status = $this->getInput('status');
		
		if ($page < 1) $page = 1;
		if ($category_id) $search['category_id'] = $category_id;
		if ($country_id) $search['country_id'] = $country_id;
        if ($name) $search['name'] = array("LIKE", $name);
		if ($status != -1) $search['status'] = $status;
		
		list($total, $brands) = Dhm_Service_Brand::getList($page, $this->perpage, $search, array('sort'=>'DESC' ,'id'=>'DESC'));

        $search['name'] = $name;

		$this->assign('brands', $brands);
		$this->assign('categorys', Common::resetKey($this->categorys, 'id'));
		$this->assign('countrys', Common::resetKey($this->countrys, 'id'));
		$this->assign('search', $search);
		$this->cookieParams();
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
		$info = Dhm_Service_Brand::get(intval($id));
		
		$this->assign('categorys', $this->categorys);
		$this->assign('countrys', $this->countrys);
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('categorys', $this->categorys);
		$this->assign('countrys', $this->countrys);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name', 'sort', 'logo', 'image','brand_desc', 'is_top', 'status', 'category_id', 'country_id'));
		
		$info = $this->_cookData($info);
		$result = Dhm_Service_Brand::add($info);
		if (!$result) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','name', 'sort', 'logo', 'image','brand_desc', 'is_top', 'status', 'category_id', 'country_id'));
		$info = $this->_cookData($info);
		
		$ret = Dhm_Service_Brand::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if(!$info['logo'] && $info['is_top'] == 1) $this->output(-1, '品牌logo不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Brand::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['logo']);
		$result = Dhm_Service_Brand::delete($id);
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
    public function uploadImgAction(){
        $ret = Common::upload('imgFile', 'Brand');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot . '/attachs/' . $ret['data'])));
    }

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Brand');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
}
