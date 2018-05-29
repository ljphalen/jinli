<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ProducesController extends Admin_BaseController {

	public $actions = array(
		'indexUrl'      => '/Admin/Produces/index',
		'addUrl'        => '/Admin/Produces/add',
		'addPostUrl'    => '/Admin/Produces/addPost',
		'editUrl'       => '/Admin/Produces/edit',
		'editPostUrl'   => '/Admin/Produces/editPost',
		'deleteUrl'     => '/Admin/Produces/delete',
		'uploadUrl'     => '/Admin/Produces/upload',
		'uploadPostUrl' => '/Admin/Produces/upload_post',
		'summaryUrl'    => '/Admin/Produces/summary'
	);

	public $pageSize = 20;

	public function indexAction() {
		$postData = $this->getInput(array('page', 'status', 'cat_id'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		if (!empty($postData['cat_id'])) {
			$where['cat_id'] = $postData['cat_id'];
		}
		list($total, $list) = User_Service_Produce::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($list as $k => $v) {
			$cate                 = User_Service_Category::get($v['cat_id']);
			$list[$k]['cat_name'] = $cate['name'];
		}
		$cateList = User_Service_Category::getsBy(array('status' => 1, 'group_id' => 2), array('id' => 'DESC'));
		$this->assign('cateList', $cateList);
		$this->assign('list', $list);
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('status', $postData['status']);
		$this->assign('cat_id', $postData['cat_id']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?status={$postData['status']}&"));
	}


	public function addAction() {
		$category = User_Service_Category::getsBy(array('status' => '1', 'group_id' => '2'), array(
			'sort' => 'DESC',
			'id'   => 'DESC'
		));
		if (!$category) {
			$this->output('-1', '您还没有添加分类，请选添加商品分类！');
		}
		$this->assign('category', $category);
	}

	public function addPostAction() {
		$postData = $this->getInput(array(
			'cat_id',
			'name',
			'link',
			'start_time',
			'end_time',
			'scores',
			'status',
			'image',
			'sort',
			'is_special',
			'image'
		));
		if (empty($postData['cat_id']) || !is_numeric($postData['scores'])) {
			$this->output('-1', '商品类别或金币数没有添加，请检查是否正确！');
		}
		$postData['start_time'] = strtotime($postData['start_time']);
		$postData['end_time']   = strtotime($postData['end_time']);
		$postData['add_time']   = time();
		$postData['add_user']   = $this->userInfo['username'];
		$res                    = User_Service_Produce::add($postData);
		if ($res) {
			$this->output('0', '添加成功');
		} else {
			$this->output('-1', '添加失败！');
		}
	}

	public function editAction() {
		$id       = $this->getInput('id');
		$data     = User_Service_Produce::get($id);
		$category = User_Service_Category::getsBy(array('status' => '1', 'group_id' => '2'), array(
			'sort' => 'DESC',
			'id'   => 'DESC'
		));
		$this->assign('category', $category);
		$this->assign('data', $data);
	}

	public function editPostAction() {
		$postData = $this->getInput(array(
			'cat_id',
			'name',
			'link',
			'type',
			'start_time',
			'end_time',
			'scores',
			'status',
			'image',
			'sort',
			'id'
		));
		if (empty($postData['cat_id']) || empty($postData['link']) || !is_numeric($postData['scores']) || empty($postData['name'])) {
			$this->output('-1', '请检查参数是否正确');
		}
		$postData['start_time'] = strtotime($postData['start_time']);
		$postData['end_time']   = strtotime($postData['end_time']);
		$postData['edit_time']  = time();
		$postData['edit_user']  = $this->userInfo['username'];
		$res                    = User_Service_Produce::update($postData, $postData['id']);
		if ($res) {
			$this->output('0', '修改成功');
		} else {
			$this->output('-1', '修改失败');
		}
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		if (!$id) return false;
		if (User_Service_Produce::delete($id)) $this->output('0', '操作成功');
		$this->output('-1', '操作失败');

	}

	//汇总信息
	public function summaryAction() {

	}

	//ajax得到所有可以设置等级的商品信息
	public function ajaxgetgoodsinfoAction() {
		$cat_id   = $this->getInput('type');
		$dataList = User_Service_Produce::getsBy(array(
			'cat_id'     => $cat_id,
			'status'     => '1',
			'is_special' => '1'
		), array('sort' => 'DESC', 'id' => "DESC"));
		echo json_encode(array('key' => '1', 'msg' => $dataList));
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
		$ret   = Common::upload('img', 'products');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}