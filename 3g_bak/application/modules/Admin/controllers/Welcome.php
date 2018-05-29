<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 打开欢迎图片
 * @author tiger
 */
class WelcomeController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Welcome/index',
		'addUrl'        => '/Admin/Welcome/add',
		'addPostUrl'    => '/Admin/Welcome/add_post',
		'editUrl'       => '/Admin/Welcome/edit',
		'editPostUrl'   => '/Admin/Welcome/edit_post',
		'deleteUrl'     => '/Admin/Welcome/delete',
		'uploadUrl'     => '/Admin/Welcome/upload',
		'uploadPostUrl' => '/Admin/Welcome/upload_post',
	);

	public function indexAction() {
		$list = Gionee_Service_Welcome::getsBy(array(),array('sort'=>'desc'));
		$this->assign('list', $list);
	}


	public function editAction() {
		if (!empty($_POST['name'])) {
			$info = $this->getPost(array(
				'id',
				'name',
				'img',
				'sort',
				'url',
				'ver',
				'text',
				'start_time',
				'end_time',
				'status'
			));
			$info = $this->_cookData($info);

			$info['start_time'] = strtotime($info['start_time']);
			$info['end_time']   = strtotime($info['end_time']);
			if (!empty($info['id'])) {
				$ret = Gionee_Service_Welcome::update($info, intval($info['id']));
			} else {
				$ret = Gionee_Service_Welcome::add($info);
			}

			if (!$ret) $this->output(-1, '操作失败');
			Admin_Service_Log::op($info);
			$this->updataVersion();
			$this->output(0, '操作成功.');
		}
		$id   = $this->getInput('id');
		$info = Gionee_Service_Welcome::get(intval($id));
		$this->assign('info', $info);
	}


	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '页面名称不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		//if (!$info['url']) $this->output(-1, '链接不能为空.');
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Welcome::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gionee_Service_Welcome::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		Admin_Service_Log::op($id);
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	private function updataVersion() {
		Gionee_Service_Config::setValue('welcome_version_3', Common::getTime());
		Gionee_Service_Config::setValue('welcome_version_4', Common::getTime());
	}
}