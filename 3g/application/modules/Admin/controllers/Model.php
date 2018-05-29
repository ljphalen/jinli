<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 机型管理
 */
class ModelController extends Admin_BaseController {

	public $actions  = array(
		'attributesUrl'    => '/Admin/Model/attributes',
		'indexUrl'         => '/Admin/Model/index',
		'editUrl'          => '/Admin/Model/edit',
		'editPostUrl'      => '/Admin/Model/editPost',
		'deleteUrl'        => '/Admin/Model/delete',
		'editmodelUrl'     => '/Admin/Model/editModel',
		'editPostModelUrl' => '/Admin/Model/editPostModel',
		'delModelUrl'      => '/Admin/Model/delModel',

	);
	public $pageSize = 20;
	public $types    = array(
		'1' => '机型',
		'2' => '版本',
		'3' => '运营商',
		'4' => '地域'
	);

	public function indexAction() {
		$page     = $this->getInput('page');
		$postData = $this->getInput(array('model', 'version', 'operator', 'province', 'city', 'prior'));
		$page     = max($page, 1);
		$params   = array();
		foreach ($postData as $k => $v) {
			$params[$k] = $v;
		}
		$sql = '  1 ';
		if (!empty($postData['model']) || !empty($postData['version']) || !empty($postData['operator'])) {
			if (!empty($postData['model'])) {
				$sql .= " AND model LIKE '%" . $postData['model'] . "%' ";
			}
			if (!empty($postData['version'])) {
				$sql .= "AND version LIKE '%" . $postData['version'] . "%' ";
			}
			if (!empty($postData['operator'])) {
				$sql .= "AND operator LIKE '%" . $postData['operator'] . "%'  ";
			}
		}
		
		list($total, $dataList) = Gionee_Service_ModelContent::getDataList($page, $this->pageSize, $sql, array('id' => 'DESC'));
		$areaData = array();
		foreach ($dataList as $k=>$v){
			$area = json_decode($v['area'],true);
			foreach ($area as $m=>$n){
				$province = Gionee_Service_Area::getArea($m);
				$cityNum = Gionee_Service_Area::count(array('parent_id'=>$m));
				if($cityNum == count($n)){
					$areaData[$m]['province'] = $province['name'];
				}else{
					foreach ($n as $s=>$t){
						$city = Gionee_Service_Area::getArea($t);
						$areaData[$m]['city'][] = $city['name'];
					}
				}
				$dataList[$k]['area'] = sprintf("%s:%s",$areaData[$m]['province'],implode(',', $areaData[$m]['city']));
				var_dump($dataList[$k]['area']);
			}
		}
		
		$searchParams = http_build_query($postData);
		$attributes   = Gionee_Service_ModelType::getTypeValueData(array(), array('id' => 'DESC'), array('value'));
		$attrTypes    = $cities = array();
		foreach ($attributes as $key => $val) {
			$attrTypes[$val['type']][] = $val;
		}

		if ($postData['province']) {
			$cities = Gionee_Service_ModelType::getTypeValueData(array('type'  => 4,
			                                                           'value' => $postData['province']
			), array('id' => 'DESC'), array('value'));
		}
		$this->assign('types', $this->types);
		$this->assign('cities', $cities);
		$this->assign('params', $params);
		$this->assign('attrTypes', $attrTypes);
		$this->assign('data', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?{$searchParams}&"));
	}


	public function editModelAction() {
		$id         = $this->getInput('id');
		$attributes = Gionee_Service_ModelType::getTypeValueData(array(), array('id' => 'DESC'), array('value'));
		$attrTypes  = $types = array();
		foreach ($attributes as $key => $val) {
			$attrTypes[$val['type']][] = $val;
		}
		$temp = array();
		foreach ($attrTypes as $k => $v) {
			$temp[] = array('key' => $k, 'val' => $v);
		}
		$info   = Gionee_Service_ModelContent::get($id);
		$cities = Gionee_Service_ModelType::getsBy(array('value' => $info['province']), array('id' => 'DESC'));
		$this->assign('info', $info);
		$this->assign('cities', $cities);
		$this->assign('jsonType', json_encode($temp));
		$this->assign('attrTypes', $attrTypes);
		$this->assign('types', $this->types);
	}

	public function editPostModelAction() {
		$postData = $this->getInput(array('post', 'area', 'id', 'prior'));
		foreach ($postData['post'] as $k => $v) {
			$postData[$k] = implode(',', array_unique(array_values($v)));
		}
		unset($postData['post']);
		
		$provinceList = array();
		$areaList = array();
		foreach($postData['area'] as $m=>$n){
			$info = Gionee_Service_Area::getArea($n);
			if($info['parent_id'] == 0){
				$provinceList[$info['id']] = $info['id'];
			}
			
			if(in_array($info['parent_id'],array_keys($provinceList))){
				$areaList[$info['parent_id']][] = $info['id'];
			}
		}
		$postData['area'] = json_encode($areaList);
		$info = array();
		if ($postData['id']) { //编辑
			$ret = Gionee_Service_ModelContent::edit($postData, $postData['id']);
		} else {
			unset($postData['id']);
			$ret = Gionee_Service_ModelContent::add($postData);
		}
		if ($ret) $this->output('0', '操作成功');
		$this->output('-1', '操作有错！');
	}


	public function delModelAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$ret = Gionee_Service_ModelContent::delete($id);
			$this->output('0', '操作成功');
		}
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$data = array();
		if (intval($id)) {
			$data = Gionee_Service_ModelType::get($id);
		}

		if ($data['type'] == '4') {
			$provinces        = Gionee_Service_Area::getProvinceList();
			$selectedProvince = Gionee_Service_Area::getByName($data['value']);
			$cities           = Gionee_Service_Area::getsBy(array('parent_id' => $selectedProvince['id']));
			$this->assign('provinces', $provinces);
			$this->assign('cities', $cities);
		}
		$this->assign('types', $this->types);
		$this->assign('data', $data);
		$this->assign('tid', $data['type']);
	}

	public function editPostAction() {
		$postData        = $this->getInput(array('id', 'type', 'value', 'province_id', 'city_name'));
		$params          = array();
		$params['type']  = $postData['type'];
		$params['value'] = $postData['value'];
		if ($postData['type'] == '4') { //地区信息
			$provinceInfo    = Gionee_Service_Area::getArea($postData['province_id']);
			$params['value'] = $provinceInfo['name'];
			$params['ext']   = $postData['city_name'];
		}
		if (intval($postData['id'])) { //编辑
			$ret = Gionee_Service_ModelType::edit($params, $postData['id']);
		} else {
			unset($postData['id']);
			$ret = Gionee_Service_ModelType::add($params);
		}
		if ($ret) $this->output('0', '操作成功！');
		$this->output('-1', '操作失败！');
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$ret = Gionee_Service_ModelType::delete($id);
			$this->output('0', '操作成功');
		}
	}


	public function  attributesAction() {
		$postData = $this->getInput(array('page', 'type', 'value', 'ext'));
		$page     = max(1, $postData['page']);
		$params   = $values = $exts = array();
		if ($postData['type']) {
			$params['type'] = $postData['type'];
			$values         = Gionee_Service_ModelType::getTypeValueData(array('type' => $postData['type']), array('id' => 'DESC'), array('value'));
		}
		if ($postData['value']) {
			$params['value'] = $postData['value'];
			$exts            = Gionee_Service_ModelType::getsBy(array('value' => $postData['value']), array('id' => 'DESC'));
		}
		if ($postData['ext']) {
			$params['ext'] = $postData['ext'];
		}

		$query = http_build_query($params);
		list($total, $dataList) = Gionee_Service_ModelType::getList($page, $this->pageSize, $params, array('id' => 'DESC'));
		$this->assign('data', $dataList);
		$this->assign('types', $this->types);
		$this->assign('params', $postData);
		$this->assign('values', $values);
		$this->assign('exts', $exts);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['listUrl'] . "?{$query}&"));
	}

	/**
	 * Ajaxs得到模块信息
	 */
	public function ajaxGetContentByTypeAction() {
		$type = $this->getInput('type');
		if ($type) {
			$data = Gionee_Service_ModelType::getTypeValueData(array('type' => $type), array('id' => 'DESC'), array('value'));
			$this->output('0', '', $data);
		}
	}

	/**
	 * Ajax得到城市信息
	 */
	public function  ajaxGetProvinceInfoAction() {
		$pid        = $this->getInput('province_id');
		$provinceId = $pid ? $pid : 0;
		$data       = Gionee_Service_Area::getListByParentId($provinceId);
		$this->output('0', '', $data);
	}

	public function ajaxGetAllDataAction(){
		$key = "USER:MODEL:CITY:LIST:";
		$data = Common::getCache()->get($key);
		if(empty($data)){
			$data = $this->_getDataByParentId(0);
			foreach ($data as $m=>$n){
				$childrenData = $this->_getDataByParentId($n['id']);
				$data[$m]['state']  = 'closed';
				$data[$m]['children'] = $childrenData;
			}
			echo json_encode($data);
			exit();
		}
	}
	
	private function _getDataByParentId($pid){
		$ret       = Gionee_Service_Area::getListByParentId($pid);
		$data = array();
		foreach ( $ret as $k=>$v){
			$data[] = array(
					'id'	=>$v['id'],
					'text'=>$v['name'],
			);
		}
		return $data;
	}
	
	/**
	 * Ajax 得到地区信息
	 */
	public function ajaxGetCityInfoAction() {
		$province = $this->getInput('province');
		$data     = array();
		if ($province) {
			$data = Gionee_Service_ModelType::getsBy(array('value' => $province));
		}
		$this->output('0', '', $data);
	}
}

?>