<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 广告管理后台
 */
class AdController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Ad/index',
		'addUrl'        => '/Admin/Ad/add',
		'addPostUrl'    => '/Admin/Ad/add_post',
		'editUrl'       => '/Admin/Ad/edit',
		'editPostUrl'   => '/Admin/Ad/edit_post',
		'deleteUrl'     => '/Admin/Ad/delete',
		'uploadUrl'     => '/Admin/Ad/upload',
		'uploadPostUrl' => '/Admin/Ad/upload_post',
	);

	public $perpage      = 20;
	public $appCacheName = 'APPC_Front_Index_index';
	public $ad_types     = array(
		0  => '所有广告',
		1  => '首页轮播广告',
		2  => '资源下载页广告',
		3  => '导航页中部广告',
		4  => '新闻页中部广告',
		5  => '新闻页头部轮播广告',
		6  => '导航页头部广告',
		7  => '导航页底部文字链广告',
		8  => '新闻页头部文字链广告',
		9  => '书签页头部广告',
		10 => '书签页热词广告',
		11 => '书签页默认关键字',
	);

	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$ad_type = $this->getInput('ad_type');
		$param   = $this->getInput(array('ad_type', 'status'));
		$perpage = $this->perpage;
		$search  = array();
		if ($ad_type) $search['ad_type'] = $ad_type;
		list($total, $ads) = Gionee_Service_Ad::getList($page, $perpage, $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('ad_types', $this->ad_types);
		$this->assign('ads', $ads);
		$this->assign('search', $search);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Ad::getAd(intval($id));
		$this->assign('ad_types', $this->ad_types);
		$this->assign('info', $info);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_types', $this->ad_types);
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info   = $this->getPost(array('sort', 'title', 'ad_type', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info   = $this->_cookData($info);
		$result = Gionee_Service_Ad::addAd($info);
		if (!$result) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'sort',
			'title',
			'ad_type',
			'link',
			'img',
			'start_time',
			'end_time',
			'status'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['title']) {
			$this->output(-1, '广告标题不能为空.');
		}
		if (!$info['start_time']) {
			$this->output(-1, '开始时间不能为空.');
		}
		if (!$info['end_time']) {
			$this->output(-1, '结束时间不能为空.');
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']);
		if ($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		if ($info['ad_type'] == 10 || $info['ad_type'] == 11) {
			$info['img']  = '';
			$info['link'] = '';
			if ($info['ad_type'] == 10) {
				if (mb_strlen($info['title'], 'utf8') < 2 || mb_strlen($info['title'], 'utf8') > 6) {
					$this->output(-1, '广告标题长度应在2到6字之间');
				}
			}

		} else {
			if (!$info['img']) {
				$this->output(-1, '广告图片不能为空.');
			}
			if (!$info['link']) {
				$this->output(-1, '广告链接不能为空.');
			}
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
		return $info;
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) {
			$this->output(-1, '无法删除');
		}
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_Ad::deleteAd($id);
		if (!$result) {
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
		$ret   = Common::upload('img', 'ad');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
