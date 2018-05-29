<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class RecurlController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Recurl/index',
		'addUrl' => '/Admin/Recurl/add',
		'addPostUrl' => '/Admin/Recurl/add_post',
		'editUrl' => '/Admin/Recurl/edit',
		'editPostUrl' => '/Admin/Recurl/edit_post',
		'deleteUrl' => '/Admin/Recurl/delete',
		'uploadUrl' => '/Admin/Recurl/upload',
		'uploadPostUrl' => '/Admin/Recurl/upload_post',
	);
	
	public $perpage = 20;
	public $models;//机型
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		list(,$this->models) = Browser_Service_Models::getAllModels();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param = $this->getInput(array('model_id'));
		if ($param['model_id']) $search['model_id'] = $param['model_id'];
		
		list($total, $recurls) = Browser_Service_Recurl::getList($page, $perpage, $search);
		$this->assign('recurls', $recurls);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
		$this->assign('models', Common::resetKey($this->models, 'id'));
		$this->assign('param', $param);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Browser_Service_Recurl::getRecurl(intval($id));		
		$this->assign('info', $info);
		$this->assign('models', $this->models);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('models', $this->models);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name', 'link', 'img', 'sort', 'status', 'model_id'));
		$info = $this->_cookData($info);
		$result = Browser_Service_Recurl::addRecurl($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'link', 'img', 'sort', 'status', 'model_id'));
		$info = $this->_cookData($info);
		$ret = Browser_Service_Recurl::updateRecurl($info, intval($info['id']));
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
		$info = Browser_Service_Recurl::getRecurl($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Browser_Service_Recurl::deleteRecurl($id);
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
		$ret = Common::upload('img', 'Recurl');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }    
}
