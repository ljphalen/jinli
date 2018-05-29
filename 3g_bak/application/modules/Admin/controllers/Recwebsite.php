<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * class RecwebsiteController
 * 推荐网址管理
 * @author huwei
 *
 */
class RecwebsiteController extends Admin_BaseController {

	public $actions = array(
		'siteUrl'       => '/Admin/RecWebsite/listsite',
		'editSiteUrl'   => '/Admin/RecWebsite/editsite',
		'delUrl'        => '/Admin/RecWebsite/delete',
		'uploadUrl'     => '/Admin/Browserurl/upload',
		'uploadPostUrl' => '/Admin/Browserurl/upload_post',
	);

	public $perpage = 20;


	public function listsiteAction() {
		$page    = intval($this->getInput('page'));
		$type    = $this->getInput('type');
		$perpage = $this->perpage;
		$where   = array('type' => $type);
		$order   = array('sort' => 'asc');

		$list  = Gionee_Service_RecWebsite::getList($page, $perpage, $where, $order);
		$total = Gionee_Service_RecWebsite::getTotal($where);
		$this->assign('type', $type);
		$this->assign('list', $list);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listSiteUrl'] . '/?'));
	}

	public function editsiteAction() {

		$id   = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$info = $this->getPost(array('id', 'title', 'url', 'type', 'icon', 'sort', 'group_name', 'status'));
		if (!empty($info['title'])) {
			if (empty($info['id'])) {
				unset($info['id']);
				$info['created_at'] = time();
				Admin_Service_Access::pass('add');
				$ret = Gionee_Service_RecWebsite::insert($info);
			} else {
				Admin_Service_Access::pass('edit');
				$ret = Gionee_Service_RecWebsite::update($info, $info['id']);
			}
			Admin_Service_Log::op($info);
			$key = 'ver_recweb_' . $info['type'];
			$ver = intval(Gionee_Service_Config::getValue($key));

			Gionee_Service_Config::setValue($key, $ver + 1);

			if (!$ret) {
				$this->output(-1, '-操作失败.');
			}
			$this->output(0, '操作成功');
			exit;
		}

		$info = Gionee_Service_RecWebsite::get($id);
		if ($info['id']) {
			$type = $info['type'];
		}
		$this->assign('info', $info);
		$this->assign('type', $type);
	}

	public function tourlAction() {
		$list = Gionee_Service_Config::getValue('recwebsite_tourl');
		$desc = Gionee_Service_Config::getValue('recwebsite_tourl_desc');
		$desc = json_decode($desc, true);
		$list = json_decode($list, true);

		$data     = $this->getPost('data');
		$descData = $this->getPost('desc');
		if (!empty($data)) {
			$ret = Gionee_Service_Config::setValue('recwebsite_tourl', json_encode($data));
			Gionee_Service_Config::setValue('recwebsite_tourl_desc', json_encode($descData));

			if (!$ret) {
				$this->output(-1, '操作失败');
			}
			$this->output(0, '操作成功');
			exit;
		}


		$keys = array_keys($list);

		$this->assign('list', $list);
		$this->assign('keys', $keys);
		$this->assign('desc', $desc);
	}

	/**
	 *
	 */
	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id  = $this->getInput('id');
		$ret = Gionee_Service_RecWebsite::delete($id);
		Admin_Service_Log::op($id);
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}


}
