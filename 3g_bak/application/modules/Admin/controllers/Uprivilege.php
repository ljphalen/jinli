<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UprivilegeController
 * 用户权限管理
 */
class UprivilegeController extends Admin_BaseController {
	public $actions = array(
		'indexUrl'      => '/Admin/Uprivilege/level',
		'addUrl'        => "/Admin/Uprivilege/add",
		'addPostUrl'    => '/Admin/Uprivilege/addPost',
		'editUrl'       => '/Admin/Uprivilege/edit',
		'editPostUrl'   => '/Admin/Uprivilege/editPost',
		'deleteUrl'     => '/Admin/Uprivilege/delete',
		'deleteByUrl'   => '/Admin/Uprivilege/deleteBy',
		'detailUrl'     => '/Admin/Uprivilege/detail',
		'editBaseUrl'   => '/Admin/Uprivilege/editBase',
		'ruleUrl'       => '/Admin/Uprivilege/rule',
		'ruleEditUrl'   => '/Admin/Uprivilege/ruleEdit',
		'uploadUrl'     => '/Admin/Uprivilege/upload',
		'uploadPostUrl' => '/Admin/Uprivilege/uploadPost',
	);

	public $pageSize = 20;

	//基本配置信息
	public function baseAction() {
		$params           = array();
		$params['3g_key'] = array(
			'IN',
			array(
				'user_ads_max_number',
				'user_person_max_exchange',
				'user_day_max_number',
				'user_voip_max_time',
				'user_signin_step',
				'user_signin_per_max',
				'user_wx_content_rewards',
				'user_give_voip_minius',
				'user_wangmeng_status',
				'user_flow_acitivity_num'
			)
		);
		$data             = Gionee_Service_Config::getsBy($params);
		$config           = array();
		foreach ($data as $key => $val) {
			$config[$val['3g_key']] = $val['3g_value'];
		}
		$this->assign('data', $config);
	}

	public function editBaseAction() {
		$postData = $this->getInput(array(
			'user_ads_max_number',
			'user_person_max_exchange',
			'user_day_max_number',
			'user_voip_max_time',
			'user_signin_per_max',
			'user_signin_step',
			'user_wx_content_rewards',
			'user_give_voip_minius',
			'user_wangmeng_status',
			'user_flow_acitivity_num'
		));
		foreach ($postData as $key => $value) {
			Gionee_Service_Config::setValue($key, $value);
		}
		$this->output('0', "操作成功！");
	}

	//使用规则说明
	public function ruleAction() {
		$content = Gionee_Service_Config::getValue('user_index_rule');
		$this->assign('content', $content);
	}

	public function ruleEditAction() {
		$content = trim($_POST['user_index_rule']);
		$res     = Gionee_Service_Config::setValue('user_index_rule', $content);
		$this->assign('0', '编辑成功！');
	}

	//用户等级权限
	public function levelAction() {
		$postData = $this->getInput(array('page', 'status'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		list($total, $dataList) = User_Service_Uprivilege::getLevelGoods($page, $this->pageSize, $where, array(
			'goods_id',
			'cat_id',
			'group_id'
		), array('id' => 'DESC'));
		$goodsList = $this->_getLevelData($dataList);
		$this->assign('list', $goodsList);
		$this->assign('groupType', $this->_getConfig('action_type'));
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('status', $postData['status']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "/?"));
	}

	public function  addAction() {
		$config         = $this->_getConfig();
		$levelGroupName = $config['level_group_name'];
		$this->assign('groupType', $config['action_type']);
		$this->assign('levelGroupName', $levelGroupName);
	}

	public function addPostAction() {
		$postData = $this->getInput(array(
			'group_id',
			'cat_id',
			'goods_id',
			'level_group',
			'user_level',
			'scores',
			'days',
			'rewards',
			'times',
			'rewards2',
			'status',
			'defaultScore'
		));
		$config   = $this->_getConfig();
		//检查类别
		if (!in_array($postData['group_id'], array_keys($config['action_type']))) $this->output('-1', '参数错误！');
		if (!is_numeric($postData['scores']) || !is_numeric($postData['days']) || !is_numeric($postData['rewards'])) {
			$this->output('-2', '请检查数据格式是否正确！');
		}

		//用户等级检测(如果签到功能则可以不用考虑)
		if (in_array($postData['group_id'], array('2', '3'))) {
			if (!$postData['level_group'] || !$postData['user_level']) {
				$this->output('-3', '请选择用户等级');
			}
		}
		//商品检测
		$goodsType = $config['action_type'][$postData['group_id']]['key'];
		if ($goodsType != 'signin') {
			if (!$postData['goods_id'] && !$postData['cat_id']) $this->output('-4', '请选择商品信息');
		}
		if ($goodsType == 'cosume' && $postData['goods_id'] > 0) {//消费类
			if ($postData['scores'] > $postData['defaultScore']) {
				$this->output('-1', '消耗金币数不能大于初始値');
			}
		} else { //生产类
			if ($postData['scores'] > 0 && $postData['scores'] < $postData['defaultScore']) {
				$this->output('-1', '产生金币数不能小于初始值');
			}
		}
		$params['group_id']    = $postData['group_id'];
		$params['level_group'] = $postData['level_group'];
		$params['user_level']  = $postData['user_level'];
		$params['cat_id']      = $postData['cat_id'];
		$params['goods_id']    = $postData['goods_id'];
		$exists                = User_Service_Uprivilege::getBy($params, array('id' => 'DESC'));
		if ($exists) {
			$this->output('-1', '该用户等级特权信息已经存在，请不要重复添加！');
		}

		$postData['add_time']  = time();
		$postData['add_admin'] = $this->userInfo['username'];
		$res                   = User_Service_Uprivilege::add($postData);
		if ($res) {
			$this->output('0', '添加成功');
		} else {
			$this->output('-1', '添加失败');
		}
	}

	public function editAction() {
		$id     = $this->getInput('id');
		$config = $this->_getConfig();
		$data   = User_Service_Uprivilege::get($id);
		$goods  = array();
		switch ($config['action_type'][$data['group_id']]['key']) {
			case 'cosume': {
				$goods = User_Service_Commodities::get($data['goods_id']);
				break;
			}
			case 'product': {
				$goods = User_Service_Produce::get($data['goods_id']);
				break;
			}
			default: {
				$goods = $config['signin'];
				break;
			}
		}
		//分类信息
		$category = array();
		if ($goods['cat_id']) {
			$category = User_Service_Category::getsBy(array(
				'id'       => $goods['cat_id'],
				'group_id' => $goods['group_id']
			), array());
		}
		//组别
		$levelGroup = $config['level_group_name'];
		$rank       = $config['rank'][$data['level_group']];
		$this->assign('levelGroup', $levelGroup);
		$this->assign('rank', $rank);
		$this->assign('category', $category);
		$this->assign('goods', $goods);
		$this->assign('data', $data);
		$this->assign('groupType', $config['action_type']);
		$this->assign('userLevel', $config['works']);
	}

	public function editPostAction() {
		$postData = $this->getInput(array(
			'group_id',
			'cat_id',
			'goods_id',
			'id',
			'user_level',
			'level_group',
			'old_level_group',
			'old_level',
			'defaultScore',
			'scores',
			'days',
			'rewards',
			'times',
			'rewards2',
			'status'
		));
		//参数检测

		if (in_array($postData['group_id'], array('2', '3'))) {
			if (!$postData['user_level']) {
				$this->output('-1', '用户等级不存在！');
			}
		}
		//重复性检测
		if (($postData['old_level'] != $postData['user_level']) && ($postData['old_level_group'] != $postData['level_group'])) {
			$number = User_Service_Uprivilege::countBy(array(
				'group_id'    => $postData['group_id'],
				'cat_id'      => $postData['cat_id'],
				'goods_id'    => $postData['goods_id'],
				'level_group' => $postData['level_group'],
				'user_level'  => $postData['user_level']
			), array());
			if ($number > 0) {
				$this->output('-1', '该用户等级信息已经存在，请重新选择！');
			}
		}
		//规则验证
		$config = $this->_getConfig('action_type');
		if ($config[$postData['group_id']]['key'] == 'cosume') {
			if (($postData['scores'] > $postData['defaultScore']) && $postData['goods_id']) $this->output('-1', '消耗金币类商品的等级金币不应大于普通用户所需金币！');
		} else {
			if ($postData['scores'] < $postData['defaultScore']) {
				$this->output('-1', '生产金币类商品的等级金币不应小于普通用户所得金币！');
			}
		}

		$postData['edit_time']  = time();
		$postData['edit_admin'] = $this->userInfo['username'];
		$res                    = User_Service_Uprivilege::update($postData, $postData['id']);
		if ($res) {
			$this->output('0', '编辑成功');
		} else {
			$this->output('-1', '编辑失败');
		}
	}

	//删除
	public function deleteAction() {
		$id = $this->getInput('id');
		if (!intval($id)) {
			$this->output('-1', '参数有错！');
		}
		$res = User_Service_Uprivilege::delete($id);
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	//根据条件删除
	public function deleteByAction() {
		$data     = $this->getInput(array('group_id', 'cat_id', 'goods_id'));
		$cat_id   = $data['cat_id'] ? $data['cat_id'] : '0';
		$goods_id = $data['goods_id'] ? $data['goods_id'] : '0';
		if (empty($data['group_id'])) {
			$this->output('-1', '参数有错！');
		}
		$res = User_Service_Uprivilege::deleteBy(array(
			'group_id' => $data['group_id'],
			'cat_id'   => $cat_id,
			'goods_id' => $goods_id
		));
		if ($res) {
			$this->output('0', '操作成功');
		} else {
			$this->output('-1', '操作失败');
		}
	}

	public function detailAction() {
		$goods_id = $this->getInput('goods_id');
		$goods_id = $goods_id ? $goods_id : '0';
		$group_id = $this->getInput('group_id');
		$cat_id   = $this->getInput('cat_id');
		if (!intval($group_id) || (in_array($group_id, array('2', '3')) && empty($cat_id))) {
			$this->output('-1', '参数有错!');
		}
		$catInfo  = User_Service_Category::get($cat_id);
		$dataList = User_Service_Uprivilege::getsBy(array(
			'goods_id' => $goods_id,
			'group_id' => $group_id
		), array('id' => 'DESC'));
		$this->assign('group', $this->_getConfig('action_type'));
		$this->assign('data', $dataList);
		$this->assign('types', $this->_getConfig('action_type'));
		$this->assign('category', $catInfo);
		$this->assign('group_id', $group_id);

	}

	//ajax 获得用户等级信息
	public function ajaxGetUserLevelGroupAction() {
		$group_id = $this->getInput('group_id');
		if (!in_array($group_id, array('1', '2'))) {
			echo json_encode(array('key' => '-1', 'msg' => '该等级分组不存在！'));
			exit();
		}
		$config    = $this->_getConfig();
		$groupRank = $config['rank'][$group_id];
		echo json_encode(array('key' => '1', 'msg' => $groupRank));
	}


	public function uploadAction() {
		$ret        = Common::upload('imgFile', 'indexRule');
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getImgPath() . $ret['data'])));
	}


	public function uploadPostAction() {
		$ret   = Common::upload('img', 'indexRule');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	//聚合信息
	private function _getLevelData($dataList) {
		$actType  = $this->_getConfig('action_type');
		$sign     = $this->_getConfig('signin');
		$goodsMsg = array();
		foreach ($dataList as $k => $v) {
			$goodsMsg[$k]['goods_id'] = $v['goods_id'];
			$goodsMsg[$k]['cat_id']   = $v['cat_id'];
			$goodsMsg[$k]['group_id'] = $v['group_id'];
			$goodsMsg[$k]['cat_name'] = '打卡';
			if ($v['cat_id'] > 0) {
				$category                 = User_Service_Category::get($v['cat_id']);
				$goodsMsg[$k]['cat_name'] = $category['name'];
			}
			switch ($actType[$v['group_id']]['key']) {
				case 'cosume': {
					$goods                  = User_Service_Commodities::get($v['goods_id']);
					$goodsMsg[$k]['name']   = $goods['name'];
					$goodsMsg[$k]['scores'] = $goods['scores'];
					break;
				}
				case 'product': {
					$goods                  = User_Service_Produce::get($v['goods_id']);
					$goodsMsg[$k]['name']   = $goods['name'];
					$goodsMsg[$k]['scores'] = $goods['scores'];
					break;
				}
				case 'signin': {
					$goodsMsg[$k]['name']   = $sign['name'];
					$goodsMsg[$k]['scores'] = $sign['scores'];
				}
			}
		}
		return $goodsMsg;
	}

	private function _getConfig($type = '') {
		return Common::getConfig('userConfig', $type);
	}
}