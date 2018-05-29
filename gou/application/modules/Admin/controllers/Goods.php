<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Goods/index',
		'addUrl' => '/Admin/Goods/add',
		'addPostUrl' => '/Admin/Goods/add_post',
		'editUrl' => '/Admin/Goods/edit',
		'editPostUrl' => '/Admin/Goods/edit_post',
		'deleteUrl' => '/Admin/Goods/delete',
		'uploadUrl' => '/Admin/Goods/upload',
		'uploadPostUrl' => '/Admin/Goods/upload_post',
		'uploadImgUrl' => '/Admin/Goods/uploadImg',
		'step1Url' => '/Admin/Goods/add_step1',
		'step2Url' => '/Admin/Goods/add_step2'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$subject_id = intval($this->getInput('subject_id'));
		$status = $this->getInput('status');
		if ($page < 1) $page = 1;
		//get all subjects
		list(,$subjects) = Gou_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');
		$this->assign('subjects', $subjects);
		
		//get search params
		$search = array();
		if ($subject_id) $search['subject_id'] = $subject_id;
		if ($status != -1) $search['status'] = $status;
		
		//get goods list
		list($total, $goods) = Gou_Service_Goods::getList($page, $this->perpage, $search);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('search', $search);
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
		$info = Gou_Service_Goods::getGoods(intval($id));
		
		list(,$subjects) = Gou_Service_Subject::getList(0, 100, array('st_type'=>2));
		$subjects = Common::resetKey($subjects, 'id');
		$this->assign('subjects', $subjects);
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
		/*$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'cid'=>$cid, 'keyword'=>$keyword, 'is_mobile'=>"true"));
		
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
		}*/
		
		$ret = $topApi->taobaoTbkItemsGet(array('page_no'=>$page, 'page_size'=>$this->perpage, 'cid'=>$cid, 'keyword'=>$keyword, 'sort'=>"commissionNum_desc"));
		
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		//get taobao num_iids
		/* $num_iids = array();
		foreach($goods as $key=>$value) {
			$num_iids[] = $value['num_iid'];
		}
		
		//get info infos;
		if (count($num_iids)) {
			//$infos = $topApi->getItemInfos($num_iids);
			$infos = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>implode(',',$num_iids)));
			if (isset($infos['num_iid'])) $infos = array($infos);
			$infos = Common::resetKey($infos, 'num_iid');
		} */
		
		$this->assign('goods', $goods);
		//$this->assign('infos', $infos);		

		//get taobao item cates
		/* $item_cats = $topApi->getTaoBaoItemCats();
		$this->assign('item_cats', $item_cats['item_cats']['item_cat']);
		
		$this->assign('cid', $cid); */
		$this->assign('keyword', $keyword);
		
		$url = $this->actions['step1Url'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * add goods page show
	 */
	public function add_step2Action() {
		$num_iid = $this->getInput('num_iid');
		//get all subjects
		list(,$subjects) = Gou_Service_Subject::getList(0, 100, array('st_type'=>2));
		$subjects = Common::resetKey($subjects, 'id');
		$this->assign('subjects', $subjects);
		$topApi  = new Api_Top_Service();
		$info = $topApi->getTbkItemInfo(array('num_iids'=>$num_iid));
		/* 
		$taokeInfo = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$num_iid));
		
		$info['commission'] = $taokeInfo['commission'];
		
		$info['price'] = $taokeInfo['promotion_price'];
		$info['item_imgs'] = $info['item_imgs']['item_img'];
		if(isset($info['item_imgs']['url'])) $info['item_imgs']  = array($info['item_imgs']); */
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'subject_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Goods::addGoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'subject_id', 'img', 'num_iid', 'price', 'commission', 'start_time', 'end_time', 'status', 'default_want', 'descrip'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Goods::updateGoods($info, intval($info['id']));
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
		if(!$info['subject_id']) $this->output(-1, '请选择主题分类.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
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
		$info = Gou_Service_Goods::getGoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Goods::deleteGoods($id);
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
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
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
