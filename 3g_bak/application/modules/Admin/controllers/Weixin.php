<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 微信管理
 */
class WeixinController extends Admin_BaseController {

	public $actions = array(
		'configUrl'      => '/Admin/Weixin/config',
		'commandUrl'     => '/Admin/Weixin/command',
		'commandeditUrl' => '/Admin/Weixin/commandedit',
		'commanddelUrl'  => '/Admin/Weixin/commanddel',
		'menuUrl'        => '/Admin/Weixin/menu',
		'feedbackUrl'    => '/Admin/Weixin/feedback',
		'userUrl'        => '/Admin/Weixin/user',
		'msgUrl'         => '/Admin/Weixin/msg',
		'msgeditUrl'     => '/Admin/Weixin/msgedit',
		'msgdelUrl'      => '/Admin/Weixin/msgdel',
		'uploadUrl'      => '/Admin/Common/upload',
		'uploadPostUrl'  => '/Admin/Common/upload_post',
		'concernUrl'     => '/Admin/Weixin/concern',
		'focusUrl'       => '/Admin/Weixin/focusWx',
	);

	public $perpage = 20;

	/**
	 * 配置
	 */
	public function configAction() {
		$data = $this->getPost(array(
			'subscribe',
			'unsubscribe',
			'interact_1_in',
			'interact_1_to',
			'interact_2_in',
			'interact_2_to',
			'appid',
			'token',
			'interact_3',
			'activity_1_to',
			'activity_1_in'
		));
		if (!empty($data['subscribe'])) {
			Gionee_Service_Config::setValue('weixin_conf', json_encode($data));
			Admin_Service_Log::op($data);
			$this->output(0, '添加成功！');
		}
		$data = Gionee_Service_Config::getValue('weixin_conf');
		$this->assign('data', json_decode($data, true));
	}

	public function focusWxAction() {
		$data = trim($_POST['weixin_focus_config']);
		//var_dump($data);exit;
		if (!empty($data)) {
			Gionee_Service_Config::setValue('weixin_focus_config', json_encode($data));
			$this->output(0, '添加成功！');
		}
		$data = Gionee_Service_Config::getValue('weixin_focus_config');
		$this->assign('data', json_decode($data));
	}

	/**
	 * 反馈
	 */
	public function feedbackAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			$where          = array();
			$where['key']   = array('IN', array('interact_1', 'interact_2'));
			list($total, $list) = Gionee_Service_WxFeedback::getList($page, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['key']        = ($v['key'] == 'interact_1') ? '问题反馈' : '功能建议';
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	/**
	 * 指令
	 */
	public function commandAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			$where          = array();

			list($total, $list) = Gionee_Service_WxKey::getList($page, $offset, $where, $orderBy);
			$types = array(1 => '文本', 2 => '图片', 3 => '图文');

			foreach ($list as $k => $v) {
				$list[$k]['type']       = $types[$v['type']];
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['status']     = ($v['status'] == '1') ? '开启' : '关闭';

			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function commandeditAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'key', 'val', 'type', 'status', 'link'));
		$now      = time();
		if (!empty($postData['key'])) {
			$postData['updated_at'] = $now;

			$imgInfo = Common::upload('img', 'nav');
			if (!empty($imgInfo['data'])) {
				$attachPath      = Common::getConfig('siteConfig', 'attachPath');
				$postData['img'] = $imgInfo['data'];
				if ($postData['type'] == 2) {
					$file            = $attachPath . $imgInfo['data'];
					$file            = realpath($file);
					$wx              = new Vendor_Weixin();
					$upInfo          = $wx->uploadFile($file);
					$postData['val'] = $upInfo['media_id'];
				}
			}

			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_WxKey::add($postData);
			} else {
				$ret = Gionee_Service_WxKey::set($postData, $postData['id']);
			}
			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Gionee_Service_WxKey::get($id);
		$this->assign('info', $info);
	}

	public function commanddelAction() {
		$id  = $this->getInput('id');
		$ret = Gionee_Service_WxKey::del($id);
		Admin_Service_Log::op($id);
		if ($ret) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败');
		}
	}

	/**
	 * 菜单
	 */
	public function menuAction() {
		$up   = $this->getInput('up');
		$wx   = new Vendor_Weixin();
		$list = $wx->menuGet();

		$menu = isset($list['menu']['button']) ? $list['menu']['button'] : array();
		if (empty($menu) || !empty($up)) {

			if ($up) {
				$wx->menuDelete();
			}
			$ret = $wx->menuCreate();

			if ($ret['errcode'] == 0) {
				$this->output(0, '添加成功');
			} else {
				$this->output(-1, '添加失败');
			}
		}


		$this->assign('list', $menu);
	}

	/**
	 * 用户
	 */
	public function userAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			$where          = array();

			list($total, $list) = Gionee_Service_WxUser::getList($page, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function msgAction() {
		$keys    = array(
			'product_1'  => Lang::_('USER_CENTER'),
			'product_2'  => '免费电话',
			'activity_2' => '最新活动',
		);
		$page    = $this->getInput('page');
		$page    = max($page, 1);
		$where   = array();
		$offset  = $this->pageSize;
		$orderBy = array('id' => 'DESC');
		list($total, $dataList) = Gionee_Service_WxMsg::getList($page, $offset, $where, $orderBy);

		foreach ($dataList as $k => $v) {
			$dataList[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
			$dataList[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
			$dataList[$k]['num']        = count(json_decode($v['sub_msg'], true));
		}

		$this->assign('keys', $keys);
		$this->assign('dataList', $dataList);
		$this->assign('page', $page);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['msgUrl'] . "&"));

	}

	public function msgeditAction() {
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'key', 'title', 'desc', 'img', 'url', 'sub_msg'));
		$now      = time();

		$keys = array(
			'product_1'  => Lang::_('USER_CENTER'),
			'product_2'  => '免费电话',
			'activity_2' => '最新活动',
		);
		if (!empty($postData['key'])) {

			$i      = 1;
			$subMsg = array();
			foreach ($postData['sub_msg'] as $v) {
				if (!empty($v['title'])) {
					$subMsg[$i] = $v;
					$i++;
				}
			}
			$postData['updated_at'] = $now;
			$postData['sub_msg']    = json_encode($subMsg);
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_WxMsg::add($postData);

			} else {
				$ret = Gionee_Service_WxMsg::set($postData, $postData['id']);
			}

			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info            = Gionee_Service_WxMsg::get($id);
		$info['sub_msg'] = json_decode($info['sub_msg'], true);
		$this->assign('info', $info);
		$this->assign('keys', $keys);
	}

	public function concernAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'asc';
			$orderBy[$sort] = $order;
			$where          = array();
			$where['key']   = 'activity_1';
			$rewards        = Gionee_Service_Config::getValue('user_wx_content_rewards');
			list($total, $list) = Gionee_Service_WxFeedback::getList($page, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$wxUser                 = Gionee_Service_WxUser::getBy(array('openid' => $v['openid']));
				$userInfo               = Gionee_Service_User::getUser($wxUser['uid']);
				$list[$k]['openid']     = $userInfo['username'];
				$list[$k]['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
				$list[$k]['key']        = $rewards;
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function msgdelAction() {
		$id  = $this->getInput('id');
		$ret = Gionee_Service_WxMsg::del($id);

		if ($ret) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败');
		}
	}

}
