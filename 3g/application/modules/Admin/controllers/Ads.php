<?php


if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class AdsController
 * 广告
 */
class AdsController extends Admin_BaseController {

	public $actions = array(

		'indexUrl'      => '/Admin/Ads/index',
		'addUrl'        => '/Admin/Ads/add',
		'addPostUrl'    => '/Admin/Ads/addPost',
		'editUrl'       => '/Admin/Ads/edit',
		'editPostUrl'   => '/Admin/Ads/editPost',
		'deleteUrl'     => '/Admin/Ads/delete',
		'uploadUrl'     => '/Admin/Ads/upload',
		'uploadPostUrl' => '/Admin/Ads/upload_post',
		'deleteUrl'     => '/Admin/Ads/delete'
	);

	public $pageSize = 20;

	public $adsType = array(
		'1' => '图片',
		'2' => '文字',
		'3' => '图文',
	);

	public function indexAction() {
		$postData = $this->getInput(array('page', 'status'));
		$page     = max(1, $postData['page']);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		list($total, $list) = Gionee_Service_Ads::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($list as $k => $v) {
			$pos                  = Gionee_Service_Position::get($v['position_id']);
			$list[$k]['pos_name'] = $pos['name'];
			$list[$k]['ad_type']  = $this->adsType[$pos['type']];
		}
		$this->assign('list', $list);
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('status', $postData['status']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?status={$postData['status']}&"));
	}

	public function addAction() {
		//广告位
		$positions = Gionee_Service_Position::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('position', $positions);
		$this->assign('types', $this->adsType);
	}

	public function addPostAction() {
		$postData          = $this->getInput(array(
			'name',
			'position_id',
			'start_time',
			'end_time',
			'sort',
			'status',
			'link',
			'image'
		));
		$postData['words'] = htmlspecialchars($_POST['words']);
		if (!$postData['position_id']) $this->output('-1', '请选择广告位');
		$postData['add_time'] = time();
		$res                  = Gionee_Service_Ads::add($postData);
		if ($res) {
			$this->output('0', '添加成功！');
		} else {
			$this->output('-1', '添加失败！');
		}

	}


	public function editAction() {
		$id = $this->getInput('id');
		if (!is_numeric($id)) {
			$this->output('-1', "内容有错！");
		}
		$positions = Gionee_Service_Position::getsBy(array('status' => 1), array('id' => 'DESC'));
		$data      = Gionee_Service_Ads::get($id);

		$this->assign('position', $positions);
		$this->assign('types', $this->adsType);
		$this->assign('data', $data);
	}

	public function editPostAction() {
		$postData              = $this->getInput(array(
			'name',
			'start_time',
			'end_time',
			'status',
			'sort',
			'id',
			'link',
			'image'
		));
		$postData['words']     = htmlspecialchars($_POST['words']);
		$postData['edit_time'] = time();
		$postData['edit_user'] = $this->userInfo['username'];
		$res                   = Gionee_Service_Ads::edit($postData, $postData['id']);
		if ($res) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '修改失败！');
		}
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		if (Gionee_Service_Ads::delete($id)) {
			$this->output('0', '删除成功！');
		} else {
			$this->output('-1', '删除失败！');
		}
	}

	//上传图片
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
		$ret   = Common::upload('img', 'ads');
		$imgId = $this->getPost('imgId');;
		$this->assign('imgId', $imgId);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}