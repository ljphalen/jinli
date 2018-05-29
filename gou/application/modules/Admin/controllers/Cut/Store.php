<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 砍价商品库
 * @author ryan
 *
 */
class Cut_StoreController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cut_Store/index',
		'addUrl' => '/Admin/Cut_Store/add',
		'upUrl' => '/Admin/Cut_Goods/add',
		'getInfoUrl' => '/Admin/Cut_Store/get_info',
		'logUrl' => '/Admin/Cut_Log/index',
		'addPostUrl' => '/Admin/Cut_Store/add_post',
		'editUrl' => '/Admin/Cut_Store/edit',
		'editPostUrl' => '/Admin/Cut_Store/edit_post',
        'copyUrl' => '/Admin/Cut_Store/copy',
		'copyPostUrl' => '/Admin/Cut_Store/copy_post',
		'deleteUrl' => '/Admin/Cut_Store/delete',
		'viewUrl' => '/Admin/Cut_Store/view',
    	'uploadUrl' => '/Admin/Cut_Store/upload',
    	'uploadPostUrl' => '/Admin/Cut_Store/upload_post',
    	'uploadImgUrl' => '/Admin/Cut_Store/uploadImg',
	    'batchUpdateUrl'=>'/Admin/Cut_Store/batchUpdate',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
        $params  = $this->getInput(array('type_id', 'shop_id', 'title'));

		$search  = array('type'=>0);
		if ($params['title'])      $search['title']   = array('LIKE',$params['title']);;
        if ($params['type_id'])    $search['type_id'] = $params['type_id'];
        if ($params['shop_id'])    $search['shop_id'] = $params['shop_id'];
		$orderBy =array('sort'=>'DESC');
		list($total, $goods) = Cut_Service_Store::getList($page, $perpage, $search,$orderBy);
        list(,$shops)        = Cut_Service_Shops::getsBy(array());
        list(,$type)         = Cut_Service_Type::getsBy(array(),array('sort'=>'DESC'));
        $this->assign('shops', Common::resetKey($shops,'id'));
        $this->assign('type', Common::resetKey($type,'id'));
		$this->assign('goods', $goods);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $params);
		$this->cookieParams();
	}

	public function editAction() {
		$id   = $this->getInput('id');
        $info = Cut_Service_Store::getStore(intval($id));

        list(,$shops) =Cut_Service_Shops::getsBy(array('status'=>1));
        list(, $type) = Cut_Service_Type::getsBy(array(),array('sort'=>'DESC'));

        $this->assign('type', $type);
        $this->assign('shops', $shops);
        $this->assign('info', $info);
	}
	

	public function addAction() {
        $id = $this->getInput('id');

        list(,$shops) = Cut_Service_Shops::getsBy(array('status'=>1));
        list(, $type) = Cut_Service_Type::getsBy(array(),array('sort'=>'DESC'));

        $this->assign('type', $type);
        $this->assign('shops', $shops);
	}

	public function add_postAction() {
		$info = $this->getPost(array('title','share_title','type_id','img','price','sort','status'));
		$info = $this->_cookData($info);
		$result = Cut_Service_Store::addStore($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function edit_postAction() {
		$info = $this->getPost(array('id','title','share_title','type_id','img','price','sort','status'));
		$info = $this->_cookData($info);
		$ret  = Cut_Service_Store::updateStore($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

    public static function getShopData($id) {
	    if (!$id) return false;
	    $info = Cut_Service_Store::getBy(array('id'=>$id));
	    if(!$info) return false;
	    $miigouApi  = new Api_Miigou_Data();
	    
	    if($info['shop_type'] == 1) {
	        $miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop', 'taobao');
	    }else {
	        $miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop', 'tmall');
	    }	    
	}


	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Store::getStore($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Cut_Service_Store::deleteStore($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}

	public function get_infoAction(){
		$iid = $this->getInput('num_iid');
		if(empty($iid)) $this->output(-1,'商品ID不能为空');
		$topApi  = new Api_Top_Service();
		//41451187006,40758432866,41203240392
		$tkInfo = $topApi->getTbkItemInfo(array('num_iids'=>$iid,'is_mobile'=>false));
		if(empty($tkInfo)) $this->output(-1,'无法获取商品信息');
		$this->output(0,'',$tkInfo);
	}

	public function uploadImgAction() {
	    $ret = Common::upload('imgFile', 'shop');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
	    $ret = Common::upload('img', 'shop');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    //create webp image
	    /* if ($ret['code'] == 0) {
	        $attachPath = Common::getConfig('siteConfig', 'attachPath');
	        $file = realpath($attachPath.$ret['data']);
	        image2webp($file, $file.'.webp');
	    } */
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	/*
	 * 批量操作
	*
	*/
	function batchUpdateAction() {
	    $info = $this->getPost(array('action', 'ids', 'sort'));
	    if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
	     
	    //排序
	    if($info['action'] =='sort'){
	        $data = array();
	        foreach ($info['ids'] as $value) {
	            $data[$value] =  $info['sort'][$value];
	        }
	        $ret = Cut_Service_Store::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Cut_Service_Store::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Cut_Service_Store::updates($info['ids'], array('status'=>0));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
	}

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        /* if(!$info['shop_id']) $this->output(-1, 'shop_id不能为空.');
        if(!$info['num_iid']) $this->output(-1, '商品ID不能为空.'); */
		if(!$info['price']) $this->output(-1, '请填写商品原价.');
		if(!$info['title']) $this->output(-1, '请填写商品名称.');
		if(!$info['share_title']) $this->output(-1, '请填写商品分享标题.');
		if(!$info['img'])   $this->output(-1, '请上传商品图片.');
		//$info['description'] = $this->updateImgUrl(html_entity_decode($info['description']));
        return $info;
    }
}
