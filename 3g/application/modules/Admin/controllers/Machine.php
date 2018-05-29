<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 功能机管理
 * @author tiger
 *
 */
class MachineController extends Admin_BaseController {

	public $actions = array(
		'editPostUrl'  => '/admin/Machine/edit_post',
		'uploadImgUrl' => '/Admin/Machine/uploadImg',
	);

	public function indexAction() {
		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('gionee_machine', $configs['gionee_machine']);
	}

	public function edit_postAction() {
		$gionee_machine = $this->getInput('gionee_machine');
		Gionee_Service_Config::setValue('gionee_machine', $gionee_machine);
		$this->output(0, '操作成功.');
	}


	public function uploadImgAction() {
		$ret        = Common::upload('imgFile', 'nav');
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getImgPath() . $ret['data'])));
	}
}