<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Goods/index',
		'addUrl' => '/Admin/Client_Goods/add_step1',
		'addPostUrl' => '/Admin/Client_Goods/add_post',
		'editUrl' => '/Admin/Client_Goods/edit',
		'editPostUrl' => '/Admin/Client_Goods/edit_post',
		'deleteUrl' => '/Admin/Client_Goods/delete',
		'uploadUrl' => '/Admin/Client_Goods/upload',
		'uploadPostUrl' => '/Admin/Client_Goods/upload_post',
		'uploadImgUrl' => '/Admin/Client_Goods/uploadImg',
		'step1Url' => '/Admin/Client_Goods/add_step1',
		'step2Url' => '/Admin/Client_Goods/add_step2'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$category_id = intval($this->getInput('category_id'));
		$status = $this->getInput('status');
		
		if ($page < 1) $page = 1;
		//get all subjects
		list(,$client_categorys) = Client_Service_Category::getAllCategory();
		$client_categorys = Common::resetKey($client_categorys, 'id');
		$areas = Common::getConfig('areaConfig', 'client_area');
		$categorys = $this->_cookCategory($areas, $client_categorys);
		
		$this->assign('categorys', $categorys);
		$this->assign('client_categorys', $client_categorys);
		
		//get search params 
		$search = array();
		if ($category_id) $search['category_id'] = $category_id;
		if($status == 1){
			$search['status'] = 1;
		} else if($status == 2){
			$search['status'] = 0;
			$st = 1;
		}
		
		//if ($status != -1) $search['status'] = $status;
		
		//get Clientgoods list
		list($total, $goods) = Client_Service_Goods::getList($page, $this->perpage, $search);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('st', $st);
		//get pager
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Goods::getgoods(intval($id));
		list(,$categorys) = Client_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$areas = Common::getConfig('areaConfig', 'client_area');
		$categorys = $this->_cookCategory($areas, $categorys);
		$this->assign('categorys', $categorys);
		$topApi  = new Api_Top_Service();
		$tinfo = $topApi->getItemInfo($info['num_iid']);
		$info['item_imgs'] = $tinfo['item_imgs']['item_img'];
		if(isset($info['item_imgs']['url'])) $info['item_imgs']  = array($info['item_imgs']);
		
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * taobaoke Clientgoods list
	 */
	public function add_step1Action() {
		$page = intval($this->getInput('page'));
		$cid = intval($this->getInput('cid'));
		$keyword = $this->getInput('keyword');
		
		if ($page < 1) $page = 1;
		
		//get Clientgoods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->taobaoTbkItemsGet(array(
			'page_no'=>$page, 
			'page_size'=>$this->perpage, 
			'cid'=>$cid, 
			'keyword'=>$keyword, 
			'is_mobile'=>"true"
		));
		
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		//get taobao num_iids
		$num_iids = array();
		foreach($goods as $key=>$value) {
			$num_iids[] = $value['num_iid'];
		}
		
		//get info infos;
		if (count($num_iids)) {
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>implode(',',$num_iids)));
			if (isset($infos['num_iid'])) $infos = array($infos);
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
	 * add Clientgoods page show
	 */
	public function add_step2Action() {
		$num_iid = $this->getInput('num_iid');
		//get all categorys
		list(,$categorys) = Client_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$areas = Common::getConfig('areaConfig', 'client_area');
		$categorys = $this->_cookCategory($areas, $categorys);
		$this->assign('categorys', $categorys);
		$topApi  = new Api_Top_Service();
		$info = $topApi->getTbkItemInfo(array('num_iids'=>$num_iid));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'category_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'descrip'));
		$info = $this->_cookData($info);
		$result = Client_Service_Goods::addgoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'category_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Client_Service_Goods::updategoods($info, intval($info['id']));
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
		if(!$info['category_id']) $this->output(-1, '请选择商品分类.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time'].'00:00:00');
		$info['end_time'] = strtotime($info['end_time'].'23:59:59');
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		
		$info['descrip'] = $this->updateImgUrl($info['descrip']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Goods::getgoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Client_Service_Goods::deletegoods($id);
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
		$ret = Common::upload('imgFile', 'Clientgoods');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Clientgoods');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    /**
     *
     * @param unknown_type $pids
     * @param unknown_type $categorys
     */
    private function _cookCategory($areas, $categorys) {
    	
    	$new_areas = array();
		foreach($areas as $key=>$value){
		     $new_areas[$key] = array('area_name'=>$value);
		}
    	$tmp = $new_areas;
    	
    	foreach ($categorys as $key=>$value) {
    		$tmp[$value['area_id']]['items'][] = $value;
    	}
    	return $tmp;
    }
}
