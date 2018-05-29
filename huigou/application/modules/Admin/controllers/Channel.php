<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ChannelController extends Admin_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Admin/Channel/index',
		'addUrl' => '/Admin/Channel/add',
		'addPostUrl' => '/Admin/Channel/add_post',
		'editUrl' => '/Admin/Channel/edit',
		'editPostUrl' => '/Admin/Channel/edit_post',
		'deleteUrl' => '/Admin/Channel/delete',
	);
	
	public $perpage = 20;
	public $parents;//上级分类

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->roots = Gc_Service_Channel::getRootList();
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		//二级
		$parents = Gc_Service_Channel::getParentList();	
		
		$pids = array();
		foreach ($parents as $key => $value) {
			$pids[] = $value['id'];
		}
		
		//三级
		$chnnels =  Gc_Service_Channel::getListByParentIds($pids);
		
		$list = $this->_cookParent($parents, $chnnels);
		
		$channellist = $this->_cookParent($this->roots, $list);		
		
		$this->assign('channels', $channellist);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){		
		$parents = Gc_Service_Channel::getParentList();
		$list = $this->_cookRoot($this->roots, $parents);
		$this->assign('list', $list);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		if ($this->getPost('parent') != 0) {
			$item = Gc_Service_Channel::getChannel($this->getPost('parent'));			
			if($item['root_id'] == 0 && $item['parent_id'] == 0) {
				//二级
				$root_id = $item['id'];
				$parent_id = $item['id'];
			} else {
				//三级
				$root_id = $item['root_id'];
				$parent_id = $item['id'];
			}
		}else{
			//项级
			$root_id = 0;
			$parent_id = 0;
		}
		$info = $this->getPost(array('name', 'sort', 'root_id', 'parent_id'));
		$info['root_id'] = $root_id;
		$info['parent_id'] = $parent_id;
		/*$info['secret'] = Common::encrypt($root_id.$parent_id.$info['name']);*/
		
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		
		//判断重复
		$list =  Gc_Service_Channel::getListBy(array('root_id'=>$info['root_id'], 'parent_id'=>$info['parent_id'], 'name'=>$info['name']));
		if($list) $this->output(-1, '名称已存在，不能重复输入.');
		$ret = Gc_Service_Channel::addChannel($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gc_Service_Channel::getChannel(intval($id)); 		
		$parents = Gc_Service_Channel::getParentList();
		$list = $this->_cookRoot($this->roots, $parents);
		$this->assign('list', $list);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		
		if ($this->getPost('parent') != 0) {
			$item = Gc_Service_Channel::getChannel($this->getPost('parent'));
			if($item['root_id'] == 0 && $item['root_id'] == 0) {
				//二级
				$root_id = $item['id'];
				$parent_id = $item['id'];
			} else {
				//三级
				$root_id = $item['root_id'];
				$parent_id = $item['id'];
			}
		}else{
			//项级
			$root_id = 0;
			$parent_id = 0;
		}		
		
		$info = $this->getPost(array('id', 'name', 'sort','root_id', 'parent_id'));
		$info['root_id'] = $root_id;
		$info['parent_id'] = $parent_id;		
		/*$info['secret'] = Common::encrypt($root_id.$parent_id.$info['name']);*/
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		$ret = Gc_Service_Channel::updateChannel($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_Channel::getChannel($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gc_Service_Channel::deleteChannel($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookRoot($roots, $parents) {
		$tmp = Common::resetKey($roots, 'id');
		foreach ($parents as $key=>$value) {
			$tmp[$value['root_id']]['items'][] = $value;
			$tmp[$value['root_id']]['items'] = Common::resetKey($tmp[$value['root_id']]['items'], 'id');
		}
		return $tmp;
	}
	
	/**
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookParent($parents, $channels) {
		$tmp = Common::resetKey($parents, 'id');
		foreach ($channels as $key=>$value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
	}
}
