<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 书签配置
 * @author tiger
 */
class BookmarkController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Bookmark/index',
		'editUrl'       => '/Admin/Bookmark/edit',
		'editPostUrl'   => '/Admin/Bookmark/edit_post',
		'deleteUrl'     => '/Admin/Bookmark/delete',
		'uploadUrl'     => '/Admin/Bookmark/upload',
		'uploadPostUrl' => '/Admin/Bookmark/upload_post',
	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$ver     = $this->getInput('ver');
		$where   = array();
		if (!empty($ver)) {
			$where = array('ver' => array('&', $ver));
		}

		$orderBy = array('sort' => 'ASC', 'id' => 'ASC');
		list($total, $bookmarks) = Gionee_Service_Bookmark::getList($page, $perpage, $where, $orderBy);
		foreach ($bookmarks as $val) {
			if (isset(Gionee_Service_Bookmark::$opParam[$val['op_type']])) {
				Gionee_Service_Bookmark::update(array('op_type' => Gionee_Service_Bookmark::$opParam[$val['op_type']]), $val['id']);
			}
		}

		$url = $this->actions['listUrl'] . '/?ver=' . $ver . '&';

		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('bookmarks', $bookmarks);
		$this->assign('ver', $ver);
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Bookmark::get(intval($id));
		if (!empty($info)) {
			$urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
			$this->assign('urlInfo', $urlInfo);
			$blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
			$this->assign('blist', $blist);
			$urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
			$this->assign('urlList', $urlList);
		}
		//合作商信息
		$cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
		$this->assign('cooperators', $cooperators);
		$this->assign('info', $info);

	}

	public function edit_postAction() {

		$info = $this->getPost(array(
			'id',
			'name',
			'icon',
			'url',
			'sort',
			'backgroud',
			'is_delete',
			'ver',
			'op_type',
			'filter_ver',
			'operation',
			'parter_id',
			'bid',
			'cp_id',
		));

		$info['url'] = Gionee_Service_ParterUrl::getLink($info['parter_id'], $info['cp_id'], $info['bid'], $info['url']);
		$info        = $this->_cookData($info);

		$info['ver']     = array_sum($info['filter_ver']);
		$info['op_type'] = array_sum($info['op_type']);
		if (empty($info['id'])) {
			Admin_Service_Access::pass('add');
			$info['operation'] = 0;
            $info['updated_at'] = Common::getTime();
			$ret               = Gionee_Service_Bookmark::add($info);
			$ver = $info['ver'];
		} else {

			$oldInfo = Gionee_Service_Bookmark::get($info['id']);
			$ver     = $oldInfo['ver'];
			unset($info['ver']);
			unset($info['op_type']);
			Admin_Service_Access::pass('edit');
            $info['updated_at'] = Common::getTime();
			$ret = Gionee_Service_Bookmark::update($info, intval($info['id']));

		}

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		Admin_Service_Log::op($info);
		foreach (array(1, 2, 4) as $v) {
			if (($ver & $v) > 0) {
				$this->updataVersion($v);
			}
		}

		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (empty($info['name']) || mb_strlen($info['name']) > 30) {
			$this->output(-1, '名称非法');
		}

		if (empty($info['icon'])) {
			$this->output(-1, '图标不能为空');
		}

		if (empty($info['url'])) {
			$this->output(-1, '地址不能为空');
		}

		if ($info['sort'] === '') {
			$this->output(-1, '排序不能为空.');
		}

		if (!$info['backgroud']) {
			$this->output(-1, '背景颜色不能为空.');
		}
		return $info;
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id   = $this->getInput('id');
		$info = Gionee_Service_Bookmark::get($id);
		if ($info && $info['id'] == 0) {
			$this->output(-1, '无法删除');
		}
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['icon']);
		//$result = Gionee_Service_Bookmark::delete($id);
		$result = Gionee_Service_Bookmark::update(array('operation' => 1), $info['id']);
		if (!$result) {
			$this->output(-1, '操作失败');
		}
		Admin_Service_Log::op($id);
		$this->updataVersion($info['ver']);
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('size', 100);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App', 100);
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->assign('size', 100);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * update_version
	 */
	private function updataVersion($ver) {
		Gionee_Service_Config::setValue('bookmark_version_' . $ver, Common::getTime());
	}
}