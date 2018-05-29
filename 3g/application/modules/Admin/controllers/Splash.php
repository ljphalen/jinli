<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 闪屏管理
 *
 */
class SplashController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Splash/index',
		'addUrl'        => '/Admin/Splash/add',
		'addPostUrl'    => '/Admin/Splash/add_post',
		'editUrl'       => '/Admin/Splash/edit',
		'editPostUrl'   => '/Admin/Splash/edit_post',
		'deleteUrl'     => '/Admin/Splash/delete',
		'uploadUrl'     => '/Admin/Splash/upload',
		'uploadPostUrl' => '/Admin/Splash/upload_post',
	);

	public $perpage = 20;


	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $splashs) = Gionee_Service_Splash::getList($page, $perpage);

		$this->assign('splashs', $splashs);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}


	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Splash::getSplash(intval($id));
		$this->assign('info', $info);
	}


	public function addAction() {
	}


	public function add_postAction() {
		Admin_Service_Access::pass('add');
		$info   = $this->getPost(array('title', 'img_url', 'version', 'start_time', 'end_time', 'status'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_Splash::addSplash($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功');
	}


	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array('id', 'title', 'img_url', 'version', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Splash::updateSplash($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}


	private function _cookData($info) {
		if (!$info['title']) $this->output(-1, '闪屏标题不能为空.');
		if (!$info['img_url']) $this->output(-1, '闪屏图片不能为空.');
		if (!$info['version']) $this->output(-1, '闪屏版本不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']);
		if ($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		return $info;
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id   = $this->getInput('id');
		$info = Gionee_Service_Splash::getSplash($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_Splash::deleteSplash($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->updataVersion();
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
		$ret   = Common::upload('img', 'splash');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * update_version
	 */
	private function updataVersion() {
		Gionee_Service_Config::setValue('splash_version', Common::getTime());
	}
}
