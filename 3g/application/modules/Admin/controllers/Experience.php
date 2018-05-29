<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ExperienceController extends Admin_BaseController {

	public $actions  = array(
		'indexUrl'       => '/Admin/Experience/index',
		'editUrl'        => '/Admin/Experience/edit',
		'editPostUrl'    => '/Admin/Experience/editPost',
		'deleteUrl'      => '/Admin/Experience/delete',
		'catUrl'         => '/Admin/Experience/cat',
		'catEditUrl'     => '/Admin/Experience/catedit',
		'cateditPostUrl' => '/Admin/Experience/cateditpost',
		'catDeleteUrl'   => '/Admin/Experience/catDelete',
		'uploadUrl'      => '/Admin/Experience/upload',
		'uploadPostUrl'  => '/Admin/Experience/upload_post',
		'configUrl'      => '/Admin/Experience/config',
		'logUrl'         => '/Admin/Experience/log',
	);
	public $pageSize = 20;

	public function indexAction() {
		$postData = $this->getInput(array('page', 'type'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (intval($postData['type'])) {
			$where['type'] = $postData['type'];
		}
		list($total, $dataList) = User_Service_ExperienceInfo::getList($page, $this->pageSize, $where, array('id' => "DESC"));
		foreach ($dataList as $k => $v) {
			$reward = json_decode($v['level_msg'], true);
			$str    = " ";
			foreach ($reward as $m => $n) {
				$catData = User_Service_ExperienceType::get($n['cat_id']);
				$str .= sprintf($catData['name'], $n['num']);
				$str .= ",";
			}
			$dataList[$k]['reward'] = substr($str, 0, -1);
		}
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?type={$postData['type']}&"));
	}

	public function editAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$data              = User_Service_ExperienceInfo::get($id);
			$data['level_msg'] = json_decode($data['level_msg'], true);
			$this->assign('data', $data);
		}
		$types       = User_Service_ExperienceType::getsBy(array('status' => '1'), array('add_time' => 'DESC'));
		$levelConfig = Common::getConfig('userConfig', 'rank');
		$this->assign('types', $types);
		$this->assign('levels', array_keys($levelConfig['1']));
	}

	public function editpostAction() {
		$postData = $this->getInput(array('name', 'id', 'status', 'info', 'level'));
		$temp     = array();
		foreach ($postData['info'] as $k => $m) {
			if (isset($m['cat_id'])) {
				$temp[$k] = $m;
			}
		}
		$postData['level_msg'] = json_encode($temp);
		foreach ($postData['info'] as $k => $v) {
			if ((intval($v['cat_id']) && empty($v['num'])) || (intval($v['num']) && empty($v['cat_id']))) {
				$this->output('-1', '请确认所填数据是否正确');
			}
		}
		unset($postData['info']);
		if ($postData['id']) {
			$ret = User_Service_ExperienceInfo::update($postData, $postData['id']);
		} else {
			$postData['add_time'] = time();
			$ret                  = User_Service_ExperienceInfo::add($postData);
		}
		$this->output('0', '操作成功!');
	}

	public function catAction() {
		$postData = $this->getInput(array('page', 'type'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (intval($postData['type'])) {
			$where['type'] = $postData['type'];
		}
		list($total, $dataList) = User_Service_ExperienceType::getList($page, $this->pageSize, $where, array('id' => "DESC"));
		$this->assign('data', $dataList);
		$this->assign('rewardTypes', Common::getConfig('userConfig', 'exprience_rewards_type'));
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['catUrl'] . "?type={$postData['type']}&"));
	}


	public function cateditAction() {
		$id   = $this->getInput('id');
		$data = User_Service_ExperienceType::get($id);
		$this->assign('data', $data);
		$this->assign('rewardTypes', Common::getConfig('userConfig', 'exprience_rewards_type'));
	}

	public function  cateditPostAction() {
		$postData = $this->getInput(array('name', 'type', 'id', 'image', 'status', 'link'));
		if ($postData['id']) {
			$ret = User_Service_ExperienceType::update($postData, $postData['id']);
		} else {
			$postData['add_time'] = time();
			$ret                  = User_Service_ExperienceType::add($postData);
		}
		$this->output('0', '操作成功!');
	}

	public function  catDeleteAction() {
		$id  = $this->getInput('id');
		$ret = User_Service_ExperienceType::delete($id);
		$this->output('0', '操作成功!');
	}

	public function logAction() {
		$postData = $this->getInput(array('page', 'type', 'sdate', 'edate', 'username'));
		!$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
		!$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
		$page  = max($postData['page'], 1);
		$where = array();
		if (!empty($postData['type'])) {
			$where['type'] = $postData['type'];
		}

		if (!empty($postData['username'])) {
			$user         = Gionee_Service_User::getBy(array('username' => $postData['username']), array('id' => 'desc'));
			$where['uid'] = $user['id'];
		}
		$where['add_time'] = array(
			array('<=', strtotime($postData['edate'] . " 23:59:59")),
			array('>=', strtotime($postData['sdate']))
		);
		list($total, $dataList) = User_Service_ExperienceLog::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$userInfo                 = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username'] = $userInfo['username'];
		}
		$this->assign('dataList', $dataList);
		$this->assign('params', $postData);
		$this->assign('types', Common::getConfig('userConfig', 'experience_activity_type'));
		unset($postData['page']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['logUrl'] . "?" . http_build_query($postData) . "&"));
	}


	public function configAction() {
		$keys     = array(
			'user_experience_points_rule',
			'user_experience_privilege_info',
		);
		$postData = $this->getPost($keys);
		if (!empty($postData['user_experience_privilege_info'])) {
			Admin_Service_Log::op($postData);
			foreach ($postData as $key => $val) {
				Gionee_Service_Config::setValue($key, trim($val));
			}
		}
		$ret = Gionee_Service_Config::getsBy(array('3g_key' => array('IN', $keys)));
		foreach ($ret as $k => $v) {
			$data[$v['3g_key']] = $v['3g_value'];
		}
		$this->assign('data', $data);
	}


	//数据导出
	public function exportAction() {
		$res = $this->_exportData();
		exit();
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
		$ret   = Common::upload('img', 'experience');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('data', $ret['data']);
		$this->assign('code', $ret['code']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}