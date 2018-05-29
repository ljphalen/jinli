<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Mall_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Mall_Goods/index',
		'addUrl' => '/Admin/Mall_Goods/add',
		'addPostUrl' => '/Admin/Mall_Goods/add_post',
		'editUrl' => '/Admin/Mall_Goods/edit',
		'editPostUrl' => '/Admin/Mall_Goods/edit_post',
		'deleteUrl' => '/Admin/Mall_Goods/delete',
		'uploadUrl' => '/Admin/Mall_Goods/upload',
		'uploadPostUrl' => '/Admin/Mall_Goods/upload_post',
		'uploadImgUrl' => '/Admin/Mall_Goods/uploadImg',
		'step1Url' => '/Admin/Mall_Goods/add_step1',
		'step2Url' => '/Admin/Mall_Goods/add_step2'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$category_id = intval($this->getInput('category_id'));
        $params = $this->getInput(array('status', 'title', 'from_time', 'to_time'));
        if($params['status']=='')$params['status']=-1;
		if ($page < 1) $page = 1;
		//get search params 
		$search = array();
        if ($params['title']) $search['title'] = array('LIKE', $params['title']);
        if ($params['from_time']) $search['start_time'] = array('>=', strtotime($params['from_time']));
        if ($params['to_time']) $search['start_time'] = array('<=', strtotime($params['to_time']));
        if ($params['from_time'] && $params['to_time']) {
            $search['start_time'] = array(
                array('>=', strtotime($params['from_time'])),
                array('<=', strtotime($params['to_time']))
            );
        }

        if ($params['status'] != -1) $search['status'] = $params['status'];
		list($total, $goods) = Mall_Service_Goods::getList($page, $this->perpage, $search);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('search', $params);
		//get pager
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('page', $page);
	}
	
	/**
	 * 批量上下架
	 */
	public function batchAction(){
		$ids = $this->getInput('ids');
		$action = $this->getInput('action');
		
		if (!count($ids)) $this->output(-1, '没有可操作的项.');
		
		if ($action == 'open') {
			Mall_Service_Goods::updates($ids, array('status'=>1));
		} else if ($action == 'close') {
			Mall_Service_Goods::updates($ids, array('status'=>0));
		}
		
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
        $id = $this->getInput('id');
        $info = Mall_Service_Goods::getMallgoods(intval($id));
        $topApi = new Api_Top_Service();
        $tinfo = $topApi->getItemInfo($info['num_iid']);
        $info['item_imgs'] = $tinfo['item_imgs']['item_img'];
        if (isset($info['item_imgs']['url'])) $info['item_imgs'] = array($info['item_imgs']);

        $this->assign('info', $info);
    }
	
	/**
	 * 
	 * taobaoke Mallgoods list
	 */
	public function add_step1Action() {
		$page = intval($this->getInput('page'));
		$cid = intval($this->getInput('cid'));
		$keyword = $this->getInput('keyword');
		
		if ($page < 1) $page = 1;
		
		//get Mallgoods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->taobaoTbkItemsGet(array('page_no'=>$page, 'page_size'=>$this->perpage, 'cid'=>$cid, 'keyword'=>$keyword, 'is_mobile'=>"true"));
		
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		//get taobao num_iids
		 $num_iids = array();
        $goods = Common::resetKey($goods, 'num_iid');
		$num_iids = array_keys($goods);
		//get info infos;
		if (count($num_iids)) {
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>implode(',',$num_iids)));
			if (isset($infos['num_iid'])) $infos = array($infos);
			$infos = Common::resetKey($infos, 'num_iid');
		}
		
		$this->assign('goods', $goods);
		$this->assign('infos', $infos);		

		$this->assign('cid', $cid);
		$this->assign('keyword', $keyword);
		
		$url = $this->actions['step1Url'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * add Mallgoods page show
	 */
	public function add_step2Action() {
		$num_iid = $this->getInput('num_iid');
        $topApi  = new Api_Top_Service();
        $info = $topApi->getTbkItemInfo(array('num_iids'=>$num_iid));

        $isExist = Mall_Service_Goods::getBy(array('num_iid'=>$num_iid));
        $this->assign('isExist', $isExist);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'category_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$result = Mall_Service_Goods::addMallgoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'category_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Mall_Service_Goods::updateMallgoods($info, intval($info['id']));
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
        if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
        $info['start_time'] = strtotime($info['start_time']);
//		if(!$info['category_id']) $this->output(-1, '请选择商品分类.');
//		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
//		$info['end_time'] = strtotime($info['end_time']);
//		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		$info['descrip'] = $this->updateImgUrl($info['descrip']);
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Goods::getMallgoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Mall_Service_Goods::deleteMallgoods($id);
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
		$ret = Common::upload('imgFile', 'Mallgoods');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Mallgoods');
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

    private function _getCate()
    {
        list(, $categorys) = Mall_Service_Category::getAllMallCategory();
        $categorys = Common::resetKey($categorys, 'id');
        $areas = Common::getConfig('areaConfig', 'area');
        $categorys = $this->_cookCategory($areas, $categorys);
        $this->assign('categorys', $categorys);
    }
}
