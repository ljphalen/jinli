<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
// http://cps.dianping.com/orderQuery/jinlicps?startTime=20131201120000&endTime=20140107200000&status=4
// http://tcps.51ping.com/orderQuery/jinlicps?startTime=20131213120000&endTime=20140107200000&status=4

class PartnerapiController extends Admin_BaseController {
	
	public $actions = array(
			'indexUrl' => '/Admin/Partnerapi/index',
			'addUrl' => '/Admin/Partnerapi/add',
			'addPostUrl' => '/Admin/Partnerapi/add_post',
			'editUrl' => '/Admin/Partnerapi/edit',
			'editPostUrl' => '/Admin/Partnerapi/edit_post',
			'deleteUrl' => '/Admin/Partnerapi/delete',
			'uploadUrl' => '/Admin/Partnerapi/upload',
			'uploadPostUrl' => '/Admin/Partnerapi/upload_post',
			'uploadImgUrl' => '/Admin/Partnerapi/uploadImg',
				
			'cateUrl' => '/Admin/Partnerapi/category',
			'addcateUrl' => '/Admin/Partnerapi/addcate',
			'addcatePostUrl' => '/Admin/Partnerapi/addcate_post',
			'editcateUrl' => '/Admin/Partnerapi/editcate',
			'editcatePostUrl' => '/Admin/Partnerapi/editcate_post',
			'deletecateUrl' => '/Admin/Partnerapi/deletecate',
	);
	
	public $perpage = 20;
	
	public function indexAction(){
		$cate_id = $this->getInput('cate_id');
		$params['cate_id'] = $cate_id;
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Gou_Service_PartnerAPI::getAPIList($page, $this->perpage, $params);
		$this->assign('list', $list);
		$cateInfo = Gou_Service_PartnerAPI::getAPICateInfo($cate_id);
		$this->assign('cate_id', $cate_id);
		$this->assign('cate_title', $cateInfo['title']);
		$url = $this->actions['indexUrl'] .'/?cate_id='. $cate_id . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addAction(){
		$cate_id = $this->getInput('cate_id');
		$category = Gou_Service_PartnerAPI::getAPICate();
		$this->assign('cate_id', $cate_id);
		$this->assign('category', $category);
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'sign', 'cate_id', 'api_url', 'status', 'remark'));
		$info = $this->_checkData($info);
		$checkSign = Gou_Service_PartnerAPI::checkSign($info['sign'], 0);
		if (!empty($checkSign)){
			$this->output(-1, '标识串重复，请重新填写.');
		}
		
		if (strpos($info['api_url'], 'http://') > 0 || strpos($info['api_url'], 'http://') === false){
			$this->output(-1, '链接前请添加http://.');
		}
		
		$ret = Gou_Service_PartnerAPI::addAPI($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$cate_id = $this->getInput('cate_id');
		$id = $this->getInput('id');
		$category = Gou_Service_PartnerAPI::getAPICate();
		$info = Gou_Service_PartnerAPI::getAPIInfo($id);
		$this->assign('info', $info);
		$this->assign('cate_id', $cate_id);
		$this->assign('category', $category);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id','title', 'sign', 'cate_id', 'api_url', 'status', 'remark'));
		$info = $this->_checkData($info);
		$checkSign = Gou_Service_PartnerAPI::checkSign($info['sign'], $info['id']);
		if (!empty($checkSign)){
			$this->output(-1, '标识串重复，请重新填写.');
		}
		
		if (strpos($info['api_url'], 'http://') > 0 || strpos($info['api_url'], 'http://') === false){
			$this->output(-1, '链接前请添加http://.');
		}
		
		$ret = Gou_Service_PartnerAPI::updateAPI($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_PartnerAPI::getAPIInfo($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_PartnerAPI::deleteAPI($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function categoryAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		list($total, $list) = Gou_Service_PartnerAPI::getList($page, $this->perpage);
		$this->assign('list', $list);
		$url = $this->actions['cateUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	public function addcateAction(){
		$category = Gou_Service_PartnerAPI::getAPICate();
		$this->assign('category', $category);
	}
	
	public function addcate_postAction(){
		$info = $this->getPost(array('title', 'status'));
		$info = $this->_checkCateData($info);
		$ret = Gou_Service_PartnerAPI::addAPICate($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editcateAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_PartnerAPI::getAPICateInfo($id);
		$category = Gou_Service_PartnerAPI::getAPICate();
		$this->assign('category', $category);
		$this->assign('info', $info);
	}
	
	public function editcate_postAction(){
		$info = $this->getPost(array('id', 'title', 'status'));
		$info = $this->_checkCateData($info);
		$ret = Gou_Service_PartnerAPI::updateAPICate($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deletecateAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_PartnerAPI::getAPICateInfo($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gou_Service_PartnerAPI::deleteAPICate($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['sign'])  $this->output(-1, '标识串不能为空.');
		if (!$info['api_url'] || Util_String::strlen($info['api_url']) <= 7)
			$this->output(-1, 'API地址不能为空.');
		return $info;
	}
	
	private function _checkCateData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		return $info;
	}
}