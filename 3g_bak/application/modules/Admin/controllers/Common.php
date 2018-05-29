<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CommonController extends Admin_BaseController {
	public $actions = array(
		'uploadUrl'     => '/Admin/Common/upload',
		'uploadPostUrl' => '/Admin/Common/upload_post',
	);


	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$imgId = $this->getPost('imgId');
		$ret   = Common::upload('img', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

}