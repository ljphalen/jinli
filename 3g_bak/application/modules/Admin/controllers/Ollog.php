<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 更新日志维护
 * @author huwei
 */
class OLlogController extends Admin_BaseController {

	public $actions = array(
		'listUrl' => '/Admin/ollog/index',
		'editUrl' => '/Admin/ollog/edit',
		'delUrl'  => '/Admin/ollog/del',
	);

	/**
	 * 日志列表
	 */
	public function indexAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = Gionee_Service_OnlineLog::getList(array(), array('add_time' => "DESC"), $page);
		foreach ($dataList as $k => $v) {
			$userinfo                   = Admin_Service_User::getUser($v['admin_id']);
			$dataList[$k]['admin_name'] = $userinfo['username'];
			$modifier                   = Admin_Service_User::getUser($v['edit_userid']);
			$dataList[$k]['modifier']   = $modifier ? $modifier['username'] : '';
		}
		$this->assign('userinfo', $this->userInfo);
		$this->assign('list', $dataList);
		$this->assign('pager', Common::getPages($total, $page, 20, $this->actions['loglist'] . "/?"));
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_OnlineLog::get($id);
		if (!empty($_POST['content'])) {
			$date  = $this->getInput('online_date');
			$title = $this->getInput('title');

			if (empty($date) || empty($title)) {
				$this->output('-1', '日期和内容不能为空');
			}

			$content = trim($_POST['content']);
			$uid     = $this->userInfo['uid'];
			if (!empty($id)) {
				$res = Gionee_Service_OnlineLog::update($id, array(
					'online_date' => $date,
					'title'       => $title,
					'content'     => $content,
					'edit_userid' => $uid,
					'edit_time'   => time()
				));
			} else {
				$res = Gionee_Service_OnlineLog::add(array(
					'online_date' => $date,
					'title'       => $title,
					'content'     => $content,
					'admin_id'    => $uid,
					'add_time'    => time()
				));
			}

			if ($res) {
				$this->output('0', '修改成功！');
			} else {
				$this->output('-1', '操作失败！');
			}
		}
		$this->assign('info', $info);
		$this->assign('id', $id);
	}

	public function delAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_OnlineLog::delete($id);
		if ($res) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '操作失败！');
		}
	}


}
	

