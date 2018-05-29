<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class MallController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Mall/index',
		'addUrl' => '/Admin/Mall/add',
		'addPostUrl' => '/Admin/Mall/add_post',
		'editUrl' => '/Admin/Mall/edit',
		'editPostUrl' => '/Admin/Mall/edit_post',
		'deleteUrl' => '/Admin/Mall/delete',
		'uploadUrl' => '/Admin/Mall/upload',
		'uploadPostUrl' => '/Admin/Mall/upload_post',
		'uploadImgUrl' => '/Admin/Mall/uploadImg',
		'deleteImgUrl' => '/Admin/Mall/deleteimg',
	);
	
	public $perpage = 20;
	public $types = array(1=>'线上', 2=>'线下');//分类
	public $countrys;//国家
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
	    parent::init();
	    list(, $this->countrys) =  Dhm_Service_Country::getAll();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$type_id = $this->getInput('type_id');
		$country_id = $this->getInput('country_id');
		$name = $this->getInput('name');
		$status = $this->getInput('status');
		
		if ($page < 1) $page = 1;
		if ($type_id) $search['type_id'] = $type_id;
		if ($country_id) $search['country_id'] = $country_id;
		if ($name) $search['name'] = array("LIKE",$name);
		if ($status) $search['status'] = $status;
		
		list($total, $malls) = Dhm_Service_Mall::getList($page, $this->perpage, $search, array('id'=>'DESC'));

        $search['name'] = $name;

        $this->assign('malls', $malls);
		$this->assign('types', $this->types);
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
		$info = Dhm_Service_Mall::get(intval($id));
		
		$this->assign('types', $this->types);
		$this->assign('countrys', $this->countrys);
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('types', $this->types);
		$this->assign('countrys', $this->countrys);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
        $info = $this->getPost(array('name', 'sort', 'logo', 'link', 'search_url', 'descript', 'is_top', 'status', 'type_id', 'country_id'));
		
		$info = $this->_cookData($info);
		$result = Dhm_Service_Mall::add($info);
		if (!$result) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
        $info = $this->getPost(array('id', 'name', 'sort', 'logo', 'link', 'search_url', 'descript', 'is_top', 'status', 'type_id', 'country_id'));
		$info = $this->_cookData($info);
		
		$ret = Dhm_Service_Mall::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		if(!$info['logo']) $this->output(-1, '品牌logo不能为空.');
        $info['search_url']=html_entity_decode($info['search_url']);
        $info['link']=html_entity_decode($info['link']);
        $info['logo']=html_entity_decode($info['logo']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Mall::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['logo']);
		$result = Dhm_Service_Mall::delete($id);
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
		$ret = Common::upload('imgFile', 'Mall');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'mall');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
}
