<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class TaokegoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/TaokeGoods/index',
		'addUrl' => '/Admin/TaokeGoods/add',
		'addPostUrl' => '/Admin/TaokeGoods/add_post',
		'editUrl' => '/Admin/TaokeGoods/edit',
		'editPostUrl' => '/Admin/TaokeGoods/edit_post',
		'deleteUrl' => '/Admin/TaokeGoods/delete',
		'uploadUrl' => '/Admin/TaokeGoods/upload',
		'uploadPostUrl' => '/Admin/TaokeGoods/upload_post',
		'uploadImgUrl' => '/Admin/TaokeGoods/uploadImg',
		'step1Url' => '/Admin/TaokeGoods/add_step1',
		'step2Url' => '/Admin/TaokeGoods/add_step2'
	);
	
	public $perpage = 20;
	public $parents;//父标签
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->parents = Gc_Service_GoodsLabel::getParentList();
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$subject_id = intval($this->getInput('subject_id'));
		$category_id = intval($this->getInput('category_id'));
		$label_id = intval($this->getInput('label_id'));
		if ($page < 1) $page = 1;
		
		//get all subjects
		list($total,$subjects) = Gc_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');
		$this->assign('subjects', $subjects);

		
		//get all categorys
		list($total, $categorys) = Gc_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		
		
		list(, $category) = Gc_Service_Category::getAllCategory();
		$this->assign('category', $category);
		
		//get all labels
		list($total,$labels) = Gc_Service_GoodsLabel::getAllGoodsLabel();
		$labels = Common::resetKey($labels, 'id');
		$this->assign('labels', $labels);
		
		
		//子标签
		$childs = Gc_Service_GoodsLabel::getChildList();
		$list = $this->_cookParent($this->parents, $childs);
		$this->assign('list', $list);
		$this->assign('parents', $this->parents);
		$this->assign('childs', $childs);
		
		
		//get search params
		$search = array();
		if ($subject_id) $search['subject_id'] = $subject_id;
		if ($category_id) $search['category_id'] = $category_id;
		if ($label_id) $search['label_id'] = $label_id;
		//get goods list
		list($total, $goods) = Gc_Service_TaokeGoods::getList($page, $this->perpage, $search);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		
		
		
		
		$this->assign('search', $search);
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
		$info = Gc_Service_TaokeGoods::getGoods(intval($id));

		$this->assign('labels', $this->getLabels());
		
		list($total,$subjects) = Gc_Service_Subject::getAllSubjectSort();
		$this->assign('subjects', $subjects);
		
		list(, $category) = Gc_Service_Category::getAllCategory();
		$this->assign('category', $category);
		
		$topApi  = new Api_Top_Service();
		$tinfo = $topApi->getItemInfo($info['num_iid']);
		$info['item_imgs'] = $tinfo['item_imgs']['item_img'];
		if(isset($info['item_imgs']['url'])) $info['item_imgs']  = array($info['item_imgs']);
		
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * taobaoke goods list
	 */
	public function add_step1Action() {
		$page = intval($this->getInput('page'));
		$cid = intval($this->getInput('cid'));
		$keyword = $this->getInput('keyword');
		
		if ($page < 1) $page = 1;
		
		//get goods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'cid'=>$cid, 'keyword'=>$keyword, 'is_mobile'=>"true"));
		
		$goods = $ret['taobaoke_items']['taobaoke_item'];
		$total = $ret['total_results'];
		
		//get taobao num_iids
		$num_iids = array();
		foreach($goods as $key=>$value) {
			$num_iids[] = $value['num_iid'];
		}
		
		//get info infos;
		if (count($num_iids)) {
			$infos = $topApi->getItemInfos($num_iids);
			$infos = Common::resetKey($infos, 'num_iid');
		}
		
		$this->assign('goods', $goods);
		$this->assign('infos', $infos);		

		//get taobao item cates
		$item_cats = $topApi->getTaoBaoItemCats();
		$this->assign('item_cats', $item_cats['item_cats']['item_cat']);
		
		$this->assign('cid', $cid);
		$this->assign('keyword', $keyword);
		
		$url = $this->actions['step1Url'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * add goods page show
	 */
	public function add_step2Action() {
		$num_iid = $this->getInput('num_iid');
	
		$this->assign('labels', $this->getLabels());
		//子标签
		$childs = Gc_Service_GoodsLabel::getChildList();
		$list = $this->_cookParent($this->parents, $childs);
		$this->assign('list', $list);
		$this->assign('parents', $this->parents);
		$this->assign('childs', $childs);
		
		
		list(, $category) = Gc_Service_Category::getAllCategory();
		$this->assign('category', $category);
		
		list($total,$subjects) = Gc_Service_Subject::getAllSubjectSort();
		$this->assign('subjects', $subjects);
		
		$topApi  = new Api_Top_Service();
			
		$info = $topApi->getItemInfo($num_iid);
		$info['item_imgs'] = $info['item_imgs']['item_img'];
		$taokeInfo = $topApi->getTaobaoke(array('num_iids'=>$num_iid));
		$info['commission'] = $taokeInfo['commission'];
		
		if(isset($info['item_imgs']['url'])) $info['item_imgs']  = array($info['item_imgs']);
		$this->assign('info', $info);
	}
	
	private function getLabels() {
		list(, $labels) = Gc_Service_GoodsLabel::getAllGoodsLabel();
		$temp = array();
		foreach($labels as $key=>$value) {
			if ($value['parent_id'] == 0) {
				$temp[$value['id']]['value'] = $value;
			} else {
				$temp[$value['parent_id']]['items'][] = $value;
			}
		}
		return $temp;
	}
	
	private function _cookParent($parents, $childs) {
		$tmp = Common::resetKey($parents, 'id');
		foreach ($childs as $key=>$value) {
			$tmp[$value['parent_id']]['items'][] = $value;
		}
		return $tmp;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'subject_id', 'category_id', 'label_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Gc_Service_TaokeGoods::getGoodsByNum_iids($info['num_iid']);
		if($ret) $this->output(-1, '该产品已经添加过了，请选择其他产品');
		$result = Gc_Service_TaokeGoods::addGoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'subject_id','category_id', 'label_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Gc_Service_TaokeGoods::updateGoods($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '商品名称不能为空.'); 
		if(!$info['img']) $this->output(-1, '商品图片不能为空.');
		if(!$info['label_id']) $this->output(-1, '请选择商品标签.');
		if(!$info['subject_id']) $this->output(-1, '请选择主题分类.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_TaokeGoods::getGoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gc_Service_TaokeGoods::deleteGoods($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'goods');
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $webroot . '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'goods');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
