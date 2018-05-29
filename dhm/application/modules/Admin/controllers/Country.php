<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class CountryController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Country/index',
		'addUrl' => '/Admin/Country/add',
		'addPostUrl' => '/Admin/Country/add_post',
		'editUrl' => '/Admin/Country/edit',
		'editPostUrl' => '/Admin/Country/edit_post',
		'deleteUrl' => '/Admin/Country/delete',
		'uploadUrl' => '/Admin/Country/upload',
		'uploadPostUrl' => '/Admin/Country/upload_post',
	);
	
	public $perpage = 20;

	/**
	 * 国家列表
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
        $param = $this->getInput(array("name", "currency", "lang_code"));
        if ($param['name'])      $search['name']      = array("LIKE", $param['name']);
        if ($param['currency'])  $search['currency']  = array("LIKE", $param['currency']);
        if ($param['lang_code']) $search['lang_code'] = array("LIKE", $param['lang_code']);
		list(,$countries)    = Dhm_Service_Country::getAll();
		list($total, $data) = Dhm_Service_Country::getList($page, $this->perpage,$search);
		$this->assign('data', $data);
		$this->assign('countries', $countries);
		$this->assign('search', $param);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url = $this->actions['listUrl'].'?'));
	}

	/**
	 * 添加国家
	 */
	public function addAction() {
	}

	/**
	 * 处理添加
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name', 'currency', 'lang_code', 'logo'));
		$info = $this->_cookData($info);
		$result = Dhm_Service_Country::addCountry($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 编辑国家
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Country::getCountry(intval($id));
		$this->assign('info', $info);
	}


	/**
	 * 处理编辑
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','name', 'currency', 'lang_code', 'logo'));
		$info = $this->_cookData($info);
		$ret = Dhm_Service_Country::updateCountry($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	/**
	 * 删除国家
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Country::getCountry($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
//		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Dhm_Service_Country::deleteCountry($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name'])     $this->output(-1, '名称不能为空.');
		if(!$info['currency']) $this->output(-1, '币种也得填写.');
		if(!$info['logo'])     $this->output(-1, '国旗不能为空.');
        $info['currency'] =strtoupper($info['currency']);
		return $info;
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
		$ret = Common::upload('img', 'country');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
