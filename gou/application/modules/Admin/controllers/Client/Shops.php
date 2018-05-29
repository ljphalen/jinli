<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 * H5:1,apk(预装版):2,channel(渠道版)：3,market(穷购物):4
 *
 */
class Client_ShopsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Shops/index',
		'addUrl' => '/Admin/Client_Shops/add',
		'addPostUrl' => '/Admin/Client_Shops/add_post',
		'editUrl' => '/Admin/Client_Shops/edit',
		'editPostUrl' => '/Admin/Client_Shops/edit_post',
        'copyUrl' => '/Admin/Client_Shops/copy',
		'copyPostUrl' => '/Admin/Client_Shops/copy_post',
		'deleteUrl' => '/Admin/Client_Shops/delete',
		'viewUrl' => '/Admin/Client_Shops/view',
    	'uploadUrl' => '/Admin/Client_Shops/upload',
    	'uploadPostUrl' => '/Admin/Client_Shops/upload_post',
    	'uploadImgUrl' => '/Admin/Client_Shops/uploadImg',
	    'batchUpdateUrl'=>'/Admin/Client_Shops/batchUpdate',
	);

    public function init()
    {
        parent::init();
        $channels = array(1 => 'H5版', 2 => '预装版', 3 => '渠道版', 4 => '穷购物', 6 => 'iOS版');
        $this->assign('channels', $channels);
	}
	public $perpage = 15;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
        $params = $this->getInput(array('nick', 'shop_title', 'channel_id', 'tag_id'));
        $params['channel_id'] = $params['channel_id']?$params['channel_id']:2;
		$search = array();
		if ($params['nick'])        $search['nick']       = $params['nick'];
		if ($params['shop_title'])  $search['shop_title'] = array('LIKE', $params['shop_title']);
		if ($params['tag_id'])      $search['tag_id']     = array('LIKE', "-{$params['tag_id']}-");
		$search['channel_id'] = $params['channel_id'];

		list($total, $shops)  = Client_Service_Shops::getList($page, $perpage, $search);
        list(,$tags)          = Client_Service_Tag::getAllTag();

        $tag = Common::resetKey($tags,'id');
        $tag = array_map(function($v){return $v['name'];},$tag);
        $channels = array(1 => 'H5版', 2 => '预装版', 3 => '渠道版', 4 => '穷购物', 6 => 'iOS版');

        $deal  = function(&$v){
            $channel = array(1 => 'h5', 2 => '预装', 3 => '渠道', 4 => 'Market', 6 => 'ios');
            $tmp_arr = array_flip(array_keys(json_decode($v['url'],1)));
            $v['channel'] = array_intersect_key($channel,$tmp_arr);
        };
        array_walk($shops,$deal);

        foreach ($shops as &$v) {
            if (empty($v['tag_id'])) continue;
            $tag_id = array_filter(explode(',', $v['tag_id']));
            if (!empty($tag_id)) $tag_id = $this->_getTid($tag_id);
            if (empty($tag_id)) continue;
            $vTag = array_intersect_key($tag, array_flip($tag_id));
            $v['tag'] = implode(',', $vTag);
        }
        $this->assign('tags', $tags);
        $this->assign('channels', $channels);
        $this->assign('shops', $shops);
        $url = $this->actions['listUrl'] . '/?' . http_build_query($params) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('search', $params);
        $this->cookieParams();
	}
	
	/**
	 * 
	 * edit an Mallcategory
	 */
	public function editAction() {
		$id             = $this->getInput('id');
        list(,$tags)    = Client_Service_Tag::getAllTag();
        $info           = Client_Service_Shops::getShops(intval($id));
        $info['tag_id'] = $this->_getTid(explode(',',$info['tag_id']));
        $this->assign('tags', $tags);
        $this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
        list(,$tags) = Client_Service_Tag::getAllTag();
		$channel_id  = intval($this->getInput('channel_id'));
		$this->assign('areas', Common::getConfig('areaConfig', 'client_area'));
		$this->assign('channel_id', $channel_id);
		$this->assign('tags', $tags);
	}

    public function copyAction(){

        $id          = $this->getInput('id');
        $info        = Client_Service_Shops::getShops(intval($id));
        $info['url'] = json_decode($info['url'],1);
        $this->assign('info', $info);

        $channel = array(
          1 => 'h5',
          2 => '预装',
          3 => '渠道',
          4 => 'Market',
          6 => 'ios',
        );
        $this->assign('channel', $channel);
    }

    public function copy_postAction()
    {
        $channel_url = $this->getInput('url');
        $id          = $this->getInput('id');
        $info        = Client_Service_Shops::getShops(intval($id));
        unset($info['id']);
        unset($info['favorite_count']);
        $channel_url = array_map('html_entity_decode',array_filter($channel_url));
        $info['url'] = json_encode($channel_url);

        $ret = array();
        foreach ($channel_url as $channel_id=>$shop_url) {
            $nick = Client_Service_Shops::getBy(array('shop_id'=>$info['shop_id'], 'channel_id'=>$channel_id));
            $info['shop_url']        = $shop_url;
            $info['channel_id']      = $channel_id;
            if($nick && $nick['id'] != $info['id']) {
                $tmp = Client_Service_Shops::updateShops($info, intval($nick['id']));
            }else{
                $tmp = Client_Service_Shops::addShops($info);
            }
            $ret[]=$tmp;
        }
        $this->output(0, '操作成功.',$ret);
    }

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
        $info = $this->getPost(array('sort', 'shop_id', 'tag_id', 'logo', 'goods_img', 'goods_imgs', 'city', 'status', 'shop_title', 'channel_id', 'shop_url', 'description', 'shop_type'));
        if(empty($info['tag_id']))$this->output(-1, '标签不能为空.');
		$info = $this->_cookData($info);
		if(Client_Service_Shops::getBy(array('shop_id'=>$info['shop_id'], 'channel_id'=>$info['channel_id']))) $this->output(-1, '店铺已存在');
		$result = Client_Service_Shops::addShops($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->getShopData($result);
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
        //goods_imgs is input by textarea
		$info = $this->getPost(array('id', 'sort', 'shop_id', 'tag_id', 'logo', 'goods_img', 'goods_imgs', 'city', 'status', 'shop_title', 'channel_id', 'shop_url', 'description', 'shop_type'));
        if(empty($info['tag_id']))$this->output(-1, '标签不能为空.');
		$info = $this->_cookData($info);
		$nick = Client_Service_Shops::getBy(array('shop_id'=>$info['shop_id'], 'channel_id'=>$info['channel_id']));
		if($nick && $nick['id'] != $info['id']) $this->output(-1, '该店铺已存在');
		$ret = Client_Service_Shops::updateShops($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->getShopData($info['id']);
		$this->output(0, '操作成功.'); 		
	}


	/**
	 * @param $id
	 * @return bool
	 */
    public static function getShopData($id) {
	    if (!$id) return false;
	    $info = Client_Service_Shops::getBy(array('id'=>$id));
	    if(!$info) return false;
	    $miigouApi  = new Api_Miigou_Data();
	    
	    if($info['shop_type'] == 1) {
	        $miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop','taobao');
	    }else {
	        $miigouApi->shopInfo($info['shop_id'], $info['id'], 'shop','tmall');
	    }	    
	}

    public function infoAction(){
        $shop_id = $this->getInput('shop_id');

        $topApi  = new Api_Top_Service();
        $shop = $topApi->getTbkShopInfo(array('sids'=>$shop_id, 'is_mobile'=>'true'));

        $this->output(0,"",$shop);
    }
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Shops::getShops($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Shops::deleteShops($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * edit an Mallcategory
	 */
	public function viewAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Shops::getShops(intval($id));
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

    private function _getTid(array $tagId,$add=false){
        if(empty($tagId)) return false;
        if($add){
            $ret = array_map(function($v){return "-$v-";},$tagId);
        }else{
            $ret = array_map(function($v){return str_replace('-','',$v);},$tagId);
        }
        return $ret;
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($info) {
        if(!$info['shop_id']) $this->output(-1, 'shop_id不能为空.');
        if(!$info['shop_url']) $this->output(-1, '店铺链接不能为空.');
        $topApi  = new Api_Top_Service();
        $shop = $topApi->getTbkShopInfo(array('sids'=>$info['shop_id'], 'is_mobile'=>'true'));

        if(!$shop) {
            if(!$info['logo']) $this->output(-1, '请上传店铺logo.');
            if(!$info['shop_title']) $this->output(-1, '请填写店铺名称.');
        }

        if(!$info['shop_title']) $info['shop_title'] = $shop['shop_title'];
        if(!$info['nick']) $info['nick'] = $shop['seller_nick'];
        if(!$info['logo']) $info['logo'] = html_entity_decode($shop['pic_url']);
		$info['shop_url']=html_entity_decode($info['shop_url']);
        $info['tag_id'] = $this->_getTid($info['tag_id'],true);
        $info['tag_id']=implode(',',$info['tag_id']);
        if (empty($info['goods_img'])) {
            $info['goods_img'] = html_entity_decode($info['goods_imgs']);
        } else {
            $info['goods_img'] = implode(',', $info['goods_img']);
        }
        return $info;
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
            $ret = Client_Service_Shops::sort($data);
        }
        //开启
        if ($info['action'] == 'open') {
            $ret = Client_Service_Shops::updates($info['ids'], array('status'=>1));
        }
        //关闭
        if ($info['action'] == 'close') {
            $ret = Client_Service_Shops::updates($info['ids'], array('status'=>0));
        }
        if (!$ret) $this->output('-1', '操作失败.');
        $this->output('0', '操作成功.');
    }
}
