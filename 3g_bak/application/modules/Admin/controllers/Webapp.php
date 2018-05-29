<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 轻应用管理后台
 */
class WebappController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Webapp/index',
		'addUrl'        => '/Admin/Webapp/add',
		'addPostUrl'    => '/Admin/Webapp/add_post',
		'editUrl'       => '/Admin/Webapp/edit',
		'editPostUrl'   => '/Admin/Webapp/edit_post',
		'deleteUrl'     => '/Admin/Webapp/delete',
		'uploadUrl'     => '/Admin/Webapp/upload',
		'uploadPostUrl' => '/Admin/Webapp/upload_post',
		'exportUrl'     => '/Admin/Webapp/export',
		'importUrl'     => '/Admin/Webapp/import',
	);

	static $headerrow = array(
		'id'           => 'ID',
		'name'         => '名称',
		'type_id'      => '类型',
		'descrip'      => '描述',
		'link'         => '链接',
		'color'        => '底色',
		'img'          => '图标',
		'img2'         => '图标2',
		'star'         => '指数',
		'hits'         => '点击量',
		'is_new'       => '新应用[1是|0否]',
		'is_must'      => '必备[1是|0否]',
		'is_recommend' => '推荐[1是|0否]',
		'status'       => '状态[1开|0关]',
		'sub_time'     => '时间',
		'sort'         => '排序'
	);

	public $perpage = 20;
	public $types; //分类

	public function init() {
		parent::init();
		list(, $this->types) = Gionee_Service_WebAppType::getWebAppType();
	}

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param   = $this->getInput(array('type_id', 'order_by', 'name'));

		//排序方式
		$order_by = $param['order_by'] ? $param['order_by'] : 'id';

		$search = array();

		if ($param['type_id'] != '') $search['type_id'] = $param['type_id'];
		if ($param['name'] != '') $search['name'] = array('LIKE', $param['name']);
		$search['order_by'] = $param['order_by'];
		list($total, $recmarks) = Gionee_Service_WebApp::getList($page, $perpage, $search, array(
			$order_by => 'DESC',
			'id'      => 'DESC'
		));
		$this->assign('recmarks', $recmarks);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('types', Common::resetKey($this->types, 'id'));
		$this->assign('order_by', $order_by);
		$this->assign('param', $param);
	}

	public function addAction() {
		$themes = Gionee_Service_WebThemeType::getAll();
		$this->assign('themes', $themes);
		$this->assign('types', $this->types);
	}

	public function add_postAction() {
		$info = $this->getPost(array(
			'name',
			'tag',
			'type_id',
			'theme_id',
			'link',
			'color',
			'img',
			'icon',
			'default_icon',
			'sort',
			'hits',
			'status',
			'sub_time',
			'is_new',
			'is_recommend',
			'is_must',
			'descrip',
			'star'
		));
		$info = $this->_cookData($info);
		//名称转拼音
		$py     = new Util_Pinyin();
		$pinyin = $py->getPinyin($info['name']);
		if ($info['tag']) {
			$info['tag'] = $info['name'] . ',' . $pinyin . ',' . $info['tag'];
		} else {
			$info['tag'] = $info['name'] . ',' . $pinyin;
		}
		Admin_Service_Access::pass('add');
		Admin_Service_Log::op($info);
		$result = Gionee_Service_WebApp::addApp($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$id      = $this->getInput('id');
		$info    = Gionee_Service_WebApp::getApp(intval($id));
		$apptype = Gionee_Service_WebAppType::getAppType($info['type_id']);
		$themes  = Gionee_Service_WebThemeType::getAll();
		$this->assign('name', $apptype['name']);
		$this->assign('themes', $themes);
		$this->assign('info', $info);
		$this->assign('types', $this->types);
	}

	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array(
			'id',
			'name',
			'tag',
			'type_id',
			'theme_id',
			'link',
			'color',
			'img',
			'icon',
			'icon2',
			'default_icon',
			'sort',
			'hits',
			'status',
			'sub_time',
			'is_new',
			'is_recommend',
			'is_must',
			'descrip',
			'star'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_WebApp::updateApp($info, intval($info['id']));
		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		if (!$info['link']) $this->output(-1, '链接地址不能为空.');
		if (!$info['star']) $this->output(-1, '星级请填写1-5之间的数字.');
		if ($info['star'] > 5 || $info['star'] < 1) $this->output(-1, '星级请填写1-5之间的数字.');
		if (!$info['color']) $this->output(-1, '颜色不能为空.');
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['sub_time']) $this->output(-1, '发布时间不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		$info['tag']      = strtolower($info['tag']);
		$info['sub_time'] = strtotime($info['sub_time']);
		return $info;
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id   = $this->getInput('id');
		$info = Gionee_Service_WebApp::getApp($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gionee_Service_WebApp::deleteApp($id);
		Admin_Service_Log::op($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 导出轻应用内容
	 */
	public function exportAction() {
		$filename = 'webapp_' . date('YmdHis') . '.csv';
		list(, $list) = Gionee_Service_WebApp::getAllApps();

		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		foreach (self::$headerrow as $k => $v) {
			self::$headerrow[$k] = iconv('UTF8', 'GBK', self::$headerrow[$k]);
		}

		fputcsv($fp, array_values(self::$headerrow));
		foreach ($list as $fields) {
			$fields['descrip'] = str_replace(',', '，', $fields['descrip']);

			$fields['name']    = iconv('UTF8', 'GBK', $fields['name']);
			$fields['descrip'] = iconv('UTF8', 'GBK', $fields['descrip']);

			$row = array(
				$fields['id'],
				$fields['name'],
				$fields['type_id'],
				$fields['descrip'],
				$fields['link'],
				$fields['img'],
				$fields['star'],
				$fields['hits'],
				$fields['is_new'],
				$fields['is_must'],
				$fields['is_recommend'],
				$fields['status'],
				date('Y-m-d H:i:s', $fields['sub_time']),
				$fields['sort'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	/**
	 * 导入轻应用内容
	 */
	public function importAction() {
		if (!empty($_FILES['webappcsv']['tmp_name'])) {
			$file  = $_FILES["webappcsv"]['tmp_name'];
			$row   = 1;
			$num   = count(self::$headerrow);
			$filed = array_keys(self::$headerrow);
			if (($handle = fopen($file, "r")) !== false) {
				while (($data = fgetcsv($handle, 1000, ",")) !== false) {
					if ($row > 1) {
						$info = array();
						for ($c = 0; $c < $num; $c++) {
							$k        = $filed[$c];
							$info[$k] = $data[$c];
						}

						$info['name']    = iconv('GBK', 'UTF8', $info['name']);
						$info['descrip'] = iconv('GBK', 'UTF8', $info['descrip']);

						$py          = new Util_Pinyin();
						$pinyin      = $py->getPinyin($info['name']);
						$info['tag'] = $info['name'] . ',' . $pinyin;

						if (!empty($info['id']) && !empty($info['name'])) {
							$tmpRow = Gionee_Service_WebApp::getApp($info['id']);
							if (!empty($tmpRow['id'])) {
								$ret = Gionee_Service_WebApp::updateApp($info, $info['id']);
							} else {
								$ret = Gionee_Service_WebApp::addApp($info);
							}
						}
					}
					$row++;

				}
				fclose($handle);
			}

			$this->output(0, '操作成功');

			exit;
		}
	}


	/**
	 * 导入轻应用图标
	 *
	 * @param string type 图标字段名称
	 */
	public function picimportAction() {
		$type = $this->getInput('type');

		$tmp = array();
		if (in_array($type, array('icon', 'icon2'))) {
			$tmpDir = '/tmp/webapppic/';
			if (!is_dir($tmpDir)) {
				mkdir($tmpDir, 0777, true);
			}

			if (!empty($_FILES['res_file'])) {
				$fileInfo = $_FILES['res_file'];
				$zip      = new ZipArchive;
				$zip->open($fileInfo['tmp_name']);
				$zip->extractTo($tmpDir);
				$zip->close();


				$attachPath = Common::getConfig('siteConfig', 'attachPath');
				$tmpPath    = '/App/' . $type . '/' . date('Ym') . '/';
				$savePath   = $attachPath . $tmpPath;
				if (!is_dir($savePath)) {
					mkdir($savePath, 0777, true);
				}

				$files = array_diff(scandir($tmpDir), array('..', '.'));
				foreach ($files as $f) {
					$fn   = basename($f, '.png');
					$info = Gionee_Service_WebApp::getByTitle($fn);
					if (!empty($info['id'])) {
						$tmpName = crc32($info['id']) . '.png';
						$icon    = $tmpPath . $tmpName;
						$newFile = $savePath . $tmpName;
						$up      = $icon;
						if ($icon != $info[$type]) {
							copy($tmpDir . $f, $newFile);
							Gionee_Service_WebApp::updateApp(array($type => $icon), $info['id']);
							$up = $newFile;
						}
						$tmp[] = $f . ' ' . $up;
					} else {
						$tmp[] = $f . ' no info';
					}
				}
			}
		}

		$this->assign('files', $tmp);
		$this->assign('type', $type);
	}

}