<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

//广告位管理

class PositionController extends Admin_BaseController {

	public $pageSize = 20;
	public $actions  = array(
		'indexUrl'    => '/Admin/Position/index',
		'editUrl'     => '/Admin/Position/edit',
		'editPostUrl' => '/Admin/Position/editPost',
		'addUrl'      => '/Admin/Position/add',
		'addPostUrl'  => '/Admin/Position/addPost',
		'deleteUrl'   => '/Admin/Position/delete',
	);

	public $adsType = array(
		'1' => '图片广告',
		'2' => '文字广告',
		'3' => '图文广告',
	);

	public function indexAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = Gionee_Service_Position::getList($page, $this->pageSize, array(), array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "/?"));
		$this->assign('types', $this->adsType);
	}

	public function addAction() {
		//	$this->assign('adType', $this->adsType);
		$this->assign('config', Common::getConfig('userConfig'));


	}

	public function addPostAction() {
		$postData               = $this->getInput(array('name', 'pos_prefix', 'status', 'type'));
		$adTypes                = Common::getConfig('userConfig', 'ads_type');
		$postData['identifier'] = $postData['pos_prefix'] . $adTypes[$postData['type']]['tag'];
		//广告位标识不为空且保证唯一
		$this->_checkIdentifier($postData['identifier']);
		$postData['add_time'] = time();
		$res                  = Gionee_Service_Position::add($postData);
		if ($res) {
			$this->output(0, '添加成功');
		} else {
			$this->output('-1', '添加失败');
		}
	}

	public function  editAction() {
		$id = $this->getInput('id');
		if (!is_numeric($id)) {
			$this->output('-1', '信息有误！');
		}
		$data   = Gionee_Service_Position::get($id);
		$config = Common::getConfig('userConfig');
		$prefix = substr($data['identifier'], 0, 2);
		$this->assign('prefix', $prefix);
		$this->assign('config', $config);
		$this->assign('data', $data);
	}

	public function editPostAction() {
		$postData               = $this->getInput(array('name', 'status', 'pos_prefix', 'type', 'id'));
		$config                 = Common::getConfig('userConfig', 'ads_type');
		$postData['identifier'] = $postData['pos_prefix'] . $config[$postData['type']]['tag'];
		$this->_checkIdentifier($postData['identifier'], $postData['id']);
		$postData['edit_time'] = time();
		$postData['edit_user'] = $this->userInfo['username'];
		$res                   = Gionee_Service_Position::edit($postData, $postData['id']);
		if ($res) {
			$this->output('0', '修改成功');
		} else {
			$this->output('-1', '修改失败');
		}
	}

	public function deleteAction() {
		$id = $this->getInput('id');
		if (Gionee_Service_Position::delete($id)) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	private function _checkIdentifier($value, $id = 0) {
		if (empty($value)) {
			$this->output('-1', '广告位标识不能为空');
		}
		$params               = array();
		$params['identifier'] = $value;
		if ($id) {
			$params['id'] = array("!=", $id);
		}
		$identify = Gionee_Service_Position::getBy($params);
		if ($identify) {
			$this->output('-1', '广告位标识不能已存在，不能重复添加！');
		}
	}
}