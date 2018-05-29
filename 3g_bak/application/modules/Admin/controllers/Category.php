<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 用户商品分类管理
 */
class CategoryController extends Admin_BaseController {

	public $actions = array(
		'indexURL'      => '/Admin/Category/index',
		'addURL'        => '/Admin/Category/add',
		'addPostURL'    => '/Admin/Category/addPost',
		'editURL'       => '/Admin/Category/edit',
		'editPostURL'   => '/Admin/Category/editPost',
		'deleteURL'     => '/Admin/Category/delete',
		'uploadUrl'     => '/Admin/Category/upload',
		'uploadPostUrl' => '/Admin/Category/upload_post',
	);

	public $pageSize = 20;

	public function indexAction() {
		$postData = $this->getInput(array('page', 'status', 'gid'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		if (!empty($postData['gid'])) {
			$where['group_id'] = $postData['gid'];
		}
		list($total, $dataList) = User_Service_Category::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('groupType', Common::getConfig('userConfig', 'action_type'));
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('gid', $postData['gid']);
		$this->assign('status', $postData['status']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexURL'] . "?status={$postData['status']}&"));
	}

	public function  addAction() {
		$this->assign('groupType', $this->_getGroupType());
		$pos = Gionee_Service_Position::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('pos', $pos);
	}

	public function addPostAction() {
		$postData = $this->getInput(array(
			'name',
			'group_id',
			'status',
			'sort',
			'img',
			'score_type',
			'ad_id',
			'pos_id',
			'max_number',
			'link',
			'description'
		));
		if (empty($postData['name']) || !$postData['group_id'] || !$postData['score_type']) {
			$this->output('-1', '参数有误');
		}
		if ($postData['ad_id']) {
			$postData['ext'] = json_encode(array('pos_id' => $postData['pos_id'], 'ad_id' => $postData['ad_id']));
		}
		$postData['add_time'] = time();
		$postData['add_user'] = $this->userInfo['username'];
		$ret                  = User_Service_Category::add($postData);
		if ($ret) {
			$key = 'USER:GOODS:PRODUCT';
			Common::getCache()->delete($key);
			$this->output('0', '添加成功！');
		} else {
			$this->output('-1', '添加失败！');
		}
	}


	public function editAction() {
		$id = $this->getInput('id');
		if (!is_numeric($id)) $this->output('-1', '参数有错！');
		$data = User_Service_Category::get($id);
		if (!$data) {
			$this->assign('-1', '该分类不存在！');
		}
		$actionConfig = Common::getConfig('userConfig', 'actions');
		$actions      = array();
		if ($data['group_id'] == 2) {
			foreach ($actionConfig as $k => $v) {
				if (substr($k, 0, 1) == '1') {
					$actions[$k] = $v;
				}
			}
		} elseif ($data['group_id'] == 3) {
			foreach ($actionConfig as $k => $v) {
				if (substr($k, 0, 1) == '3') {
					$actions[$k] = $v;
				}
			}
		}
		$ext = json_decode($data['ext'], true);
		$pos = Gionee_Service_Position::getsBy(array('status' => 1), array('id' => 'desc'));
		if (!empty($ext['pos_id'])) {
			$ads = Gionee_Service_Ads::getsBy(array('position_id' => $ext['pos_id']), array('id' => 'DESC'));
			$this->assign('ads', $ads);
		}
		$this->assign('pos', $pos);
		$this->assign('ext', $ext);
		$this->assign('actions', $actions);
		$this->assign('data', $data);
		$this->assign('groupType', $this->_getGroupType());
	}


	public function editPostAction() {
		$postData = $this->getInput(array(
			'name',
			'group_id',
			'id',
			'status',
			'sort',
			'img',
			'score_type',
			'pos_id',
			'ad_id',
			'max_number',
			'link',
			'description'
		));
		if (empty($postData['name']) || !$postData['group_id']) {
			$this->output('-1', '参数有错！');
		}
		if ($postData['ad_id']) {
			$postData['ext'] = json_encode(array('pos_id' => $postData['pos_id'], 'ad_id' => $postData['ad_id']));
		}
		$postData['edit_time'] = time();
		$postData['edit_user'] = $this->userInfo['username'];
		$res                   = User_Service_Category::update($postData, $postData['id']);
		if ($res) {
			$key = 'USER:GOODS:PRODUCT';
			Common::getCache()->delete($key);
			$this->output('0', '编辑成功');
		} else {
			$this->output('-1', '编辑失败');
		}
	}

	public function deleteAction() {
		$id  = $this->getInput('id');
		$res = User_Service_Category::delete($id);
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	//ajax获得积分变化的动作
	public function ajaxGetScoreActionTypeAction() {
		$group_id = $this->getInput('group_id');
		if (!in_array($group_id, array('2', '3'))) {
			echo json_encode(array('key' => '-1', 'msg' => '参数有错！'));
		}
		$data    = array();
		$actions = Common::getConfig('userConfig', 'actions');
		if ($group_id == '2') { //产生积分的动作
			foreach ($actions as $k => $v) {
				if (substr($k, 0, 1) == '1') {
					$data[$k] = $v;
				}
			}
		} else {
			foreach ($actions as $k => $v) {
				if (substr($k, 0, 1) == '3') {
					$data[$k] = $v;
				}
			}
		}
		echo json_encode(array('key' => '1', 'msg' => $data));
		exit();
	}

	//aja请求分类信息
	public function ajaxGetCategoryInfoAction() {
		$groupId = $this->getInput('groupId');
		if (!in_array($groupId, array('2', '3'))) {
			echo json_encode(array('key' => '-1', 'msg' => '商品组别信息有错！'));
			exit();
		}
		$dataList = User_Service_Category::getsBy(array(
			'status'   => '1',
			'group_id' => $groupId
		), array('sort' => 'DESC', 'id' => 'DESC'));
		echo json_encode(array('key' => '1', 'msg' => $dataList));
	}

	public function ajaxGetAdsByPosIdAction() {
		$posId = $this->getInput('pos_id');
		$ads   = Gionee_Service_Ads::getsBy(array('position_id' => $posId, 'status' => 1), array('id' => 'DESC'));
		$this->output('0', '', $ads);
	}

	//获得商品组别
	private function _getGroupType() {
		$config = Common::getConfig('userConfig', 'action_type');
		foreach ($config as $k => $v) {
			if ($v['key'] == 'signin') { //去掉打卡
				unset($config[$k]);
			}
		}
		return $config;
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
		$ret   = Common::upload('img', 'cat');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}