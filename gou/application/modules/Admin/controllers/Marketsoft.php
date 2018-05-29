<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 软件商店功能
 * @author huangsg
 */
class MarketsoftController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Marketsoft/index',
		'addUrl' => '/Admin/Marketsoft/add',
		'addPostUrl' => '/Admin/Marketsoft/add_post',
		'editUrl' => '/Admin/Marketsoft/update',
		'editPostUrl' => '/Admin/Marketsoft/update_post',
		'deleteUrl' => '/Admin/Marketsoft/delete'
	);
	
	public $perpage = 20;
	
	/**
	 * 软件列表
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $list) = Gou_Service_Marketsoft::getList($page, $perpage);
		
		$this->assign('list', $list);
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 添加软件
	 */
	public function addAction(){
		
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('package', 'apk', 'version', 'download_url', 'status', 'apk_md5'));
		$info = $this->_cookData($info);
		if (Gou_Service_Marketsoft::checkApkMd5($info['apk_md5'])){
			$this->output(-1, '操作失败, 软件md5值重复，请确认后再输入。');
		}
		
		$result = Gou_Service_Marketsoft::addSoft($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 更新软件
	 */
	public function updateAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_Marketsoft::getOneSoft(intval($id));
		$this->assign('info', $info);
	}
	
	public function update_postAction(){
		$info = $this->getPost(array('id', 'package', 'apk', 'version', 'download_url', 'status', 'apk_md5'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Marketsoft::updateSoft($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 删除软件记录信息
	 */
	public function deleteAction(){
		$id = $this->getInput('id');
		$result = Gou_Service_Marketsoft::deleteSoft($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 参数过滤
	 * @param array $info
	 * @return array
	 */
	private function _cookData($info) {
		if(!$info['package']) $this->output(-1, '软件包名称不能为空.');
		if(!$info['apk']) $this->output(-1, 'APK名称不能为空.');
		if(!$info['version']) $this->output(-1, '软件版本号不能为空.');
		if(!$info['download_url']) $this->output(-1, '软件下载地址不能为空.');
		if(!$info['apk_md5']) $this->output(-1, '软件md5串不能为空.');
		return $info;
	}
}