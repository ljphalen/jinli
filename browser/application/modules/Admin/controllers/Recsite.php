<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RecsiteController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Recsite/index',
		'addUrl' => '/Admin/Recsite/add',
		'addPostUrl' => '/Admin/Recsite/add_post',
		'editUrl' => '/Admin/Recsite/edit',
		'editPostUrl' => '/Admin/Recsite/edit_post',
		'deleteUrl' => '/Admin/Recsite/delete',
		'uploadUrl' => '/Admin/Recsite/upload',
		'uploadPostUrl' => '/Admin/Recsite/upload_post',
	);
	
	public $perpage = 20;
	public $models;//机型
	public $types;//分类
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		list(,$this->models) = Browser_Service_Models::getAllModels();
		list(,$this->types) = Browser_Service_RecsiteType::getAllRecsiteType();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param = $this->getInput(array('model_id', 'type_id'));
		$search = array();
		if ($param['model_id'] != '') $search['model_id'] = $param['model_id'];
		if ($param['type_id'] != '') $search['type_id'] = $param['type_id'];
		list($total, $recmarks) = Browser_Service_Recsite::getList($page, $perpage, $search);
		$this->assign('recmarks', $recmarks);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
		$this->assign('models', Common::resetKey($this->models, 'id'));
		$this->assign('types', Common::resetKey($this->types, 'id'));
		
		$this->assign('param', $param);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Recsite::getRecsite(intval($id));		
		$this->assign('info', $info);
		$this->assign('models', $this->models);
		$this->assign('types', $this->types);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('models', $this->models);
		$this->assign('types', $this->types);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name', 'link', 'img', 'sort', 'status', 'model_id','type_id'));
		$info = $this->_cookData($info);
		$result = Browser_Service_Recsite::addRecsite($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'link', 'img', 'sort', 'status', 'model_id','type_id'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_Recsite::updateRecsite($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.'); 
		if(!$info['link']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Recsite::getRecsite($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Browser_Service_Recsite::deleteRecsite($id);
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
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Recsite');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }    
}
