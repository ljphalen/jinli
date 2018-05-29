<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_SourceController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Widget_Source/index',
		'editUrl'       => '/Admin/Widget_Source/edit',
		'editPostUrl'   => '/Admin/Widget_Source/editPost',
		'uploadUrl'     => '/Admin/Widget_Source/upload',
		'uploadPostUrl' => '/Admin/Widget_Source/upload',
	);

	public $perpage = 20;

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$status  = $this->getInput('status');
		$status  = strlen($status) == 0 ? 1 : $status;
		$perpage = $this->perpage;

		$title  = $this->getInput('title');
		$url_id = $this->getPost('url_id');

		if (!$url_id && ($url_id = $this->getInput("url_id"))) {
			$url_id = explode(",", $url_id);
		}

		$search           = array();
		$search['status'] = $status;
		if ($title) {
			$search['title'] = array('LIKE', $title);
		}
		if ($url_id) {
			$search['url_id'] = array('IN', $url_id);
		}

		$total  = Widget_Service_Source::getTotal($search);
		$result = Widget_Service_Source::getList($page, $perpage, $search, array('create_time' => 'DESC'));
		$this->assign('total', $total);
		$this->assign('result', $result);


		$cpUrls = Widget_Service_Cp::getAll();
		$this->assign('cpurl', $cpUrls);
		$cpCate = array();
		foreach ($cpUrls as $val) {
			$cpCate[$val['cp_id']][] = $val;
		}
		$this->assign('cpcate', $cpCate);
		$this->assign('status', $status);
		$this->assign('cp', Widget_Service_Cp::$CpCate);

		if ($title) {
			$search['title'] = $title;
		}
		if ($url_id) {
			$search['url_id'] = implode(",", $url_id);
		}
		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';

		unset($search['status']);
		$this->assign('status_url', $this->actions['listUrl'] . '/?' . http_build_query($search) . '&');
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('attachroot', Yaf_Application::app()->getConfig()->attachroot);
	}

	public function batchAction() {
		$action = $this->getInput('action');
		$ids    = $this->getInput('ids');
		$sort   = $this->getInput('sort');

		if ($action != 'clear') {
			if (!count($ids)) $this->output(-1, '没有可操作的项.');
		}

		if ($action == 'open') {
			Widget_Service_Source::updates($ids, array('status' => Widget_Service_Source::STATUS_OK));
		} else if ($action == 'close') {
			Widget_Service_Source::updates($ids, array('status' => Widget_Service_Source::STATUS_CLOSE));
		} else if ($action == 'delete') {
			Widget_Service_Source::deletes($ids, array('status' => Widget_Service_Source::STATUS_CLOSE));
		} else if ($action == 'clear') {
			Widget_Service_Source::deleteBy(array('status' => Widget_Service_Source::STATUS_DORP));
		}
		$this->output(0, '操作成功.');
	}

	public function editPostAction() {
		$info             = $this->getInput(array('id', 'title', 'subtitle', 'out_link', 'img', 'summary', 'content', 'url_id', 'color', 'w3_color','status', 'source', 'create_time'));
		$info['out_link'] = trim($_POST['out_link']);
		$info['content']  = trim($_POST['content']);
		$info['summary']  = trim($_POST['summary']);
		$existTitle       = false;
		if (empty($info['id'])) {
			$tmpData = Widget_Service_Source::getByTitle($info['title']);
			if (!empty($tmpData)) {
				$existTitle = true;
			}
		}

		$retCode = -1;
		$retMsg  = '';
		if (!$info['title']) {
			$retMsg = '标题不能为空.';
		} else if (!$info['create_time']) {
			$retMsg = '发布时间不能为空.';
		} else if (!$info['img']) {
			$retMsg = '图片不能为空.';
		} else if ($existTitle) {
			$retMsg = '标题已存在.';
		} else if (!$info['out_link']) {
			$retMsg = '外链不能为空.';
		} else if (!$info['content']) {
			$retMsg = '内容不能为空.';
		}

		if (empty($retMsg)) {
			if (empty($info['color'])) {
				$file          = Widget_Service_Adapter::getSavePath() . "/" . $info['img'];
				$info['color'] = Util_Color::getImgColor($file);
			}

			if (empty($info['w3_color'])) {
				$path    = Widget_Service_Adapter::getSavePath();
				$imgType = strtolower(pathinfo($path . '/' . $info['img'], PATHINFO_EXTENSION));
				$imgPath = $path . '/' . $info['img'] . '_400x357.' . $imgType;
				$im      = new Util_W3Img($imgPath);
				$rgb     = $im->getRGB();
				$info['w3_color']   = $im->hexColor($rgb);
			}

			$info['create_time'] = strtotime($info['create_time']);
			$info['source_time'] = strtotime($info['create_time']);
			$info['cp_id']       = $info['source'];

			if ($info['cp_id'] == Widget_Service_Cp::CP_GIONEE) {
				//广告只能用外部链接
				$info['url']    = $info['out_link'];
			}

			if ($info['id']) {
				$ret = Widget_Service_Source::edit($info);
			} else {
				$outId           = 'G' . date('YmdHis');
				$outIid          = crc32($outId);
				$info['out_id']  = $outId;
				$info['out_iid'] = $outIid;
				$ret             = Widget_Service_Source::add($info);
			}

			if ($ret) {
				$retCode = 0;
				$retMsg  = '操作成功.';
			}

		}

		$this->output($retCode, $retMsg);
	}

	public function editAction() {
		$id = $this->getInput('id');
		if ($id) {
			$info = Widget_Service_Source::get($id);
			if ($info['id']) {
				$info['title'] = htmlspecialchars($info['title']);
				$this->assign('info', $info);
			}
		}
		$cpUrls = Widget_Service_Cp::getAll();

		$this->assign('cpUrls', $cpUrls);
		$this->assign('cp', Widget_Service_Cp::$CpCate);
	}

	public function uploadAction() {
		$imgId = $this->getPost('imgId');

		if (empty($imgId)) {
			$imgId = $this->getInput('imgId');
		} else {
			$ret = $this->_upload('img');
			$this->assign('code', $ret['data']);
			$this->assign('msg', $ret['msg']);
			$this->assign('data', $ret['data']);
		}
		$this->assign('imgId', $imgId);
		$this->getView()->display('widget/source/upload.phtml');
		exit;
	}

	private function _upload($name) {
		$img = $_FILES[$name];
		if ($img['error']) {
			return Common::formatMsg(-1, '上传图片失败:' . $img['error']);
		}

		$allowType = array('jpg' => '', 'jpeg' => '', 'png' => '');
		$uploader  = new Util_Upload($allowType);

		$savePath = Widget_Service_Adapter::getSavePath() . "/" . date('Ymd');

		if (!file_exists($savePath)) {
			mkdir($savePath, 0777, true);
		}

		$ret = $uploader->upload('img', md5($img['tmp_name']), $savePath);
		if (empty($ret['newName'])) {
			return Common::formatMsg(-1, '上传失败');
		}

		$file    = $savePath . '/' . $ret['newName'];
		$imgInfo = Util_Image::getImgInfo($file);

		if ($imgInfo['width'] < 400 || $imgInfo['height'] < 357) {
			return Common::formatMsg(-1, '图片尺寸 宽大于400 和 高大于357');
		}

		$ext         = end(explode("/", mime_content_type($file)));
		$newFilename = pathinfo($ret['newName'], PATHINFO_FILENAME) . '.' . $ext;

		$newFile = $savePath . '/' . $newFilename;
		rename($file, $newFile);

		if (!file_exists($newFile)) {
			return Common::formatMsg(-1, '文件更新失败');
		}
		$url = date('Ymd') . '/' . $newFilename;

		$imgs = array(
			array('file' => $url),
		);

		$newFileThumb = $newFile . "_400x357." . $ext;
		if ($imgInfo['width'] == 400 && $imgInfo['height'] == 357) {
			copy($newFile, $newFileThumb);
		} else {
			Util_Image::makeThumb($newFile, $newFileThumb, 400, 357);
		}

		$thumbArr = array(
			array(171, 101),
			array(152, 101),
			array(121, 101),
		);

		foreach ($thumbArr as $val) {
			$thumb = "{$newFile}_{$val[0]}x{$val[1]}.{$ext}";
			Util_Image::makeThumb($newFileThumb, $thumb, $val[0], $val[1]);
			if (!file_exists($thumb . ".webp")) {
				image2webp($thumb, $thumb . ".webp");
			}
		}

		return Common::formatMsg(0, '', $url);
	}
}
