<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Category Controller
 * @author rainkid
 *
 */
class CategoryController extends Front_BaseController {
	
	public $perpage = 8;
	

	
    public function indexAction() {
    	$page = intval($this->getInput('page'));
    	$title = "商品分类";
    	if ($page < 1) $page = 1;
    	list(, $categorys) = Gc_Service_Category::getAllCategory();
    	$this->assign('categorys', $categorys);
    	$this->assign('title', $title);
    }
    
    /**
     *
     */
    public function searchAction() {
    	$category_id = intval($this->getInput('category_id'));
    	$category = Gc_Service_Category::getCategory($category_id);
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	list($total, $goods) = Gc_Service_TaokeGoods::getList($page, $this->perpage, array('category_id'=>$category_id));
    	$temp = array();
		$i = 0;
		foreach($goods as $key=>$value) {
			$temp[$i][] = $value;
			$i++;
			if($i == 3) $i = 0; 
		}
		$goods = $temp;
    	$this->assign('goods', $goods);
    	$this->assign('total', $total); 
    	$this->assign('title', $category['title']);
    }
    
    /**
     *
     */
    public function moreAction() {
    	$subject_id = intval($this->getInput('subject_id'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $goods) = Gc_Service_TaokeGoods::getList($page, $this->perpage, array('subject_id'=>$subject_id));
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0,'', array('hasnext'=> $hasnext, 'list'=> $goods, 'curpage'=> $page));
    }
}