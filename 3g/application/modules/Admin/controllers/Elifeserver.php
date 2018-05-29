<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 产品管理
 * @author rainkid
 */
class ElifeserverController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Elifeserver/index',
		'sortPostUrl'   => '/Admin/Elifeserver/sort_post',
		'addUrl'        => '/Admin/Elifeserver/add',
		'addPostUrl'    => '/Admin/Elifeserver/add_post',
		'editUrl'       => '/Admin/Elifeserver/edit',
		'editPostUrl'   => '/Admin/Elifeserver/edit_post',
		'deleteUrl'     => '/Admin/Elifeserver/delete',
		'deleteImgUrl'  => '/Admin/Elifeserver/delete_img',
		'uploadUrl'     => '/Admin/Elifeserver/upload',
		'uploadPostUrl' => '/Admin/Elifeserver/upload_post',
		'importUrl'     => '/Admin/Elifeserver/import',
		'uploadImgUrl'  => '/Admin/Elifeserver/uploadImg'
	);
	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$params  = array();
		$orderBy = array('sort' => 'DESC', 'id' => 'DESC');
		list($total, $list) = Gionee_Service_ElifeServer::getList($page, $perpage, $params, $orderBy);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('list', $list);
	}

	public function sort_postAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '请选中需要修改的记录');
		foreach ($info['ids'] as $key => $value) {
			$result = Gionee_Service_ElifeServer::updateBy(array('sort' => $info['sort'][$key]), array('id' => $value));
			if (!$result) $this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功.');
	}

	public function addAction() {
	}

	public function add_postAction() {
		$info = $this->getPost(array('name', 'function', 'outward', 'parameter', 'sort', 'status'));
		$simg = $this->getPost('simg');
		if (!$info['name']) $this->output(-1, '机型名称不能为空！');
		if (!$info['function']) $this->output(-1, '功能不能为空！');
		if (!$info['outward']) $this->output(-1, '外观不能为空！');
		if (!$info['parameter']) $this->output(-1, '参数不能为空！');
		if (!$simg) $this->output('-1', '至少上传一张产品主图！.');
		$ret = Gionee_Service_ElifeServer::add($info);
		if (!$ret) $this->output(-1, '操作失败.');

		$gimgs = array();
		foreach ($simg as $key => $value) {
			if ($value != '') {
				$gimgs[] = array('elife_id' => $ret, 'img' => $value);

			}
		}
		$ret = Gionee_Service_ElifeServerImages::addImg($gimgs);

		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ElifeServer::get(intval($id));
		list($total, $pimgs) = Gionee_Service_ElifeServerImages::getsBy($id);

		$this->assign('info', $info);
		$this->assign('pimgs', $pimgs);
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name', 'function', 'outward', 'parameter', 'sort', 'status'));
		if (!$info['name']) $this->output(-1, '机型名称不能为空！');
		if (!$info['function']) $this->output(-1, '功能不能为空！');
		if (!$info['outward']) $this->output(-1, '外观不能为空！');
		if (!$info['parameter']) $this->output(-1, '参数不能为空！');
		$ret = Gionee_Service_ElifeServer::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');

		//修改的图片
		$upImgs = $this->getPost('upImg');

		//新增加的图片
		$simg = $this->getPost('simg');


		if (!$upImgs && !$simg) $this->output('-1', '至少上传一张产品主图！');
		foreach ($upImgs as $key => $value) {
			if ($key && $value) {
				$ret = Gionee_Service_ElifeServerImages::updateImg(array('img' => $value), $key);
				if (!$ret) $this->output(-1, '操作失败.');
			}
		}

		if ($simg[0] != null) {
			$gimgs = array();
			foreach ($simg as $key => $value) {
				if ($value != '') {
					$gimgs[] = array('elife_id' => $info['id'], 'img' => $value);
				}
			}
			$ret = Gionee_Service_ElifeServerImages::addImg($gimgs);
			if (!$ret) $this->output(-1, '操作失败.');
		}


		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ElifeServer::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');

		$ret = Gionee_Service_ElifeServer::delete($id);
		$ret = Gionee_Service_ElifeServerImages::deleteImgs($id);

		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function delete_imgAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_ElifeServerImages::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_ElifeServerImages::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function uploadImgAction() {
		$ret        = Common::upload('imgFile', 'elife');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getImgPath() . $ret['data'])));
	}

	/**
	 *产品主图本地上传
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'elife');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
