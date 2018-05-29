<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 搜狐新闻页对应接口管理
 */
class SohuController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Sohu/index',
		'editUrl'       => '/Admin/Sohu/edit',
		'delUrl'        => '/Admin/Sohu/delete',
		'uploadUrl'     => '/Admin/Sohu/upload',
		'uploadPostUrl' => '/Admin/Sohu/upload_post',
	);

	public $perpage = 20;


	public function indexAction() {

		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
			$page            = max(intval($get['page']), 1);
			$offset          = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort            = !empty($get['sort']) ? $get['sort'] : 'id';
			$order           = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort]  = $order;
			$orderBy['sort'] = 'desc';
			$where           = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					if ($k == 'start_time') {
						$where['start_time'] = array('>', strtotime($v));
					} else if ($k == 'end_time') {
						$where['end_time'] = array('<', strtotime($v));
					} else {
						$where[$k] = $v;
					}

				}

			}

			list($total, $list) = Gionee_Service_Sohu::getList($page, $offset, $where, $orderBy);

			$imgPath   = Common::getImgPath();
			$positions = Gionee_Service_Sohu::$positions;
			foreach ($list as $k => $v) {
				if (!empty($v['partner_id'])) {
					$parnter             = Gionee_Service_Parter::get($v['partner_id']);
					$list[$k]['parnter'] = $parnter['name'];
				} else {
					$list[$k]['parnter'] = '内部';
				}

				$list[$k]['position']   = $positions[$v['position']];
				$list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
				$list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
				$list[$k]['status']     = $v['status'] == 1 ? '开启' : '关闭';
				$list[$k]['pic']        = $v['pic'] ? $imgPath . $v['pic'] : '';

			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}

		$this->assign('positions', Gionee_Service_Sohu::$positions);
	}

	public function editAction() {
		$id         = $this->getInput('id');
		$pD         = $this->getPost(array(
			'id',
			'sort',
			'title',
			'position',
			'link',
			'pic',
			'start_time',
			'end_time',
			'status',
			'attribute',
			'parter_id',
			'color',
			'cp_id',
			'bid'
		));
		$pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
		if (!empty($pD['title'])) {
			$imgInfo = Common::upload('pic', 'ad');
			if (!empty($imgInfo['data'])) {
				$pD['pic'] = $imgInfo['data'];
			}

			if (empty($pD['title'])) {
				$this->output(-1, '输入名称');
			}

			$info = $this->_cookData($pD);
			if (empty($info['id'])) {
				Admin_Service_Access::pass('add');
				$ret = Gionee_Service_Sohu::addAd($info);
			} else {
				Admin_Service_Access::pass('edit');
				$ret = Gionee_Service_Sohu::updateAd($info, intval($info['id']));
			}
			Admin_Service_Log::op($info);
			if (!$ret) $this->output(-1, '操作失败');
			$this->output(0, '操作成功.');

		}
		$info = Gionee_Service_Sohu::getAd(intval($id));
		if (empty($info['attribute'])) {
			$info['attribute'] = '普通';
		}
		if (intval($id)) {
			$urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
			$this->assign('urlInfo', $urlInfo);
			$blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
			$this->assign('blist', $blist);
			$urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
			$this->assign('urlList', $urlList);
		}
		$cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
		$this->assign('info', $info);
		$this->assign('cooperators', $cooperators);
		$this->assign('attriubtes', $this->_getAllAttriubtes());
		$this->assign('positions', Gionee_Service_Sohu::$positions);
	}


	private function _getAllPartners() {
		$coopterator = Gionee_Service_Parter::getAll(array('id' => 'DESC'));
		array_unshift($coopterator, array('id' => 0, 'name' => '内部', 'url' => ''));
		return $coopterator;
	}

	private function _getAllAttriubtes() {
		$attriutes = Gionee_Service_Attribute::getAll();
		array_unshift($attriutes, array('id' => 0, 'name' => '普通'));
		return $attriutes;
	}

	private function _cookData($info) {
		if (!$info['link']) {
			$this->output(-1, '广告链接不能为空.');
		}
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if ($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']);
		if ($info['position'] != 2 && $info['position'] != 5) {
			$info['pic'] = '';
		}
		return $info;
	}

	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$ids = (array)$this->getPost('id');
		foreach ($ids as $id) {
			$info = Gionee_Service_Sohu::getAd($id);
			if (empty($info['id'])) {
				$this->output(-1, '无法删除');
			}
			$result = Gionee_Service_Sohu::deleteAd($id);
		}

		Admin_Service_Log::op($ids);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

}