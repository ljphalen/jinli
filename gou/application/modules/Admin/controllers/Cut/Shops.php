<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 砍价店铺
 * @author ryan
 *
 *
 */
class Cut_ShopsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cut_Shops/index',
		'addUrl' => '/Admin/Cut_Shops/add',
		'addPostUrl' => '/Admin/Cut_Shops/add_post',
		'editUrl' => '/Admin/Cut_Shops/edit',
		'editPostUrl' => '/Admin/Cut_Shops/edit_post',
        'copyUrl' => '/Admin/Cut_Shops/copy',
		'copyPostUrl' => '/Admin/Cut_Shops/copy_post',
		'deleteUrl' => '/Admin/Cut_Shops/delete',
		'viewUrl' => '/Admin/Cut_Shops/view',
    	'uploadUrl' => '/Admin/Cut_Shops/upload',
    	'uploadPostUrl' => '/Admin/Cut_Shops/upload_post',
    	'uploadImgUrl' => '/Admin/Cut_Shops/uploadImg',
	);
	
	public $perpage = 20;

	public $shop_type=array(
		1=>'淘宝',
		2=>'天猫'
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
        $params=$this->getInput(array('nick','shop_title','channel_id','tag_id'));
		$search = array();
		if ($params['nick']) $search['nick'] = $params['nick'];
		if ($params['shop_title']) $search['shop_title'] = array('LIKE',$params['shop_title']);
		if ($params['tag_id']) $search['tag_id'] = array('LIKE',"-{$params['tag_id']}-");
		$search['channel_id'] = $params['channel_id'];

		list($total, $shops) = Cut_Service_Shops::getList($page, $perpage, $search);
        $shop_id = Common::resetKey($shops,'shop_id');
        list(,$ext) = Cut_Service_Store::getsBy(array('type'=>1,'shop_id'=>array('IN',array_keys($shop_id))));
        foreach ($ext as $v) {
            $pic[$v['shop_id']][]=$v['img'];
        }
        $this->assign('shops', $shops);
        $this->assign('pic', $pic);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('search', $params);
		$this->assign('shop_type', $this->shop_type);
		$this->cookieParams();
	}
	
	/**
	 * 
	 * edit an Mallcategory
	 */
	public function editAction() {
		$id = $this->getInput('id');
        $info = Cut_Service_Shops::getShops(intval($id));
		$this->assign('shop_type', $this->shop_type);
        $this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('shop_type', $this->shop_type);
	}


    /**
     * 添加扩展链接
     * @param $info
     */
    private function _addExt($info){
        $topApi  = new Api_Top_Service();
        $tkInfo = $topApi->getTbkItemInfo(array('num_iids'=>html_entity_decode($info['goods_ids'])));
        $tkInfo = Common::resetKey($tkInfo,'num_iid');
        foreach ($tkInfo as $item) {
            $item['shop_id']=$info['shop_id'];
            $item['type']   =1;
            Cut_Service_Store::updateExt($item,array('num_iid'=>$item['num_iid'],'shop_id'=>$item['shop_id'],'type'=>1));
        }
    }
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('shop_id', 'logo', 'goods_ids', 'status', 'shop_title', 'shop_type', 'shop_url'));
		$info = $this->_cookData($info);
		if(Cut_Service_Shops::getBy(array('shop_id'=>$info['shop_id']))) $this->output(-1, '店铺已存在');
		$result = Cut_Service_Shops::addShops($info);
        $this->_addExt($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->getShopData($result);
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'shop_id', 'logo', 'goods_ids', 'status', 'shop_title', 'shop_type', 'shop_url'));
		$info = $this->_cookData($info);
		$nick = Cut_Service_Shops::getBy(array('shop_id'=>$info['shop_id']));
		if($nick && $nick['id'] != $info['id']) $this->output(-1, '该店铺已存在');
		$ret = Cut_Service_Shops::updateShops($info, intval($info['id']));
        $this->_addExt($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->getShopData($info['id']);
		$this->output(0, '操作成功.'); 		
	}
	
	
	/**
	 * get shop data
	 * @param unknown_type $shop_id
	 * @param unknown_type $type
	 * @return boolean
	 */
    public static function getShopData($id) {
	    if (!$id) return false;
	    $info = Cut_Service_Shops::getBy(array('id'=>$id));
	    if(!$info) return false;
	    $miigouApi  = new Api_Miigou_Data();

		if($info['shop_type'] == 1) {
			$miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop','taobao');
		}else {
			$miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop','tmall');
		}
	}


	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Shops::getShops($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Cut_Service_Shops::deleteShops($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * edit an Mallcategory
	 */
	public function viewAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Shops::getShops(intval($id));
		$topApi  = new Api_Top_Service();
		$shop = $topApi->TaobaokeShopsConvert(array('seller_nicks'=>$info['nick'], 'is_mobile'=>"true"));
		if(!$shop) exit('Oh Fuck! shop title error!');
		$this->redirect($shop['click_url']);
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
	    $ret = Common::upload('imgFile', 'shop');
	    if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
	    exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
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
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if(!$info['shop_id']) $this->output(-1, 'shop_id不能为空.');
        if(!$info['shop_url']) $this->output(-1, '店铺链接不能为空.');
		$info['shop_url']=html_entity_decode($info['shop_url']);
		$topApi  = new Api_Top_Service();
		$shop = $topApi->getTbkShopInfo(array('sids'=>$info['shop_id'], 'is_mobile'=>'true'));

		if(!$shop) {
			if(!$info['logo']) $this->output(-1, '请上传店铺logo.');
			if(!$info['shop_title']) $this->output(-1, '请填写店铺名称.');
		}
        if(!$info['shop_title']) $info['shop_title'] = $shop['shop_title'];
        if(!$info['logo']) $info['logo'] = $shop['pic_url'];
        if(!$info['goods_ids'])return $info;
        return $info;

    }
}
