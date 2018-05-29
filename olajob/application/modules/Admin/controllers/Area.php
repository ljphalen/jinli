<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class AreaController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Area/index',
		'addUrl' => '/Admin/Area/add',
		'addPostUrl' => '/Admin/Area/add_post',
		'editUrl' => '/Admin/Area/edit',
		'editPostUrl' => '/Admin/Area/edit_post',
		'deleteUrl' => '/Admin/Area/delete',
		'importUrl' => '/Admin/Area/import',
		'importPostUrl' => '/Admin/Area/import_post',
	);
	
	public $perpage = 5;
	public $roots;//根
	public $parents;

	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		list(, $this->roots) = Ola_Service_Area::getsBy(array('root_id'=>0, 'parent_id'=>0), array('sort'=>'DESC' ,'id'=>'DESC'));
		list(, $this->parents) =  Ola_Service_Area::getsBy(array('root_id'=>array('!=', 0), 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));

		list($total, $root_list) = Ola_Service_Area::getList($page, $this->perpage, array('root_id'=>0, 'parent_id'=>0));
		$root_list = Common::resetKey($root_list, 'id');
		list(, $parent_list) = Ola_Service_Area::getsBy(array('root_id'=>array('IN', array_keys($root_list)), 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
	    foreach ($root_list as $key=>$value) {
	        /*list(, $areas) = Ola_Service_Area::getsBy(array('parent_id'=>$value['id'], 'root_id'=>$value['root_id']), array('id'=>'DESC'));
	        if($areas) {
	        	$this->parents[$key]['areas'] = $areas;
	        }*/
	    }

		$list = $this->_cookdata($root_list, $parent_list);
		$this->assign('list', $list);
		$this->assign('areas', $areas);
		//$url = $this->actions['listUrl']. '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl']. '/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('name', 'parent_id', 'root_id', 'sort'));
		if (!$info['name']) $this->output(-1, '名称不能为空.');
		
		//检测重复
		$area = Ola_Service_Area::getBy(array('name'=>$info['name']));
		if ($area) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Ola_Service_Area::add($info);
		if (!$ret) {
			$this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Ola_Service_Area::get(intval($id)); 
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
	    $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id','name', 'root_id', 'parent_id', 'sort'));
		
		//检测重复
		$area = Ola_Service_Area::getBy(array('name'=>$info['name']));
		if ($area && $area['id'] != $info['id']) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Ola_Service_Area::update($info, $info['id']);
		if (!$ret) {
		    $this->output(-1, '操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Ola_Service_Area::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Ola_Service_Area::delete($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * @param unknown_type $pids
	 * @param unknown_type $categorys
	 */
	private function _cookdata($roots, $parent) {
		$tmp = Common::resetKey($roots, 'id');
		foreach ($parent as $key=>$value) {
			$tmp[$value['root_id']]['items'][] = $value;
		}
		return $tmp;
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function rootsAction() {
	    $list = Ola_Service_Area::getsData(array('root_id'=>0, 'parent_id'=>0), array('id'=>'DESC'));
	    $data = array();
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function parentsAction() {
	    $root_id = $this->getInput('id');
	    $list = Ola_Service_Area::getsData(array('root_id'=>$root_id, 'parent_id'=>0), array('id'=>'DESC'));
	    $data = array();
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public function areasAction() {
	    $parent_id = $this->getInput('id');
	    $list = Ola_Service_Area::getsData(array('parent_id'=>$parent_id), array('id'=>'DESC'));
	    $data = array();
	    foreach ($list as $key=>$value) {
	        $data[] = array(
	        'id'=>$value['id'],
	        'name'=>$value['name']
	        );
	    }
	    $this->output(0, '', $data);
	}
}
