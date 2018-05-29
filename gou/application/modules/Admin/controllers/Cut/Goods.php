<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 砍价商品
 * @author ryan
 *
 */
class Cut_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cut_Goods/index',
		'addUrl' => '/Admin/Cut_Goods/add',
		'stepUrl' => '/Admin/Cut_Store/index',
        'logUrl' => '/Admin/Cut_User/index',
		'addPostUrl' => '/Admin/Cut_Goods/add_post',
		'editUrl' => '/Admin/Cut_Goods/edit',
		'editPostUrl' => '/Admin/Cut_Goods/edit_post',
		'deleteUrl' => '/Admin/Cut_Goods/delete',
		'uploadUrl' => '/Admin/Cut_Goods/upload',
		'uploadPostUrl' => '/Admin/Cut_Goods/upload_post',
		'uploadImgUrl' => '/Admin/Cut_Goods/uploadImg',
		'step1Url' => '/Admin/Cut_Goods/add_step1',
		'step2Url' => '/Admin/Cut_Goods/add_step2'
	);

	public $status = array(
	  1 => '上架',
	  2 => '已结束',
	  3 => '待领取',
	  4 => '已领奖',
	  10 => '失效',
	  //4 => '已购买',
	  //5 => '下架',
	);


	public $perpage = 20;
	/**
	 *
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$status = $this->getInput('status');

		if ($page < 1) $page = 1;
		$search = array();
		$params  = $this->getInput(array('id', 'no', 'type_id', 'store_id', 'start_time', 'end_time', 'status', 'shop_id', 'title'));
        if ($params['id']) $search['id'] = $params['id'];
        if ($params['no']) $search['no'] = $params['no'];
        if ($params['title']) $search['title'] = array('LIKE', $params['title']);
        if ($params['type_id']) $search['type_id'] = $params['type_id'];
        if ($params['shop_id']) $search['shop_id'] = $params['shop_id'];
        if ($params['store_id']) $search['store_id'] = $params['store_id'];
        if ($params['start_time']) $search['start_time'] = array('>=', strtotime($params['start_time'] . " 00:00:00"));
        if ($params['end_time']) $search['end_time'] = array('<=', strtotime($params['end_time'] . " 23:59:59"));
        if ($params['status']) $search['status'] = $params['status'];

		//get search params
		$sort = array('start_time'=>'DESC','id'=>'DESC');
		//获取商品库的记录
        list($total, $data) = Cut_Service_Goods::getList($page, $this->perpage, $search, $sort);
        $arr = Common::resetKey($data, 'store_id');
        if (!empty($arr)) list(, $store) = Cut_Service_Store::getsBy(array('id' => array('IN', array_keys($arr))));

		//现价计算
//		$arr = Common::resetKey($data, 'id');
//        if(!empty($arr)) list(, $logs)  = Cut_Service_Log::getsBy(array('goods_id' => array('IN', array_keys($arr))));
//        foreach ($logs as $x) {
//			$log[]                    = $x['goods_id'];
//			$prices[$x['goods_id']][] = $x['price'];
//        }
//		foreach ($data as &$item) {
//            $item['cur_price'] = min($prices[$item['id']]);
//		}


        $authors = Gou_Service_UserAuthor::getAuthors();

        list(, $shops) = Cut_Service_Shops::getsBy(array());
        list(, $type) = Cut_Service_Type::getsBy(array(), array('sort' => 'DESC'));

        $this->assign('shops', Common::resetKey($shops, 'id'));
        $this->assign('type', Common::resetKey($type, 'id'));
//        $this->assign('log', array_count_values($log));
        $this->assign('data', $data);
        $this->assign('authors', $authors);
        $this->assign('store', Common::resetKey($store, 'id'));
        $this->assign('total', $total);
        $this->assign('search', $params);
        $this->assign('status', $this->status);

		//get pager
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {

		$id = $this->getInput('id');
        $info = Cut_Service_Goods::getgoods(intval($id));
        $item = Cut_Service_Store::getStore($info['store_id']);
        $authors = Gou_Service_UserAuthor::getAuthors();
        $this->assign('info', $info);
        $this->assign('item', $item);
        $this->assign('authors',$authors);
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * taobaoke Cut goods list
	 */
	public function addAction() {
		$store_id = $this->getInput('store_id');
		$item = Cut_Service_Store::getStore($store_id);
        $authors = Gou_Service_UserAuthor::getAuthors();
        $this->assign('item', $item);
        $this->assign('authors',$authors);
        $this->assign('status', $this->status);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('store_id', 'price', 'author_id', 'author_say', 'min_price', 'range', 'start_time', 'end_time', 'sort', 'status', 'no', 'increase'));
		$info = $this->_cookData($info);
		$result = Cut_Service_Goods::addgoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','store_id', 'price', 'author_id', 'author_say', 'min_price', 'range', 'start_time', 'end_time', 'sort', 'status', 'no', 'increase', 'shortest_time', 'uid'));
		$info = $this->_cookData($info);
		$ret = Cut_Service_Goods::updategoods($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cut_Service_Goods::getgoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Cut_Service_Goods::deletegoods($id);
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
		$ret = Common::upload('imgFile', 'Cutgoods');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}

	public function upload_postAction() {
		$ret = Common::upload('img', 'Cutgoods');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }


    private function _cookData($info) {
        $item = Cut_Service_Store::getStore($info['store_id']);
        $info['title']=$item['title'];
        //$info['shop_id']=$item['shop_id'];
        $info['type_id']=$item['type_id'];
        if(!$info['title']) $this->output(-1, '商品名称不能为空.');
        if(!$info['no']) $this->output(-1, '期数不能为空.');
        if(!$info['price']) $this->output(-1, '请填写起始价.');
        if(!$info['min_price']) $this->output(-1, '请填写最低价.');
        if(!$info['range']) $this->output(-1, '请填写最高价.');
        if(!$info['increase']) $this->output(-1, '请填写N次增幅.');
        if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
        if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
        $info['start_time'] = strtotime($info['start_time'].':00');
        $info['end_time'] = strtotime($info['end_time'].':59');
		if($item['price'] < $info['min_price'])$this->output(-1, '最低价不能大于商品原价.');
		if($info['price'] < $info['range'])$this->output(-1, '低高不能大于商品起拍价.');
		if(($info['range'] - $info['min_price']) < 0)$this->output(-1, '最高价须不能小于最低价.');
        if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
        return $info;
    }

}
