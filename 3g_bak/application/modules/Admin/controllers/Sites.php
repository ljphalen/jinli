<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网址大全
 */
class SitesController extends Admin_BaseController {

	public $pageSize = 20;
	public $actions  = array(
		'indexUrl'       => '/Admin/Sites/index',
		'catAddUrl'      => '/Admin/Sites/catAdd',
		'catPostUrl'     => '/Admin/Sites/catPost',
		'cateditUrl'     => '/Admin/Sites/catedit',
		'cateditPostUrl' => '/Admin/Sites/cateditPost',
		'catDeleteUrl'   => '/Admin/Sites/catDelete',
		'uploadUrl'      => '/Admin/Ads/upload',
		'uploadPostUrl'  => '/Admin/Ads/upload_post',
		'contentUrl'     => '/Admin/Sites/content',
		'addContentUrl'  => '/Admin/Sites/addContent',
		'addPostUrl'     => '/Admin/Sites/addPost',
		'ceditUrl'       => '/Admin/Sites/cedit',
		'ceditPostUrl'   => '/Admin/Sites/ceditPost',
		'cdeleteUrl'     => '/Admin/Sites/cdelete',
		'importUrl'      => '/Admin/Sites/import',
		'importPostUrl'  => '/Admin/Sites/importPost',
		'tongjiUrl'      => '/Admin/Sites/tongji',
		'exportUrl'      => '/Admin/Sites/export',
	);


	//--------------------------分类的增删查改-------------------------------------------------------------------------------///

	public function indexAction() {
		$postData = $this->getInput(array('page', 'first_level'));
		$page     = max(1, $postData['page']);
		$params   = array();
		if ($postData['first_level']) {
			$params['parent_id'] = $postData['first_level'];
		}

		$order = array('parent_id' => 'ASC', 'sort' => 'asc', 'id' => "DESC");
		list($total, $dataList) = Gionee_Service_SiteCategory::getList($page, $this->pageSize, $params, $order);
		foreach ($dataList as $k => $v) {
			$dataList[$k]['parent_name'] = '';
			if ($v['parent_id'] > 0) {
				$parent                      = Gionee_Service_SiteCategory::get($v['parent_id']);
				$dataList[$k]['parent_name'] = $parent['name'];
			}
		}
		$topLevel = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => "DESC"));
		$this->assign('topData', $topLevel);
		$this->assign('postData', $postData);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?first_level={$postData['first_level']}&"));
		$this->assign('styles', Gionee_Service_SiteCategory::$styles);
	}

	public function catAddAction() {
		$parent = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => 'DESC'));
		$this->assign('parents_cat', $parent);
		$this->assign('styles', Gionee_Service_SiteCategory::$styles);

	}

	public function catPostAction() {
		$postData             = $this->getInput(array(
			'parent_id',
			'name',
			'status',
			'sort',
			'image',
			'link',
			'is_show',
			'style'
		));
		$postData['add_time'] = time();
		Admin_Service_Log::op($postData);
		$ret = Gionee_Service_SiteCategory::add($postData);
		if ($ret) {
			$this->output('0', '添加成功！');
		} else {
			$this->output('-1', '添加失败！');
		}
	}

	public function cateditAction() {
		$id      = $this->getInput('id');
		$data    = Gionee_Service_SiteCategory::get($id);
		$parents = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => 'DESC'));
		$this->assign('data', $data);
		$this->assign('parents', $parents);
		$this->assign('styles', Gionee_Service_SiteCategory::$styles);
	}

	public function cateditPostAction() {
		$postData = $this->getInput(array(
			'parent_id',
			'name',
			'status',
			'sort',
			'image',
			'link',
			'is_show',
			'id',
			'style'
		));
		$ret      = Gionee_Service_SiteCategory::update($postData['id'], $postData);
		Admin_Service_Log::op($postData);
		if ($ret) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '修改失败！');
		}
	}

	public function catDeleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_SiteCategory::delete($id);
		Admin_Service_Log::op($id);
		if ($res) {
			$this->output('0', '删除成功！');
		} else {
			$this->output('-1', '删除失败！');
		}
	}
	//-----------------------------------------------END----------------------------------------------//

	//------------------------------------------内容－－－－－－－－－－－－－－－//
	public function contentAction() {
		$postData = $this->getInput(array('page', 'first_level', 'second_level'));
		$page     = max(1, $postData['page']);
		$topLevel = $params = array();
		if ($postData['first_level']) {
			$secondLevelData = Gionee_Service_SiteCategory::getsBy(array('parent_id' => $postData['first_level']));
			if ($postData['second_level']) {
				$params['cat_id'] = $postData['second_level'];
			} else {
				$ids = array();
				foreach ($secondLevelData as $k => $v) {
					$ids[] = $v['id'];
				}
				$params['cat_id'] = array('IN', $ids);
			}
		}

		list($total, $dataList) = Gionee_Service_SiteContent::getList($page, $this->pageSize, $params, array('id' => "DESC"));
		foreach ($dataList as $k => $v) {
			$cat                         = Gionee_Service_SiteCategory::get($v['cat_id']);
			$dataList[$k]['cat_name']    = $cat['name'] ? $cat['name'] : '--';
			$parent                      = Gionee_Service_SiteCategory::getBy(array('id' => $cat['parent_id']));
			$dataList[$k]['first_level'] = $parent['name'];
		}
		$topLevel  = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => "DESC"));
		$urlParams = http_build_query(array(
			'first_level'   => $postData['first_level'],
			'seconde_level' => $postData['second_level']
		));
		$this->assign('topData', $topLevel);
		$this->assign('secondData', $secondLevelData);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['contentUrl'] . "?{$urlParams}&"));
		$this->assign('postData', $postData);
	}

	public function addContentAction() {
		$category = Gionee_Service_SiteCategory::getsBy(array('parent_id' => array('=', '0')), array('id' => 'DESC'));

		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($parters, array('id' => '0', 'name' => '普通'));
		$this->assign('parters', $parters);
		$this->assign('category', $category);
	}

	public function addPostAction() {
		$postData = $this->getInput(array(
			'cat_id',
			'name',
			'status',
			'sort',
			'image',
			'link',
			'add_time',
			'start_time',
			'end_time',
			'is_special',
			'parter_id',
			'bid',
			'cp_id',
		));

		if (empty($postData['cat_id'])) $this->output('-1', '请选择分类！');
		list($postData['cp_id'], $postData['link']) = $this->_getCpInfo($postData['parter_id'], $postData['bid'], $postData['cp_id'], $postData['link']);
		$postData['add_time'] = time();
		$ret                  = Gionee_Service_SiteContent::add($postData);
		Admin_Service_Log::op($postData);
		if ($ret) {
			$this->output('0', '添加成功！');
		} else {
			$this->output('-1', '添加失败！');
		}
	}

	public function ceditAction() {
		$id   = $this->getInput('id');
		$data = Gionee_Service_SiteContent::get($id);
		if (!empty($data)) {
			$urlInfo = Gionee_Service_ParterUrl::get($data['cp_id']);
			$this->assign('urlInfo', $urlInfo);
			$blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
			$this->assign('blist', $blist);
			$urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
			$this->assign('urlList', $urlList);
		}
		$category = Gionee_Service_SiteCategory::getsBy(array('status' => 1), array('id' => 'DESC'));
		$this->assign('data', $data);
		$this->assign('category', $category);
		$parters = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($parters, array('id' => '0', 'name' => '普通'));
		$this->assign('parters', $parters);
	}

	public function ceditPostAction() {
		$postData = $this->getInput(array(
			'cat_id',
			'name',
			'status',
			'sort',
			'image',
			'link',
			'start_time',
			'end_time',
			'is_special',
			'id',
			'parter_id',
			'bid',
			'cp_id',
		));
		if (empty($postData['cat_id'])) $this->output('-1', '请选择分类！');
		list($postData['cp_id'], $postData['link']) = $this->_getCpInfo($postData['parter_id'], $postData['bid'], $postData['cp_id'], $postData['link']);
		$ret = Gionee_Service_SiteContent::update($postData['id'], $postData);
		Admin_Service_Log::op($postData);
		if ($ret) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '修改失败！');
		}
	}

	public function cdeleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_SiteContent::delete($id);
		Admin_Service_Log::op($id);
		if ($res) {
			$this->output('0', '删除成功！');
		} else {
			$this->output('-1', '删除失败！');
		}
	}

	//统计信息
	public function tongjiAction() {
		$postData = $this->getInput(array('first_level', 'second_level', 'start_time', 'end_time'));
		!$postData['start_time'] && $postData['start_time'] = date('Y-m-d', strtotime("-7 day"));
		!$postData['end_time'] && $postData['end_time'] = date('Y-m-d', strtotime("today"));
		if ($postData['first_level']) {
			$words = $ids = array();
			if ($postData['second_level']) {
				$contentList = Gionee_Service_SiteContent::getsBy(array('cat_id' => $postData['second_level']));
				foreach ($contentList as $v) {
					$ids[]           = $v['id'];
					$words[$v['id']] = $v['name'];
				}
			} else {
				$subCatList = Gionee_Service_SiteCategory::getsBy(array('parent_id' => $postData['first_level']));
				if ($subCatList) {
					foreach ($subCatList as $m) {
						$contents = Gionee_Service_SiteContent::getsBy(array('cat_id' => $m['id']));
						foreach ($contents as $k) {
							$ids[]           = $k['id'];
							$words[$k['id']] = $k['name'];
						}
					}
				}
			}
			$params         = array();
			$params['ver']  = array('IN', $ids);
			$params['type'] = 'sitemap';
			$params['date'] = array(
				array('>=', date('Ymd', strtotime($postData['start_time']))),
				array('<=', date('Ymd', strtotime($postData['end_time'])))
			);
			$dataList       = Gionee_Service_Log::getsBy($params);
			$data           = $date = array();
			foreach ($dataList as $k => $v) {
				$data[$v['ver']][$v['date']] = $v['val'];
				$data[$v['ver']]['name']     = $words[$v['ver']];
			}

			for ($i = strtotime($postData['start_time']); $i <= strtotime($postData['end_time']); $i += 86400) {
				$date[] = date('Ymd', $i);
			}
		}
		$parents = Gionee_Service_SiteCategory::getsBy(array('parent_id' => 0), array('id' => 'DESC'));
		$this->assign('data', $data);
		$this->assign('date', $date);
		$this->assign('topData', $parents);
		$this->assign('postData', $postData);

	}

	public function ajaxGetSecondLevelDataAction() {
		$parent_id = $this->getInput('parent_id');
		$data      = array();
		if ($parent_id > 0) {
			$data = Gionee_Service_SiteCategory::getsBy(array('parent_id' => $parent_id), array('id' => 'DESC'));
		}
		$this->output(0, '', $data);
	}


	private function _getCpInfo($pid, $bid, $cid, $link) {
		if (intval($pid)) {
			if (empty($bid)) {
				$this->output('-1', '请选择业务！');
			}
			if (empty($cid)) {
				if (empty($link)) {
					$this->output('-1', '请添加正常的链接！');
				}
				$data = array(
					'name'         => '--',
					'pid'          => $pid,
					'bid'          => $bid,
					'url'          => $link,
					'status'       => 1,
					'created_time' => time()
				);
				$cid  = Gionee_Service_ParterUrl::add($data);
				if (!intval($cid)) {
					$this->output('-1', '业务链接添加失败!');
				}
			} else {
				$urlInfo = Gionee_Service_ParterUrl::get($cid);
				$link    = $urlInfo['url'];
			}
		}
		return array($cid, $link);
	}

	//内容上传
	public function importAction() {
		if (!empty($_FILES['category'])) {
			$file = $_FILES['category']['tmp_name'];
			$res  = $this->_importCategoryData($file);
			$this->output('0', '操作成功！');
		} elseif (!empty($_FILES['content'])) {
			$file = $_FILES['content']['tmp_name'];
			$res  = $this->_importContentData($file);
		}
	}

	public function exportAction() {
		$type = $this->getInput('type');
		if ($type == 'category') {
			$this->_exportCategoryData();
			exit();
		} elseif ($type == 'content') {
			$this->_exportContentData();
			exit();
		}
	}

	//上传图片
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = Common::upload('site_cat', 'ads');
		$imgId = $this->getPost('imgId');;
		$this->assign('imgId', $imgId);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	//导入分类数据
	private function _importCategoryData($file = '') {
		$fields = array('id', 'parent_id', 'name', 'status', 'sort', 'is_show', 'style');
		$num    = count($fields);
		$row    = 1;//初始值
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$contents = array();
					for ($i = 0; $i < $num; $i++) {
						$contents[$fields[$i]] = $data[$i];
					}
					$contents['name'] = iconv('GBK', 'UTF8', $contents['name']);
					if (!empty($contents['id']) && !empty($contents['name'])) { //更新
						$meta = Gionee_Service_SiteCategory::get($contents['id']);
						if ($meta) {
							$out = Gionee_Service_SiteCategory::update($contents['id'], $contents);
						} else {
							$out = Gionee_Service_SiteCategory::add($contents);//添加
						}
					} else {
						unset($contents['id']);
						$out = Gionee_Service_SiteCategory::add($contents);//添加
					}
					Common::getCache()->delete("GIONEE:SITE:MAP");//删除缓存
				}
				$row++;
			}
		}
		fclose($handle);
	}

	//导入内容数据
	private function _importContentData($file = '') {
		$field = array('id', 'cat_id', 'name', 'link', 'status', 'sort', 'is_special');
		$num   = count($field);
		$row   = 1;
		if (($handle = fopen($file, 'r')) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$contents = array();
					for ($i = 0; $i < $num; $i++) {
						$contents[$field[$i]] = $data[$i];
					}
					$contents['name']       = iconv('GBK', 'UTF8', $contents['name']);
					$contents['link']       = iconv('GBK', 'UTF8', $contents['link']);
					$contents['add_time']   = time();
					$contents['start_time'] = date('Y-m-d H:i:s', time());
					$contents['end_time']   = date('Y-m-d H:i:s', strtotime(" +2 year"));
					if ($contents['id'] && !empty($contents['name']) && !empty($contents['cat_id'])) {
						$metaData = Gionee_Service_SiteContent::get($contents['id']);
						if ($metaData) {
							$res = Gionee_Service_SiteContent::update($contents['id'], $contents);
						} else {
							$res = Gionee_Service_SiteContent::add($contents);
						}
					} else {
						unset($contents['id']);
						$res = Gionee_Service_SiteContent::add($contents);
					}
					Common::getCache()->delete("GIONEE:SITE:MAP");//删除缓存
				}
				$row++;
			}
		}
		fclose($handle);
	}

	private function _exportCategoryData() {
		$filename = 'site_category' . date('YmdHis') . '.csv';
		$list     = Gionee_Service_SiteCategory::getAll();
		$headers  = array('id', 'parent_id', 'name', 'status', 'sort', 'is_show', 'style');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$row = array(
				$fields['id'],
				$fields['parent_id'],
				$fields['name'] = iconv('UTF8', 'GBK', $fields['name']),
				$fields['status'],
				$fields['sort'],
				$fields['is_show'],
				$fields['style'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	private function _exportContentData() {
		$filename = 'site_content' . date('YmdHis') . '.csv';
		$list     = Gionee_Service_SiteContent::getAll();
		$headers  = array('id', 'cat_id', 'name', 'link', 'status', 'sort', 'is_special');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$row = array(
				$fields['id'],
				$fields['cat_id'],
				iconv('UTF8', 'GBK', $fields['name']),
				$fields['link'],
				$fields['status'],
				$fields['sort'],
				$fields['is_special']
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}
}