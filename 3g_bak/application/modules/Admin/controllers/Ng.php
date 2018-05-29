<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 导航管理
 * @author tiger
 */
class NgController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Ng/index',
		'editUrl'       => '/Admin/Ng/edit',
		'delUrl'        => '/Admin/Ng/delete',
		'editPostUrl'   => '/Admin/Ng/edit_post',
		'deleteUrl'     => '/Admin/Ng/delete',
		'sortPostUrl'   => '/Admin/Ng/sort_post',
		'uploadUrl'     => '/Admin/Ng/upload',
		'uploadPostUrl' => '/Admin/Ng/upload_post',
		'searchUrl'     => '/Admin/Ng/search',
		'searchPostUrl' => '/Admin/Ng/search_post',
		'exportUrl'     => '/Admin/Ng/export',
		'importUrl'     => '/Admin/Ng/import',
		'cleanUrl'      => '/Admin/Ng/cleanCache',
	);

	public $operateTypes = array(
		'0' => '普通',
		'1' => '分机型运营',
		'2' => '精准运营'
	);

	public $perpage = 20;

	public function indexAction() {


		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
			$imgPath         = Common::getImgPath();
			$page            = max(intval($get['page']), 1);
			$offset          = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort            = !empty($get['sort']) ? $get['sort'] : 'id';
			$order           = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort]  = $order;
			$orderBy['sort'] = 'asc';
			$where           = array();

			//$orderBy = array('type_id' => 'ASC', 'column_id' => 'ASC', 'sort' => 'ASC', 'id' => 'ASC');
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					if ($k == 'title') {
						$where['title'] = array('LIKE', $v);
					} else if ($k == 'partner') {
						$where[$k] = array('LIKE', $v);
					} else if ($k == 'start_time') {
						$where['start_time'] = array('>=', strtotime($v . ' 00:00:00'));
					} else if ($k == 'end_time') {
						$where['end_time'] = array('<=', strtotime($v . ' 23:59:59'));
					} else {
						$where[$k] = $v;
					}
				}
			}

			if (!empty($where['type_id'])) {
				$orderBy = array('sort' => 'asc', 'id' => 'desc');
			}

			list($total, $list) = Gionee_Service_Ng::getList($page, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
				$list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
				$list[$k]['status']     = $v['status'] == 1 ? '开启' : '关闭';
				$info                   = Gionee_Service_NgType::get($v['type_id']);
				$list[$k]['type_id']    = $info['name'];
				$info                   = Gionee_Service_NgColumn::get($v['column_id']);
				$list[$k]['column_id']  = $info['name'];
				if (strpos($v['img'], 'http') !== false) {
					$img = $v['img'];
				} else {
					$img = $imgPath . $v['img'];
				}
				$list[$k]['img']          = $img;
				$ext                      = json_decode($v['ext'], true);
				$list[$k]['ptype']        = !empty($ext['ptype']) && isset(Gionee_Service_Ng::$pageType[$ext['ptype']]) ? Gionee_Service_Ng::$pageType[$ext['ptype']] : '普通';
				$list[$k]['created_time'] = date('y/m/d H:i', $v['created_time']);
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}


		$types   = Gionee_Service_NgType::getAll();
		$columns = Gionee_Service_NgColumn::getAll();

		$this->assign('types', common::resetKey($types, 'id'));
		$this->assign('columns', common::resetKey($columns, 'id'));
	}


	public function editAction() {
		$id = $this->getInput('id');
		$this->edit_post();
		$info       = Gionee_Service_Ng::get(intval($id));
		$typeNumber = 1;
		if (!empty($info)) {
			$urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
			$this->assign('urlInfo', $urlInfo);
			$blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
			$this->assign('blist', $blist);
			$urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
			$this->assign('urlList', $urlList);
			$labelIdList = explode(',', $info['label_id_list']); //标签内容
			Gionee_Service_Label::getSortedLabelData($labelIdList);
		}

		if (empty($info['style'])) $info['style'] = '普通';
		if (empty($info['partner'])) $info['partner'] = '普通';
		$ext     = json_decode($info['ext']);
		$types   = Gionee_Service_NgType::getAll();
		$columns = Gionee_Service_NgColumn::getAll();

		//合作商信息
		$cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
		//属性信息
		$attributes = Gionee_Service_Attribute::getAll(array('id' => "DESC"));
		array_unshift($attributes, array('id' => '-1', 'name' => '普通'));
		//彩票类型
		$lotteryType = Gionee_Service_Config::getValue('LOTTERY_TYPE_DATA');
		$lotteryType = !empty($lotteryType) ? json_decode($lotteryType, true) : array();
		//新闻来源
		$where          = array('status' => 1, 'group' => 'news');
		$newsSourceList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));

		//机型信息
		$modelList = Gionee_Service_ModelContent::getsBy(array(), array('id' => 'DESC'));
		$this->assign('modelList', $modelList);

		//标签管理
		$levelTypes = Gionee_Service_Label::getsBy(array('parent_id' => 0, 'status' => 1), array('id' => 'DESC'));

		//Imei 管理
		$imeiList = Gionee_Service_Label::getLabelImeiDataDao()->getAll();
		$this->assign('imeiList', $imeiList);
		$this->assign('levelTypes', $levelTypes);
		$this->assign('typeNumber', $typeNumber);

		$this->assign('newsSourceList', $newsSourceList);
		$this->assign('lotteryType', $lotteryType);
		$this->assign('cooperators', $cooperators);
		$this->assign('attributes', $attributes);
		$this->assign('types', $types);
		$this->assign('columns', $columns);
		$this->assign('info', $info);
		$this->assign('ext', $ext);
		$this->assign('id', $id);
		$this->assign('pageType', Gionee_Service_Ng::$pageType);
		$this->assign('operateTypes', $this->operateTypes);
	}

	public function edit_post() {
		$preUrl = urldecode(Util_Cookie::get('pre_edit_url'));
		$info   = $this->getPost(array(
			'id',
			'title',
			'partner',
			'color',
			'link',
			'img',
			'sort',
			'status',
			'is_interface',
			'type_id',
			'column_id',
			'style',
			'create_time',
			'start_time',
			'end_time',
			'reverse_img1',
			'reverse_img2',
			'boardcast',
			'pageType',
			'newsStyle',
			'more',
			'ext',
			'imgswitchExt',
			'newsSourceExt',
			'is_news_ad',
			'checkType',
			'price',
			'model_type',
			'model_id',
			'parter_id',
			'bid',
			'cp_id',
			'level',
			'label',
			'topicDesc'
		));
		if (empty($info['title'])) {
			return false;
		}

		$imgInfo = Common::upload('img', 'nav');
		if (!empty($imgInfo['data'])) {
			$info['img'] = $imgInfo['data'];
		}

		$info['title']    = $_POST['title'];
		$info['model_id'] = $info['model_id'] ? $info['model_id'] : 0;
		if ($info['partner'] == '普通') {
			$info['partner'] = '';
		}
		if ($info['style'] == '普通') {
			$info['style'] = '';
		}

		//CP合作商URL
		if (intval($info['parter_id'])) {
			if (empty($info['bid'])) {
				$this->output('-1', '请选择业务！');
			}
			if (empty($info['cp_id'])) {
				if (empty($info['link'])) {
					$this->output('-1', '请添加正常的链接！');
				}
				$data = array(
					'name'         => '--',
					'pid'          => $info['parter_id'],
					'bid'          => $info['bid'],
					'url'          => $info['link'],
					'status'       => 1,
					'created_time' => time()
				);
				$id   = Gionee_Service_ParterUrl::add($data);
				if (!intval($id)) {
					$this->output('-1', '业务链接添加失败!');
				}
				$info['cp_id'] = $id;
			} else {
				$urlInfo      = Gionee_Service_ParterUrl::get($info['cp_id']);
				$info['link'] = $urlInfo['url'];
			}
		}
		/* $ids = array();
		//标签
		if (!empty($info['label_id'])) {
			foreach ($info['label_id'] as $k => $v) {
				foreach ($v as $m => $n) {
					$ids[] = $n;
				}
			}
		} elseif (!empty($info['label_type'])) {
			foreach ($info['label_type'] as $k => $v) {
				if (intval($v)) {
					$ids[] = $v;
				}
			}
		} */
	
		$info['label_id_list'] = implode(',', $info['label']);
		$info                  = $this->_cookData($info);
		if (!empty($info['id'])) {
			Admin_Service_Access::pass('edit');
			$ret = Gionee_Service_Ng::update($info, intval($info['id']));
		} else {
			Admin_Service_Access::pass('add');
			$ret = Gionee_Service_Ng::add($info);
		}

		Admin_Service_Log::op($info);
		if (!$ret) $this->output(-1, '操作失败');
		Gionee_Service_Ng::updataVersion();
		Gionee_Service_Ng::cleanNgTypeData($info['type_id']);
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if ($info['pageType'] == '3' && !$info['ext']) $this->output(-1, '彩票类型不能为空');
		if ($info['pageType'] == '4' && !$info['imgswitchExt']) $this->output(-1, '列名不能为空');
		if ($info['pageType'] == '5' && !$info['newsSourceExt']) $this->output(-1, '新闻来源不能为空');
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		/* if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		} */
		if (!$info['sort']) $this->output(-1, '排序不能为空和0.');
		if (!$info['type_id']) $this->output(-1, '分类不能为空.');
		if (!$info['column_id']) $this->output(-1, '栏目不能为空.');
		if ($info['partner'] > 0 && empty($info['price'])) $this->output(-1, 'cp单价不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']);
		if ($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		$info['create_time'] = time();

		switch ($info['pageType']) {
			case '1':
				break;
			case '2':
				$data = array(
					'ptype'     => $info['pageType'],
					'img1'      => $info['reverse_img1'],
					'img2'      => $info['reverse_img2'],
					'newsStyle' => $info['newsStyle']
				);
				if ($info['more']) {
					$data['more'] = $info['more'];
				}
				if ($info['boardcast']) {
					$data['boardcast'] = $info['boardcast'];
				}
				break;
			case '3':
				$data = array(
					'ptype'       => $info['pageType'],
					'lotteryType' => $info['ext']
				);
				break;
			case '4':
				$data = array(
					'ptype'      => $info['pageType'],
					'switchName' => $info['imgswitchExt']
				);
				break;
			case '5':
				$data = array(
					'ptype'        => $info['pageType'],
					'newsSourceId' => $info['newsSourceExt'],
					'is_ad'        => $info['is_news_ad']
				);
				break;
			case '6':
				$data = array(
					'ptype'     => $info['pageType'],
					'topicDesc' => $info['topicDesc'],
				);
				break;
			default:
				break;
		}
		$info['ext'] = json_encode($data);

		return $info;
	}

	public function ajaxImeiDataListAction() {
		$data = Gionee_Service_Label::getLabelImeiDataDao()->getAll();
		$this->output('0', '', $data);
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$ids = (array)$this->getInput('id');
		foreach ($ids as $id) {
			$info = Gionee_Service_Ng::get($id);
			if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
			Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
			$result = Gionee_Service_Ng::delete($id);
			if (!$result) $this->output(-1, '操作失败');
		}
		Admin_Service_Log::op($ids);

		Gionee_Service_Ng::updataVersion();
		Gionee_Service_Ng::cleanNgTypeData($info['type_id']);
		$this->output(0, '操作成功');
	}

	public function ajaxGetAllLabelDataAction(){
		$labelIds = array();
		$id = $this->getInput('id');
		if(intval($id)){
				$ngData = Gionee_Service_Ng::get($id);
				$labelIds = explode(',', $ngData['label_id_list']);
		}
		//var_dump($labelIds);
		$data = Gionee_Service_Label::getSortedLabelData($labelIds);
		//$this->output('0','',$data);
		exit(json_encode($data));
	}
	
	public function sort_postAction() {
		$info = $this->getPost(array('sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '请选中需要修改的记录');
		foreach ($info['ids'] as $key => $value) {
			$result = Gionee_Service_Ng::updateBy(array('sort' => $info['sort'][$key]), array('id' => $value));
			if (!$result) $this->output(-1, '操作失败');
			$last = Gionee_Service_Ng::get($value);

		}
		Gionee_Service_Ng::cleanNgTypeData($last['type_id']);
		Gionee_Service_Ng::updataVersion();
		$this->output(0, '操作成功.');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'ng');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}


	public function cleanCacheAction() {
		Gionee_Service_Ng::getIndexData(true);
		$this->output(0, '操作成功.');
	}

	private function _ngtypecsv($file) {
		$ngtypeheader = array('id', 'name', 'description', 'sort', 'page_id', 'color', 'desc_color', 'status');
		$row          = 1;
		$num          = count($ngtypeheader);
		$filed        = $ngtypeheader;
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}

					$info['name']        = iconv('GBK', 'UTF8', $info['name']);
					$info['description'] = iconv('GBK', 'UTF8', $info['description']);

					if (!empty($info['id']) && !empty($info['name']) && isset($info['page_id']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_NgType::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_NgType::update($info, $info['id']);
						} else {
							$ret = Gionee_Service_NgType::add($info);
						}

						Gionee_Service_Ng::cleanNgTypeData($info['id']);
						$verKey = Gionee_Service_Ng::KEY_NG_TYPE_VER . $info['id'];
						Common::getCache()->delete($verKey);
						Gionee_Service_Ng::updataVersion();
					}
				}
				$row++;

			}
			fclose($handle);
		}
	}


	private function _ngcolumncsv($file) {
		$ngcolumnheader = array('id', 'name', 'style', 'type_id', 'sort', 'status');
		$row            = 1;
		$num            = count($ngcolumnheader);
		$filed          = $ngcolumnheader;
		$styles         = Gionee_Service_NgColumn::$styles;
		$styles         = array_flip($styles);
		$succ           = array();
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}

					$info['name']    = iconv('GBK', 'UTF8', $info['name']);
					$info['style']   = iconv('GBK', 'UTF8', $info['style']);
					$info['type_id'] = iconv('GBK', 'UTF8', $info['type_id']);
					$info['style']   = $styles[$info['style']];
					$typeInfo        = Gionee_Service_NgType::getBy(array('name' => $info['type_id']), array());
					$info['type_id'] = $typeInfo['id'];

					if (!empty($info['id']) && !empty($info['name']) && !empty($info['type_id']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_NgColumn::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_NgColumn::update($info, $info['id']);
						} else {
							$ret = Gionee_Service_NgColumn::add($info);
						}
						if ($ret) {
							$succ[] = $info['id'];
						}
					}
				}
				$row++;
			}
			fclose($handle);
		}
		return $succ;
	}

	private function _ngcsv($file) {
		$ngheader = array('id', 'title', 'column_id', 'type_id', 'sort', 'link', 'status', 'label_id_list');
		$row      = 1;
		$num      = count($ngheader);
		$filed    = $ngheader;
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}

					$info['title']      = iconv('GBK', 'UTF8', $info['title']);
					$column_id          = iconv('GBK', 'UTF8', $info['column_id']);
					$columnInfo         = Gionee_Service_NgColumn::getBy(array('name' => $column_id));
					$info['column_id']  = $columnInfo['id'];
					$info['type_id']    = $columnInfo['type_id'];
					$info['status']     = 1;
					$info['start_time'] = time() - 86400;
					$info['end_time']   = time() + 86400000;
					if (!empty($info['id']) && !empty($info['title']) && !empty($info['column_id']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_Ng::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_Ng::update($info, $info['id']);
						} else {
							$ret = Gionee_Service_Ng::add($info);
						}
					}
				}
				$row++;

			}
			fclose($handle);
		}
	}


	public function importAction() {

		if (!empty($_FILES['ngtypecsv']['tmp_name'])) {
			$file = $_FILES["ngtypecsv"]['tmp_name'];
			$this->_ngtypecsv($file);
			$this->output(0, '操作成功');
			exit;
		} else if (!empty($_FILES['ngcolumncsv']['tmp_name'])) {
			$file = $_FILES["ngcolumncsv"]['tmp_name'];
			$succ = $this->_ngcolumncsv($file);
			$this->output(0, '操作成功' . implode(',', $succ));
			exit;
		} else if (!empty($_FILES['ngcsv']['tmp_name'])) {
			$file = $_FILES["ngcsv"]['tmp_name'];
			$this->_ngcsv($file);
			$this->output(0, '操作成功');
			exit;
		}
	}

	private function _ngtypecsvExport() {
		$filename = 'ng1_type_' . date('YmdHis') . '.csv';
		$list     = Gionee_Service_NgType::getAll();
		//$headers = array('id'=>'ID', 'name'=>'名称', 'sort'=>'排序', 'page_id'=>'页面', 'status'=>'状态');
		$headers = array('id', 'name', 'description', 'sort', 'page_id', 'color', 'desc_color', 'status');
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$fields['name']        = iconv('UTF8', 'GBK', $fields['name']);
			$fields['description'] = iconv('UTF8', 'GBK', $fields['description']);
			$row                   = array(
				$fields['id'],
				$fields['name'],
				$fields['description'],
				$fields['sort'],
				$fields['page_id'],
				$fields['color'],
				$fields['desc_color'],
				$fields['status'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	private function _ngcolumncsvExport() {
		$headers  = array('id', 'name', 'style', 'type_id', 'sort', 'status');
		$filename = 'ng2_column' . date('YmdHis') . '.csv';
		$order    = array('type_id' => 'asc', 'sort' => 'asc', 'id' => 'desc');
		$list     = Gionee_Service_NgColumn::getsBy(array(), $order);
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$fields['name']  = iconv('UTF8', 'GBK', $fields['name']);
			$style           = Gionee_Service_NgColumn::$styles[$fields['style']];
			$fields['style'] = iconv('UTF8', 'GBK', $style);
			$typeInfo        = Gionee_Service_NgType::get($fields['type_id']);
			$type            = iconv('UTF8', 'GBK', $typeInfo['name']);
			$row             = array(
				$fields['id'],
				$fields['name'],
				$fields['style'],
				$type,
				$fields['sort'],
				$fields['status'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	private function _ngcsvExport() {
		$headers  = array('id', 'title', 'column_id', 'type_id', 'sort', 'link', 'status', 'label_id_list');
		$orderBy  = array('type_id' => 'asc', 'sort' => 'asc', 'id' => 'desc');
		$list     = Gionee_Service_Ng::getsBy(array('status' => 1), $orderBy);
		$filename = 'ng3_record' . date('YmdHis') . '.csv';
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headers);
		foreach ($list as $fields) {
			$fields['title'] = iconv('UTF8', 'GBK', $fields['title']);
			$typeInfo        = Gionee_Service_NgType::get($fields['type_id']);
			$type            = iconv('UTF8', 'GBK', $typeInfo['name']);
			$columnInfo      = Gionee_Service_NgColumn::get($fields['column_id']);
			$column          = iconv('UTF8', 'GBK', $columnInfo['name']);
			$row             = array(
				$fields['id'],
				$fields['title'],
				$column,
				$type,
				$fields['sort'],
				html_entity_decode($fields['link']),
				$fields['status'],
				$fields['label_id_list'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	public function exportAction() {
		$type = $this->getInput('type');
		if ($type == 'ngtypecsv') {
			$this->_ngtypecsvExport();
			exit;
		} else if ($type == 'ngcolumncsv') {
			$this->_ngcolumncsvExport();
			exit;
		} else if ($type == 'ngcsv') {
			$this->_ngcsvExport();
			exit;
		}
	}

	/**
	 * 导入导航图标
	 *
	 * @param string type 图标字段名称
	 */
	public function picimportAction() {
		$tmp = array();

		$tmpDir = '/tmp/ngpic/';
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
			$tmpPath    = '/ng/' . date('Ym') . '/';
			$savePath   = $attachPath . $tmpPath;
			if (!is_dir($savePath)) {
				mkdir($savePath, 0777, true);
			}

			$files = array_diff(scandir($tmpDir), array('..', '.'));
			foreach ($files as $f) {
				$fn   = basename($f, '.png');
				$info = Gionee_Service_Ng::getBy(array('title' => $fn));
				if (!empty($info['id'])) {
					$tmpName = crc32($info['id']) . '.png';
					$icon    = $tmpPath . $tmpName;
					$newFile = $savePath . $tmpName;
					$up      = $icon;
					if ($icon != $info['img']) {
						copy($tmpDir . $f, $newFile);
						Gionee_Service_Ng::update(array('img' => $icon), $info['id']);
						$up = $newFile;
					}
					$tmp[] = $f . ' ' . $up;
				} else {
					$tmp[] = $f . ' no info';
				}
			}
		}

		$this->assign('files', $tmp);
	}


	/**
	 *ajax 动态获取分机型信息
	 */
	public function ajaxGetModelInfoAction() {
		$dataList = Gionee_Service_ModelContent::getsBy(array(), array('id' => 'DESC'));
		$this->output('0', '', $dataList);
	}
}