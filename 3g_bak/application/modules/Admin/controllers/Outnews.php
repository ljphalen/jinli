<?php if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 聚合新闻管理
 * @author rainkid
 *
 */
class OutnewsController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Outnews/index',
		'addUrl'        => '/Admin/Outnews/add',
		'addPostUrl'    => '/Admin/Outnews/add_post',
		'editUrl'       => '/Admin/Outnews/edit',
		'editPostUrl'   => '/Admin/Outnews/edit_post',
		'deleteUrl'     => '/Admin/Outnews/delete',
		'uploadUrl'     => '/Admin/Outnews/upload',
		'uploadPostUrl' => '/Admin/Outnews/upload_post',
	);

	public $perpage = 20;

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page      = intval($this->getInput('page'));
		$title     = $this->getInput('title');
		$source_id = $this->getInput('source_id');

		$search = array();
		if ($title) $search['title'] = $title;
		if ($source_id) $search['source_id'] = $source_id;

		list($total, $result) = Gionee_Service_OutNews::search($page, $this->perpage, $search, array('timestamp' => 'DESC'));

		$config = Common::getConfig('outnewsConfig', 'news');
		foreach ($config as $type => $val) {
			foreach ($val as $k => $v) {
				$v['num']   = Gionee_Service_OutNews::getTotal(array('source_id' => $k));
				$source[$k] = $v;
			}
		}
		$this->assign('sources', $source);

		$this->cookieParams();
		$this->assign('search', $search);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl'] . '/?'));
	}


	public function batchAction() {
		$action = $this->getInput('action');
		$ids    = $this->getInput('ids');
		$sort   = $this->getInput('sort');

		if (!count($ids)) $this->output(-1, '没有可操作的项.');

		if ($action == 'open') {
			Gionee_Service_OutNews::updates($ids, array('status' => 1));
		} else if ($action == 'close') {
			Gionee_Service_OutNews::updates($ids, array('status' => 0));
		} else if ($action == 'delete') {
			Gionee_Service_OutNews::deletes($ids, array('status' => 0));
		}
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Column::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_Column::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
