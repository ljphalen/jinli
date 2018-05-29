<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化导航管理
 */
class LocalnavController extends Admin_BaseController {
	static $styles  = array(
		'banner'      => '广告条',
		'search'      => '搜索链接',
		'hot_site'    => '热门站点',
		'news_img'    => '新闻图片',
		'news_list'   => '新闻列表',
		'tip_link'    => '提示链接',
		'fun_text'    => '笑话段子',
		'fun_list'    => '笑话短句',
		'gou_img3'    => '购物3列图',
		'img3'        => '三列图片',
		'img4'        => '四列图片',
		'img5'        => '美图展示',
		'words5'      => '五列热词',
		'lottery'     => '彩票',
		'topic'       => '专题列表',
		'comic'       => '漫画模块',
		'share_index' => '股票指数',
		'stock_news'  => '股票新闻',
	);
	public $actions = array(
		'listUrl'       => '/Admin/Localnav/list',
		'listtypeUrl'   => '/Admin/Localnav/listtype',
		'listcolumnUrl' => '/Admin/Localnav/listcolumn',
		'editUrl'       => '/Admin/Localnav/edit',
		'delUrl'        => '/Admin/Localnav/del',
		'edittypeUrl'   => '/Admin/Localnav/edittype',
		'deltypeUrl'    => '/Admin/Localnav/deltype',
		'editcolumnUrl' => '/Admin/Localnav/editcolumn',
		'delcolumnUrl'  => '/Admin/Localnav/delcolumn',
		'exportUrl'     => '/Admin/Localnav/export',
		'importUrl'     => '/Admin/Localnav/import',
		'upcacheUrl'    => '/Admin/Localnav/upcache',
		'optionUrl'     => '/Admin/Localnav/option',
        'cardtitleUrl'  => '/Admin/Localnav/cardtitle',
	);
  
	public $pageSize = '20';

	public $operateType = array(
		'0'	=>'普通',
		'1'	=>'分机型运营',
		'2'	=>'imei精准运营',
	);
	
	
	public function optionAction() {
		$t   = $this->getInput('t');
		$ret = array();
		switch ($t) {
			case 1:
				$types = Gionee_Service_LocalNavType::options();
				$ret   = array(array('value' => '', 'text' => '请选择卡片'));
				foreach ($types as $v) {
					$ret[] = array('value' => $v['id'], 'text' => $v['name']);
				}
				break;
			case 2:
				$id  = $this->getInput('id');
				$ret = array(array('value' => '', 'text' => '请选择栏目'));
				if (!empty($id)) {
					$columns = Gionee_Service_LocalNavColumn::getsBy(array('type_id' => $id));
					foreach ($columns as $v) {
						$ret[] = array('value' => $v['id'], 'text' => $v['name']);
					}
				}
				break;
		}

		echo Common::jsonEncode($ret);
		exit;
	}

	//列表数据--------------------------------------------
	public function listAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
			$page   = max(intval($get['page']), 1);
			$offset = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort   = !empty($get['sort']) ? $get['sort'] : 'id';
			$order  = !empty($get['order']) ? $get['order'] : 'desc';

			if (empty($_POST['filter'])) {
				$orderBy[$sort] = $order;
			} else {
				$orderBy['column_id'] = 'asc';
				$orderBy['sort']      = 'asc';
			}

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					if ($k == 'name') {
						$where['name'] = array('LIKE', $v);
					} else {
						$where[$k] = $v;
					}
				}
			}

			if (empty($where)) {
				$where['is_out'] = 0;
			}

			$total = Gionee_Service_LocalNavList::count($where);
			$list  = Gionee_Service_LocalNavList::getList($page, $offset, $where, $orderBy);

			foreach ($list as $k => $v) {
				$list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
				$list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['status']     = $v['status'] == 1 ? '开启' : '关闭';
				$info                   = Gionee_Service_LocalNavType::get($v['type_id']);
				$list[$k]['type_id']    = $info['name'];
				$info                   = Gionee_Service_LocalNavColumn::get($v['column_id']);
				$list[$k]['column_id']  = $info['name'];
				$list[$k]['is_out']     = $v['is_out'] == 1 ? '接口' : '';
				$list[$k]['img']        = Common::getImgPath() . $v['img'];
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}
		$outOption = array(
			array('value' => '', 'text' => '所有'),
			array('value' => 0, 'text' => '内部'),
			array('value' => 1, 'text' => '接口'),
		);
		$this->assign('outOption', Common::jsonEncode($outOption));
	}

	public function editAction() {
		$id  = $this->getInput('id');
		$pD  = $this->getPost(array(
			'id',
			'name',
			'link',
			'color',
			'start_time',
			'end_time',
			'column_id',
			'sort',
			'status',
			'model_id',
			'label',
			'model_type',
		));
		$now = time();
		if (!empty($pD['name'])) {
			$imgInfo = Common::upload('img', 'nav');
			if (!empty($imgInfo['data'])) {
				$pD['img'] = $imgInfo['data'];
			}
			if (empty($pD['name'])) {
				$this->output(-1, '输入名称');
			}
			
			if (!empty($pD['column_id'])) {
				$tmp           = Gionee_Service_LocalNavColumn::get($pD['column_id']);
				$pD['type_id'] = $tmp['type_id'];

				$ext       = $this->getPost($tmp['style'] . '_ext');
				$pD['ext'] = '';
				if (in_array($tmp['style'], array('fun_list', 'fun_text'))) {
					$pD['ext'] = $ext;
				} else if (in_array($tmp['style'], array('tip_link', 'lottery', 'topic'))) {
					$pD['ext'] = Common::jsonEncode($ext);
				}

				if ($tmp['style'] == 'topic') {
					$topicInfo = Gionee_Service_Topic::getInfo($ext['id']);
					if (empty($topicInfo['id'])) {
						$this->output(-1, '请添加正确的专题ID');
					}
				}
			} else {
				$this->output(-1, '请选择栏目');
			}
			if(!empty($pD['label'])){
				$pD['label_id_list'] = implode(',', $pD['label']);
			}
			unset($pD['label']);
			$pD['start_time'] = strtotime($pD['start_time']);
			$pD['end_time']   = strtotime($pD['end_time']);
			$pD['updated_at'] = $now;

			if (empty($pD['id'])) {
				unset($pD['label_id']);
				unset($pD['label_type']);
				Admin_Service_Access::pass('add');
				$pD['created_at'] = $now;
				$ret              = Gionee_Service_LocalNavList::add($pD);
			} else {
				Admin_Service_Access::pass('edit');
				$ret = Gionee_Service_LocalNavList::set($pD, $pD['id']);
			}
			Admin_Service_Log::op($pD);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$lotteryType = Gionee_Service_Config::getValue('LOTTERY_TYPE_DATA');
		$lotteryType = !empty($lotteryType) ? json_decode($lotteryType, true) : array();

		$info = Gionee_Service_LocalNavList::get($id);
		$this->assign('operateType', $this->operateType);
		$this->assign('models', Gionee_Service_ModelContent::getModelData());
		$this->assign('imeiList', Gionee_Service_Label::getLabelImeiDataDao()->getAll());
		$types   = Gionee_Service_LocalNavType::options();
		$columns = Gionee_Service_LocalNavColumn::options();
		$style   = '';
		if ($info['column_id']) {
			$tmp   = Gionee_Service_LocalNavColumn::get($info['column_id']);
			$style = $tmp['style'];
		}

		$this->assign('lotteryType', $lotteryType);
		$this->assign('types', $types);
		$this->assign('columns', $columns);
		$this->assign('info', $info);
		$this->assign('style', $style);
		$this->assign('id', $id);
	}

	public function delAction() {
		Admin_Service_Access::pass('del');
		$idArr = (array)$this->getInput('id');
		$i     = 0;
		$succ  = array();
		foreach ($idArr as $id) {
			$ret = Gionee_Service_LocalNavList::del($id);
			if ($ret) {
				$i++;
				$succ[] = $id;
			}
		}

		Admin_Service_Log::op($succ);
		if ($i == count($succ)) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败', $succ);
		}
	}


	public function ajaxGetLabelDataAction(){
		$id = $this->getInput('id');
		$labelIds = array();
		if(intval($id)){
			$info = Gionee_Service_LocalNavList::get($id);
			$labelIds = explode(',', $info['label_id_list']);
		}
		$data = Gionee_Service_Label::getSortedLabelData($labelIds);
		exit(json_encode($data));
	}
	
	//分类数据--------------------------------------------
	public function listtypeAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {

			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			$where          = array('flag' => 0);

			if (!isset($orderBy['id'])) {
				$orderBy['id'] = 'ASC';
			}

			list($total, $list) = Gionee_Service_LocalNavType::getList($page, $offset, $where, $orderBy);
			$types = array(1 => 'A', 2 => 'B', 3 => 'C');
			foreach ($list as $k => $v) {
				$list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
				$list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['status']     = $v['status'] == 1 ? '开启' : '关闭';
				$list[$k]['is_show']    = $v['is_show'] == 1 ? '是' : '否';
				$list[$k]['type']       = $types[$v['type']];
				$list[$k]['img']        = Common::getImgPath() . $v['img'];
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function edittypeAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array(
			'id',
			'name',
			'desc',
			'type',
			'status',
			'is_show',
			'sort',
			'start_time',
			'end_time',
		));
		$now      = time();
		if (!empty($postData['name'])) {

			$imgInfo = Common::upload('img', 'nav');
			if (!empty($imgInfo['data'])) {
				$postData['img'] = $imgInfo['data'];
			}

			$postData['start_time'] = strtotime($postData['start_time']);
			$postData['end_time']   = strtotime($postData['end_time']);
			$postData['updated_at'] = $now;
			$postData['flag']       = 0;
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_LocalNavType::add($postData);
			} else {
				$ret = Gionee_Service_LocalNavType::set($postData, $postData['id']);
			}

			Admin_Service_Log::op($postData);
			Gionee_Service_Config::setValue('LOCALNAV_TYPE_VER', Common::getTime());
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$modelList = Gionee_Service_ModelContent::getsBy(array(), array('id' => 'DESC'));
		$this->assign('modelList', $modelList);
		$info = Gionee_Service_LocalNavType::get($id);


		$this->assign('info', $info);
	}


	public function upcacheAction() {
		Gionee_Service_LocalNavList::banner(true);
		Gionee_Service_LocalNavList::hotsite(true);

		$out = Gionee_Service_LocalNavList::make_appcache_file();
		$this->output(0, str_replace("\n", '<hr>', $out));
	}

	public function deltypeAction() {
		$idArr = (array)$this->getInput('id');
		foreach ($idArr as $id) {
			$data = array('flag' => 1, 'updated_at' => time());
			$ret  = Gionee_Service_LocalNavType::set($data, $id);
		}

		Admin_Service_Log::op($idArr);
		if ($ret) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败');
		}
	}


	//栏目数据--------------------------------------------

	public function listcolumnAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			if (!isset($orderBy['sort'])) {
				$orderBy['sort'] = 'ASC';
			}
			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					$where[$k] = $v;
				}
			}
			list($total, $list) = Gionee_Service_LocalNavColumn::getList($page, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['status']     = $v['status'] == 1 ? '开启' : '关闭';
				$info                   = Gionee_Service_LocalNavType::get($v['type_id']);
				$list[$k]['type_id']    = $info['name'];
				$list[$k]['style']      = self::$styles[$v['style']];
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function editcolumnAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'name', 'style', 'type_id', 'sort', 'status'));
		$now      = time();
		if (!empty($postData['name'])) {
			$postData['updated_at'] = $now;
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_LocalNavColumn::add($postData);
			} else {
				$ret = Gionee_Service_LocalNavColumn::set($postData, $postData['id']);
			}
			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info  = Gionee_Service_LocalNavColumn::get($id);
		$cates = Gionee_Service_LocalNavType::options();
		$this->assign('styles', self::$styles);
		$this->assign('cates', $cates);
		$this->assign('info', $info);
	}

	public function delcolumnAction() {
		$idArr = (array)$this->getInput('id');
		Admin_Service_Log::op($idArr);
		foreach ($idArr as $id) {
			if ($id < 125) {
				$this->output(-1, '操作失败');
			}
			$ret = Gionee_Service_LocalNavColumn::del($id);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}


	}

	public function importAction() {

		foreach ($_FILES['localnav']['tmp_name'] as $key => $file) {
			$func = '_import_' . $key;
			$this->$func($file);
			$this->output(0, '操作成功');
		}


		$types = Gionee_Service_LocalNavType::getsBy();
		$this->assign('types', $types);

	}

	public function exportAction() {
		$type = $this->getInput('type');
		$func = '_export_' . $type;
		$this->$func();
		exit;
	}

	public function uploadImgAction() {
		$ret = Common::upload('img', 'nav');
		$this->output($ret);
	}

	private function _import_type($file) {
		$headersType = array('id', 'name', 'desc', 'sort', 'status');
		$row         = 1;
		$num         = count($headersType);
		$filed       = $headersType;
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}

					$info['name'] = iconv('GBK', 'UTF8', $info['name']);
					$info['desc'] = iconv('GBK', 'UTF8', $info['desc']);

					if (!empty($info['id']) && !empty($info['name']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_LocalNavType::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_LocalNavType::set($info, $info['id']);
						} else {
							$ret = Gionee_Service_LocalNavType::add($info);
						}
					}
				}
				$row++;
			}
			fclose($handle);
		}
	}

	private function _import_column($file) {
		$headersColumn = array('id', 'name', 'style', 'type_id', 'sort', 'status');
		$row           = 1;
		$num           = count($headersColumn);
		$filed         = $headersColumn;
		$styles        = array_flip(self::$styles);
		$succ          = array();
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
					$typeInfo        = Gionee_Service_LocalNavType::getBy(array('name' => $info['type_id']), array());
					$info['type_id'] = $typeInfo['id'];

					if (!empty($info['id']) && !empty($info['name']) && !empty($info['type_id']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_LocalNavColumn::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_LocalNavColumn::set($info, $info['id']);
						} else {
							$ret = Gionee_Service_LocalNavColumn::add($info);
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

	private function _import_list($file) {
		$headersList = array('id', 'name', 'column_id', 'type_id', 'sort', 'link', 'status', 'ext');
		$row         = 1;
		$num         = count($headersList);
		$filed       = $headersList;
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$info = array();
					for ($c = 0; $c < $num; $c++) {
						$k        = $filed[$c];
						$info[$k] = $data[$c];
					}

					$info['name']       = iconv('GBK', 'UTF8', $info['name']);
					$column_id          = iconv('GBK', 'UTF8', $info['column_id']);
					$columnInfo         = Gionee_Service_LocalNavColumn::getBy(array('name' => $column_id));
					$info['column_id']  = $columnInfo['id'];
					$info['type_id']    = $columnInfo['type_id'];
					$info['status']     = 1;
					$info['start_time'] = time() - 86400;
					$info['end_time']   = time() + 86400000;
					$info['ext']        = iconv('GBK', 'UTF8', $info['ext']);
					if (!empty($info['id']) && !empty($info['name']) && !empty($info['column_id']) && isset($info['status'])) {
						$tmpRow = Gionee_Service_LocalNavList::get($info['id']);
						if (!empty($tmpRow['id'])) {
							$ret = Gionee_Service_LocalNavList::set($info, $info['id']);
						} else {
							$ret = Gionee_Service_LocalNavList::add($info);
						}
					}
				}
				$row++;

			}
			fclose($handle);
		}
	}

	private function _export_type() {
		$filename = 'localnav_type_' . date('YmdHis') . '.csv';
		$list     = Gionee_Service_LocalNavType::getsBy();
		//$headers = array('id'=>'ID', 'name'=>'名称', 'sort'=>'排序', 'page_id'=>'页面', 'status'=>'状态');
		$headersType = array('id', 'name', 'desc', 'sort', 'status');
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headersType);
		foreach ($list as $fields) {
			$fields['name'] = iconv('UTF8', 'GBK', $fields['name']);
			$fields['desc'] = iconv('UTF8', 'GBK', $fields['desc']);
			$row            = array(
				$fields['id'],
				$fields['name'],
				$fields['desc'],
				$fields['sort'],
				$fields['status'],
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	private function _export_column() {
		$headersColumn = array('id', 'name', 'style', 'type_id', 'sort', 'status');
		$filename      = 'localnav_column_' . date('YmdHis') . '.csv';
		$list          = Gionee_Service_LocalNavColumn::options();
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headersColumn);
		foreach ($list as $fields) {
			$fields['name']  = iconv('UTF8', 'GBK', $fields['name']);
			$style           = Gionee_Service_NgColumn::$styles[$fields['style']];
			$fields['style'] = iconv('UTF8', 'GBK', $style);
			$typeInfo        = Gionee_Service_LocalNavType::get($fields['type_id']);
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

	private function _export_list() {
		$headersList = array('id', 'name', 'column_id', 'type_id', 'sort', 'link', 'status', 'ext');
		$orderBy     = array('type_id' => 'asc', 'sort' => 'asc', 'id' => 'desc');
		$typeId      = $this->getInput('type_id');
		$where       = array('is_out' => 0, 'type_id' => $typeId);
		$list        = Gionee_Service_LocalNavList::getsBy($where, $orderBy);
		$filename    = 'localnav_list_' . date('YmdHis') . '.csv';
		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		fputcsv($fp, $headersList);
		foreach ($list as $fields) {
			$fields['name'] = iconv('UTF8', 'GBK', $fields['name']);
			$typeInfo       = Gionee_Service_LocalNavType::get($fields['type_id']);
			$type           = iconv('UTF8', 'GBK', $typeInfo['name']);
			$columnInfo     = Gionee_Service_LocalNavColumn::get($fields['column_id']);
			$column         = iconv('UTF8', 'GBK', $columnInfo['name']);
			$ext            = iconv('UTF8', 'GBK', $fields['ext']);
			$row            = array(
				$fields['id'],
				$fields['name'],
				$column,
				$type,
				$fields['sort'],
				$fields['link'],
				$fields['status'],
				$ext,
			);
			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

    public function cardtitleAction(){
        $keys = array('text', 'textcolor');
        if (!empty($_POST['text'])) {
            $tmp = array();
            foreach ($keys as $k) {
                $tmp[$k]=$_POST[$k];
            }
            Gionee_Service_Config::setValue('LocalnavCardTitleConf', json_encode($tmp));
            $this->output(0, '操作成功');
            exit;
        }
        $ret  = Gionee_Service_Config::getValue('LocalnavCardTitleConf');
        $info = json_decode($ret, true);
        $this->assign('info', $info);

    }

}