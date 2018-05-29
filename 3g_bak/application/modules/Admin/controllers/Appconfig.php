<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class AppconfigController
 * 应用配置
 * @author rainkid
 */
class AppconfigController extends Admin_BaseController {

	public $actions = array(
		'editUrl'       => '/admin/Appconfig/index',
		'editPostUrl'   => '/admin/Appconfig/edit_post',
		'uploadUrl'     => '/Admin/Appconfig/upload',
		'uploadPostUrl' => '/Admin/Appconfig/upload_post',
	);

	public function indexAction() {
		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	public function edit_postAction() {
		$config = $this->getInput(array(
			'must_num',
			'recommend_num',
			'ranking_num',
			'new_num',
			'type_num',
			'theme_num',
			'search_num',
			'user_num',
			'default_icon'
		));
		if (!$config['must_num']) {
			$this->output(-1, '必备应用数量不能为空.');
		}
		if (!$config['recommend_num']) {
			$this->output(-1, '推荐应用数量不能为空.');
		}
		if (!$config['ranking_num']) {
			$this->output(-1, '排行应用数量不能为空.');
		}
		if (!$config['new_num']) {
			$this->output(-1, '新品应用数量不能为空.');
		}
		if (!$config['type_num']) {
			$this->output(-1, '分类应用数量不能为空.');
		}
		if (!$config['theme_num']) {
			$this->output(-1, '专题应用数量不能为空.');
		}
		if (!$config['search_num']) {
			$this->output(-1, '搜索应用数量不能为空.');
		}
		if (!$config['user_num']) {
			$this->output(-1, '搜索推荐应用数量不能为空.');
		}
		if (!$config['default_icon']) {
			$this->output(-1, '应用默认图标不能为空.');
		}

		foreach ($config as $key => $value) {
			Gionee_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'App');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
