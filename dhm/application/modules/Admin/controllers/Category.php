<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class CategoryController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Category/index',
		'addUrl' => '/Admin/Category/add',
		'addPostUrl' => '/Admin/Category/add_post',
		'editUrl' => '/Admin/Category/edit',
		'editPostUrl' => '/Admin/Category/edit_post',
		'deleteUrl' => '/Admin/Category/delete',
		'importUrl' => '/Admin/Category/import',
		'importPostUrl' => '/Admin/Category/import_post',
        'uploadUrl' => '/Admin/Category/upload',
        'uploadPostUrl' => '/Admin/Category/upload_post',
	);
	
	public $perpage = 20;
	public $roots;//根
	public $parents;

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		list(, $this->roots) = Dhm_Service_Category::getsBy(array('root_id'=>0, 'parent_id'=>0), array('sort'=>'DESC' ,'id'=>'DESC'));
		list(, $this->parents) =  Dhm_Service_Category::getsBy(array('root_id'=>array('!=', 0), 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
	    $roots = $this->roots;
	    $parents = $this->parents;
	    $id = $this->getInput('id');
	    if ($id) {
	        list(, $roots) = Dhm_Service_Category::getsBy(array('id'=>$id), array('id'=>'DESC'));
	        list(, $parents) = Dhm_Service_Category::getsBy(array('root_id'=>$id, 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
	    }
	    
	    foreach ($parents as $key=>$value) {
	        list(, $categorys) = Dhm_Service_Category::getsBy(array('parent_id'=>$value['id'], 'root_id'=>$value['root_id']), array('sort'=>'DESC', 'id'=>'DESC'));
	        $parents[$key]['areas'] = $categorys;
	    }
		$list = $this->_cookdata($roots, $parents);
		$this->assign('list', $list);
		$this->assign('categorys', $categorys);
		$this->assign('roots', $this->roots);
		$this->assign('id', $id);
		$this->cookieParams();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'parent_id', 'root_id', 'sort', 'image'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		
		//检测重复
		$area = Dhm_Service_Category::getBy(array('name'=>$info['name']));
		if ($area) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Dhm_Service_Category::add($info);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Dhm_Service_Category::get(intval($id)); 
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id','name', 'root_id', 'parent_id', 'sort', 'image'));
		
		//检测重复
		$area = Dhm_Service_Category::getBy(array('name'=>$info['name']));
		if ($area && $area['id'] != $info['id']) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Dhm_Service_Category::update($info, $info['id']);
		if (!$ret) {
		    $this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Category::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		
		//删除子分类
		$cates = Dhm_Service_Category::getSubCategory($info['id']);
		if($cates) {
		    $cates = Common::resetKey($cates, 'id');
		    $ids = array_keys($cates);
		    Dhm_Service_Category::deleteBy(array('id'=>array('IN', $ids)));
		}
		
		$ret = Dhm_Service_Category::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookdata($roots, $parent) {
		$tmp = Common::resetKey($roots, 'id');
		foreach ($parent as $key=>$value) {
			$tmp[$value['root_id']]['items'][] = $value;
		}
		return $tmp;
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function rootsAction() {
	    $list = Dhm_Service_Category::getsData(array('root_id'=>0, 'parent_id'=>0), array('id'=>'DESC'));
	    $data = array();
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function parentsAction() {
	    $root_id = $this->getInput('id');
	    $list = Dhm_Service_Category::getsData(array('root_id'=>$root_id, 'parent_id'=>0), array('id'=>'DESC'));
	    $data = array();
        $data[] = array('id'=>0,'name'=>"请选择");
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function areasAction() {
	    $parent_id = $this->getInput('id');
	    $list = Dhm_Service_Category::getsData(array('parent_id'=>$parent_id), array('id'=>'DESC'));
	    $data = array();
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
	
	/**
	 * 上传页面
	 */
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	/**
	 * 处理上传
	 */
	public function upload_postAction() {
	    $ret = Common::upload('img', 'category');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
}
