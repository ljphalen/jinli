<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 短链接管理类
 * @author panxb
 *
 */
class ShorturlController extends Admin_BaseController {

	public $actions = array(
		'editUrl' => '/admin/ShortUrl/edit',
		'listUrl' => '/admin/shortUrl/list',
		'delUrl'  => '/admin/Shorturl/del'
	);

	public $perpage = 20;

	/**
	 * 添加短链接
	 */
	public function editAction() {
		$id       = $this->getInput('id');
		$key      = $this->getInput('key');
		$postData = $this->getPost(array('id', 'name', 'url', 'sub_type'));
		if (!empty($postData['name'])) {
			$realUrl = html_entity_decode($postData['url']);
			if (!filter_var($realUrl, FILTER_VALIDATE_URL) || !stristr($realUrl, 'http://')) {
				$this->output(-1, '操作失败');
			}

			$redirect = $postData['url'];
			$name     = $postData['name'];
			$sub_type = $postData['sub_type'];

			if (empty($postData['id'])) {
				$id   = 0;
				$type = 'OTHER';
				$t    = Gionee_Service_ShortUrl::genTVal($id . $redirect . $type .$sub_type. Common::$urlPwd);
				$info = array('id' => $id, 'type' => $type, '_url' => $redirect, 'name' => $name);
				$data = array(
					'key'        => $t,
					'mark'       => Common::jsonEncode($info),
					'url'        => $redirect,
					'created_at' => time(),
					'type'       => $type,
					'sub_type'   => $sub_type,
				);
				$res  = Gionee_Service_ShortUrl::add($data);
				if ($res) {
					$info['id'] = $res;
					Gionee_Service_ShortUrl::set(array('mark' => Common::jsonEncode($info)), $res);
				}
			} else {
				$info = Gionee_Service_ShortUrl::get($postData['id']);

				$tmp = json_decode($info['mark'], true);
				if (is_array($tmp)) {
					$tmp['name'] = $name;
				}
				$data = array('mark' => Common::jsonEncode($tmp), 'sub_type' => $sub_type);
				$res  = Gionee_Service_ShortUrl::set($data, $postData['id']);
			}

			if ($res) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}
		$info         = Gionee_Service_ShortUrl::get($id);
		$tmp          = json_decode($info['mark'], true);
		$info['name'] = $tmp['name'];
		$this->assign('info', $info);
	}

	/**
	 * 手动添加短链接列表
	 *
	 */
	public function listAction() {
		$get = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		if (!empty($get['togrid'])) {
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

			$where['mark'] = array('LIKE', 'OTHER');
			$total         = Gionee_Service_ShortUrl::getTotal($where);
			$list          = Gionee_Service_ShortUrl::getList($page, $offset, $where, array('id' => 'DESC'));
			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$tmp                    = json_decode($v['mark'], true);
				$list[$k]['name']       = $tmp['name'];
				$list[$k]['url']        = $v['url'];
				$list[$k]['key']        = sprintf('%s?t=%s', Common::getPrevShortUrl(), $v['key']);
			}

			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo Common::jsonEncode($ret);
			exit;
		}


	}

	public function delAction() {
		$id = $this->getInput('id');
		if (!$id) {
			exit(json_encode(array('key' => '0', 'msg' => 'ID不能为空')));
		}
		$res = Gionee_Service_ShortUrl::del($id);
		if ($res) {
			echo json_encode(array('key' => '1', 'msg' => '操作成功'));
		} else {
			echo json_decode(array('key' => '0', 'msg' => '操作失败'));
		}
	}
}