<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * class ResourceController
 * @author rainkid
 *
 */
class ResourceController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Resource/index',
		'addUrl'        => '/Admin/Resource/add',
		'addPostUrl'    => '/Admin/Resource/add_post',
		'editUrl'       => '/Admin/Resource/edit',
		'editPostUrl'   => '/Admin/Resource/edit_post',
		'deleteUrl'     => '/Admin/Resource/delete',
		'deleteImgUrl'  => '/Admin/Resource/delete_img',
		'uploadUrl'     => '/Admin/Resource/upload',
		'uploadPostUrl' => '/Admin/Resource/upload_post',

	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		list($total, $resource) = Gionee_Service_Resource::getList($page, $perpage);
		$this->assign('resource', $resource);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] . '/?'));
	}


	public function addAction() {
	}


	public function add_postAction() {
		$info = $this->getPost(array(
			'name',
			'down_url',
			'company',
			'size',
			'icon',
			'description',
			'sort',
			'status',
			'summary',
			'star'
		));
		$simg = $this->getPost('simg');
		$info = $this->_cookData($info);

		if (!$simg) $this->output('-1', '至少上传一张预览图片.');

		$ret = Gionee_Service_Resource::addResource($info);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		$gimgs = array();
		foreach ($simg as $key => $value) {
			if ($value != '') {
				$gimgs[] = array('rid' => $ret, 'img' => $value);
			}
		}
		$ret = Gionee_Service_ResourceImg::addResourceImg($gimgs);
		if (!$ret) $this->output(-1, '-操作失败.');
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Resource::getResource(intval($id));
		list(, $pimgs) = Gionee_Service_ResourceImg::getList(0, 10, array('rid' => intval($id)));
		$this->assign('pimgs', $pimgs);
		$this->assign('info', $info);
	}


	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'name',
			'down_url',
			'company',
			'size',
			'icon',
			'description',
			'sort',
			'status',
			'summary',
			'star'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Resource::updateResource($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		Admin_Service_Log::op($info);
		//修改的图片
		$upImgs = $this->getPost('upImg');

		//新增加的图片
		$simg = $this->getPost('simg');

		if (!$upImgs && !$simg) $this->output('-1', '至少上传一张预览图片.');

		foreach ($upImgs as $key => $value) {
			if ($key && $value) {
				Gionee_Service_ResourceImg::updateResourceImg(array('img' => $value), $key);
			}
		}

		if ($simg[0] != null) {
			$gimgs = array();
			foreach ($simg as $key => $value) {
				if ($value != '') {
					$gimgs[] = array('rid' => $info['id'], 'img' => $value);
				}
			}
			$ret = Gionee_Service_ResourceImg::addResourceImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		$this->output(0, '操作成功.');
	}


	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Resource::getResource($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Gionee_Service_ResourceImg::deleteByResourceId($id);
		$ret = Gionee_Service_Resource::deleteResource($id);
		Admin_Service_Log::op($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['down_url']) $this->output(-1, '下载地址不能为空.');
		if (strpos($info['down_url'], 'http://') === false || !strpos($info['down_url'], 'https://') === false) {
			$this->output(-1, '下载地址不规范.');
		}
		if (!$info['company']) $this->output(-1, '公司名称不能为空.');
		if (!$info['star']) $this->output(-1, '星级请填写1-5之间的数字.');
		if ($info['star'] > 5 || $info['star'] < 1) $this->output(-1, '星级请填写1-5之间的数字.');
		if (!$info['size']) $this->output(-1, '资源大小不能为空.');
		if (!$info['icon']) $this->output(-1, '图标不能为空.');
		if (!$info['summary']) $this->output(-1, '简述不能为空.');

		return $info;
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
		$id   = $this->getInput('id');
		$info = Gionee_Service_ResourceImg::getResourceImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_ResourceImg::deleteResourceImg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
