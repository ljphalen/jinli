<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GuidetypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/GuideType/index',
		'addUrl' => '/Admin/GuideType/add',
		'addPostUrl' => '/Admin/GuideType/add_post',
		'editUrl' => '/Admin/GuideType/edit',
		'editPostUrl' => '/Admin/GuideType/edit_post',
		'deleteUrl' => '/Admin/GuideType/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		list(, $pptype) = Gc_Service_GuideType::getAllGuideType();
		$this->assign('pptype', $pptype);
		list($total, $result) = Gc_Service_GuideType::getList($page, $perpage);
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl']));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_GuideType::getGuidetype(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_GuideType::getGuidetype(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title'));
		$info = $this->_cookData($info);
		$result = Gc_Service_GuideType::addGuidetype($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title'));
		$info = $this->_cookData($info);
		$ret = Gc_Service_GuideType::updateGuidetype($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_GuideType::getGuidetype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gc_Service_GuideType::deleteGuidetype($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 */
	public function toolAction() {
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->Gc_index_tool_file;
		if (is_file($cache_file)) {
			$info = include $cache_file;
		}
		$this->assign('tool', $info);
	}
	
	/**
	 * 
	 */
	public function edit_toolAction() {
		$Gc_index_tools = $this->getPost('tool');
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->Gc_index_tool_file;
		$ret = Util_File::write($cache_file, "<?php\n return ".str_replace("\'",'', var_export($Gc_index_tools, TRUE))."\n?>");
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
}
