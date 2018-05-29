<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UserinnermsgController
 * 站内信模板管理
 * @author huwei
 */
class UserinnermsgController extends Admin_BaseController {
	public $actions = array(
		'listtplUrl' => '/Admin/Userinnermsg/listtpl',
		'edittplUrl' => '/Admin/Userinnermsg/edittpl',
	);

	public function listtplAction() {
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {
			$sort           = !empty($get['sort']) ? $get['sort'] : 'create_time';
			$order          = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort] = $order;
			$where          = array();
			foreach ($_POST['filter'] as $k => $v) {
				$where[$k] = $v;
			}

			$list = User_Service_InnerMsg::getTplDao()->getsBy($where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
			}
			$ret = array(
				'total' => count($list),
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function edittplAction() {
		$id               = $this->getInput('id');
		$postData         = $this->getPost(array('id', 'code', 'name', 'text', 'desc'));
		$postData['text'] = $_POST['text'];
		$now              = time();
		if (!empty($postData['code'])) {

			if (empty($postData['code'])) {
				$this->output(-1, '输入代码');
			}

			if (empty($postData['name'])) {
				$this->output(-1, '输入名称');
			}

			if (empty($postData['text'])) {
				$this->output(-1, '输入内容');
			}

			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$postData['updated_at'] = $now;
				$ret                    = User_Service_InnerMsg::getTplDao()->insert($postData);
			} else {
				$postData['updated_at'] = $now;
				$ret                    = User_Service_InnerMsg::getTplDao()->update($postData, $postData['id']);
			}

			if ($ret) {
				User_Service_InnerMsg::getTplData('', true);
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = User_Service_InnerMsg::getTplDao()->get($id);
		$this->assign('info', $info);
	}
}