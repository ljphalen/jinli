<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 端午活动
 * @author huwei
 */
class DuanwuController extends Admin_BaseController {

	public $actions = array(
		'configUrl'   => '/Admin/Duanwu/config',
		'listuserUrl' => '/Admin/Duanwu/listuser',
		'listlogUrl'  => '/Admin/Duanwu/listlog',
		'edituserUrl' => '/Admin/Duanwu/edituser',
		'editlogUrl'  => '/Admin/Duanwu/editlog',
	);

	public function configAction() {
		$it                         = array(
			'duanwu_times',
			'duanwu_open',
			'duanwu_goods',
			'duanwu_kind',
		);
		$params                     = $this->getInput($it);
		$params['duanwu_kind']      = $_POST['duanwu_kind'];
		$params['duanwu_coin_rate'] = $_POST['duanwu_coin_rate'];


		if ($_POST['duanwu_times']) {
			$config['duanwu_config'] = Common::jsonEncode($params);

			foreach ($config as $key => $value) {
				Gionee_Service_Config::setValue($key, $value);
			}
			Admin_Service_Log::op($config);
			$this->output(0, '操作成功.');
		}
		$configs = Gionee_Service_Config::getValue('duanwu_config');
		$this->assign('configs', $configs);
		$goodsList = User_Service_Commodities::getsBy(array('event_flag' => 1), array('sort' => 'asc'));
		$this->assign('configs', json_decode($configs, true));
		$this->assign('goodsList', $goodsList);
		$this->assign('rankKinds', Event_Service_Duanwu::getKindRate());


	}

	public function listuserAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
			$page    = max(intval($get['page']), 1);
			$offset  = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort    = !empty($get['sort']) ? $get['sort'] : 'id';
			$order   = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy = array();
			if (empty($_POST['filter'])) {
				$orderBy[$sort] = $order;
			}

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					$where[$k] = $v;
				}
			}

			$total = Event_Service_Duanwu::getUserDao()->count($where);
			$start = ($page - 1) * $offset;
			$list  = Event_Service_Duanwu::getUserDao()->getList($start, $offset, $where, $orderBy);

			$status = array(0 => '初始', 1 => '开始', 2 => '结束');
			foreach ($list as $k => $v) {
				$list[$k]['updated_at'] = date('y/m/d H:i:s', $v['updated_at']);
				$list[$k]['created_at'] = date('y/m/d H:i:s', $v['created_at']);
				$list[$k]['status']     = $status[$v['status']];
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

	}

	public function listlogexportAction() {
		$where = array('status' => 1);
		$list  = Event_Service_Duanwu::getLogDao()->getsBy($where);
		foreach ($list as $k => $v) {
			$list[$k]['created_at'] = date('y/m/d H:i:s', $v['created_at']);
			$list[$k]['updated_at'] = date('y/m/d H:i:s', $v['updated_at']);
			$list[$k]['status']     = $v['status'] == 1 ? '已领取' : '未领取';
			$area                   = Vendor_IP::find($v['ip_addr']);
			$list[$k]['ip_area']    = implode(',', array($area[1], $area[2]));
		}
		Common::export($list, '', '', "端午活动奖励列表");
	}

	public function listlogAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
			$page    = max(intval($get['page']), 1);
			$offset  = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort    = !empty($get['sort']) ? $get['sort'] : 'id';
			$order   = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy = array();
			if (empty($_POST['filter'])) {
				$orderBy[$sort] = $order;
			}

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					$where[$k] = $v;
				}
			}

			$total = Event_Service_Duanwu::getLogDao()->count($where);
			$start = ($page - 1) * $offset;
			$list  = Event_Service_Duanwu::getLogDao()->getList($start, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['status']     = $v['status'] == 1 ? '已领取' : '未领取';
				$area                   = Vendor_IP::find($v['ip_addr']);
				$list[$k]['ip_area']    = implode(',', array($area[1], $area[2]));
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

	}


}