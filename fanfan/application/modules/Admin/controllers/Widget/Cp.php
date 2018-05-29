<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Widget_CpController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Widget_Cp/index',
		'addUrl'        => '/Admin/Widget_Cp/add',
		'addPostUrl'    => '/Admin/Widget_Cp/add_post',
		'editUrl'       => '/Admin/Widget_Cp/edit',
		'editPostUrl'   => '/Admin/Widget_Cp/edit_post',
		'parseUrl'      => '/Admin/Widget_Cp/parse',

		'deleteUrl'     => '/Admin/Widget_Cp/delete',
		'manageUrl'     => '/Admin/Widget_Cp/manage',
		'verifyUrl'     => '/Admin/Widget_Cp/verifymd5',
		'clientListUrl' => '/Admin/Widget_Cp/clientList',
		'clientDelUrl'  => '/Admin/Widget_Cp/clientDel',
		'clientEditUrl' => '/Admin/Widget_Cp/clientEdit',
		'clientPostUrl' => '/Admin/Widget_Cp/clientPost',

		'exportUrl'     => '/Admin/Widget_Cp/export',
		'importUrl'     => '/Admin/Widget_Cp/import',
	);

	public $perpage = 20;

	static $headerrow = array(
		'id'     => 'ID',
		'title'  => 'CP接口名称',
		'type'   => '类别',
		'resume' => '栏目名称',
		'cp_id'  => 'CP来源',
		'url'    => '数据抓取地址',
	);

	public function clientListAction() {
		$cpId = intval($this->getInput('cp_id'));
		$cpId = !empty($cpId) ? $cpId : 1;
		$list = Widget_Service_CpClient::getsBy(array('cp_id' => $cpId), array('id' => 'asc'));
		$this->assign('list', $list);
		$this->assign('cpId', $cpId);
		$this->assign('cps', Widget_Service_Cp::$CpCate);

	}

	public function clientEditAction() {
		$id            = $this->getInput('id');
		$cpId          = intval($this->getInput('cp_id'));
		$info          = Widget_Service_CpClient::get(intval($id));
		$info['cp_id'] = !empty($cpId) ? $cpId : $info['cp_id'];
		$info['data']  = json_decode($info['data'], true);
		$this->assign('info', $info);
		$this->assign('cp', Widget_Service_Cp::$CpCate);
	}

	public function clientDelAction() {
		$id  = $this->getInput('id');
		$ret = Widget_Service_CpClient::delete($id);

		Widget_Service_Config::setValue('w3_cp_ver', time());
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}

	public function clientPostAction() {
		$info = $this->getPost(array('id', 'channel_name', 'detail_id', 'channel_name', 'down_url', 'cp_id', 'data'));
		if (!$info['cp_id']) {
			$this->output(-1, 'cp_id不能为空.');
		}
		if (!$info['detail_id']) {
			$this->output(-1, 'detail_id不能为空.');
		}
		if (!$info['channel_name']) {
			$this->output(-1, 'channel_name不能为空.');
		}

		if (!$_POST['data']) {
			$this->output(-1, 'data不能为空.');
		}

		$typeArr = array();

		$data = array();
		foreach ($_POST['data'] as $i => $v) {
			if ($v['key']) {
				$type = strtolower($v['type']);
				$val  = $v['value'];
				if (stristr($type, 'array')) {
					$val = explode(',', $v['value']);
				}

				if (!in_array($type, $typeArr)) {
					//$this->output(-1, 'type格式非法:'.implode(',',$typeArr));
				}

				$data[] = array('key' => strtolower($v['key']), 'value' => $val, 'type' => $type);
			}
		}

		$info['data'] = json_encode($data);

		if ($info['id']) {
			$ret = Widget_Service_CpClient::update($info, $info['id']);
		} else {
			unset($info['id']);
			$ret = Widget_Service_CpClient::insert($info);
		}

		Widget_Service_Config::setValue('w3_cp_ver', time());
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	public function indexAction() {
		$cpId = intval($this->getInput('cp_id'));
		$cpId = !empty($cpId) ? $cpId : 1;

		$cpUrls = Widget_Service_Cp::getsBy(array('cp_id' => $cpId));
		$list   = array();
		$types  = Widget_Service_Column::$types;

		foreach ($cpUrls as $val) {
			$val['stats'] = Widget_Service_Source::getLastRow($val['id']);
			$val['type']  = isset($types[$val['type']]) ? $types[$val['type']] : $types[1];
			$list[]       = $val;
		}

		$this->assign('list', $list);
		$this->assign('cpId', $cpId);
		$this->assign('cpcate', Widget_Service_Cp::$CpCate);
	}

	public function editAction() {
		$cpId = $this->getInput('cp_id');
		$info = $this->getPost(array('id', 'title', 'resume', 'url', 'cp_id', 'type', 'freq_num', 'freq_sort'));
		if (!empty($info['title'])) {
			if (!$info['title']) {
				$this->output(-1, '标题不能为空.');
			}
			if (!$info['url']) {
				$this->output(-1, 'URL不能为空.');
			}
			if (!$info['type']) {
				$this->output(-1, '类型不能为空.');
			}
			$info['url']     = html_entity_decode($info['url']);
			$info['url_iid'] = crc32($info['url']);

			if (!empty($info['id'])) {
				$ret = Widget_Service_Cp::update($info, $info['id']);
			} else {
				$ret = Widget_Service_Cp::add($info);
			}

			if (!$ret) {
				$this->output(-1, '操作失败.');
			}
			$this->output(0, '操作成功.');
		}

		$id            = $this->getInput('id');
		$info          = Widget_Service_Cp::get(intval($id));
		$info['cp_id'] = !empty($info['cp_id']) ? $info['cp_id'] : intval($cpId);
		$this->assign('info', $info);
		$this->assign('types', Widget_Service_Column::$types);
		$this->assign('cp', Widget_Service_Cp::$CpCate);
	}

	public function parseAction() {
		$urlId   = intval($this->getInput('id'));
		$urlInfo = Widget_Service_Cp::get($urlId);
		$num = 0;
		if ($urlInfo) {
			$data   = Widget_Service_Adapter::getData($urlInfo);
			$ids =	Widget_Service_Adapter::done($data, $urlInfo, $out);
			$num = count($ids);
		}
		$this->output(0, "操作成功:{$num}<br>".str_replace("\n",'<br>',$out));
	}

	public function manageAction() {

		$info = $_POST['cpaction'];
		if (!empty($info)) {
			Widget_Service_Config::setValue('cp_action', json_encode($info));
			$this->output(0, '操作成功.');
		} else {
			$str = Widget_Service_Config::getValue('cp_action');
			$act = json_decode($str, true);
			$this->assign('act', $act);
			$this->assign('cp', Widget_Service_Cp::$CpCate);
		}
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Widget_Service_Cp::get(intval($id));
		if ($info && $info['id'] == 0) {
			$this->output(-1, '信息不存在无法删除');
		}

		$ret = Widget_Service_Cp::delete($id);

		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}


	/**
	 * 验证内容上次api请求内容和现在是否相同
	 */
	public function verifymd5Action() {
		$id     = $this->getInput('id');
		$cp     = Widget_Service_Cp::get($id);
		$params = Widget_Service_Adapter::getParams($cp);
		$url    = html_entity_decode($cp['url']);
		$data   = Widget_Service_Adapter::getResponse($url, $params);
		$md5Str = md5(json_encode($data));

		if ($cp['md5_data'] != $md5Str) {
			$this->output(0, "内容不同<br>老:{$cp['md5_data']}<br>新:{$md5Str}");
		}
		$this->output(0, "已经最新数据");
	}

	public function exportAction() {
		$filename = 'cp_' . date('YmdHis') . '.csv';
		$cpUrls   = Widget_Service_Cp::getAll();

		//header( 'Content-Type: text/csv; charset=utf-8' );
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . $filename);
		$fp = fopen('php://output', 'w');

		foreach (self::$headerrow as $k => $v) {
			self::$headerrow[$k] = iconv('UTF8', 'GBK', self::$headerrow[$k]);
		}

		Widget_Service_Cp::getIdsByCp(true);

		$keys = array_keys(self::$headerrow);
		fputcsv($fp, array_values(self::$headerrow));
		foreach ($cpUrls as $fields) {

			$row = array();
			foreach ($keys as $key) {
				if ($key == 'type') {
					$type         = $fields[$key];
					$fields[$key] = Widget_Service_Column::$types[$type];
				} else if ($key == 'cp_id') {
					$cpId         = $fields[$key];
					$fields[$key] = Widget_Service_Cp::$CpCate[$cpId][0];
				}

				$row[] = iconv('UTF8', 'GB2312//IGNORE', $fields[$key]);
			}

			fputcsv($fp, $row);
		}
		fclose($fp);
		exit;
	}

	public function importAction() {
		if (!empty($_FILES['csv_file']['tmp_name'])) {
			$file  = $_FILES["csv_file"]['tmp_name'];
			$row   = 1;
			$num   = count(self::$headerrow);
			$filed = array_keys(self::$headerrow);

			$tmpCate = array();
			foreach (Widget_Service_Cp::$CpCate as $cpId => $val) {
				$tmpCate[$val[0]] = $cpId;
			}

			$tmpType = array();
			foreach (Widget_Service_Column::$types as $t => $v) {
				$tmpType[$v] = $t;
			}

			if (($handle = fopen($file, "r")) !== false) {
				while (($data = fgetcsv($handle, 1000, ",")) !== false) {
					if ($row > 1) {
						$info = array();
						for ($c = 0; $c < $num; $c++) {
							$k        = $filed[$c];
							$info[$k] = iconv('GB2312', 'UTF8', $data[$c]);
						}

						$info['title'] = trim($info['title']);

						if (!empty($info['id']) && !empty($info['title'])) {

							$info['cp_id'] = isset($tmpCate[$info['cp_id']]) ? $tmpCate[$info['cp_id']] : '';
							if (empty($info['cp_id'])) {
								$this->output(-1, '来源CP不存在');
							}

							$info['type'] = isset($tmpType[$info['type']]) ? $tmpType[$info['type']] : '';
							if (empty($info['type'])) {
								$this->output(-1, '类别不存在');
							}

							$info['url']     = html_entity_decode(trim($info['url']));
							$info['url_iid'] = crc32(trim($info['url']));
							$tmpRow          = Widget_Service_Cp::get($info['id']);
							if (!empty($tmpRow['id'])) {
								$ret = Widget_Service_Cp::update($info, $info['id']);
							} else {
								$ret = Widget_Service_Cp::add($info);
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


}
