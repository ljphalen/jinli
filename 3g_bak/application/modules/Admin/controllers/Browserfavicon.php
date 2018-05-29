<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 浏览器收藏图标
 */
class BrowserfaviconController extends Admin_BaseController {

	public $actions = array(
		'listUrl' => '/Admin/Browserfavicon/list',
		'editUrl' => '/Admin/Browserfavicon/edit',
		'delUrl'  => '/Admin/Browserfavicon/del',
	);

	public $perpage = 20;

	public function listAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
		if (!empty($get['togrid'])) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) > 0) {
					$where[$k] = array('LIKE', "%{$v}%");
				}
			}

			$total = Gionee_Service_BrowserFavicon::getDao()->count($where);
			$start = (max($page, 1) - 1) * $offset;
			$list  = Gionee_Service_BrowserFavicon::getDao()->getList($start, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['img']        = Common::getImgPath() . $v['img'];
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

	}

	public function editAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'title', 'val',));
		$now      = time();
		if (!empty($postData['title'])) {

			$imgInfo = Common::upload('img', 'nav');
			if (!empty($imgInfo['data'])) {
				$postData['img'] = $imgInfo['data'];

			}

			$postData['key'] = md5($postData['val']);
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_BrowserFavicon::getDao()->insert($postData);
			} else {
				$ret = Gionee_Service_BrowserFavicon::getDao()->update($postData, $postData['id']);
			}

			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Gionee_Service_BrowserFavicon::getDao()->get($id);
		$this->assign('info', $info);
	}


	public function delAction() {
		$ids = (array)$this->getInput('id');
		foreach ($ids as $id) {
			$info = Gionee_Service_BrowserFavicon::getDao()->get($id);
			if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
			$result = Gionee_Service_BrowserFavicon::getDao()->delete($id);
			if (!$result) $this->output(-1, '操作失败');
		}
		Admin_Service_Log::op($ids);
		$this->output(0, '操作成功');
	}

	public function upiconAction() {
		$tmp = array();

		$tmpDir = '/tmp/favicon/';
		if (!is_dir($tmpDir)) {
			mkdir($tmpDir, 0777, true);
		}


		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$tmpPath    = '/favicon/' . date('Ym') . '/';
		$savePath   = $attachPath . $tmpPath;
		if (!is_dir($savePath)) {
			mkdir($savePath, 0777, true);
		}

		$tmp = array();
		if (($handle = fopen($tmpDir . "/icon.csv", "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				$name  = $data[0];
				$url   = $data[1];
				$tmp[] = array(
					'title' => $name,
					'url'   => html_entity_decode($url),
				);
			}
			fclose($handle);
		}

		foreach ($tmp as $val) {
			$filename = crc32($val['title']) . '.png';
			$icon     = $tmpDir . '/pic/' . $val['title'] . '.png';
			if (!file_exists($icon)) {
				continue;
			}
			$newFile = $savePath . $filename;
			copy($icon, $newFile);
			if (!file_exists($newFile)) {
				continue;
			}

			$url  = strtolower(parse_url($val['url'], PHP_URL_HOST));
			$data = array(
				'title'      => $val['title'],
				'val'        => $url,
				'key'        => md5($url),
				'img'        => $tmpPath . $filename,
				'created_at' => time(),
			);
			$info = Gionee_Service_BrowserFavicon::getDao()->getBy(array('key' => $data['key']));

			//var_dump($data,$info['id']);
			//echo "<hr>";

			if (!empty($info['id'])) {
				Gionee_Service_BrowserFavicon::getDao()->update($data, $info['id']);
			} else {
				Gionee_Service_BrowserFavicon::getDao()->insert($data);
			}
		}
		exit;
	}

}