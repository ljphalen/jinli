<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Amigo商城退换货理由管理
 * @author huangsg
 *
 */
class Amigo_ReasonController extends Admin_BaseController {

	public $actions = array(
			'listUrl' => '/Admin/Amigo_Reason/index',
			'addUrl' => '/Admin/Amigo_Reason/add',
			'addPostUrl' => '/Admin/Amigo_Reason/add_post',
			'editUrl' => '/Admin/Amigo_Reason/edit',
			'editPostUrl' => '/Admin/Amigo_Reason/edit_post',
			'deleteUrl' => '/Admin/Amigo_Reason/delete',
	);
	
	public $perpage = 20;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		$type = intval($this->getInput('type'));
		if (!empty($type)) $search['type'] = $type;
		
		list($total, $list) = Amigo_Service_Reason::getList($page, $this->perpage, $search);
		$this->assign('list', $list);
		$url = $this->actions['listUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('type', $type);
		$this->cookieParams();
	}
	
	public function addAction(){
		
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('reason', 'sort', 'type', 'status'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Reason::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Reason::getOne($id);
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'reason', 'type', 'sort', 'status'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Reason::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Reason::getOne($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Amigo_Service_Reason::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['reason']) $this->output(-1, '原因内容不能为空.');
		return $info;
	}
}