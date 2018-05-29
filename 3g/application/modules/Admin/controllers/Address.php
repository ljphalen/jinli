<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网点管理
 */
class AddressController extends Admin_BaseController {

	public $actions      = array(
		'listUrl'       => '/Admin/Address/index',
		'addUrl'        => '/Admin/Address/add',
		'addPostUrl'    => '/Admin/Address/add_post',
		'editUrl'       => '/Admin/Address/edit',
		'editPostUrl'   => '/Admin/Address/edit_post',
		'deleteUrl'     => '/Admin/Address/delete',
		'importUrl'     => '/Admin/Address/import',
		'importPostUrl' => '/Admin/Address/import_post',
	);
	public $perpage      = 20;
	public $address_type = array(
		'1' => '销售网点',
		'2' => '服务网点'
	);
	public $service_type = array(
		'1' => '客服中心',
		'2' => '特约服务站'
	);

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;

		$param  = $this->getInput(array('province', 'city', 'address_type', 'service_type'));
		$search = array();
		if ($param['province']) {
			$search['province'] = $param['province'];
		}
		if ($param['city']) {
			$search['city'] = $param['city'];
		}
		if ($param['address_type']) {
			$search['address_type'] = $param['address_type'];
		}
		if ($param['service_type']) {
			$search['service_type'] = $param['service_type'];
		}

		list($total, $address) = Gionee_Service_Address::getList($page, $perpage, $search);
		//省市
		$province = Gionee_Service_Area::getProvinceList();
		$city     = Gionee_Service_Area::getAllCity();

		$this->assign('province', Common::resetKey($province, 'id'));
		$this->assign('searchcity', $city);
		$this->assign('city', Common::resetKey($city, 'id'));
		$this->assign('address', $address);
		$this->assign('search', $search);
		$this->assign('address_type', $this->address_type);
		$this->assign('service_type', $this->service_type);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	public function addAction() {
		//省市
		$province = Gionee_Service_Area::getProvinceList();
		$city     = Gionee_Service_Area::getAllCity();

		$this->assign('province', $province);
		$this->assign('city', $city);
		$this->assign('address_type', $this->address_type);
		$this->assign('service_type', $this->service_type);
	}

	public function add_postAction() {
		$info = $this->getPost(array(
			'name',
			'province',
			'city',
			'address_type',
			'sort',
			'service_type',
			'address',
			'tel'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Address::addAddress($info);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Address::getAddress(intval($id));

		//省市
		$province = Gionee_Service_Area::getProvinceList();
		$city     = Gionee_Service_Area::getAllCity();

		$this->assign('province', $province);
		$this->assign('city', $city);
		$this->assign('address_type', $this->address_type);
		$this->assign('service_type', $this->service_type);

		$this->assign('info', $info);
	}

	/**
	 *
	 * Enter description here ...
	 */

	public function edit_postAction() {
		$info = $this->getPost(array(
			'id',
			'name',
			'province',
			'city',
			'address_type',
			'sort',
			'service_type',
			'address',
			'tel'
		));
		$info = $this->_cookData($info);
		$ret  = Gionee_Service_Address::updateAddress($info, $info['id']);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Address::getAddress($id);
		if ($info && $info['id'] == 0) {
			$this->output(-1, '无法删除');
		}
		$ret = Gionee_Service_Address::deleteAddress($id);
		if (!$ret) {
			$this->output(-1, '操作失败');
		}
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if (!$info['name']) {
			$this->output(-1, '网点名称不能为空.');
		}
		if (!$info['address']) {
			$this->output(-1, '地址不能为空.');
		}
		return $info;
	}

	/**
	 *
	 * Enter description here ...-
	 */

	public function importAction() {
	}


	/**
	 *
	 * Enter description here ...
	 */

	public function import_postAction() {
		$content = @ file_get_contents($_FILES['content']['tmp_name']);
		if (!$content) {
			$this->output(-1, '请选择文件');
		}

		$list = explode("\n", $content);

		$data = array();
		foreach ($list as $key => $value) {
			if ($value) {
				$item = explode('|', $value);
				if ($item[0] && $item[1] && $item[2] && $item[4] && $item[5]) {
					$data[$key]['id']           = '';
					$data[$key]['province']     = $item[0];
					$data[$key]['city']         = $item[1];
					$data[$key]['address_type'] = $item[2];
					$data[$key]['service_type'] = $item[3];
					$data[$key]['name']         = $item[4];
					$data[$key]['address']      = $item[5];
					$data[$key]['tel']          = $item[6];
					$data[$key]['sort']         = 0;
				}
			}
		}

		$ret = Gionee_Service_Address::batchaddAddress($data);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功');
	}
}
