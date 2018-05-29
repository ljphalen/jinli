<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class TagController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Tag/index',
		'addUrl' => '/Admin/Tag/add',
		'addPostUrl' => '/Admin/Tag/add_post',
		'editUrl' => '/Admin/Tag/edit',
		'editPostUrl' => '/Admin/Tag/edit_post',
		'deleteUrl' => '/Admin/Tag/delete',
        'getRootstUrl' => '/Admin/Category/roots',
        'getParenstUrl' => '/Admin/Category/parents',
	);
	
	public $perpage = 20;
	public $categorys;//分类
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
	    parent::init();
	    list(, $this->roots) = Dhm_Service_Category::getsBy(array('root_id'=>0, 'parent_id'=>0), array('id'=>'DESC'));
	    list(, $this->parents) =  Dhm_Service_Category::getsBy(array('root_id'=>array('!=', 0), 'parent_id'=>0), array('id'=>'DESC'));
        list(, $category) = Dhm_Service_Category::getAll();
        $tmp = array();
        foreach ($category as $key => $cate) {
            if ($cate['root_id'] == 0) {//根类
                $tmp['root'][] = $cate;
            } elseif ($cate['parent_id'] == 0) {//父类
                $tmp['parent'][$cate['root_id']][] = $cate;
            } else {//子类
                $tmp['child'][$cate['parent_id']][] = $cate;
            }
        }

        $this->category  = $tmp;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$root_id = $this->getInput('root_id');
		$category_id = $this->getInput('category_id');
        $cat = array_filter(array($root_id, $category_id));
		if(!empty($cat)) $search['category_id'] = array("IN",$cat);
		if ($page < 1) $page = 1;
		if ($category_id) {
		    $category = Dhm_Service_Category::get($category_id);
		    $this->assign('category', $category);

		}
        $parent_category = $this->category['parent'][$root_id];

        list($total, $tags) = Dhm_Service_Tag::getList($page, $this->perpage, $search, array('id'=>'DESC'));
        list(, $cats)  =Dhm_Service_Category::getAll();

        $this->assign('tags', $tags);
		$this->assign('categories',Common::resetKey($cats,'id'));
		$this->assign('search', $search);

        $this->assign('root_id', $root_id);
        $this->assign('category_id', $category_id);

        $this->assign('roots', $this->roots);
        $this->assign('parents', $parent_category);
		$this->cookieParams();
		//get pager
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Tag::get(intval($id));
		$category = Dhm_Service_Category::get($info['category_id']);
		$this->assign('category', $category);
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('roots', $this->roots);
		$this->assign('parents', $this->parents);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array(array('name', '#s_zb'), 'sort', 'status', 'category_id', 'root_id'));
		$info = $this->_cookData($info);
		//批量添加
		if(strpos($info['name'], '<br />') !== false) {
		    $names = explode('<br />', $info['name']);
		    $data = array();
		    foreach ($names as $key=>$value) {
		        $this->checkRepeat(array('name'=>$value, 'category_id'=>$info['category_id']));
		        if($value) {
    		        $data[] = array(
    		        	'id'=>'',
                        'name'=>$value,
                        'sort'=>$info['sort'],
                        'status'=>$info['status'],
                        'category_id'=>$info['category_id']
    		        );
		        }
		    }
		    
		    $result = Dhm_Service_Tag::batchadd($data);
		    if (!$result) $this->output(-1, '操作失败');
		    
		    $this->output(0, '操作成功');
		}
		
		//检测重复
		$this->checkRepeat(array('name'=>$value, 'category_id'=>$info['category_id']));
		
		$result = Dhm_Service_Tag::add($info);
		if (!$result) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功');
	}

    public function getAction()
    {
        $cat = $this->getInput(array('cate_id','root_id'));
        if($cat['cate_id']||$cat['root_id'])
        list(, $tags) = Dhm_Service_Tag::getsBy(array('category_id' => array('IN',array_filter($cat)),'status' => 1));
        $this->output(0, '获取分类标签成功', $tags);
    }
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','name', 'sort', 'status', 'category_id', 'root_id'));
		$info = $this->_cookData($info);
		//检测重复
		$tag = Dhm_Service_Tag::getBy(array('name'=>$info['name'], 'category_id'=>$info['category_id']));
		if ($tag && $tag['id'] != $info['id']) $this->output(-1, $info['name'].'已存在.');
		
		$ret = Dhm_Service_Tag::update($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
        if(!$info['category_id'])$info['category_id']=$info['root_id'];
		if(!$info['category_id']) $this->output(-1, '请选择分类.');
		return $info;
	}
	
	
	/**
	 * check repeat
	 */
	private function checkRepeat($params = array()) {
	    //检测重复
	    $tag = Dhm_Service_Tag::getBy($params);
	    if ($tag) $this->output(-1, $tag['name'].'已存在.');
	    
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Dhm_Service_Tag::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Dhm_Service_Tag::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
