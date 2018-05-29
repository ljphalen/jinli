<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class LinksController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/links/index',
		'addUrl' => '/Admin/links/add',
		'addPostUrl' => '/Admin/links/add_post',
		'editUrl' => '/Admin/links/edit',
		'editPostUrl' => '/Admin/links/edit_post',
		'deleteUrl' => '/Admin/links/delete',
		'editToolPostUrl' => '/admin/links/edit_tool'
	);
	
	public $perpage = 20;
	public $gou_index_tool_file = '/cache/gou_index_tool.php';
	public $link_type = array(
				1=>'流行风向标'
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $links) = Gou_Service_Links::getList($page, $perpage);
		
		$this->assign('links', $links);
		$this->assign('types', $this->link_type);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl']));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Links::getLinks(intval($id));
		$this->assign('info', $info);
		$this->assign('types', $this->link_type);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Links::getLinks(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('types', $this->link_type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'link', 'ptype'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Links::addLinks($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'link', 'ptype'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Links::updateLinks($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		if(!$info['link']) $this->output(-1, '链接不能为空.'); 
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Links::getLinks($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_Links::deleteLinks($id);
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
