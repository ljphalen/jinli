<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class TypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Type/index',
		'addUrl' => '/Admin/Type/add',
		'addPostUrl' => '/Admin/Type/add_post',
		'editUrl' => '/Admin/Type/edit',
		'editPostUrl' => '/Admin/Type/edit_post',
		'deleteUrl' => '/Admin/Type/delete',
	);
	
	public $perpage = 20;
	public $ptype = array(
				1=>'区域一',
				2=>'区域二',
				3=>'区域三',
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Games_Service_Type::getList($page, $perpage);
		$this->assign('result', $result);
		$this->assign('ptype', $this->ptype);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl']));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Type::getType(intval($id));
		$this->assign('info', $info);
		$this->assign('ptype', $this->ptype);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Games_Service_Type::getType(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ptype', $this->ptype);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'status'));
		$info = $this->_cookData($info);
		$result = Games_Service_Type::addType($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'status'));
		$info = $this->_cookData($info);
		$ret = Games_Service_Type::updateType($info, intval($info['id']));
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
		$info = Games_Service_Type::getType($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Games_Service_Type::deleteType($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 */
	public function toolAction() {
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->gou_index_tool_file;
		if (is_file($cache_file)) {
			$info = include $cache_file;
		}
		$this->assign('tool', $info);
	}
	
	/**
	 * 
	 */
	public function edit_toolAction() {
		$gou_index_tools = $this->getPost('tool');
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->gou_index_tool_file;
		$ret = Util_File::write($cache_file, "<?php\n return ".str_replace("\'",'', var_export($gou_index_tools, TRUE))."\n?>");
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
}
