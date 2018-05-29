<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class W3_CpController extends Admin_BaseController {

	public $actions = array(
		'setUrl'        => '/Admin/W3_Cp/setting',
		'resUrl'        => '/Admin/W3_Cp/res',
		'uploadUrl'     => '/Admin/Widget_Down/upload',
		'uploadPostUrl' => '/Admin/Widget_Down/upload_post',
	);

	/**
	 *
	 */
	public function settingAction() {
		$cpId = intval($this->getInput('cp_id'));
		$cpId = !empty($cpId) ? $cpId : 1;
		if (!empty($_POST['jmp_text'])) {
			$row = W3_Service_Cp::get($_POST['id']);
			if (empty($row['id'])) {
				$ret = W3_Service_Cp::add($_POST);
			} else {
				$ret = W3_Service_Cp::set($_POST, $_POST['id']);
			}
			$this->output(0, '更新成功.');
		}

		$info        = W3_Service_Cp::get($cpId);
		$info['pic'] = json_decode($info['pic'], true);
		$cps         = Widget_Service_Cp::$CpCate;
		unset($cps[1]);
		$this->assign('info', $info);
		$this->assign('cpId', $cpId);
		$this->assign('cps', $cps);

	}

	/**



	public function resAction() {
		$cpId = intval($this->getInput('cp_id'));
		$cpId = !empty($cpId) ? $cpId : 1;
		if (!empty($_POST['name'])) {
			$row = W3_Service_Cp::get($_POST['id']);

			$tmpPic = array();
			foreach ($_POST['simg'] as $pic) {
				if ($pic) {
					$tmpPic[] = $pic;
				}
			}
			$_POST['pic'] = json_encode($tmpPic);

			if (empty($row['id'])) {
				$ret = W3_Service_Cp::add($_POST);
			} else {
				$ret = W3_Service_Cp::set($_POST, $_POST['id']);
			}
			$this->output(0, '更新成功.');
		}

		$info        = W3_Service_Cp::get($cpId);
		$info['pic'] = json_decode($info['pic'], true);
		$cps         = Widget_Service_Cp::$CpCate;
		unset($cps[1]);
		$this->assign('info', $info);
		$this->assign('cpId', $cpId);
		$this->assign('cps', $cps);
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}


	public function upload_postAction() {
		$ret   = Common::upload('img', 'resource');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	 */
}