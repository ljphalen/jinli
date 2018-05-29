<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 升级峰值
 * Resource_UpgradeController
 * @author wupeng
 */
class Resource_UpgradeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Upgrade/index',
		'addUrl' => '/Admin/Resource_Upgrade/add',
		'editUrl' => '/Admin/Resource_Upgrade/edit',
		'addPostUrl' => '/Admin/Resource_Upgrade/addPost',
		'editPostUrl' => '/Admin/Resource_Upgrade/editPost',
		'deleteUrl' => '/Admin/Resource_Upgrade/delete',
		'batchUpdateUrl'=>'/Admin/Resource_Upgrade/batchUpdate'
	);
	
	public $perpage = 20;
	
	/**
	 * 列表界面
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('phone_ram_min', 'phone_ram_max', 'max_apk'));
		$searchParams = $this->getSearchParams($requestData);
		$sortParams = array('id' => 'DESC');

		list($total, $list) = Resource_Service_Upgrade::getPageList($page, $this->perpage, $searchParams, $sortParams);

		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 添加
	 */
	public function addAction() {
	    
	}
	
	/**
	 * 提交添加内容
	 */
	public function addPostAction() {
		$requestData = $this->getInput(array('phone_ram_min', 'phone_ram_max', 'max_apk'));
		$postData = $this->checkRequestData($requestData);
		$result = Resource_Service_Upgrade::addUpgrade($postData);
		if (!$result) $this->output(-1, '操作失败');
		Resource_Service_Upgrade::deleteUpgradeCacheKey();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
		$keys = $this->getInput(array('id'));
		$info = Resource_Service_Upgrade::getUpgrade($keys['id']);
		$this->assign('info', $info);
	}
	
	/**
	 * 提交编辑内容
	 */
	public function editPostAction() {
		$requestData = $this->getInput(array('id', 'phone_ram_min', 'phone_ram_max', 'max_apk'));
		
		$postData = $this->checkRequestData($requestData);
		$editInfo = Resource_Service_Upgrade::getUpgrade($requestData['id']);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');	

		$updateParams = $this->getUpdateParams($postData, $editInfo);
		if (count($updateParams) >0) {
			$ret = Resource_Service_Upgrade::updateUpgrade($updateParams, $requestData['id']);
			if (!$ret) $this->output(-1, '操作失败');
		}
		Resource_Service_Upgrade::deleteUpgradeCacheKey();
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 删除
	 */
	public function deleteAction() {
		$keys = $this->getInput(array('id'));
		$info = Resource_Service_Upgrade::getUpgrade($keys['id']);
		if (!$info) $this->output(-1, '无法删除');
		$result = Resource_Service_Upgrade::deleteUpgrade($keys['id']);
		if (!$result) $this->output(-1, '操作失败');
		Resource_Service_Upgrade::deleteUpgradeCacheKey();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 批量操作
	 */
	public function batchUpdateAction() {
		$requestData = $this->getPost(array('action', 'keys'));
		if (!count($requestData['keys'])) $this->output(-1, '没有可操作的项.');
		$keys = $requestData['keys'];

		if($requestData['action'] =='delete'){
			$ret = Resource_Service_Upgrade::deleteUpgradeList($keys);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		Resource_Service_Upgrade::deleteUpgradeCacheKey();
		$this->output('0', '操作成功.');
	}
	
	/**查询参数*/
	private function getSearchParams($search) {
	    $searchParams = array();
		if ($search['phone_ram_min']) $searchParams['phone_ram_min'] =  $search['phone_ram_min'];
		if ($search['phone_ram_max']) $searchParams['phone_ram_max'] =  $search['phone_ram_max'];
		if ($search['max_apk']) $searchParams['max_apk'] =  $search['max_apk'];
	    return $searchParams;
	}
	
	/**取需要更新的数据*/
	private function getUpdateParams($postData, $dbData) {
	    $updateParams = array();
	    foreach ($postData as $key => $value) {
	        if ($value == $dbData[$key])
	            continue;
	        $updateParams[$key] = $value;
	    }
	    return $updateParams;
	}
	
	/**
	 * 检查数据有效性
	 */
	private function checkRequestData($requestData) {
		if(!isset($requestData['phone_ram_min'])) $this->output(-1, '内存下限不能为空.');
		if(!isset($requestData['phone_ram_max'])) $this->output(-1, '内存上限不能为空.');
		if(!isset($requestData['max_apk'])) $this->output(-1, 'apk包峰值不能为空.');
		return $requestData;
	}

}
