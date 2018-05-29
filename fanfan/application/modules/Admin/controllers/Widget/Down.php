<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Widget_DownController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Widget_Down/index',
		'editUrl'       => '/Admin/Widget_Down/edit',
		'editPostUrl'   => '/Admin/Widget_Down/edit_post',
		'deleteUrl'     => '/Admin/Widget_Down/delete',
		'uploadUrl'     => '/Admin/Widget_Down/upload',
		'uploadPostUrl' => '/Admin/Widget_Down/upload_post',
	);

	public $perpage = 20;

	public function indexAction() {
		$resource = Widget_Service_Down::getAllCp();
		$this->assign('resource', $resource);
		$cps = Widget_Service_Cp::filterCpId();
		$this->assign('cps', $cps);
	}

	public function editAction() {
		$id          = $this->getInput('id');
		$info        = Widget_Service_Down::get(intval($id));
		$info['pic'] = json_decode($info['pic'], true);
		$this->assign('info', $info);
		$cps = Widget_Service_Cp::filterCpId();
		$this->assign('cps', $cps);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'cp_id', 'desc_s','desc', 'tip', 'url', 'size', 'icon', 'company', 'mark'));
		if (!$info['name']) $this->output(-1, '标题不能为空.');
		if (!$info['desc']) $this->output(-1, '简述不能为空.');
		if (!$info['url']) $this->output(-1, '下载地址不能为空.');
		if (!$info['icon']) $this->output(-1, '图标不能为空.');
		if (!$info['cp_id']) $this->output(-1, 'CP不能为空.');

		$info['desc'] = $_POST['desc'];
		$tmpPic       = array();
		foreach ($_POST['simg'] as $pic) {
			if ($pic) {
				$tmpPic[] = $pic;
			}
		}

		$where['cp_id'] = $info['cp_id'];
		if (!empty($info['id'])) {
			$where['id'] = array('!=', $info['id']);
		}
		$tmp = Widget_Service_Down::getBy($where);
		if (!empty($tmp['id'])) {
			$this->output(-1, 'CP存在.');
		}

		$info['pic'] = json_encode($tmpPic);
		if (empty($info['id'])) {
			$ret = Widget_Service_Down::add($info);
		} else {
			$ret = Widget_Service_Down::set($info, $info['id']);
		}

		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Widget_Service_Cp::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}

		$ret = Widget_Service_Down::del($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
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


	public function delete_imgAction() {
		$id     = $this->getInput('id');
		$result = false;
		if (!$result) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}


}
